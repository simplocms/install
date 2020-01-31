<template>
    <div>
        <!-- Add content buttons -->
        <add-content-buttons v-if="layoutEditMode"
                             :localization="localization"
                             @add-container="chooseContainerLayout(true)"
                             @add-row="chooseRowLayout(true)"
                             @add-module="addModule(true)"
        ></add-content-buttons>

        <!-- Content -->
        <div class="grid" ref="content" :class="[receiveClass]">
            <drop-zone :accept="sortableItems" :target="innerContent"></drop-zone>

            <template v-if="innerContent"
                      v-for="(contentObject, index) in innerContent">
                <div :key="contentObject.uuid"
                     :path="index.toString()"
                     :is="'cms-ge-content-' + contentObject.type"
                     :item="contentObject"
                     ref="contentItem"
                     :layout-edit-mode="layoutEditMode"
                     @remove="removeContentItem(index)"
                     @cloned="addContent"
                     :localization="localization"
                     :source-list="innerContent"
                ></div>

                <drop-zone :accept="sortableItems"
                           :target="innerContent"
                           :position="index + 1"
                ></drop-zone>
            </template>
        </div>

        <!-- Add content buttons -->
        <add-content-buttons v-if="layoutEditMode"
                             :localization="localization"
                             @add-container="chooseContainerLayout"
                             @add-row="chooseRowLayout"
                             @add-module="addModule(false)"
        ></add-content-buttons>
    </div>

</template>

<script>
    import AddContentButtons from './add-content-buttons';
    import ContentBehaviourMixin from './content-behaviour-mixin';

    export default {
        mixins: [ContentBehaviourMixin],

        data() {
            return {
                sortableItems: ['row', 'container', 'module'],
                prepend: false
            }
        },

        components: {
            'add-content-buttons': AddContentButtons,
        },

        methods: {
            chooseContainerLayout(prepend = false) {
                this.prepend = prepend;
                this.$root.$emit('show::row-layouts-modal', this.addContainerWithLayout);
            },

            addContainerWithLayout(columns) {
                this.addContainer(columns, this.prepend);
            },

            chooseRowLayout(prepend = false) {
                this.prepend = prepend;
                this.$root.$emit('show::row-layouts-modal', this.addRowWithLayout);
            },

            addRowWithLayout(columns) {
                this.addRow(columns, this.prepend);
            }
        }
    }
</script>
