import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['input', 'list', 'deleteContainer'];

  connect() {
    this.objectUrls = [];
    this.dataTransfer = new DataTransfer();
    this.syncFromInput();
    this.render();
  }

  disconnect() {
    this.cleanup();
  }

  change() {
    this.mergeFromInput();
    this.render();
  }

  render() {
    this.cleanup();

    // Remove only previews created from newly selected files,
    // keep server-rendered existing images (edit page).
    for (const el of Array.from(this.listTarget.querySelectorAll('[data-image-preview-kind="new"]'))) {
      el.remove();
    }

    const files = Array.from(this.dataTransfer.files || []);
    if (files.length === 0) return;

    for (const file of files) {
      if (!file.type?.startsWith('image/')) continue;

      const url = URL.createObjectURL(file);
      this.objectUrls.push(url);

      const item = document.createElement('div');
      item.className = 'build-images-preview__item';
      item.setAttribute('data-image-preview-kind', 'new');

      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'build-images-preview__remove';
      removeBtn.setAttribute('aria-label', 'Supprimer');
      removeBtn.setAttribute('data-action', 'image-preview#remove');
      removeBtn.setAttribute('data-image-preview-key-param', this.fileKey(file));
      removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';

      const img = document.createElement('img');
      img.className = 'build-images-preview__img';
      img.src = url;
      img.alt = file.name || 'Aperçu image';
      img.loading = 'lazy';

      item.appendChild(removeBtn);
      item.appendChild(img);
      this.listTarget.appendChild(item);
    }
  }

  remove(event) {
    const key = event.params.key;
    if (!key) return;

    const next = new DataTransfer();
    for (const file of Array.from(this.dataTransfer.files || [])) {
      if (this.fileKey(file) !== key) {
        next.items.add(file);
      }
    }

    this.dataTransfer = next;
    this.applyToInput();
    this.render();
  }

  removeExisting(event) {
    const id = event.params.id;
    if (!id) return;

    if (this.hasDeleteContainerTarget) {
      const selector = `input[name="delete_images[]"][value="${CSS.escape(String(id))}"]`;
      if (!this.deleteContainerTarget.querySelector(selector)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_images[]';
        input.value = String(id);
        this.deleteContainerTarget.appendChild(input);
      }
    }

    const item = event.currentTarget?.closest?.('.build-images-preview__item');
    if (item) item.remove();
  }

  syncFromInput() {
    const next = new DataTransfer();
    for (const file of Array.from(this.inputTarget.files || [])) {
      next.items.add(file);
    }
    this.dataTransfer = next;
    this.applyToInput();
  }

  mergeFromInput() {
    const incoming = Array.from(this.inputTarget.files || []);
    const existingKeys = new Set(Array.from(this.dataTransfer.files || []).map((f) => this.fileKey(f)));

    for (const file of incoming) {
      const key = this.fileKey(file);
      if (existingKeys.has(key)) continue;
      if (this.dataTransfer.files.length >= 5) break;
      this.dataTransfer.items.add(file);
      existingKeys.add(key);
    }

    this.applyToInput();
  }

  applyToInput() {
    this.inputTarget.files = this.dataTransfer.files;
    this.inputTarget.dispatchEvent(new Event('input', { bubbles: true }));
  }

  fileKey(file) {
    return `${file.name}|${file.size}|${file.lastModified}`;
  }

  cleanup() {
    for (const url of this.objectUrls || []) {
      URL.revokeObjectURL(url);
    }
    this.objectUrls = [];
  }
}
