<template>
    <th v-html="columnContent"
        :class="{
            sortable: column.isSortable,
            'asc': sortASC,
            'desc': sortDESC
        }"
        :style="columnStyle"
        @click.prevent="sort"
        :rowspan="rowspan"
        :colspan="colspan"
    ></th>
</template>

<script>
    import {SORT_DIRECTIONS} from './enums';

    export default {
        data() {
            return {
                sortDirection: null
            };
        },

        props: {
            column: {
                type: Object,
                required: true
            },
            store: {
                'type': Object,
                required: true
            },
            headerRowsCount: {
                type: Number,
                default: 1
            }
        },

        computed: {
            sortASC() {
                return this.sortDirection === SORT_DIRECTIONS.ASC;
            },
            sortDESC() {
                return this.sortDirection === SORT_DIRECTIONS.DESC;
            },
            rowspan() {
                if (this.headerRowsCount === 1) {
                    return null;
                }

                return this.column.subColumns.length ? null : this.headerRowsCount;
            },
            colspan() {
                if (!this.column.subColumns.length) {
                    return null;
                }

                return this.column.subColumns.length;
            },
            columnContent() {
                let content = this.column.label;

                if (this.column.isSortable) {
                    let iconType = 'sort';
                    if (this.sortASC) {
                        iconType = 'sort-asc';
                    } else if (this.sortDESC) {
                        iconType = 'sort-desc';
                    }

                    content += `<i class="fa fa-${iconType} sort-icon"></i>`
                }

                return content;
            },
            columnStyle() {
                const styles = {};
                if (this.column.width) {
                    styles.width = this.column.width + 'px';
                }

                return styles;
            }
        },

        methods: {
            sort() {
                if (this.store.locked || !this.column.isSortable) {
                    return;
                }

                switch (this.sortDirection) {
                    case SORT_DIRECTIONS.ASC:
                        this.sortDirection = SORT_DIRECTIONS.DESC;
                        break;
                    case SORT_DIRECTIONS.DESC:
                        this.sortDirection = SORT_DIRECTIONS.NONE;
                        break;
                    default:
                        this.sortDirection = SORT_DIRECTIONS.ASC;
                        break;
                }

                this.store.$emit('sort', this.column.name, this.sortDirection);
            },

            sortingChanged(columnName, direction) {
                if (this.column.name !== columnName) {
                    this.sortDirection = SORT_DIRECTIONS.NONE;
                }
            }
        },

        created() {
            if (this.store.sorting && this.store.sorting.column === this.column.name) {
                this.sortDirection = this.store.sorting.direction;
            }

            this.store.$on('sort', this.sortingChanged);
        }
    };

</script>

<style lang="scss">
    th.sortable {
        position: relative;
        cursor: pointer;

        > i.sort-icon {
            position: absolute;
            right: 18px;
            top: 13px;
            color: #bbb;
        }

        &:hover,
        > i.sort-icon.fa-sort-asc,
        > i.sort-icon.fa-sort-desc,
        &:hover > i.sort-icon {
            color: #26a69a;
        }
    }
</style>
