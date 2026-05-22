import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['input', 'name', 'deleteContainer'];

  connect() {
    this.render();
  }

  change() {
    this.render();
  }

  removeExisting(event) {
    if (this.hasDeleteContainerTarget) {
      const selector = 'input[name="delete_world_asset"][value="1"]';
      if (!this.deleteContainerTarget.querySelector(selector)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_world_asset';
        input.value = '1';
        this.deleteContainerTarget.appendChild(input);
      }
    }

    const item = event.currentTarget?.closest?.('.build-images-preview__item');
    if (item) item.remove();
  }

  render() {
    const file = this.inputTarget?.files?.[0];
    this.nameTarget.textContent = file ? file.name : '';
  }
}
