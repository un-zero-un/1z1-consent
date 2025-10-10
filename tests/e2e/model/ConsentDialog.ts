import { type Page, type Locator, expect } from '@playwright/test';

export class ConsentDialog {
    private readonly dialogLocator: Locator;
    private readonly acceptAllButtonLocator: Locator;
    private readonly denyAllButtonLocator: Locator;
    private readonly acceptSelectionButtonLocator: Locator;

    constructor(private readonly page: Page) {
        this.dialogLocator = this.page.locator('#consent-dialog');
        this.acceptAllButtonLocator = this.page.locator('#accept-all');
        this.denyAllButtonLocator = this.page.locator('#decline-all');
        this.acceptSelectionButtonLocator = this.page.locator('#accept-selection');
    }

    async expectDialogToMatchScreenshot(): Promise<void> {
        await expect(this.dialogLocator).toHaveScreenshot({ maxDiffPixelRatio: 0.03 });
    }

    async checkTracker(index: number): Promise<void> {
        await this.page.locator(`.Dialog__tracker:nth-child(${index}) label`).click();
    }

    async acceptAll(): Promise<void> {
        await this.acceptAllButtonLocator.click();
        await this.page.waitForLoadState('networkidle');
    }

    async declineAll(): Promise<void> {
        await this.denyAllButtonLocator.click();
        await this.page.waitForLoadState('networkidle');
    }

    async acceptSelection(): Promise<void> {
        await this.acceptSelectionButtonLocator.click();
        await this.page.waitForLoadState('networkidle');
    }
}
