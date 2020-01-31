<template>
    <div class="panel panel-white">
        <div class="panel-heading clearfix">
            <h6 class="panel-title text-semibold pull-left">{{ localization.trans('title') }}</h6>
            <button class="btn btn-success btn-sm pull-right"
                    @click.prevent="selectImages"
                    type="button"
            >
                <i class="fa fa-picture-o"></i> {{ localization.trans('btn_select_photos') }}
            </button>
        </div>

        <table class="table table-striped media-library table-lg">
            <thead>
            <tr>
                <th width="150">{{ localization.trans('table_columns.preview') }}</th>
                <th>{{ localization.trans('table_columns.description') }}</th>
                <th>{{ localization.trans('table_columns.author') }}</th>
                <th width="240">{{ localization.trans('table_columns.info') }}</th>
                <th width="80" class="text-center">{{ localization.trans('table_columns.action') }}</th>
            </tr>
            </thead>
            <tbody>

            <tr v-for="(photo, index) in innerPhotos" :key="photo.id">
                <td>
                    <a :href="photo.image.getUrl()" target="_blank"
                       @click.prevent="showLightbox(photo.image)"
                    >
                        <img :src="photo.image.getPreview().fitToCanvas(140, 100).preview().getUrl()"
                             :alt="photo.image.getUrl()" class="img-rounded img-preview"
                        >
                    </a>
                </td>
                <td>
                    <editable v-model="photo.title"
                              :title="localization.trans('table_row.edit_description_title')"
                              :fallback="localization.trans('table_row.empty_description_text')"
                    ></editable>
                </td>
                <td>
                    <editable v-model="photo.author"
                              :title="localization.trans('table_row.edit_author_title')"
                              :fallback="localization.trans('table_row.empty_author_text')"
                    ></editable>
                </td>
                <td>
                    <ul class="list-condensed list-unstyled no-margin">
                        <li>
                            <span class="text-semibold">
                                {{ localization.trans('table_row.uploaded_at') }}:
                            </span>
                            {{ photo.image.getCreatedAt() }}
                        </li>
                        <li>
                            <span class="text-semibold">
                                {{ localization.trans('table_row.size') }}:
                            </span>
                            {{ photo.image.getHumanSize() }}
                        </li>
                        <li>
                            <span class="text-semibold">
                                {{ localization.trans('table_row.resolution') }}:
                            </span>
                            {{ photo.image.getResolutionText() }}
                        </li>
                    </ul>
                </td>
                <td class="text-center">
                    <a class="delete-photo" href="#" @click.prevent="removeImage(index)">
                        <i class="fa fa-trash"></i> {{ localization.trans('table_row.btn_remove') }}
                    </a>
                </td>
            </tr>

            <tr v-if="!innerPhotos.length">
                <td colspan="5" class="text-center"><em>{{ localization.trans('empty_table_text') }}</em></td>
            </tr>

            </tbody>
        </table>

        <lightbox :images="images"
                  :thumbnail-size="[140, 100]"
                  name="photogallery"
        ></lightbox>
    </div>
</template>

<script>
    import Editable from  './xeditable';
    import Lightbox from  './lightbox';
    import MediaFile from '../media-library/models/MediaFile';
    import LocalizationMixin from '../vue-mixins/localization';

    export default {
        mixins: [LocalizationMixin],

        data() {
            return {
                innerPhotos: []
            };
        },

        props: {
            photos: {
                type: Array,
                default: () => []
            }
        },

        components: {
            'editable': Editable,
            'lightbox': Lightbox,
        },

        computed: {
            images() {
                return this.innerPhotos.map(photo => photo.image);
            }
        },

        methods: {
            removeImage(index) {
                this.innerPhotos.splice(index, 1);
            },

            selectImages() {
                window.MediaLibraryPrompt.multiImage()
                    .selectFiles(this.innerPhotos.map(photo => photo.image))
                    .onSelect(this.addImage)
                    .onUnselect(image => {
                        const index = this.innerPhotos.findIndex(photo => photo.image.getId() === image.getId());

                        if (index !== -1) {
                            this.innerPhotos.splice(index, 1);
                        }
                    })
                    .open();
            },

            addImage(image) {
                this.addInnerPhoto({
                    id: null,
                    title: image.getDescription(),
                    author: '',
                    image: image
                });
            },

            addInnerPhoto(photo) {
                if (typeof photo.image.getId !== 'function') {
                    photo.image = new MediaFile(photo.image);
                }

                this.innerPhotos.push(photo);
            },

            getFormData() {
                return this.innerPhotos.map(photo => {
                    return {
                        id: photo.id,
                        title: photo.title,
                        author: photo.author,
                        image_id: photo.image.getId()
                    };
                });
            },

            fileUpdated(file) {
                this.innerPhotos.some(photo => {
                    if (photo.image.getId() === file.getId()) {
                        photo.image.updateData(file.data);
                        return true;
                    }

                    return false;
                })
            },

            showLightbox(url) {
                EventBus.$emit('lightbox::photogallery', url);
            }
        },

        created() {
            this.photos.forEach(this.addInnerPhoto);

            EventBus.$on('media-library::file-updated', this.fileUpdated);
        },

        destroyed() {
            EventBus.$off('media-library::file-updated', this.fileUpdated);
        }
    }
</script>
