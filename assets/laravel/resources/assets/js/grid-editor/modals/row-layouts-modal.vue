<template>
    <div tabindex="-1"
         role="dialog"
         :id="id"
         class="modal fade"
         ref="modal"
    >
        <div role="document" class="modal-dialog">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <button type="button" @click.prevent="hide" class="close"><span>×</span></button>
                    <h4 class="modal-title">{{ localization.trans('row_layouts_modal.title') }}</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3" v-for="(layout, index) in layouts" :key="index">
                            <a :class="'_grid-row-layout-' + layout.columns.join('-')"
                               href="#"
                               @click.prevent="submit(layout.columns)"
                            >
                                <div class="preview"></div>
                                {{ localization.choice('row_layouts_modal.layout_label', layout.columns.length, {
                                    count: layout.columns.length
                                })}}
                                <small class="text-muted" v-if="layout.info">{{ layout.info }}</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                id: 'row-layouts-modal',
                submitCallback: null,
                layouts: [
                    {
                        columns: [12]
                    },
                    {
                        columns: [6, 6]
                    },
                    {
                        columns: [4, 4, 4]
                    },
                    {
                        columns: [3, 3, 3, 3]
                    },
                    {
                        columns: [2, 2, 2, 2, 2, 2]
                    },
                    {
                        columns: [4, 8],
                        info: '1/3 + 2/3'
                    },
                    {
                        columns: [8, 4],
                        info: '2/3 + 1/3'
                    },
                    {
                        columns: [3, 6, 3],
                        info: '1/4 + 1/2 + 1/4'
                    }
                ]
            }
        },

        props: {
            localization: Object
        },

        methods: {
            show(submit) {
                this.submitCallback = submit;
                $(this.$refs.modal).modal('show');
            },

            submit(columns) {
                this.submitCallback(columns);
                this.hide();
            },

            hide() {
                $(this.$refs.modal).modal('hide');
            },
        },

        mounted() {
            this.$root.$on('show::' + this.id, this.show);
        },

        destroyed() {
            this.$root.$off('show::' + this.id, this.show);
        }
    }
</script>

<style lang="scss" scoped>
    $blueColor: #26a69a;

    a[class*="_grid-row-layout-"] {
        text-align: center;
        margin-bottom: 25px;
        display: block;
        color: #2F2F2F;

        .preview
        {
            margin-bottom: 10px;
            padding: 30px 0;
            width: 100%;
            border: 1px dashed #c2c2c2;
            background-position: center !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;

            &:hover
            {
                border: 1px solid $blueColor;
            }
        }

        small {
            display: block;
        }

        &:hover
        {
            color: $blueColor;
            .preview
            {
                border: 1px solid $blueColor;
            }
        }
    }

    @each $layout in "12" "6-6" "4-4-4" "3-3-3-3" "2-2-2-2-2-2" "4-8" "8-4" "3-6-3"
    {
        ._grid-row-layout-#{$layout} > .preview
        {
            background: url(/media/admin/images/grideditor/grid-row-layout-#{$layout}.png);
        }
        ._grid-row-layout-#{$layout}:hover > .preview
        {
            background: url(/media/admin/images/grideditor/grid-row-layout-#{$layout}-active.png);
        }
    }
</style>
