<template>
    <div class="datatable-wrapper">
        <div class="panel panel-flat">
            <!-- Header -->
            <table-header :store="store" :localization="localization"></table-header>

            <!-- Body -->
            <table-body :store="store" :localization="localization" v-cloak></table-body>

            <div class="col-md-5 pt-10" v-if="store.totalRows">
                {{ localization.choice('row_count_info', store.totalRows, {
                    first: pageOffset,
                    last: topRowOffset,
                    count: store.totalRows
                }) }}
            </div>
            <div class="col-md-7" v-if="store.totalRows">
                <div class="pagination-wrapper pull-right">
                    <pagination :per-page="store.rowLimit"
                                :total="store.totalRows"
                                :value="store.currentPage"
                                @change="changePage"
                    ></pagination>
                </div>
            </div>

            <div class="clearfix"></div>
        </div>

        <spinner-lock :is-locked="store.isTableLocked"></spinner-lock>
    </div>
</template>

<style scoped>
    .datatable-wrapper {
        position: relative;
    }
</style>

<script>
    import Header from './header.vue';
    import Body from './body.vue';
    import Store from './store.js';
    import Pagination from '../pagination';
    import {SORT_DIRECTIONS} from './enums';
    import SpinnerLock from '../form/spinner-lock';

    export default {
        data() {
            return {
                store: new Store(this)
            };
        },
        components: {
            'table-header': Header,
            'table-body': Body,
            'pagination': Pagination,
            'spinner-lock': SpinnerLock,
        },
        props: {
            table: {
                type: Object,
                required: true
            }
        },

        computed: {
            localization() {
                return new Localization(this.table.translations);
            },

            pageOffset() {
                return (this.store.currentPage - 1) * this.store.rowLimit + 1;
            },

            topRowOffset() {
                const offset = this.store.currentPage * this.store.rowLimit;

                if (offset > this.store.totalRows) {
                    return this.store.totalRows;
                }

                return offset;
            }
        },

        methods: {
            changePage(page) {
                this.store.currentPage = page;
                this.reloadData();
            },

            initTable() {
                // Page
                this.store.currentPage = this.table.currentPage;

                // Sorting
                this.store.sorting = this.table.sortOptions;

                // Row limit
                this.store.rowLimit = this.table.rowLimit;
            },

            getTableConfig() {
                const data = {};

                if (this.store.sorting) {
                    data['sort_column'] = this.store.sorting.column;
                    data['sort_direction'] = this.store.sorting.direction;
                }

                if (this.store.searchText) {
                    data['search_text'] = this.store.searchText;
                }

                if (this.store.setRowLimit) {
                    data['row_limit'] = this.store.setRowLimit;
                    this.store.rowLimit = this.store.setRowLimit;
                    delete this.store.setRowLimit;

                    if (this.store.rowLimit * this.store.currentPage > this.store.totalRows) {
                        this.store.currentPage = 1;
                    }
                }

                if (this.store.currentPage) {
                    data['page'] = this.store.currentPage;
                }

                return data;
            },

            reloadData() {
                this.store.lockTable();

                return axios.get(this.table.dataLink, {params: this.getTableConfig()})
                    .then(response => {
                        this.store.rows = response.data.rows;
                        this.store.totalRows = Number(response.data.total);
                        this.store.unlockTable();
                    })
                    .catch(thrown => {
                        this.store.unlockTable();
                    });
            },

            /**
             * Set datatable sorting.
             * @param {string} columnName
             * @param {number} direction
             */
            setSorting(columnName, direction) {
                if (SORT_DIRECTIONS.NONE === direction) {
                    this.store.sorting = null;
                } else {
                    this.store.sorting = {
                        column: columnName,
                        direction: direction
                    };
                }

                this.reloadData();
            },

            createColumnOrder(columns) {
                if (!this.store.columnOrder) {
                    this.store.columnOrder = [];
                }

                for (const i in columns) {
                    if (!columns[i].subColumns.length) {
                        this.store.columnOrder.push(columns[i]);
                    }
                    this.createColumnOrder(columns[i].subColumns);
                }
            }
        },

        created() {
            this.createColumnOrder(this.table.columns);
            this.$on('sort', this.setSorting);
            this.initTable();
        },

        mounted() {
            this.store.unlockTable();
            this.reloadData();
        }
    };

</script>
