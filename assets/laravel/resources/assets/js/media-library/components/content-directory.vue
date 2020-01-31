<template>
    <div class="cms-media-library__directory">
        <span @click.prevent="open"><i class="fa fa-folder-o"></i> {{ directory.name }}</span>
    </div>
</template>


<script>

    import {COMMANDS} from "../enums";

    export default {
        methods: {
            open() {
                EventBus.$emit(COMMANDS.ACTIVATE_DIRECTORY, this.directory.id);
            }
        },

        props: {
            directory: {
                type: Object,
                required: true
            }
        }
    }
</script>

<style lang="scss" scoped>
    .cms-media-library__directory {
        font-size: 1.5rem;
        margin: 1rem;
        flex: 1 1 calc(20% - 2rem);

        > span {
            cursor: pointer;
            color: #585858;

            > .fa {
                margin-right: 5px;
            }

            &:hover {
                color: #898989;
            }
        }
    }
</style>
