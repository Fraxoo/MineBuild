import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['panel', 'button']

    toggle() {
        const isHidden = this.panelTarget.classList.toggle('hidden')
        const form = this.panelTarget.querySelector('form')
        const comment = this.panelTarget.querySelector('#comment')

        if(comment){
            comment.classList.toggle('flex')
        }

        this.buttonTarget.setAttribute('aria-expanded', String(!isHidden));
        const logo = this.buttonTarget.querySelector('i')
        
        logo.classList.toggle('ti-chevron-up')
        logo.classList.toggle('ti-chevron-down')
    }
}
