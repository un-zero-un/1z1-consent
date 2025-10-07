import { test, expect, describe } from '@playwright/test';

describe('Consent dialog box', () => {
    test('It displays consent dialog box', async ({page}) => {
        await page.goto('/');
        await page.evaluate(() => document.querySelector('.sf-toolbar').style.display = 'none');

        await expect(page.locator('#consent-dialog')).toHaveScreenshot({maxDiffPixelRatio: 0.03});
    });

    test('It checks trackers manually', async ({page}) => {
        await page.goto('/');
        await page.evaluate(() => document.querySelector('.sf-toolbar').style.display = 'none');

        await page.locator('.Dialog__tracker:nth-child(1) label').click();
        await page.locator('.Dialog__tracker:nth-child(2) label').click();

        await expect(page.locator('#consent-dialog')).toHaveScreenshot({maxDiffPixelRatio: 0.03});
    });

    test('It checks trackers when accepting all', async ({page}) => {
        await page.goto('/');
        await page.evaluate(() => document.querySelector('.sf-toolbar').style.display = 'none');

        await page.locator('#accept-all').click();
        await page.waitForLoadState('networkidle');
        await expect(page.locator('#show-consent-dialog')).toHaveScreenshot({maxDiffPixelRatio: 0.03});

        await page.reload({waitUntil: 'networkidle'});
        await page.evaluate(() => document.querySelector('.sf-toolbar').style.display = 'none');

        await page.locator('#show-consent-dialog').click();
        await expect(page.locator('#consent-dialog')).toHaveScreenshot({maxDiffPixelRatio: 0.03});
    });
});
