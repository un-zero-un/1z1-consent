import { type Page } from '@playwright/test';

export class SymfonyToolbar {
    constructor(private readonly page: Page) {}

    async hide(): Promise<void> {
        await this.page.evaluate(() => {
            const toolbar = document.querySelector<HTMLElement>('.sf-toolbar');
            if (toolbar) {
                toolbar.style.display = 'none';
            }
        });
    }
}
