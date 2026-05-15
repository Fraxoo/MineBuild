import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['image', 'input'];

    connect() {
        this.originalSrc = this.hasImageTarget ? this.imageTarget.getAttribute('src') : null;
        this.objectUrl = null;
    }

    disconnect() {
        this.revokeObjectUrl();
    }

    preview() {
        const file = this.hasInputTarget ? this.inputTarget.files?.[0] : null;

        if (!file) {
            this.revokeObjectUrl();

            if (this.hasImageTarget && this.originalSrc) {
                this.imageTarget.setAttribute('src', this.originalSrc);
            }

            return;
        }

        if (!file.type?.startsWith('image/')) {
            return;
        }

        this.revokeObjectUrl();
        this.objectUrl = URL.createObjectURL(file);

        if (!this.hasImageTarget) {
            return;
        }

        this.imageTarget.onload = () => this.revokeObjectUrl();
        this.imageTarget.setAttribute('src', this.objectUrl);
    }

    revokeObjectUrl() {
        if (this.objectUrl) {
            URL.revokeObjectURL(this.objectUrl);
            this.objectUrl = null;
        }
    }
}
