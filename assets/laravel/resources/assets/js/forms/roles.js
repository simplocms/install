Vue.component('roles-form', {
    mounted () {
        $('.maxlength').maxlength();

        // Checkboxes in table header
        $('thead input[type="checkbox"]').on('click', function () {
            var $this = $(this);
            var isChecked = $this.is(':checked');
            var $table = $this.closest('table');

            // if is for all, check whole table
            if (this.hasAttribute('data-all')) {
                $table.find('input[type="checkbox"]')
                    .prop('checked', isChecked)
                    .uniform('update');
            } else {
                var index = $this.closest('th').prevAll().length - 1;

                // check / uncheck whole column
                $table.find('tbody tr')
                    .find('td:eq(' + index + ')')
                    .find('input[type="checkbox"]')
                    .prop('checked', isChecked)
                    .uniform('update');

                // if is unchecked, uncheck "all" checkbox on each row
                if (!isChecked) {
                    $table.find('tr').each(function () {
                        $(this).find('input[data-all=1]')
                            .prop('checked', false)
                            .uniform('update');
                    });
                }
            }
        }).uniform();

        // Checkboxes in table body
        $('tbody input[type="checkbox"]').on('click', function () {
            var $this = $(this);
            var isChecked = $this.is(':checked');

            // If is unchecked
            if (!isChecked) {
                var index = $this.closest('td').prevAll().length;
                var $headerRow = $this.closest('table')
                    .find('thead tr');

                // uncheck "all" checkbox in table header
                $headerRow.find('input[data-all=1]')
                    .prop('checked', false)
                    .uniform('update');

                // uncheck checkbox for current column in table header
                $headerRow.find('th:eq(' + index + ')')
                    .find('input[type="checkbox"]')
                    .prop('checked', false)
                    .uniform('update');

                // uncheck "all" checkbox on current row
                $this.closest('tr').find('input[data-all=1]')
                    .prop('checked', false)
                    .uniform('update');
            }

            // If is for all on the row
            if (this.hasAttribute('data-all')) {
                // check / uncheck all checkboxes on current row
                $this.closest('tr').find('input[type="checkbox"]')
                    .prop('checked', isChecked)
                    .uniform('update');

                // if is unchecked
                if (!isChecked) {

                    // for each column uncheck checkbox in table header
                    $this.closest('td').siblings().each(function () {
                        var siblingIndex = $(this).prevAll().length;
                        $this.closest('table')
                            .find('thead tr')
                            .find('th:eq(' + siblingIndex + ')')
                            .find('input[type="checkbox"]')
                            .prop('checked', false)
                            .uniform('update');
                    })
                }
            }
        }).uniform();

        $('tbody input[type="checkbox"][data-all]:checked').each(function () {
            // check / uncheck all checkboxes on current row
            $(this).closest('tr').find('input[type="checkbox"]')
                .prop('checked', true)
                .uniform('update');
        });

        new Switchery(document.getElementById('input-enabled'));
    }
});