<template>
    <tr @click="onClick"
        @contextmenu.prevent="onContextMenu"
        :class="{'datatable-row-selected': row.isSelected}"
    >
        <table-column v-for="(column, index) in sortedColumns"
                      :key="index"
                      :column="column"
                      :row="row"
                      :store="store"
        ></table-column>

        <td v-if="store.table.actionsVisible" class="text-center" ref="actionsColumn">
            <row-actions :controls="row.controls"
                         @emits="onEventEmitted"
            ></row-actions>
        </td>
    </tr>
</template>

<script>
    import TableColumn from './body-row-column.vue';
    import RowActions from './row-actions';

    export default {
        props: {
            store: {
                'type': Object,
                required: true
            },
            row: {
                'type': Object,
                required: true
            },
            index: {
                type: Number,
                'default': 0
            }
        },

        computed: {
            sortedColumns() {
                return this.store.columnOrder.map(column => {
                    return this.row.columns[column.name];
                });
            }
        },

        components: {
            'table-column': TableColumn,
            'row-actions': RowActions,
        },

        methods: {
            onClick($event) {
                let node = $event.target;
                while (node !== null) {
                    if (node === this.$refs.actionsColumn) {
                        return;
                    }
                    node = node.parentNode;
                }

                let url = this.row.dblAction;

                if (!url) {
                    return;
                }

                window.location = url;
            },

            onEventEmitted($event) {
                $event.row = this.row;
                this.store.$emit($event.type, $event);
            }
        }
    };

</script>

<style lang="scss" scoped>
    tr {
        background-color: #ffffff;

        &.odd {
            background-color: #f9f9f9;
        }

        > td {
            padding: 8px 10px;
            border-top: 1px solid #ddd;
        }

        &:first-child > td {
            border-top: none;
        }

        &:hover {
            background-color: rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }
    }
</style>
