<style scoped>
    .sorted {
        background-color: rgba(0, 0, 0, 0.03);
    }
</style>

<script>
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
            column: {
                'type': Object,
                required: true
            }
        },

        directives: {
            startWithHtml: {
                inserted(el, binding) {
                    el.insertAdjacentHTML('afterbegin', binding.value);
                }
            }
        },

        render (h) {
            return h(
                'td',
                {
                    attrs: {
                        class: this.columnClass
                    },
                    domProps: this.slotIsDefined ? {} : {
                        innerHTML: this.formattedContent
                    },
                },
                this.slotIsDefined ? this.store.datatable.$scopedSlots[this.column.name]({
                    column: this.column,
                    row: this.row
                }) : null
            )
        },

        mounted() {
        },

        computed: {
            slotIsDefined() {
                return typeof this.store.datatable.$scopedSlots[this.column.name] !== 'undefined';
            },

            formattedContent() {
                return this.column.content;
            },

            isSorted() {
                return this.store.sorting && this.store.sorting.column === this.column.name;
            },

            columnClass() {
                const classes = [];

                if (this.isSorted) {
                    classes.push('sorted');
                }

                if (this.headColumn.align) {
                    classes.push('text-' + this.headColumn.align);
                }

                return classes.join(' ');
            },

            headColumn() {
                return this.store.table.columns[this.column.name];
            }
        },
    };

</script>
