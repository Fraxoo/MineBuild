import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['input', 'list', 'hidden'];
  static values = {
    max: { type: Number, default: 10 },
  };

  connect() {
    this.render();
  }

  add() {
    const raw = (this.inputTarget.value || '').trim();
    const value = raw.replace(/^#/, '').trim();
    if (!value) return;

    const tags = this.getTags();
    if (tags.includes(value)) {
      this.inputTarget.value = '';
      return;
    }

    if (tags.length >= this.maxValue) return;

    tags.push(value);
    this.setTags(tags);
    this.inputTarget.value = '';
    this.render();
  }

  submit() {
    this.add();
  }

  remove(event) {
    const value = event.params.value;
    const tags = this.getTags().filter((t) => t !== value);
    this.setTags(tags);
    this.render();
  }

  render() {
    const tags = this.getTags();
    this.listTarget.innerHTML = '';

    for (const tag of tags) {
      const chip = document.createElement('button');
      chip.type = 'button';
      chip.className = 'build-tags__chip';
      chip.textContent = tag;
      chip.setAttribute('data-action', 'tags-input#remove');
      chip.setAttribute('data-tags-input-value-param', tag);
      this.listTarget.appendChild(chip);
    }
  }

  getTags() {
    const raw = (this.hiddenTarget.value || '').trim();
    if (!raw) return [];
    return raw
      .split(',')
      .map((t) => t.trim())
      .filter(Boolean);
  }

  setTags(tags) {
    this.hiddenTarget.value = tags.join(',');
  }
}
