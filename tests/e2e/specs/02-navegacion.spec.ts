import { expect, Page, test } from "@playwright/test";
import { login, MAX_STRUCTURES, Rol, visit } from "../helpers";

/**
 * Recorrido de solo lectura: listados, búsqueda, paginación, ficha,
 * resumen, relaciones y alias-id de las primeras estructuras.
 */

async function structureIds(page: Page): Promise<string[]> {
    await visit(page, "content.php?a=list_structures");

    const hrefs = await page
        .locator('main a[href*="a=list_contents"]')
        .evaluateAll((links) => links.map((a) => a.getAttribute("href") ?? ""));
    const ids = hrefs.map((href) => new URLSearchParams(href.split("?")[1]).get("id") ?? "");

    expect(ids.length, "Hay estructuras en el editor").toBeGreaterThan(0);

    return ids.slice(0, MAX_STRUCTURES);
}

for (const rol of ["developer", "editor"] as Rol[]) {
    test(`Recorrido de lectura con el rol ${rol}`, async ({ page }) => {
        test.setTimeout(10 * 60 * 1000);
        await login(page, rol);

        for (const id of await structureIds(page)) {
            await test.step(`Estructura ${id}`, async () => {
                const list = `content.php?a=list_contents&id=${encodeURIComponent(id)}`;

                await visit(page, list);
                await visit(page, `${list}&p=1`);
                await visit(page, `content.php?a=new&idt=${encodeURIComponent(id)}`);
                // Caracteres especiales de expresiones regulares en la búsqueda
                for (const search of ["a", "(test)", ".*", "[", "ñ"]) {
                    await visit(page, `${list}&s=${encodeURIComponent(search)}`);
                }

                await visit(page, list);
                const first = page.locator('#contents_list a[href*="a=edit"]').first();
                if ((await first.count()) === 0) {
                    return;
                }
                const edit = (await first.getAttribute("href")) ?? "";
                const query = edit.split("?")[1];

                await visit(page, `content.php?${query}`);
                const alias = page.locator('a[href^="alias_id.php"]').first();
                const aliasHref = (await alias.count()) ? await alias.getAttribute("href") : null;
                const relSearch = page.locator('a[href*="content_rel.php?a=select_type"]').first();
                const relHref = (await relSearch.count()) ? await relSearch.getAttribute("href") : null;

                await visit(page, `content.php?${query.replace("a=edit", "a=summary")}`);
                await visit(page, `content.php?${query.replace("a=edit", "a=clone")}`);
                await visit(page, `relation.php?${query.replace("a=edit&", "")}`);
                if (aliasHref) {
                    await visit(page, aliasHref);
                }
                if (relHref) {
                    await visit(page, relHref);
                }
            });
        }
    });
}
