import { expect, test } from "@playwright/test";
import { login, RELATED_STRUCTURE, STRUCTURE, visit } from "../helpers";

/**
 * Parámetros que llegan como array (s[]=x) en lugar de texto. En PHP 8 acababan en
 * TypeError y error 500; ahora se tratan como si no se hubieran enviado.
 */

test.beforeEach(async ({ page }) => {
    await login(page, "developer");
});

test("La búsqueda del listado con s[] se trata como búsqueda vacía", async ({ page }) => {
    await visit(page, `content.php?a=list_contents&id=${encodeURIComponent(STRUCTURE)}&s[]=x`);
    await expect(page.locator("input[name=s]")).toHaveValue("");
});

test("Ficha y búsqueda de relaciones aceptan parámetros como array", async ({ page }) => {
    await visit(page, `content.php?a=list_contents&id=${encodeURIComponent(STRUCTURE)}`);
    const first = page.locator('#contents_list a[href*="a=edit"]').first();
    test.skip((await first.count()) === 0, `La estructura ${STRUCTURE} no tiene contenidos`);
    const query = ((await first.getAttribute("href")) ?? "").split("?")[1];

    await visit(page, `content.php?${query}&idp[]=x&idtp[]=y`);

    const relSearch = page.locator('a[href*="content_rel.php?a=select_type"]').first();
    test.skip((await relSearch.count()) === 0, `La estructura ${STRUCTURE} no tiene campos de relación`);
    const relHref = (await relSearch.getAttribute("href")) ?? "";
    await visit(page, `${relHref.replace("a=select_type", "a=search")}&idt=${encodeURIComponent(RELATED_STRUCTURE)}&s[]=x`);
    await expect(page.locator("input[name=s]")).toHaveValue("");
});

test("Un id como array no crea una estructura sin id", async ({ page }) => {
    const response = await page.request.post("do_process_structure.php", {
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        data: "a=new&id%5B%5D=x&name=e2e&storage=mongodb",
        maxRedirects: 0
    });

    expect(response.status()).toBe(302);
    expect(response.headers()["location"]).toContain("r=ko");
    // Con una estructura sin id guardada, el listado de estructuras fallaría
    await visit(page, "index.php");
});
