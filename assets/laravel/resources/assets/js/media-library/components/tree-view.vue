<template>
    <ul class="directory-tree">
        <media-library-tree-item v-if="root" :directory="root"></media-library-tree-item>
    </ul>
</template>


<script>
    import Item from './tree-item.vue';
    Vue.component('media-library-tree-item', Item);

    export default {
        props: {
            root: {
                type: Object,
                default: null
            },
        },

        watch: {
            root (newVal, oldVal) {
                this.$nextTick(() => {
                    this.$emit('rendered');
                });
            }
        }
    };
</script>

<style lang="scss">
    .directory-tree {
        list-style: none;
        line-height: 25px;
        margin-bottom: 0;
        padding-left: 10px;

        ul {
            list-style: none;
        }
    }
</style>
