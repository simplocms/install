<template>
    <div>
        <h6 class="panel-title mb-5">{{ localization.trans('title') }}</h6>

        <div class="mb-15">
            {{ localization.trans('help_text') }}
            <div v-for="(input, index) in localization.trans('inputs', null, [])"
                 :key="index"
                 v-if="input.info"
            >
                <kbd>{{ input.label }}</kbd> - {{ input.info }}
            </div>
        </div>

        <!-- OG title -->
        <v-form-group :error="form.getError('open_graph.title')">
            <label for="input-og-title">{{ localization.trans('inputs.title.label') }}</label>
            <input :maxlength="titleMaxLength"
                   :placeholder="truncatedTitlePlaceholder"
                   name="open_graph[title]"
                   type="text"
                   id="input-og-title"
                   v-model="form.open_graph.title"
                   class="form-control"
                   v-maxlength
            >
        </v-form-group>

        <!-- OG type -->
        <v-form-group :error="form.getError('open_graph.type')">
            <label for="input-og-type">{{ localization.trans('inputs.type.label') }}</label>
            <input :maxlength="100"
                   name="open_graph[type]"
                   type="text"
                   id="input-og-type"
                   v-model="form.open_graph.type"
                   class="form-control"
                   list="og-types-list"
                   v-maxlength
            >
            <datalist id="og-types-list">
                <option value="article"></option>
                <option value="website"></option>
            </datalist>
        </v-form-group>

        <!-- OG description -->
        <v-form-group :error="form.getError('open_graph.description')">
            <label for="input-og-description">{{ localization.trans('inputs.description.label') }}</label>
            <textarea :maxlength="descriptionMaxLength"
                      name="open_graph[description]"
                      :placeholder="truncatedDescriptionPlaceholder"
                      id="input-og-description"
                      class="form-control"
                      v-model="form.open_graph.description"
                      rows="3"
                      v-maxlength
            ></textarea>
        </v-form-group>

        <!-- OG url -->
        <v-form-group :error="form.getError('open_graph.url')">
            <label for="input-og-url">{{ localization.trans('inputs.url.label') }}</label>
            <input :placeholder="urlPlaceholder"
                   name="open_graph[url]"
                   type="text"
                   id="input-og-url"
                   v-model="form.open_graph.url"
                   class="form-control"
            >
        </v-form-group>

        <!-- OG image -->
        <v-form-group :has-error="form.getError('open_graph.image_id')">
            <label>{{ localization.trans('inputs.image.label') }}</label>
            <media-library-file-selector :image="true"
                                         name="open_graph[image_id]"
                                         :error="form.getError('open_graph.image_id')"
                                         v-model="form.open_graph.image_id"
            ></media-library-file-selector>
        </v-form-group>
    </div>
</template>

<script>
    import LocalizationMixin from '../../vue-mixins/localization';

    export default {
        mixins: [LocalizationMixin],

        data() {
            return {
                titleMaxLength: 90,
                descriptionMaxLength: 300,
            };
        },

        props: {
            /** @type {string} */
            titlePlaceholder: String,
            /** @type {string} */
            descriptionPlaceholder: String,
            /** @type {string} */
            urlPlaceholder: String,
            /** @type {Form} */
            form: {
                type: Object,
                required: true
            }
        },

        computed: {
            truncatedTitlePlaceholder() {
                if (!this.titlePlaceholder) {
                    return '';
                }

                return this.titlePlaceholder.substring(0, this.titleMaxLength);
            },

            truncatedDescriptionPlaceholder() {
                if (!this.descriptionPlaceholder) {
                    return '';
                }

                return this.descriptionPlaceholder.substring(0, this.descriptionMaxLength);
            }
        },
    }
</script>

<style scoped>

</style>
