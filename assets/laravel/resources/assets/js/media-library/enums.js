export const FILE_TYPE = {
    ANY: null,
    IMAGE: 'image',
    VIDEO: ['video/mp4', 'video/ogg', 'video/webm'],
};

export const COMMANDS = {
    ACTIVATE_DIRECTORY: 'media-library::activate-directory',
    UPDATE_DIRECTORY_CONTENT: 'media-library::update-directory-content',
    FETCH_DIRECTORY_CONTENT: 'media-library::fetch-directory-content',
    SHOW_FILE_DETAIL: 'media-library::show-file-detail',
    UPLOAD_FILES: 'media-library::upload-files',
    OPEN_PROMPT: 'media-library:open-prompt',
    CLOSE_PROMPT: 'media-library:close-prompt',
    DEACTIVATE_FILE: 'media-library:deactivate-file',
};

export const EVENTS = {
    DIRECTORY_ACTIVATED: 'media-library::directory-activated',
    FILE_SELECTED: 'media-library::file-selected',
    FILE_UNSELECTED: 'media-library::file-unselected',
    FILE_SELECTION_CONFIRMED: 'media-library::file-selection-confirmed',
    FILE_UPDATED: 'media-library::file-updated',
    FILE_DELETED: 'media-library::file-deleted'
};

export const DETAIL_INVOCABLE_ACTIONS = {
    NONE: null,
    RENAME: 1,
    OVERRIDE: 2
};
