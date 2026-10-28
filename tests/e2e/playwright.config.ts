import { defineConfig, devices } from "@playwright/test";
import fs from "fs";
import path from "path";

// Variables de entorno primero; .env solo rellena las que falten
const envFile = path.resolve(import.meta.dirname, ".env");
if (fs.existsSync(envFile)) {
    process.loadEnvFile(envFile);
}

export default defineConfig({
    timeout: 60000,
    testDir: "./specs",
    // Las pruebas de escritura comparten datos: se ejecutan en orden
    fullyParallel: false,
    workers: 1,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 1 : 0,
    reporter: [["list"], ["html", { open: "never" }]],
    use: {
        baseURL: process.env.ACD_E2E_BASE_URL ?? "http://127.0.0.1:18085/",
        trace: "retain-on-failure",
        locale: "es-ES",
        timezoneId: "Europe/Madrid"
    },
    projects: [
        {
            name: "chromium",
            use: { ...devices["Desktop Chrome"] }
        }
    ]
});
