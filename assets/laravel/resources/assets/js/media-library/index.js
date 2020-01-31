import MediaLibrary from './wrapper';
import MediaLibraryPrompt from './prompt-wrapper';
import MediaLibraryPromptClass from './prompt';
import FileSelector from './file-selector/wrapper';

window.MediaLibraryPrompt = MediaLibraryPromptClass;
Vue.component('media-library', MediaLibrary);
Vue.component('media-library-prompt', MediaLibraryPrompt);
Vue.component('media-library-file-selector', FileSelector);
