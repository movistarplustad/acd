import { expect, test } from "@playwright/test";
import { expectPageWithoutPhpErrors, login, PASSWORD, ROLES, visit } from "../helpers";

/** Login, logout y permisos por rol */

test("La página de login se muestra sin errores", async ({ page }) => {
    await visit(page, "index.php");
    await expect(page.locator("#login")).toBeVisible();
    await expect(page.locator("#password")).toBeVisible();
});

test("Sin sesión, el editor redirige al login", async ({ page }) => {
    await page.goto("content.php?a=list_structures");
    await expect(page).toHaveURL(/index\.php\?re=/);
    await expectPageWithoutPhpErrors(page);
});

test("Una contraseña incorrecta no inicia sesión", async ({ page }) => {
    await page.goto("index.php");
    await page.locator("#login").fill(ROLES.developer);
    await page.locator("#password").fill(PASSWORD + "-incorrecta");
    await page.locator('input[type=submit][value="Enter"]').click();

    await expect(page.locator(".logout")).toHaveCount(0);
    await expectPageWithoutPhpErrors(page);
});

test("El rol developer accede a estructuras, enumerados y usuarios", async ({ page }) => {
    await login(page, "developer");

    await visit(page, "index.php");
    await expect(page.locator("main a").first()).toBeVisible();
    await visit(page, "enumerated.php");
    await visit(page, "user.php");
});

test("El rol editor no accede a enumerados ni usuarios", async ({ page }) => {
    await login(page, "editor");

    await visit(page, "content.php?a=list_structures");
    await visit(page, "enumerated.php", 403);
    await visit(page, "user.php", 403);
});

test("Logout cierra la sesión", async ({ page }) => {
    await login(page, "developer");
    // El enlace está en el menú plegado de herramientas
    await page.goto((await page.locator(".logout").getAttribute("href")) ?? "");

    await page.goto("content.php?a=list_structures");
    await expect(page).toHaveURL(/index\.php\?re=/);
});
