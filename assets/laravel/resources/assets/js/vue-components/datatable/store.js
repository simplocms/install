export default class {
	/**
	 * Constructor.
	 */
	constructor (datatable) {
		// Datatable context.
		this.datatable = datatable;
		// Table data.
		this.table = datatable.table;
		// Lock.
		this.isTableLocked = true;
		// Sorting.
		this.sorting = null;
		// Text search.
		this.searchText = null;
		// Rows.
		this.rows = [];
		// Pages.
		this.currentPage = 1;
		// Row limit.
		this.rowLimit = 15;
		// total rows
		this.totalRows = 0;
	}

	/**
	 * Emit event on datatable.
	 * @param {string} event
	 * @param {*} args
	 */
	$emit (event, ...args) {
		this.datatable.$emit(event, ...args);
	}

	/**
	 * Listen for event on datatable.
	 * @param {string} event
	 * @param {function} callback
	 */
	$on (event, callback) {
		this.datatable.$on(event, callback);
	}

	/**
	 * Stop listening for event on datatable.
	 * @param {string|string[]} event
	 * @param {function} callback
	 */
	$off (event, callback) {
		this.datatable.$off(event, callback);
	}

	/**
	 * Lock table.
	 */
	lockTable () {
		this.isTableLocked = true;
	}

	/**
	 * Unlock table.
	 */
	unlockTable () {
		this.isTableLocked = false;
	}

	/**
	 * Search text.
	 * @param {string} text
	 */
	setSearchText (text) {
		this.searchText = text;
	}
};

class RowSelection {
	constructor (store) {
		this.allSelected = false;
		this.selectedCount = 0;
		this.store = store;

		this.store.$on('selection-changed', this.selectionChanged.bind(this));

		this.init();
	}

	init () {
		this.all = false;
		this.selected = [];
		this.unselected = [];
		this.selectedMap = {};
		this.unselectedMap = {};
	}

	selectAll (value) {
		this.init();

		this.all = Boolean(value);
		this.store.rows.map(row => row.isSelected = value);
		this.store.$emit('selection-changed');
	}

	selectSingleRow (row) {
		this.init();
		this.store.rows.map(tableRow => tableRow.isSelected = row.id === tableRow.id);
		this.selectRow(row);
	}

	selectRow (row) {
		if (this.all) {
			const index = this.unselected.findIndex(unselectedRow => unselectedRow.id === row.id);

			if (index === -1) {
				return;
			}

			this.unselected.splice(index, 1);
			delete this.unselectedMap[row.id];
		} else {
			this.selected.push(row);
			this.selectedMap[row.id] = row;
		}

		this.store.$emit('selection-changed');
	}

	unselectRow (row) {
		if (this.all) {
			this.unselected.push(row);
			this.unselectedMap[row.id] = row;
			this.store.$emit('selection-changed');
		} else {
			const index = this.selected.findIndex(selectedRow => selectedRow.id === row.id);

			if (index === -1) {
				return;
			}

			this.selected.splice(index, 1);
			delete this.selectedMap[row.id];
		}

		this.store.$emit('selection-changed');
	}

	selectionChanged () {
		this.allSelected = this.all ? this.unselected.length === 0 : this.selected.length === this.store.totalRows;
		this.selectedCount = this.all ? this.store.totalRows - this.unselected.length : this.selected.length;
	}

	getLastSelectedRow () {
		return this.selected.slice(-1)[0] || null;
	}

	isRowSelected(rowId) {
		return Boolean(this.all ? !this.unselectedMap[rowId] : this.selectedMap[rowId]);
	}

	/**
	 * Unregister events.
	 */
	destroy () {
		this.store.$off('selection-changed', this.selectionChanged);
	}
}
