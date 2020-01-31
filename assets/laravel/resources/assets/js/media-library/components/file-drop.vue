<template>
    <div class="cms-media-library__upload" v-show="isVisible">
        <i class="fa fa-upload"></i>
        {{ localization.trans('file_drop_text') }}
    </div>
</template>


<script>
    import {COMMANDS} from "../enums";

    export default {
        data() {
            return {
                isVisible: false,
                dropzone: null,
            };
        },

        props: {
            localization: Object
        },

        mounted() {
            this.dropzone = document;

            this.dropzone.addEventListener('dragover', this.dragStart);
            this.dropzone.addEventListener('dragenter', this.dragStart);
            this.dropzone.addEventListener('dragleave', this.dragEnd);

            this.dropzone.addEventListener('drop', this.onDrop);
        },

        beforeDestroy() {
            this.dropzone.removeEventListener('dragover', this.dragStart);
            this.dropzone.removeEventListener('dragenter', this.dragStart);
            this.dropzone.removeEventListener('dragleave', this.dragEnd);

            this.dropzone.removeEventListener('drop', this.onDrop);
        },

        methods: {
            dragStart(event) {
                event.preventDefault();

                this.isVisible = event.dataTransfer.types.some(type => {
                    return type === 'Files'
                });
            },

            dragEnd(event) {
                if (event.pageX !== 0 && event.pageY !== 0) {
                    return;
                }

                this.isVisible = false;
            },

            onDrop(event) {
                event.preventDefault();

                this.isVisible = false;
                EventBus.$emit(COMMANDS.UPLOAD_FILES, event.dataTransfer.files);
            }
        },
    }
</script>

<style lang="scss">
    .cms-media-library__upload {
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(245, 245, 245, 0.9);
        z-index: 10;
        top: 0;
        outline: 3px dashed #2FA99E;
        outline-offset: -15px;
        text-align: center;
        font-size: 2em;
        padding: 150px 35% 0;

        > i.fa {
            font-size: 4em;
            margin-bottom: 20px;
            display: block;
        }
    }
</style>
