import { startStimulusApp } from '@symfony/stimulus-bundle';
import AvatarPreviewController from './controllers/avatar_preview_controller.js';

const app = startStimulusApp();
app.register('avatar-preview', AvatarPreviewController);
