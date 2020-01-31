<template>
    <li>
        <div class="tree-item" :class="{active: isActive, empty: isEmpty}">
            <div @click="open">
                <i class="fa fa-fw" :class="chevronIcon" v-if="!isEmpty" @click.stop="expand"></i>
                <i class="fa fa-fw" :class="folderIcon"></i>
                {{directory.name}}
            </div>
        </div>
        <ul v-show="isOpen" class="directory-tree">
            <media-library-tree-item v-for="subDirectory in directory.children"
                                     :key="subDirectory.id"
                                     :directory="subDirectory"
                                     @activated="childActivated"
            ></media-library-tree-item>
        </ul>
    </li>
</template>

<script>
    import {EVENTS, COMMANDS} from "../enums";

    export default {
        data() {
            return {
                isOpen: false,
                isActive: false,
            }
        },

        props: {
            directory: {
                type: Object,
                required: true
            }
        },

        computed: {
            folderIcon() {
                return this.isOpen || this.isActive ? 'fa-folder-open-o' : 'fa-folder-o'
            },

            isEmpty() {
                return !this.directory.children || !this.directory.children.length;
            },

            chevronIcon() {
                return this.isOpen ? 'fa-chevron-down' : 'fa-chevron-right';
            }
        },

        methods: {
            open() {
                if (!this.isActive) {
                    EventBus.$emit(EVENTS.DIRECTORY_ACTIVATED, this.directory);
                }
            },

            expand() {
                this.isOpen = !this.isOpen;
            },

            /**
             * Event: Some directory was activated.
             * @param {Object} directory
             */
            directoryActivated(directory) {
                this.isActive = this.directory.id === (directory ? directory.id : null);

                if (this.isActive) {
                    this.isOpen = true;
                    this.$emit('activated');
                } else if (this.isEmpty) {
                    this.isOpen = false;
                }
            },

            /**
             * Event: Child directory was activated.
             */
            childActivated() {
                this.isOpen = true;
                this.$emit('activated');
            },

            /**
             * Command: Update directory children.
             * @param {number} id
             * @param {Object[]} children
             */
            updateChildren(id, children) {
                if (id === this.directory.id) {
                    this.directory.children = children;
                }
            },

            /**
             * Command: Activate directory.
             * @param {number} id
             */
            activateDirectory(id) {
                if ((id || null) === (this.directory.id || null)) {
                    this.open();
                }
            }
        },

        created() {
            EventBus.$on(EVENTS.DIRECTORY_ACTIVATED, this.directoryActivated);
            EventBus.$on(COMMANDS.UPDATE_DIRECTORY_CONTENT, this.updateChildren);
            EventBus.$on(COMMANDS.ACTIVATE_DIRECTORY, this.activateDirectory);
        },

        destroyed() {
            EventBus.$off(EVENTS.DIRECTORY_ACTIVATED, this.directoryActivated);
            EventBus.$off(COMMANDS.UPDATE_DIRECTORY_CONTENT, this.directoryActivated);
            EventBus.$off(COMMANDS.ACTIVATE_DIRECTORY, this.activateDirectory);
        }
    };
</script>

<style lang="scss">
    .tree-item {
        display: inline-block;
        white-space: nowrap;
        cursor: pointer;
        color: #585858;

        > div {
            display: inline-block;

            > i {
                &:first-child {
                    padding-right: 3px;
                    font-size: 0.9em;
                }

                &:last-child {
                    padding-right: 5px;
                    font-size: 1.5em;
                    position: relative;
                    bottom: -3px;
                }
            }
        }

        &:hover:not(.active) {
            color: #898989;
        }

        &.active {
            color: #26A69A;
        }

        &.empty {
            padding-left: 18px;
        }
    }
</style>
