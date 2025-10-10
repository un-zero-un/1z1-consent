import { type Page, type Locator, expect } from '@playwright/test';

export class PreferenceButton {
    private readonly showDialogButtonLocator: Locator;

    constructor(private readonly page: Page) {
        this.showDialogButtonLocator = this.page.locator('#show-consent-dialog');
    }

    async expectShowDialogButtonToMatchScreenshot(): Promise<void> {
        await expect(this.showDialogButtonLocator).toHaveScreenshot({ maxDiffPixelRatio: 0.03 });
    }

    async show(): Promise<void> {
        await this.showDialogButtonLocator.click();
    }

    async hover(): Promise<void> {
        await this.showDialogButtonLocator.hover();
    }
}
