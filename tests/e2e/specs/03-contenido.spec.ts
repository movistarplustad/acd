import { expect, Page, test } from "@playwright/test";
import {
    contentAction,
    contentId,
    expectPageWithoutPhpErrors,
    login,
    PNG_1X1,
    RELATED_SEARCH,
    RELATED_STRUCTURE,
    STRUCTURE,
    visit
} from "../helpers";

/**
 * Ciclo de vida de un contenido: alta, edición, subida de fichero,
 * relaciones, clonado y borrado. Las pruebas dependen unas de otras y
 * dejan la base de datos como estaba.
 */
test.describe.configure({ mode: "serial" });

const TITLE = `E2E PHP 8.5 ñáé ${Date.now()}`;
const ALIAS = `e2e-php85-${Date.now()}`;
let page: Page;
let id = "";
let cloneId = "";
let childId = "";

function editUrl(contentId: string, structure = STRUCTURE) {
    return `content.php?a=edit&id=${contentId}&idt=${encodeURIComponent(structure)}`;
}

async function deleteContent(contentId: string) {
    await visit(page, editUrl(contentId));
    page.once("dialog", (dialog) => dialog.accept());
    await contentAction(page, "delete");
    await expect(page).toHaveURL(/a=delete&r=ok/);
    await expect(page.locator(".result")).toContainText("Done");
}

test.beforeAll(async ({ browser }) => {
    page = await browser.newPage();
    await login(page, "editor");
});

test.afterAll(async () => {
    // Limpieza si alguna prueba falló a medias
    for (const pending of [cloneId, id]) {
        if (pending) {
            await deleteContent(pending).catch(() => undefined);
        }
    }
    await page.close();
});

test("Crear un contenido", async () => {
    await visit(page, `content.php?a=new&idt=${encodeURIComponent(STRUCTURE)}`);
    await page.locator('input[name^="title["]').fill(TITLE);
    await page.locator('input[name^="aliasId["]').fill(ALIAS);
    await contentAction(page, "save");

    await expect(page).toHaveURL(/a=edit&r=ok/);
    await expectPageWithoutPhpErrors(page);
    id = contentId(page);
    expect(id).toMatch(/^[0-9a-f]{24}$/);
    await expect(page.locator('input[name^="title["]')).toHaveValue(TITLE);
    await expect(page.locator('input[name^="aliasId["]')).toHaveValue(ALIAS);
});

test("Subir un fichero y verlo en la vista previa", async () => {
    await visit(page, editUrl(id));
    await page.locator("input[type=file]").first().setInputFiles({
        name: "e2e.png",
        mimeType: "image/png",
        buffer: PNG_1X1
    });
    await contentAction(page, "save");
    await expectPageWithoutPhpErrors(page);

    const preview = page.locator('img[src^="file_preview.php"]').first();
    await expect(preview).toBeVisible();
    const response = await page.request.get((await preview.getAttribute("src")) ?? "");
    expect(response.status()).toBe(200);
    expect(response.headers()["content-type"]).toContain("image/png");
    expect((await response.body()).equals(PNG_1X1)).toBe(true);
});

test("Relacionar contenidos desde la búsqueda", async () => {
    await visit(page, editUrl(id));
    await page.locator('a[href*="content_rel.php?a=select_type"]').first().click();
    await expectPageWithoutPhpErrors(page);

    await page.locator("#idt").selectOption(RELATED_STRUCTURE);
    await page.locator("#title").fill(RELATED_SEARCH);
    await page.locator('input[type=submit][name=a][value="search"]').click();
    await expectPageWithoutPhpErrors(page);

    const results = page.locator('input[name="posElement[]"]');
    expect(await results.count(), "La búsqueda devuelve resultados").toBeGreaterThan(1);
    await results.nth(0).check();
    await results.nth(1).check();
    childId = (await page.locator('input[name$="[refm]"]').nth(0).getAttribute("value")) ?? "";
    await page.locator('input[type=submit][value="add bottom"]').click();
    await expectPageWithoutPhpErrors(page);

    await contentAction(page, "save");
    await expectPageWithoutPhpErrors(page);
    await expect(page.locator(`a[href*="id=${childId}"]`).first()).toBeAttached();

    await visit(page, `relation.php?id=${childId}&idt=${encodeURIComponent(RELATED_STRUCTURE)}`);
    await expect(page.locator("#enumerated_list")).toContainText(TITLE);
});

test("No se puede borrar un contenido que tiene padres", async () => {
    await visit(page, editUrl(childId, RELATED_STRUCTURE));
    await expect(page.locator('input[type=submit][name=a][value="delete"]')).toBeDisabled();
});

test("Ver el resumen", async () => {
    await visit(page, `content.php?a=summary&id=${id}&idt=${encodeURIComponent(STRUCTURE)}`);
    await expect(page.locator("main")).toContainText(TITLE);
});

test("Clonar un contenido", async () => {
    await visit(page, editUrl(id));
    await contentAction(page, "clone");
    await expect(page.locator('input[name^="title["]')).toHaveValue(`[copy] ${TITLE}`);
    await contentAction(page, "save");

    await expect(page).toHaveURL(/a=edit&r=ok/);
    await expectPageWithoutPhpErrors(page);
    cloneId = contentId(page);
    expect(cloneId).toMatch(/^[0-9a-f]{24}$/);
    expect(cloneId).not.toBe(id);
});

test("Borrar la copia y el original", async () => {
    for (const deleted of [cloneId, id]) {
        await deleteContent(deleted);
        await visit(page, editUrl(deleted), 404);
    }
    cloneId = "";
    id = "";

    // Al borrar el padre desaparecen sus relaciones
    await visit(page, `relation.php?id=${childId}&idt=${encodeURIComponent(RELATED_STRUCTURE)}`);
    await expect(page.locator("#enumerated_list")).not.toContainText(TITLE);
});
