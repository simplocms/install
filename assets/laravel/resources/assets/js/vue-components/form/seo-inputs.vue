<template>
    <div>
        <h6 class="panel-title mb-5">{{ localization.trans('title') }}</h6>

        <div class="mb-15">
            {{ localization.trans('help_text') }}
            <div v-for="(input, index) in localization.trans('inputs', null, [])"
                 :key="index"
                 v-if="input.info"
            >
                <kbd>{{ input.label }}</kbd> {{ input.info }}
            </div>
        </div>

        <!-- SEO title -->
        <v-form-group :error="form.getError('seo_title')">
            <label for="input-seo-title">{{ localization.trans('inputs.title.label') }}</label>
            <input :maxlength="titleMaxLength"
                   :placeholder="truncatedTitlePlaceholder"
                   name="seo_title"
                   type="text"
                   id="input-seo-title"
                   v-model="form.seo_title"
                   class="form-control"
                   v-maxlength
            >
        </v-form-group>

        <!-- SEO description -->
        <v-form-group :error="form.getError('seo_description')">
            <label for="input-seo-description">{{ localization.trans('inputs.description.label') }}</label>
            <textarea :maxlength="descriptionMaxLength"
                      name="seo_description"
                      :placeholder="truncatedDescriptionPlaceholder"
                      id="input-seo-description"
                      class="form-control"
                      v-model="form.seo_description"
                      v-maxlength
                      rows="3"
            ></textarea>
        </v-form-group>

        <!-- SEO indexing -->
        <v-checkbox-switch v-model="form.seo_index" name="seo_index" v-if="showIndex">
            {{ localization.trans('inputs.index.label') }}
        </v-checkbox-switch>

        <!-- SEO follow -->
        <v-checkbox-switch v-model="form.seo_follow" name="seo_follow" v-if="showFollow">
            {{ localization.trans('inputs.follow.label') }}
        </v-checkbox-switch>

        <!-- SEO sitemap -->
        <v-checkbox-switch v-model="form.seo_sitemap" name="seo_sitemap" v-if="showSitemap">
            {{ localization.trans('inputs.sitemap.label') }}
        </v-checkbox-switch>
    </div>
</template>

<script>
    import LocalizationMixin from '../../vue-mixins/localization';

    export default {
        mixins: [LocalizationMixin],

        data() {
            return {
                titleMaxLength: 65,
                descriptionMaxLength: 320
            };
        },

        props: {
            /** @type {string} */
            titlePlaceholder: String,
            /** @type {string} */
            descriptionPlaceholder: String,
            /** @type {Form} */
            form: {
                type: Object,
                required: true
            },
            /** @type {Boolean} */
            showIndex: {
                type: Boolean,
                default: true
            },
            /** @type {Boolean} */
            showFollow: {
                type: Boolean,
                default: true
            },
            /** @type {Boolean} */
            showSitemap: {
                type: Boolean,
                default: true
            },
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
        }
    }
</script>

<style scoped>

</style>
