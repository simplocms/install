<template>
    <div>
        <div class="lightbox" v-if="isVisible" @click="hide">
            <button type="button" @click.prevent="hide" class="close"><span>×</span></button>
            <div class="lightbox__element" @click.stop="">
                <div
                    class="lightbox__arrow lightbox__arrow--left"
                    @click.stop.prevent="prev"
                    :class="{'lightbox__arrow--invisible': !hasPrev}"
                >
                    <i class="fa fa-angle-left fa-fw"></i>
                </div>
                <div class="lightbox__image" @click.stop="">
                    <img :src="activeImageSrc"
                         v-if="activeImage && displayImage"
                         @load="activeImageLoaded"
                         :class="{loaded: isImageLoaded}"
                    >
                </div>
                <div
                    class="lightbox__arrow lightbox__arrow--right"
                    @click.stop.prevent="next"
                    :class="{'lightbox__arrow--invisible': !hasNext}"
                >
                    <i class="fa fa-angle-right fa-fw"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            images: Array,
            name: String,
            thumbnailSize: {
                type: Array,
                default: null
            }
        },

        data() {
            return {
                isVisible: false,
                index: 0,
                displayImage: true,
                isImageLoaded: false
            }
        },

        computed: {
            activeImage() {
                return this.images[this.index] || null;
            },

            activeImageSrc() {
                if (this.isImageLoaded) {
                    return this.activeImage.getUrl();
                }

                return this.activeImage.getPreview().fitToCanvas(140, 100).preview().getUrl();
            },

            hasNext() {
                return this.index + 1 < this.images.length;
            },

            hasPrev() {
                return this.index - 1 >= 0;
            },
        },

        mounted() {
            if (this.name) {
                EventBus.$on('lightbox::' + this.name, this.show);
            }
            window.addEventListener('keydown', this.eventListener)
        },

        destroyed() {
            if (this.name) {
                EventBus.$off('lightbox::' + this.name, this.show);
            }
            window.removeEventListener('keydown', this.eventListener)
        },

        methods: {
            show(image) {
                this.isVisible = true;
                this.index = this.images.indexOf(image);
            },

            hide() {
                this.isVisible = false;
                this.index = 0;
                this.isImageLoaded = false;
            },

            prev() {
                if (this.hasPrev) {
                    this.index -= 1;
                    this.isImageLoaded = false;
                }
            },

            next() {
                if (this.hasNext) {
                    this.index += 1;
                    this.isImageLoaded = false;
                }
            },

            activeImageLoaded() {
                if (this.isImageLoaded) {
                    return;
                }

                const imgLarge = new Image();
                imgLarge.src = this.activeImage.getUrl();
                imgLarge.onload = () => {
                    this.isImageLoaded = true;
                };
            },

            eventListener(e) {
                if (this.isVisible) {
                    switch (e.key) {
                        case 'ArrowRight':
                            this.next();
                            break;
                        case 'ArrowLeft':
                            this.prev();
                            break;
                        case 'ArrowDown':
                        case 'ArrowUp':
                        case ' ':
                            e.preventDefault();
                            break;
                        case 'Escape':
                            this.hide();
                            break
                    }
                }
            },
        },
    }
</script>

<style lang="scss" scoped>
    .lightbox {
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(0, 0, 0, .8);
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;

        > .close {
            position: fixed;
            right: 0;
            top: 0;
            color: #fff;
            width: 4rem;
            height: 4rem;
            font-size: 3.5rem;
        }
    }

    .lightbox__thumbnail {
        width: 100%;
        height: 100%;
    }

    .lightbox__thumbnail img {
        width: 100%;
    }

    .lightbox__close {
        position: fixed;
        right: 0;
        top: 0;
        padding: 1rem;
        font-size: 1.5rem;
        cursor: pointer;
        color: #fff;
        width: 4rem;
        height: 4rem;
    }

    .lightbox__arrow--invisible {
        visibility: hidden;
    }

    .lightbox__element {
        display: flex;
        width: 100%;
        height: fit-content;
        max-height: 100%;
    }

    .lightbox__arrow {
        padding: 0 2rem;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .lightbox__arrow i {
        color: #fff;
        pointer-events: none;
        font-size: 2.5rem;
    }

    .lightbox__image {
        flex: 1;
        text-align: center;
    }

    .lightbox__image img {
        &.loaded {
            width: auto !important;
            max-height: 100%;
            height: auto !important;
            max-width: 100%;
        }

        &:not(.loaded) {
            filter: blur(5px);
            /* this is needed so Safari keeps sharp edges */
            transform: scale(1);
            width: 100%;
            height: auto !important;
        }
    }

    @media screen and (max-width: 720px) {
        .lightbox__arrow {
            padding: 0 1rem;
        }
    }

    @media screen and (max-width: 500px) {
        .lightbox__element {
            position: relative;
        }

        .lightbox__arrow {
            position: absolute;
            padding: 0 2rem;
            height: 100%;
        }

        .lightbox__arrow--right {
            right: 0;
            background: linear-gradient(to right, transparent, rgba(0, 0, 0, .3));
        }

        .lightbox__arrow--left {
            left: 0;
            background: linear-gradient(to left, transparent, rgba(0, 0, 0, .3));
        }
    }
</style>
