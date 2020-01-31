import Photogallery from '../vue-components/photogallery';
import SeoInputs from '../vue-components/form/seo-inputs';
import OpenGraphInputs from '../vue-components/form/open-graph-inputs';
import PublishingStateInputs from '../vue-components/form/publishing-state-inputs';
import Form from '../vendor/Form';

Vue.component('articles-form', {
    data() {
        return {
            form: new Form({
                ...this.article,
                set_unpublish_at: this.article.unpublish_at_date !== null,
                categories: this.categories,
            }).addDataCollector(this.getFormData),
            ckEditor: null,
            showPublishingInputs: true,
            noCategories: false
        };
    },

    props: {
        article: {
            type: Object,
            required: true
        },
        useTags: {
            type: Boolean,
            required: true
        },
        useGridEditor: {
            type: Boolean,
            required: true
        },
        tags: {
            type: Array,
            required: true
        },
        categoriesTreeUrl: {
            type: String,
            required: true
        },
        categories: {
            type: Array,
            required: true
        }
    },

    components: {
        'photogallery': Photogallery,
        'seo-inputs': SeoInputs,
        'open-graph-inputs': OpenGraphInputs,
        'publishing-state-inputs': PublishingStateInputs,
    },

    mounted() {
        this.$nextTick(() => {
            this.initializeForm();
        });
    },

    methods: {
        tabActivated (tab) {
            // When first tab (index zero) is activated
            this.showPublishingInputs = tab === 0;
        },

        initializeForm() {
            if (this.useTags) {
                $('#input-tags').tagsinput({
                    confirmKeys: [13, 44],
                    typeahead: {
                        afterSelect: function (val) {
                            this.$element.val("");
                        },
                        source: this.tags
                    },
                    freeInput: true,
                    trimValue: true
                });
            }

            // CK Editor
            if (!this.useGridEditor) {
                ClassicEditor.create(this.$refs.inputText)
                    .then(editor => this.ckEditor = editor);
            }

            // Category tree
            $(".tree-checkbox").fancytree({
                checkbox: true,
                selectMode: 2,
                autoScroll: true,
                icon: false,
                source: {
                    url: this.categoriesTreeUrl,
                    complete: (xhr) => {
                        this.noCategories = xhr.responseJSON.length === 0;
                    }
                },
                select: (event, data) => {
                    this.form.categories = data.tree.getSelectedNodes().map(node => Number(node.key));
                }
            });
        },

        /**
         * Fired when title is changed.
         * @param {Event} $event
         */
        onTitleChanged($event) {
            this.form.title = $event.target.value;

            if (this.form.url === null || !this.form.url.length) {
                this.form.url = this.form.title;
            }
        },

        /**
         * Fired when url is changed.
         * @param {Event} $event
         */
        onUrlChanged($event) {
            this.form.url = $event.target.value;
        },

        getFormData() {
            const additionalData = this.useGridEditor ? this.$refs.gridEditor.getFormData() : {};

            // Photogallery
            additionalData.photogallery = this.$refs.photogallery.getFormData();

            // Tags
            if (this.useTags) {
                additionalData.tags = $('#input-tags').val();
            }

            // CKEditor content
            if (!this.useGridEditor) {
                additionalData.text = this.ckEditor.getData();
            }

            return additionalData;
        }
    },

    watch: {
        'form.url'(newUrl) {
            this.form.url = Converter.removeDiacritics(newUrl);
        }
    }

});
