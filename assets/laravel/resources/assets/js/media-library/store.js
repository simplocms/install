export const MediaLibrary = {
    namespaced: true,

    state: {
        activeFile: null,
        isPrompt: false
    },

    mutations: {
        changeActiveFile(state, file) {
            state.activeFile = file;
        },

        makePrompt(state) {
            state.isPrompt = true;
        }
    },

    actions: {
        activateFile({commit}, file) {
            commit('changeActiveFile', file);
        },

        deactivateFile({commit, state}, file = null) {
            if (state.activeFile && (file === null || file.getId() === state.activeFile.getId())) {
                commit('changeActiveFile', null);
            }
        },
    },

    getters: {
        activeFileId(state) {
            return state.activeFile ? state.activeFile.getId() : null;
        }
    }
};
