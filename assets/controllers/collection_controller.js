import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static values = {
    prototype: String,
    index: Number,
  };

  static targets = ['list'];

  connect() {
    if (!this.hasIndexValue) {
      this.indexValue = this.listTarget.children.length;
    }
  }

  add() {
    const html = this.prototypeValue.replaceAll('__name__', String(this.indexValue));
    this.indexValue += 1;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = html.trim();
    const element = wrapper.firstElementChild;

    if (element) {
      this.listTarget.appendChild(element);
    }
  }

  remove(event) {
    const item = event.target.closest('[data-collection-item]');
    if (item) {
      item.remove();
    }
  }
}
