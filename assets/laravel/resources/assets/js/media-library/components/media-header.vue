<template>
    <div class="header">
        <div class="cms-media-library__header">
            <h2><i class="fa fa-picture-o"></i> {{ localization.trans('header_title') }}</h2>

            <button type="button"
                    class="close"
                    @click.prevent="closePrompt"
                    v-if="isPrompt"
            ><span>×</span></button>

            <div class="cms-media-library__search">
                <input type="text"
                       class="form-control"
                       :placeholder="localization.trans('search_placeholder')"
                       v-model="searchText"
                       v-on:keyup.enter="search"
                >
                <button @click.prevent="search" class="cms-media-library__search-submit" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>

        <div class="p-15" v-if="warnCacheDriver">
            <div class="alert alert-danger mb-5">
                {{ localization.trans('cache_driver_warning', {size: '200kB'}) }}
            </div>
        </div>

        <nav class="navbar navbar-default" style="width: 100%">
            <ul class="nav navbar-nav">
                <li>
                    <a href="#" @click.prevent="$emit('create-directory')"
                       :disabled="!enableActions"
                    >
                        <i class="fa fa-folder-o"></i> {{ localization.trans('navbar.btn_create_folder') }}
                    </a>
                </li>
                <li>
                    <a href="#" @click.prevent="$emit('upload')"
                       :disabled="!enableActions"
                    >
                        <i class="fa fa-upload"></i> {{ localization.trans('navbar.btn_select_files') }}
                    </a>
                </li>
                <li>
                    <a href="#"
                       @click.prevent="$emit('delete-selected')"
                       :disabled="!canDelete"
                    >
                        <i class="fa fa-trash-o"></i> {{ localization.trans('navbar.btn_delete_files') }}
                    </a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-sort-amount-asc" v-if="activeSortOption.direction === 'ASC'"></i>
                        <i class="fa fa-sort-amount-desc" v-else></i>
                        {{ getSortOptionText(activeSortOption) }} <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li v-for="sortOption in sortOptions"
                            :key="sortOption.by + '-' + sortOption.direction"
                        >
                            <a href="#" @click.prevent="$emit('sort', sortOption)">
                                <i class="fa fa-sort-amount-asc" v-if="sortOption.direction === 'ASC'"></i>
                                <i class="fa fa-sort-amount-desc" v-else></i>
                                {{ getSortOptionText(sortOption) }}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</template>


<script>

    export default {
        methods: {
            search() {
                if (this.searchText === '') {
                    this.searchText = null;
                }

                if (this.searchText === this.lastSearched) {
                    return;
                }

                this.lastSearched = this.searchText;
                this.$emit('search', this.searchText);
            },

            resetSearch() {
                this.lastSearched = null;
                this.searchText = null;
            },

            setSearch(text) {
                this.searchText = text;
                this.lastSearched = text;
            },

            closePrompt() {
                this.$emit('close');
            },

            getSortOptionText(sortOption) {
                return this.localization.trans(`sort_options.${sortOption.by}-${sortOption.direction}`);
            }
        },

        props: {
            canDelete: Boolean,
            enableActions: Boolean,
            sortOptions: Array,
            activeSortOption: Object,
            isPrompt: Boolean,
            localization: Object,

            warnCacheDriver: {
                type: Boolean,
                default: false
            }
        },

        data() {
            return {
                searchText: null,
                lastSearched: null
            };
        }
    }
</script>

<style lang="scss" scoped>

    .header {
        width: 100%;
    }

    .cms-media-library__header {
        width: 100%;
        height: 8rem;
        background: #fff;
        padding: 2rem;
        border-bottom: 1px solid #E7E7E7;

        > h2 {
            font-size: 1.6em;
            display: inline-block;
            float: left;
            margin-top: 5px;

            > i.fa {
                padding-right: 5px;
            }
        }

        > .close {
            font-size: 2.5em;
            margin-left: 15px;
        }
    }

    .cms-media-library__search {
        max-width: 30rem;
        flex: 1 1 30rem;
        position: relative;
        float: right;
        width: 250px;
    }

    .cms-media-library__search-submit {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
        background: none;
        border: 0;
    }

    .navbar {
        border-bottom: 2px solid #EBEBEB;
        border-radius: 0;
    }

    .navbar, .navbar-nav {
        margin: 0;
        padding: 0;
    }

    .navbar-nav {
        > li > a {
            color: #585858 !important;

            &:hover {
                color: #898989 !important;
            }

            &[disabled], &[disabled]:hover {
                color: rgba(47, 47, 47, 0.6)  !important;
                cursor: not-allowed;
            }

            > i.fa {
                font-size: 1.3em;
                padding-right: 5px;
            }
        }
    }
</style>
