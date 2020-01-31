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
                    <h4 class="modal-title">{{ localization.trans('modules_modal.title') }}</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Modules -->
                        <div class="col-md-3" v-for="(module, index) in modules" :key="module.name">
                            <a class="_grid-module-list-icon" @click="selectModule(index)">
                                <div class="preview">
                                    <i class="fa" :class="'fa-' + module.icon"></i>
                                </div>
                                {{ module.title }}
                            </a>
                        </div>

                        <!-- Universal modules-->
                        <div class="col-md-3" v-for="(module, index) in universalModules" :key="module.name">
                            <a class="_grid-module-list-icon" @click="selectUniversalModule(index)">
                                <div class="preview">
                                    <i class="fa" :class="'fa-' + module.icon"></i>
                                </div>
                                {{ module.title }}
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
                id: 'modules-modal',
                submitCallback: null,
            }
        },

        props: {
            modules: {
                type: Array,
                default: []
            },
            universalModules: {
                type: Array,
                default: []
            },
            localization: Object
        },

        methods: {
            show(submit) {
                this.submitCallback = submit;
                $(this.$refs.modal).modal('show');
            },

            selectModule(index) {
                this.hide();
                $(this.$refs.modal).one('hidden.bs.modal', () => {
                    this.submitCallback({...this.modules[index]});
                });
            },

            selectUniversalModule(index) {
                this.hide();
                $(this.$refs.modal).one('hidden.bs.modal', () => {
                    this.submitCallback({...this.universalModules[index]});
                });
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
