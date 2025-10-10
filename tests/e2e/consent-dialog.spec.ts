import { test, describe } from '@playwright/test';
import { ConsentDialog } from './model/ConsentDialog';
import { SymfonyToolbar } from './model/SymfonyToolbar';
import {PreferenceButton} from "./model/PreferenceButton";
import {DemoPage} from "./model/DemoPage";

describe('Consent dialog box', () => {
    let consentDialog: ConsentDialog;
    let symfonyToolbar: SymfonyToolbar;
    let preferenceButton: PreferenceButton;
    let demoPage: DemoPage;

    test.beforeEach(async ({ page }) => {
        consentDialog = new ConsentDialog(page);
        symfonyToolbar = new SymfonyToolbar(page);
        preferenceButton = new PreferenceButton(page);
        demoPage = new DemoPage(page);
    });

    test('It displays consent dialog box', async ({ page }) => {
        await page.goto('/');
        await symfonyToolbar.hide();

        await consentDialog.expectDialogToMatchScreenshot();
    });

    test('It checks trackers manually', async ({ page }) => {
        await page.goto('/');
        await symfonyToolbar.hide();

        await consentDialog.checkTracker(1);
        await consentDialog.checkTracker(2);
        await consentDialog.expectDialogToMatchScreenshot();
    });

    test('It checks trackers when accepting all', async ({ page }) => {
        await page.goto('/');
        await symfonyToolbar.hide();

        await consentDialog.acceptAll();
        await preferenceButton.expectShowDialogButtonToMatchScreenshot();

        await page.reload({ waitUntil: 'networkidle' });
        await symfonyToolbar.hide();

        await preferenceButton.show();
        await consentDialog.expectDialogToMatchScreenshot();
    });

    test('It uncheck trackers when declining all', async ({ page }) => {
        await page.goto('/');
        await symfonyToolbar.hide();

        await consentDialog.declineAll();
        await preferenceButton.expectShowDialogButtonToMatchScreenshot();

        await page.reload({ waitUntil: 'networkidle' });
        await symfonyToolbar.hide();

        await preferenceButton.show();
        await consentDialog.expectDialogToMatchScreenshot();
    });

    test('It has hover test on cookie button', async ({ page }) => {
        await page.goto('/');
        await symfonyToolbar.hide();

        await consentDialog.acceptSelection();

        await preferenceButton.hover();
        await preferenceButton.expectShowDialogButtonToMatchScreenshot();
    });

    test('It opens with custom button', async ({ page }) => {
        await page.goto('/');
        await symfonyToolbar.hide();

        await consentDialog.acceptSelection();

        await demoPage.showDialog();
        await demoPage.expectPageToMatchScreenshot();
    });
});
