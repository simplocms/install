import {EVENTS} from '../enums';
import DraggableItem from '../../vue-components/draggable/draggable-item';
import DropZone from '../../vue-components/draggable/drop-zone';

const CONTAINER_MODEL = {
    type: 'container',
};

const ROW_MODEL = {
    type: 'row',
};

const COLUMN_MODEL = {
    type: 'column',
};

const MODULE_MODEL = {
    type: 'module',
};

const SELECTORS = {
    container: 'div._grid-container',
    row: 'div.row',
    column: 'div._grid-column',
    module: 'div._grid-module',
};

export default {
    /**
     * The mixin's data.
     */
    data() {
        return {
            innerContent: this.item ? this.item.content || [] : [],
            sortableItems: [],
            sortableConnections: [],
            isForceUpdating: false,
            isCloning: false,
            prependContent: false,
        }
    },

    props: {
        item: Object,
        layoutEditMode: Boolean,
        localization: Object,
        sourceList: Array,
        path: {
            type: String,
            default: ''
        },
    },

    components: {
        'draggable-item': DraggableItem,
        'drop-zone': DropZone,
    },

    updated() {
        if (!this.isForceUpdating && this.item.type !== 'module') {
            this.$root.$emit(EVENTS.CONTENT_CHANGED);
        }
    },

    methods: {
        /**
         * Get output content.
         * @returns {{}}
         */
        clone() {
            if (this.isCloning) {
                return;
            }

            this.isCloning = true;
            this.cloneItemAndContent()
                .then(clone => {
                    this.$emit('cloned', clone);
                    this.isCloning = false;
                });
        },

        /**
         * Clone item and its content.
         * @returns {Promise<{}>}
         */
        cloneItemAndContent() {
            return new Promise((resolve, reject) => {
                const item = this.cloneItem();
                item.content = [];
                const contentClonePromises = [];

                for (const i in this.$refs.contentItem) {
                    const itemContent = this.$refs.contentItem[i].cloneItemAndContent();
                    contentClonePromises.push(itemContent);
                }

                Promise.all(contentClonePromises)
                    .then(contentClones => {
                        item.content = contentClones;
                        resolve(item);
                    })
                    .catch(reason => reject(reason));
            });
        },

        /**
         * Clone item.
         * @returns {{}}
         */
        cloneItem() {
            const clone = JSON.parse(JSON.stringify(this.item));
            clone.uuid = Utils.guid();
            return clone;
        },

        /**
         * Get item output.
         * @returns {{}}
         */
        getOutputItem() {
            const item = {...this.item};
            delete item.uuid;
            return item;
        },

        /**
         * Create content with columns by given layout.
         * @param {Array<number>} layout
         * @returns {Array}
         */
        createColumnsByLayout(layout) {
            const content = [];

            const sizesDesc = ['col', 'sm', 'md', 'lg', 'xl'];
            const activeLayout = window.GE.activeLayout || 'col';

            layout.forEach(column => {
                const sizes = {};
                let minSizeReached = false;

                sizesDesc.forEach(size => {
                    if (minSizeReached || size === activeLayout) {
                        minSizeReached = true;
                        sizes[size] = column;
                    }
                });

                content.push({...COLUMN_MODEL, content: [], size: sizes});
            });

            return content;
        },

        /**
         * Create row with columns by given layout.
         * @param {Array<number>} layout
         * @returns {*}
         */
        createRowWithLayout(layout) {
            let content = [];

            if (layout && layout.length) {
                content = this.createColumnsByLayout(layout);
            }

            return {...ROW_MODEL, content: content};
        },

        /**
         * Add container to content.
         * @param {Array<number>} layout
         * @param {boolean} prepend
         */
        addContainer(layout = null, prepend = false) {
            this.prependContent = prepend;
            this.addContent({
                ...CONTAINER_MODEL,
                content: [this.createRowWithLayout(layout)]
            });
        },

        /**
         * Add row to content.
         * @param {Array<number>} layout
         * @param {boolean} prepend
         */
        addRow(layout = null, prepend = false) {
            this.prependContent = prepend;
            this.addContent(this.createRowWithLayout(layout));
        },

        /**
         * Add column to content.
         * @param {boolean} prepend
         */
        addColumn(prepend) {
            this.prependContent = prepend;
            this.addContent({...COLUMN_MODEL, content: [], size: {}});
        },

        /**
         * Add module to content: 1. open modal to select module.
         * @param {boolean} prepend
         */
        addModule(prepend) {
            this.prependContent = prepend;
            this.$root.$emit('show::modules-modal', this.openNewModuleModal);
        },

        /**
         * Add module to content: 2. open modal form for module.
         * @param {{}} module
         */
        openNewModuleModal(module) {
            this.$root.$emit('show::module-form-modal', module, module => {
                this.addContent({
                    ...MODULE_MODEL,
                    ...module
                });
            });
        },

        /**
         * Add content item.
         * @param {{}} item
         */
        addContent(item) {
            item.uuid = Utils.guid();

            if (this.prependContent) {
                this.innerContent.unshift(item);
            } else {
                this.innerContent.push(item);
            }

            // reset value
            this.prependContent = false;

            // if content if current item is not defined, initialize it.
            if (!this.item.content) {
                this.item.content = [item];
            }
        },

        /**
         * Open modal to confirm deletion.
         * @param {string} title
         * @param {string} text
         */
        showRemoveConfirmation(title, text) {
            swal({
                title: title,
                text: text,
                icon: "warning",
                buttons: {
                    cancel: {
                        text: this.localization.trans('remove_confirmation.cancel'),
                        visible: true
                    },
                    confirm: {
                        text: this.localization.trans('remove_confirmation.confirm'),
                        value: true
                    }
                },
                dangerMode: true
            })
                .then(isConfirm => {
                    if (isConfirm) {
                        this.$emit('remove');
                    }
                });
        },

        /**
         * Remove item from content on specified index.
         * @param index
         */
        removeContentItem(index) {
            this.innerContent.splice(index, 1);
        },

        /**
         * Change content.
         * @param {Array<{}>} content
         */
        changeContent(content) {
            this.item.content = this.innerContent = [...content];
        },
    },

    computed: {
        receiveClass() {
            return this.sortableItems.map(item => {
                return '_grid-receive-' + item;
            }).join(' ');
        }
    },

    watch: {
        innerContent(newVal) {
            this.item.content = newVal;
        }
    }
};
