import dialogTemplateNode from './dialogTemplate';
import trackerTemplateNode from './trackerFormRowTemplate';

const LOCAL_STORAGE_PREFIX = '1z1-content:consent:';

interface Tracker {
    id: string;
    name: string;
    type: string;
}

interface Consent {
    done: boolean,
    userId: string,
    trackers: { [id: string]: boolean },
}

class ConsentDialog extends HTMLElement {
    private _snippets: { [id: string]: () => void } = {};
    private shadow: ShadowRoot;
    private dialogBox: HTMLDialogElement;
    private form: HTMLFormElement;
    private acceptAllButton: HTMLButtonElement;
    private declineAllButton: HTMLButtonElement;
    private trackers: HTMLUListElement;
    private trackerConsent: Consent | null = null;
    private consentUserId: string | null = null;
    private showOpenButton: boolean = true;
    private customCss: string | null = null;

    set snippets(snippets: { [id: string]: () => void }) {
        this._snippets = snippets;
    }

    constructor() {
        super();

        this.shadow = this.attachShadow({mode: 'open'});
        this.shadow.appendChild(dialogTemplateNode.content.cloneNode(true));

        this.dialogBox = this.shadow.getElementById('consent-dialog') as HTMLDialogElement;
        this.form = this.shadow.getElementById('form') as HTMLFormElement;
        this.acceptAllButton = this.shadow.getElementById('accept-all') as HTMLButtonElement;
        this.declineAllButton = this.shadow.getElementById('decline-all') as HTMLButtonElement;
        this.trackers = this.shadow.getElementById('trackers') as HTMLUListElement;
    }

    connectedCallback() {
        this.trackerConsent = JSON.parse(localStorage.getItem(LOCAL_STORAGE_PREFIX + this.dataset.websiteId) || 'null') as Consent | null;
        this.trackerConsent = this.trackerConsent || {done: false, userId: this.getUid(), trackers: {}};
        this.consentUserId  = this.trackerConsent.userId;

        Object.keys(this.trackerConsent.trackers).forEach((tracker: string) => this.trackerConsent?.trackers[tracker] && this.processTracker(tracker));

        const titleSlot = this.shadow.querySelector('[slot=DialogTitle]');
        if (this.dataset.dialogTitle && titleSlot) {
            titleSlot.textContent = this.dataset.dialogTitle;
        }

        const textSlot = this.shadow.querySelector('[slot=DialogText]');
        if (this.dataset.dialogText && textSlot) {
            textSlot.innerHTML = this.dataset.dialogText;
        }

        JSON.parse(this.dataset.trackers || '[]').forEach((tracker: Tracker) => {
            const trackerRow = trackerTemplateNode.content.cloneNode(true) as HTMLLIElement;
            const checkbox = trackerRow.querySelector('[data-tracker-checkbox]') as HTMLInputElement;

            (trackerRow.querySelector('[data-tracker-name]') as HTMLElement).textContent = tracker.name;
            checkbox.name = `tracker[${tracker.id}]`;
            checkbox.id = tracker.id;
            (trackerRow.querySelector('[data-tracker-label]') as HTMLLabelElement).setAttribute('for', tracker.id);

            if ('undefined' !== typeof checkbox.checked) {
                checkbox.checked = this.trackerConsent?.trackers[tracker.id] || false;
            }

            this.trackers.appendChild(trackerRow);
        });

        this.showOpenButton = JSON.parse(this.dataset.showOpenButton || 'true') as boolean;
        this.customCss = this.dataset.customCss || null;

        this.acceptAllButton.addEventListener('click', this.acceptAll.bind(this));
        this.declineAllButton.addEventListener('click', this.declineAll.bind(this));
        this.form.addEventListener('submit', this.submit.bind(this));

        if (!this.trackerConsent.done) {
            this.dialogBox.setAttribute('open', '');
        }

        document.addEventListener('1z1-content:show-dialog', this.show.bind(this));

        (this.shadow.getElementById('show-consent-dialog') as HTMLButtonElement)
            .addEventListener('click', () => {
                document.dispatchEvent(new CustomEvent('1z1-content:show-dialog'));
            });

        if (!this.showOpenButton) {
            (this.shadow.getElementById('show-consent-dialog') as HTMLButtonElement).remove()
        }

        (this.shadow.getElementById('inline-style-tag') as HTMLStyleElement).textContent += this.customCss || '';
    }

    show() {
        this.dialogBox.setAttribute('open', '');
    }

    async acceptAll(e: MouseEvent) {
        this.shadow
            .querySelectorAll<HTMLInputElement>('input[type=checkbox]')
            .forEach(checkbox => checkbox.checked = true);

        return await this.submit(e as unknown as SubmitEvent);
    }

    async declineAll(e: MouseEvent) {
        this.shadow
            .querySelectorAll<HTMLInputElement>('input[type=checkbox]')
            .forEach(checkbox => checkbox.checked = false );

        return await this.submit(e as unknown as SubmitEvent);
    }

    async submit(e: SubmitEvent) {
        e.preventDefault();

        try {
            const formData = new FormData(this.form);
            formData.set('user_id', this.getUid());

            await fetch(this.dataset.postConsentUrl || '', {method: 'POST', body: formData, credentials: 'include'});

            const trackers = Array
                .from(formData.entries())
                .filter(entry => 'user_id' !== entry[0])
                .map(entry => [entry[0].substring(8, entry[0].length - 1), !!entry[1]]);

            trackers
                .filter(entry => entry[1] && !this.trackerConsent?.trackers[entry[0] as string])
                .forEach(entry => this.processTracker(entry[0] as string));

            this.trackerConsent = {
                done: true,
                userId: this.trackerConsent?.userId || this.getUid(),
                trackers: trackers.reduce((acc, entry) => ({...acc, [entry[0] as string]: !!entry[1]}), {})
            };

            localStorage.setItem(LOCAL_STORAGE_PREFIX + this.dataset.websiteId, JSON.stringify(this.trackerConsent));

            this.dialogBox.removeAttribute('open');
        } catch (e) {
            console.error(e);
        }
    }

    processTracker(tracker: string) {
        if (!this.trackerConsent) {
            return;
        }

        this.trackerConsent.trackers[tracker] = true;

        if (this._snippets[tracker]) {
            this._snippets[tracker]();
        }
    }

    getUid() {
        if (this.consentUserId) {
            return this.consentUserId;
        }

        return this.consentUserId = crypto.randomUUID();
    }
}

customElements.define('uzu-consent', ConsentDialog);
