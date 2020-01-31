const options = window.imageModuleOptions();

new Vue({
    el: '#m-image-configuration-form',

    data: {
        localization: new Localization(window.cms_trans),
        formData: {...options.model},
        $form: null,
        image: null
    },

    mounted: function () {
        this.$form = $(this.$el);
        // Form fill
        this.$form.trigger('admin:form-fill-ready', this.fillForm);
        this.$form.on('admin:form-submit-data', this.getFormData);
    },

    beforeDestroy() {
        this.$form.off('admin:form-submit-data', this.getFormData);
    },

    computed: {
        aspectRatio() {
            if (!this.isResolutionAvailable) {
                return 1;
            }

            return this.image.getWidth() / this.image.getHeight();
        },

        isResolutionAvailable() {
            return this.image && this.image.isResolutionAvailable();
        }
    },

    watch: {
        image (newVal) {
            if (newVal && newVal.isResolutionAvailable() &&
                (newVal.getId() !== this.formData.image_id || this.formData.width === null)
            ) {
                this.formData.width = newVal.getWidth();
                this.formData.height = newVal.getHeight();
            }

            if (newVal && this.formData.alt === null) {
                this.formData.alt = newVal.getDescription();
            }
        }
    },

    methods: {
        fillForm(data) {
            this.formData = data;
            this.image = data._temp ? data._temp.image || null : null;
        },

        getFormData(event, output) {
            output.alt = this.formData.alt;
            output.img_class = this.formData.img_class;
            output.is_sized = this.formData.is_sized;
            output.image_id = this.image ? this.image.getId() : null;

            if (this.formData.is_sized) {
                output.width = Number(this.formData.width);
                output.height = Number(this.formData.height);
            }

            output._temp = {
                image: this.image
            };
        },

        widthChanged(event) {
            if (!this.isResolutionAvailable) {
                return;
            }

            if (event.target.value > this.image.getWidth()) {
                this.formData.width = this.image.getWidth();
            }

            this.formData.height = Math.round(this.formData.width / this.aspectRatio);
        },

        heightChanged(event) {
            if (!this.isResolutionAvailable) {
                return;
            }

            if (event.target.value > this.image.getHeight()) {
                this.formData.height = this.image.getHeight();
            }

            this.formData.width = Math.round(this.formData.height * this.aspectRatio);
        }
    }
});
