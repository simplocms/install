<template>
    <div :class="classList"></div>
</template>

<script>
    import {DragStartEvent, DragStopEvent} from './Events';

    const CLASSES = {
        'dropzone:visible': 'draggable--visible',
        'dropzone': 'draggable--dropzone',
    };

    export default {
        props: {
            accept: {
                type: Array,
                default: () => []
            },
            target: {
                type: Array,
                default: null
            },
            position: {
                type: Number,
                default: 0
            },
            path: {
                type: String,
                default: null
            }
        },

        data() {
            return {
                visible: false,
                acceptMap: {}
            };
        },

        computed: {
            classList() {
                const classList = [CLASSES['dropzone']];

                if (this.visible) {
                    classList.push(CLASSES['dropzone:visible']);
                }

                return classList.join(' ');
            },

            unacceptablePaths() {
                const paths = [];
                const pathPrefix = this.path === null ? '' : this.path + '-';
                paths.push(pathPrefix + this.position);

                if (this.position !== 0) {
                    paths.push(pathPrefix + (this.position - 1));
                }

                return paths;
            }
        },

        methods: {
            initializeListeners() {
                document.addEventListener(DragStartEvent.type, this.dragStart);
            },

            /**
             * Dragging started.
             * @param {Event} event
             * @param {DragStartEvent} event.detail
             */
            dragStart(event) {
                const dragEvent = event.detail;

                if (this.accept.length && this.acceptMap[dragEvent.itemType] !== true) {
                    this.visible = false;
                    return;
                }

                if (this.target === dragEvent.source && this.unacceptablePaths.indexOf(dragEvent.itemPath) !== -1) {
                    this.visible = false;
                    return;
                }

                if (this.path !== null && this.path.indexOf(dragEvent.itemPath) === 0) {
                    this.visible = false;
                    return;
                }

                this.visible = true;

                if (this.visible) {
                    document.addEventListener(DragStopEvent.type, this.dragStop);
                }
            },

            /**
             * Dragging started.
             * @param {Event} event
             * @param {DragStopEvent} event.detail
             */
            dragStop(event) {
                if (!this.visible) {
                    return;
                }

                const dragEvent = event.detail;

                if (this.$el === dragEvent.target) {
                    this.moveItem(dragEvent.item, dragEvent.source);
                }

                document.removeEventListener(DragStopEvent.type, this.dragStop);
                this.visible = false;
            },

            moveItem(item, source) {
                let shift = false;

                if (source !== null) {
                    let sourceIndex = source.indexOf(item);

                    if (sourceIndex !== -1) {
                        if (this.target === source && this.position > sourceIndex) {
                            shift = true;
                        }

                        source.splice(sourceIndex, 1);
                    }
                }

                if (this.target !== null) {
                    this.target.splice(shift ? this.position - 1 : this.position, 0, item);
                }
            }
        },

        created() {
            for (const ai in this.accept) {
                this.acceptMap[this.accept[ai]] = true;
            }
        },

        mounted() {
            this.initializeListeners();
        },

        beforeDestroy() {
            document.removeEventListener(DragStartEvent.type, this.dragStart);
            document.removeEventListener(DragStopEvent.type, this.dragStop);
        }
    };
</script>
