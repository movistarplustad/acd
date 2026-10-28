import { expect, Page, Response } from "@playwright/test";

/**
 * Textos que delatan un error de PHP volcado en la página. En PHP 8.5 los
 * avisos que antes eran silenciosos pueden acabar en la salida o en un 500.
 */
const PHP_ERROR_PATTERNS = [
    /Fatal error:/i,
    /Parse error:/i,
    /<b>Warning<\/b>:/i,
    /<b>Deprecated<\/b>:/i,
    /<b>Notice<\/b>:/i,
    /Uncaught (Error|Exception|TypeError)/i,
    /Stack trace:/i,
    /Undefined (variable|array key|offset|property)/i
];

export const ROLES = {
    developer: process.env.ACD_E2E_DEVELOPER_USER ?? "",
    editor: process.env.ACD_E2E_EDITOR_USER ?? ""
};
export type Rol = keyof typeof ROLES;

export const PASSWORD = process.env.ACD_E2E_PASSWORD ?? "";
export const STRUCTURE = process.env.ACD_E2E_STRUCTURE ?? "abonos";
export const RELATED_STRUCTURE = process.env.ACD_E2E_RELATED_STRUCTURE ?? "menu";
export const RELATED_SEARCH = process.env.ACD_E2E_RELATED_SEARCH ?? "movistar";
export const MAX_STRUCTURES = Number(process.env.ACD_E2E_MAX_STRUCTURES ?? 20);

/** Id con formato válido de MongoDB que no existe */
export const MISSING_ID = "0123456789abcdef01234567";

export function findPhpErrors(body: string): string[] {
    return PHP_ERROR_PATTERNS.filter((pattern) => pattern.test(body)).map(
        (pattern) => pattern.source
    );
}

export async function expectPageWithoutPhpErrors(page: Page) {
    expect(
        findPhpErrors(await page.content()),
        `La página ${page.url()} contiene errores de PHP`
    ).toEqual([]);
}

/** Abre una ruta y comprueba el estado HTTP y que no haya errores de PHP */
export async function visit(page: Page, url: string, status = 200) {
    const response = (await page.goto(url)) as Response;

    expect(response, `Sin respuesta para ${url}`).not.toBeNull();
    expect(response.status(), `Estado HTTP de ${url}`).toBe(status);
    await expectPageWithoutPhpErrors(page);

    return response;
}

export async function login(page: Page, rol: Rol) {
    expect(ROLES[rol], `Falta el usuario de ${rol} en .env`).not.toBe("");
    expect(PASSWORD, "Falta ACD_E2E_PASSWORD en .env").not.toBe("");

    await page.goto("index.php");
    await page.locator("#login").fill(ROLES[rol]);
    await page.locator("#password").fill(PASSWORD);
    await page.locator('input[type=submit][value="Enter"]').click();
    await expect(page.locator(".logout")).toContainText(ROLES[rol]);
}

/** Pulsa uno de los botones de acción de la ficha (save, clone, delete...) */
export async function contentAction(page: Page, action: string) {
    await page.locator(`input[type=submit][name=a][value="${action}"]`).click();
    await page.waitForLoadState();
}

/** Id del contenido abierto en la ficha de edición */
export function contentId(page: Page): string {
    return new URL(page.url()).searchParams.get("id") ?? "";
}

/** Imagen PNG mínima (1x1) para las pruebas de subida */
export const PNG_1X1 = Buffer.from(
    "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==",
    "base64"
);
