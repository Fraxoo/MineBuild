import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['panel', 'button']

    toggle() {
        const isHidden = this.panelTarget.classList.toggle('hidden')
        const form = this.panelTarget.querySelector('form')



        this.buttonTarget.setAttribute('aria-expanded', String(!isHidden));
        const logo = this.buttonTarget.querySelector('i')
        
        logo.classList.toggle('ti-chevron-up')
        logo.classList.toggle('ti-chevron-down')
    }
}
