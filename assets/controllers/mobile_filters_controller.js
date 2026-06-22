import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['panel']

    toggle() {
        const isHidden = this.panelTarget.classList.toggle('hidden')
        const form = this.panelTarget.querySelector('form')
        console.log(form);


        this.buttonTarget.setAttribute('aria-expanded', String(!isHidden));
    }
}
