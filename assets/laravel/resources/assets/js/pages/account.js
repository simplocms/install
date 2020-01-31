const options = window.pageAccountEditOptions();

Vue.component('page-account-edit', {
    data () {
        return {
            defaultThumbnail: options.defaultThumbnailSrc,
            imageSrc: options.imageSrc,
            imageSelected: options.imageSelected
        };
    },
    computed: {
        removeImageValue () {
            return this.imageSelected ? 'false' : 'true';
        }
    },
    methods: {
        openFileInput () {
            $('#image-input').click();
        },
        removeImage () {
            this.imageSrc = this.defaultThumbnail;
            this.imageSelected = false;
            $('#image-input').val('');
        },
        previewThumbnail (event) {
            var input = event.target;

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = function (e) {
                    vm.imageSrc = e.target.result;
                    vm.imageSelected = true;
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    },
    created () {
        if (!this.imageSrc) {
            this.imageSrc = this.defaultThumbnail;
        }
    },
    mounted () {
        // Maxlength
        $('.maxlength').maxlength({
            alwaysShow: true
        });
    }
});