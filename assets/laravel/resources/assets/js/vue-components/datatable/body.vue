<template>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 datatable-padding">
                <div class="table-responsive">
                    <table class="datatable">
                        <thead>
                        <tr v-for="(headerRow, index) in headerRows"
                            :key="index"
                        >
                            <head-column v-for="column in headerRow"
                                         :key="column.name"
                                         :column="column"
                                         :store="store"
                                         :header-rows-count="headerRows.length - index"
                            ></head-column>
                            <th class="actions-column-header"
                                v-if="index === 0 && store.table.actionsVisible"
                                :rowspan="headerRows.length"
                            >
                                {{ localization.trans('column_actions') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr is="table-row"
                            v-for="(row, index) in store.rows"
                            :key="row.id"
                            :index="index"
                            :row="row"
                            :store="store"
                            :class="[index % 2 === 0 ? 'odd' : 'even']"
                        >
                        </tr>

                        <tr v-if="!store.rows || !store.rows.length" class="datatable-empty-row">
                            <td :colspan="totalColumns">
                                {{ localization.trans('no_results') }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import ColumnHead from './column-head.vue';
	import TableRow from './body-row.vue';

	export default {
		data () {
			return {
				headerRows: [],
                totalColumns: 1
			};
		},

		components: {
			'head-column': ColumnHead,
			'table-row': TableRow
		},

		props: {
			store: {
				'type': Object,
				required: true
			},
            localization: {
                type: Object,
                required: true
            }
		},

		methods: {
			addHeaderRowColumn (column, depth = 0) {
				if (!this.headerRows[depth]) {
					this.headerRows[depth] = [];
				}

				this.headerRows[depth].push(column);

				if (column.subColumns.length) {
					for (const i in (column.subColumns || [])) {
						this.addHeaderRowColumn(column.subColumns[i], depth + 1);
						this.totalColumns++;
					}
                } else if (depth === 0) {
					this.totalColumns++;
                }
			}
		},

		created () {
			for (const i in this.store.table.columns) {
				this.addHeaderRowColumn(this.store.table.columns[i]);
			}
		}
	};

</script>

<style lang="scss" scoped>
    .actions-column-header {
        width: 35px;
    }

    .datatable-padding {
        padding: 0;
    }

    .datatable {
        width: 100%;
        margin: 0 auto;
        clear: both;
        border-collapse: separate;
        border-spacing: 0;
        box-sizing: content-box;
    }

    .datatable thead > tr > th {
        padding: 10px 18px;
        border-bottom: 1px solid #111;
    }

    .table-responsive {
        overflow-x: visible;
    }

    .datatable-empty-row > td {
        text-align: center;
        padding: 10px;
        font-style: italic;
        background-color: #f9f9f9;
    }
</style>
