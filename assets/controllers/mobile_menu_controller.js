import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'panel', 'overlay', 'closeButton'];

    toggle() {
        this.isOpen() ? this.close() : this.open();
    }

    open() {
        this.panelTarget.classList.remove('-translate-x-full');
        this.overlayTarget.classList.remove('opacity-0', 'pointer-events-none');
        this.buttonTarget.setAttribute('aria-expanded', 'true');
        this.closeButtonTarget.focus();
    }

    close() {
        this.panelTarget.classList.add('-translate-x-full');
        this.overlayTarget.classList.add('opacity-0', 'pointer-events-none');
        this.buttonTarget.setAttribute('aria-expanded', 'false');
        this.buttonTarget.focus();
    }

    isOpen() {
        return !this.panelTarget.classList.contains('-translate-x-full');
    }
}