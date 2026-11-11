import { test as setup } from '@playwright/test';

/** Log in once; every visual test reuses this session (tests/visual/.auth is gitignored). */
setup('log in', async ({ page }) => {
    await page.goto('/admin/login');
    await page.locator('input[type=email]').fill('admin@lazyui.test');
    await page.locator('input[type=password]').fill('password');
    await page.getByRole('button', { name: 'Sign in' }).click();
    await page.waitForURL('**/admin', { timeout: 60_000 });
    await page.context().storageState({ path: 'tests/visual/.auth/admin.json' });
});
