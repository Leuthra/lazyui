import { expect, test, type Page } from '@playwright/test';

/**
 * One screenshot per Filament area, in light, dark and mobile (see playwright.config.ts projects).
 * Add a scene here whenever LazyUI starts styling another part of Filament.
 */

/** The session comes from auth.setup.ts; only the theme mode is set per project. */
async function login(page: Page, mode: string) {
    await page.addInitScript((m) => localStorage.setItem('filament:theme:mode', m), mode);
}

/** Relative times ("3 days ago") and charts change between runs: mask them. */
function masks(page: Page) {
    return [page.getByText(/\bago$/), page.locator('canvas')];
}

async function shot(page: Page, name: string, fullPage = false) {
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000); // lazy widgets, fonts, Alpine transitions
    await expect(page).toHaveScreenshot(`${name}.png`, { fullPage, mask: masks(page) });
}

const scenes: Array<[string, (page: Page) => Promise<void>, boolean?]> = [
    ['dashboard', async (page) => { await page.goto('/admin'); }, true],
    ['products', async (page) => { await page.goto('/admin/products'); }],
    ['products-filters', async (page) => {
        await page.goto('/admin/products');
        await page.getByRole('button', { name: 'Filter' }).click();
    }],
    ['products-row-menu', async (page) => {
        await page.goto('/admin/products');
        await page.getByRole('button', { name: 'Actions', exact: true }).first().click();
    }],
    ['modal-form', async (page) => {
        await page.goto('/admin/products');
        await page.getByRole('button', { name: 'Actions', exact: true }).first().click();
        await page.locator('.fi-dropdown-panel').getByText('Restock').first().click();
    }],
    ['slide-over', async (page) => {
        await page.goto('/admin/products');
        await page.getByRole('button', { name: 'Actions', exact: true }).first().click();
        await page.locator('.fi-dropdown-panel').getByText('Preview').first().click();
    }],
    ['product-create', async (page) => { await page.goto('/admin/products/create'); }, true],
    ['product-view', async (page) => { await page.goto('/admin/products/1'); }, true],
    ['category-relation-manager', async (page) => { await page.goto('/admin/categories/1/edit'); }, true],
    ['order-wizard', async (page) => { await page.goto('/admin/orders/create'); }],
    ['settings-page', async (page) => { await page.goto('/admin/settings'); }],
    ['empty-state', async (page) => {
        await page.goto('/admin/settings');
        await page.getByRole('tab', { name: 'Integrations' }).click();
    }],
    ['appearance-sheet', async (page) => {
        await page.goto('/admin');
        await page.getByLabel('Appearance settings').click();
    }],
];

test.describe('LazyUI login', () => {
    test.use({ storageState: { cookies: [], origins: [] } });

    test('login', async ({ page }, info) => {
        await page.addInitScript((m) => localStorage.setItem('filament:theme:mode', m), info.project.name === 'dark' ? 'dark' : 'light');
        await page.goto('/admin/login');
        await shot(page, 'login');
    });
});

test.describe('LazyUI panel', () => {

    for (const [name, open, fullPage] of scenes) {
        test(name, async ({ page }, info) => {
            await login(page, info.project.name === 'dark' ? 'dark' : 'light');
            await open(page);
            await shot(page, name, fullPage ?? false);
        });
    }

    test('no horizontal overflow on any page', async ({ page }, info) => {
        await login(page, 'light');

        for (const path of ['/admin', '/admin/products', '/admin/products/create', '/admin/orders', '/admin/settings']) {
            await page.goto(path);
            const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
            expect(overflow, `${path} overflows by ${overflow}px on ${info.project.name}`).toBeLessThanOrEqual(0);
        }
    });

    test('no JavaScript errors', async ({ page }) => {
        const errors: string[] = [];
        page.on('pageerror', (error) => errors.push(error.message));
        page.on('console', (message) => message.type() === 'error' && errors.push(message.text()));

        await login(page, 'light');
        for (const path of ['/admin', '/admin/products', '/admin/products/1/edit', '/admin/orders/create', '/admin/settings']) {
            await page.goto(path);
            await page.waitForLoadState('networkidle');
        }

        expect(errors).toEqual([]);
    });
});
