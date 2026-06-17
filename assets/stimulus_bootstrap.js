import { startStimulusApp } from '@symfony/stimulus-bundle';
import AvatarPreviewController from './controllers/avatar_preview_controller.js';
import CollectionController from './controllers/collection_controller.js';
import TagsInputController from './controllers/tags_input_controller.js';
import ImagePreviewController from './controllers/image_preview_controller.js';
import FilePreviewController from './controllers/file_preview_controller.js';
import ModalController from "./controllers/modal.js"

const app = startStimulusApp();
app.register('avatar-preview', AvatarPreviewController);
app.register('collection', CollectionController);
app.register('tags-input', TagsInputController);
app.register('image-preview', ImagePreviewController);
app.register('file-preview', FilePreviewController);
app.register('modal-form', ModalController)
