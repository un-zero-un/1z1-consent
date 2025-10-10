import { type Page, type Locator, expect } from '@playwright/test';

export class DemoPage {
    private readonly customOpenDialogButton: Locator;
    private readonly pageLocator: Locator;

    constructor(private readonly page: Page) {
        this.customOpenDialogButton = this.page.locator('#custom-button');
        this.pageLocator = this.page.locator('body');
    }

    async showDialog(): Promise<void> {
        await this.customOpenDialogButton.click();
    }

    async expectPageToMatchScreenshot(): Promise<void> {
        await expect(this.page).toHaveScreenshot({ maxDiffPixelRatio: 0.03 });
    }
}
