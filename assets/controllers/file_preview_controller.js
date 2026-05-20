import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['input', 'name'];

  connect() {
    this.render();
  }

  change() {
    this.render();
  }

  render() {
    const file = this.inputTarget?.files?.[0];
    this.nameTarget.textContent = file ? file.name : '';
  }
}
