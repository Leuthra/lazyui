import { defineConfig, devices } from '@playwright/test';

/**
 * Visual regression for the LazyUI panel design, against the workbench (composer workbench).
 *
 *   npm run test:visual           compare with the baselines
 *   npm run test:visual:update    accept the current look as the new baseline
 *
 * Baselines depend on fonts and rendering, so each environment keeps its own set:
 * tests/visual/__screenshots__/<LAZYUI_SNAPSHOTS>/ ("ci" is committed and used by GitHub Actions,
 * "local" is gitignored).
 */
const snapshots = process.env.LAZYUI_SNAPSHOTS ?? 'local';
const auth = 'tests/visual/.auth/admin.json';

export default defineConfig({
    testDir: './tests/visual',
    snapshotPathTemplate: `{testDir}/__screenshots__/${snapshots}/{projectName}/{arg}{ext}`,
    fullyParallel: false,
    timeout: 90_000,
    workers: 1,
    retries: process.env.CI ? 1 : 0,
    reporter: process.env.CI ? [['github'], ['html', { open: 'never' }]] : 'list',
    use: {
        baseURL: 'http://127.0.0.1:8100',
        trace: 'retain-on-failure',
    },
    expect: {
        timeout: 20_000,
        toHaveScreenshot: { maxDiffPixelRatio: 0.01, animations: 'disabled', caret: 'hide' },
    },
    projects: [
        { name: 'setup', testMatch: /auth\.setup\.ts/ },
        { name: 'light', dependencies: ['setup'], use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 900 }, colorScheme: 'light', storageState: auth } },
        { name: 'dark', dependencies: ['setup'], use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 900 }, colorScheme: 'dark', storageState: auth } },
        { name: 'mobile', dependencies: ['setup'], use: { ...devices['Pixel 7'], colorScheme: 'light', storageState: auth } },
    ],
    webServer: {
        command: 'php vendor/bin/testbench serve --port=8100 --no-reload',
        url: 'http://127.0.0.1:8100/admin/login',
        reuseExistingServer: true,
        env: { PHP_CLI_SERVER_WORKERS: '4' },
        timeout: 60_000,
    },
});
