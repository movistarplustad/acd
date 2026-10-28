import { expect, test } from "@playwright/test";
import { login, MISSING_ID, STRUCTURE, visit } from "../helpers";

/** Contenidos inexistentes (NEODES-618) y navegación sin historial */

test.beforeEach(async ({ page }) => {
    await login(page, "developer");
});

test("Editar un contenido inexistente devuelve 404 con enlace al listado", async ({ page }) => {
    await visit(page, `content.php?a=edit&id=${MISSING_ID}&idt=${encodeURIComponent(STRUCTURE)}`, 404);
    await expect(page.locator("body")).toContainText(`content with ID ${MISSING_ID} not found`);
    await expect(page.locator('a[href*="a=list_contents"]')).toBeAttached();
});

test("Un id no válido también devuelve 404", async ({ page }) => {
    await visit(page, `content.php?a=edit&id=no-valido&idt=${encodeURIComponent(STRUCTURE)}`, 404);
});

test("El 404 escapa el id recibido", async ({ page }) => {
    await visit(page, `content.php?a=edit&id=${encodeURIComponent("<b>x</b>")}&idt=${encodeURIComponent(STRUCTURE)}`, 404);
    await expect(page.locator("body b")).toHaveCount(0);
});

test("Relaciones de un contenido inexistente devuelve 404", async ({ page }) => {
    await visit(page, `relation.php?id=${MISSING_ID}&idt=${encodeURIComponent(STRUCTURE)}`, 404);
});

test("Volver sin historial lleva a la portada", async ({ page }) => {
    await page.goto("back.php?p=50");
    await expect(page).toHaveURL(/\/(index\.php)?(\?.*)?$/);
});
