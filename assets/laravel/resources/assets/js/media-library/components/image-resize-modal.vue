<template>
    <div tabindex="-1"
         role="dialog"
         class="modal fade"
         v-if="isVisible"
         ref="modal"
    >
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" @click.prevent="close" class="close"><span>×</span></button>
                    <h4 class="modal-title">{{ localization.trans('resize_modal.title') }}</h4>
                </div>

                <div class="modal-body clearfix">
                    <div class="col-xs-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> {{ localization.trans('resize_modal.info_text') }}
                        </div>

                        <!-- Resolution -->
                        <div class="form-group">
                            <label for="ml-input-image-width">{{ localization.trans('resize_modal.label') }}</label>
                            <div class="input-group">
                                <input id="ml-input-image-width"
                                       type="number"
                                       class="form-control"
                                       min="1"
                                       :max="image.getWidth()"
                                       v-model.number="width"
                                       @change="widthChanged"
                                >
                                <div class="input-group-addon">x</div>
                                <input type="number"
                                       class="form-control"
                                       min="1"
                                       :max="image.getHeight()"
                                       v-model.number="height"
                                       @change="heightChanged"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" @click.prevent="close">
                        {{ localization.trans('resize_modal.btn_cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" @click.prevent="submit">
                        {{ localization.trans('resize_modal.btn_save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                image: null,
                width: 0,
                height: 0,
                isVisible: false,
                resolveCallback: null,
                rejectCallback: null
            }
        },

        props: {
            localization: Object
        },

        methods: {
            open(image) {
                this.image = image;
                this.width = this.image.getWidth();
                this.height = this.image.getHeight();

                this.isVisible = true;

                this.$nextTick(() => {
                    $(this.$refs.modal).modal('show');
                });

                return new Promise((resolve, reject) => {
                    this.resolveCallback = resolve;
                    this.rejectCallback = reject;
                });
            },

            submit() {
                this.resolveCallback({width: this.width, height: this.height});
                this.close(false);
            },

            close(reject) {
                if (reject !== false) {
                    this.rejectCallback();
                }

                $(this.$refs.modal).modal('hide').on('hidden.bs.modal', () => this.isVisible = false);
            },

            widthChanged(event) {
                if (event.target.value > this.image.getWidth()) {
                    this.width = this.image.getWidth();
                }

                this.height = Math.round(this.width / this.aspectRatio);
            },

            heightChanged(event) {
                if (event.target.value > this.image.getHeight()) {
                    this.height = this.image.getHeight();
                }

                this.width = Math.round(this.height * this.aspectRatio);
            }
        },

        computed: {
            aspectRatio() {
                if (!this.image || !this.image.getHeight()) {
                    return 1;
                }

                return this.image.getWidth() / this.image.getHeight();
            }
        },
    }
</script>
