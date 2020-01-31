<template>
    <component is="div" class="panel-heading">
        <div class="row">

            <div class="col-md-5">
                {{ localization.trans('row_limit_before') }}
                <select :value="store.rowLimit"
                        class="form-control row-limit-select"
                        @change="changeRowLimit"
                >
                    <option v-for="(option, index) in rowLimitOptions" :key="index" :value="option">
                        {{ option }}
                    </option>
                </select>
                {{ localization.trans('row_limit_after') }}
            </div>

            <!-- Search box and button -->
            <div class="col-md-7">
                <div class="pull-right datatable-search-box"
                     v-if="store.table.isSearchEnabled"
                >
                    <div class="input-group has-feedback"
                         :class="[feedbackClass]"
                    >
                        <input type="search"
                               name="table_search"
                               class="form-control input-sm pull-right"
                               :placeholder="localization.trans('search_placeholder')"
                               v-model="textToSearch"
                               v-on:keyup.enter="startFilteringText"
                        >
                        <div class="input-group-btn">
                            <button class="btn btn-sm btn-default"
                                    @click.prevent="startFilteringText"
                            >
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>

<style scoped>
    .datatable-search-box {
        width: 250px;
    }
    .datatable-search-box input[type="search"] {
        height: 32px;
    }
    .row-limit-select {
        height: 24px;
        line-height: 24px;
        width: 50px;
        display: inline-block;
        padding: 0;
    }
</style>

<script>
    export default {
        data() {
            return {
                textToSearch: this.store.table.searchValue || '',
                rowLimitOptions: [
                    5, 10, 15, 25, 50, 100
                ]
            };
        },
        props: {
            store: {
                type: Object,
                required: true
            },

            localization: {
                type: Object,
                required: true
            }
        },
        computed: {
            feedbackClass() {
                if (!this.textToSearch.length) {
                    return '';
                }

                if (this.store.rows.length) {
                    return 'has-success';
                }

                return 'has-warning';
            },
        },

        methods: {
            startFilteringText() {
                this.store.setSearchText(this.textToSearch);
                this.store.currentPage = 1;
                this.store.datatable.reloadData();
            },

            changeRowLimit(event) {
                this.store.setRowLimit = Number(event.target.value);
                this.store.datatable.reloadData();
            }
        },
    };

</script>
