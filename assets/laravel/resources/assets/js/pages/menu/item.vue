<template id="menu-item-template">
    <draggable-item handle=":scope > .dd-handle"
                    :item="item"
                    :source="sourceList"
                    :path="path"
    >
        <li class="dd-item" :data-index="index">
            <div class="dd-handle">
                <i class="fa fa-arrows icon-move"></i>
            </div>

            <div class="dd-content">
                <a class="collapsed" data-toggle="collapse" :data-target="'#item-menu-' + id">
                    <span class="label label-success label-roundless" v-show="item.pageId">
                        {{ localization.trans('menu_item.types.page') }}
                    </span>
                    <span class="label label-primary" v-show="item.categoryId">
                        {{ localization.trans('menu_item.types.category') }}
                    </span>
                    <span class="label label-default" v-show="isCustomUrl">
                        {{ localization.trans('menu_item.types.custom_page') }}
                    </span>

                    {{ item.name }}
                </a>

                <div :id="'item-menu-' + id" class="collapse">
                    <div class="dd-item-setting-content">

                        <div class="row">

                            <div class="col-md-6">
                                <label>{{ localization.trans('menu_item.labels.name') }}</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" v-model="item.name" :disabled="!canEdit">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label>{{ localization.trans('menu_item.labels.class') }}</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" v-model="item.class" :disabled="!canEdit">
                                </div>
                            </div>

                            <div class='col-md-6' v-if="isCustomUrl">
                                <label>{{ localization.trans('menu_item.labels.url') }}</label>
                                <div class='form-group'>
                                    <input type='text' class='form-control' v-model="item.url" :disabled="!canEdit">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="styled" v-model="item.openNewWindow"
                                               :disabled="!canEdit">
                                        {{ localization.trans('menu_item.labels.open_new_window') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row" v-if="canEdit">
                            <div class="col-md-12 text-right">
                                <button class="btn pull-right bg-danger" @click='removeItem'>
                                    <i class="fa fa-trash"></i>
                                    {{ localization.trans('menu_item.btn_delete') }}
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <ol :class="{ 'empty': !item.children.length }" v-if="!isOnMaxDepth">
                <drop-zone :target="item.children"
                           :path="path"
                ></drop-zone>
                <template v-for="(menuItem, index) in item.children">
                    <menu-item :item="menuItem"
                               :localization="localization"
                               :index="index"
                               :key="index"
                               :path="path + '-' + index"
                               :source-list="item.children"
                               :max-depth="maxDepth"
                               @remove="removeChild(index)"
                    ></menu-item>
                    <drop-zone :target="item.children"
                               :path="path"
                               :position="index + 1"
                    ></drop-zone>
                </template>
            </ol>

        </li>

        <template slot="helper">
            <div class="menu-item-helper">
                <span class="label label-success label-roundless" v-show="item.pageId">
                    {{ localization.trans('menu_item.types.page') }}
                </span>
                    <span class="label label-primary" v-show="item.categoryId">
                    {{ localization.trans('menu_item.types.category') }}
                </span>
                    <span class="label label-default" v-show="isCustomUrl">
                    {{ localization.trans('menu_item.types.custom_page') }}
                </span>
                {{ item.name }}
            </div>
        </template>
    </draggable-item>
</template>

<script>
    import DraggableItem from '../../vue-components/draggable/draggable-item';

    const options = window.menuPageOptions();
    export default {
        props: {
            item: Object,
            index: Number,
            sourceList: Array,
            path: String,
            maxDepth: {
                type: Number,
                'default': 0
            },
            localization: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                id: 0,
                canEdit: options.canEdit
            }
        },

        components: {
            'draggable-item': DraggableItem
        },

        methods: {
            removeItem() {
                this.$emit('remove');
            },

            removeChild(index) {
                this.item.children.splice(index, 1);
            }
        },

        computed: {
            /**
             * Is item with custom url?
             * @return {Boolean}
             */
            isCustomUrl: function () {
                return !this.item.pageId && !this.item.categoryId;
            },

            isOnMaxDepth() {
                return this.maxDepth > 0 && this.maxDepth === this.path.split('-').length;
            }
        },

        created() {
            this.id = Utils.guid();
        },

        mounted() {
            $(this.$el).find('input[type="checkbox"]').uniform();
        }
    };
</script>
