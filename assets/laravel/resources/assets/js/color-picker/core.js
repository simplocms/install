export default class ColorPicker {

    /**
     * GridEditor constructor.
     * @param {HTMLInputElement} input
     * @param {object} options
     */
    constructor (input, options)
    {
        this.$input = $(input).first();
        this.$customColor = null;
        this.$selectedColor = null;

        this.options = {
            colors: [
                '#ffffff', '#000000', '#474747',
                '#ff0000', '#00ff00', '#0000ff',
                '#ffff00', '#00ffff', '#ff00ff'
            ],
            defaultCustomColor: '#4a0005'
        };

        // Extend options
        if (typeof options === 'object') {
            this.options = $.extend(this.options, options);
        }

        // Initialize default color.
        this.color = this.options.color || null;
        if (this.color) {
            this.$input.val(this.color);
        } else {
            const value = this.$input.val();

            if (value) {
                this.color = value;
            }
        }

        this.initialize();
    }

    /**
     * Initialize color picker.
     */
    initialize ()
    {
        let color = this.$input.val() || '';

        this.$input.hide();

        const $divider = $('<li />').addClass('divider');
        const $colorsList = $('<ul />').addClass('dropdown-menu')
            .append($divider);
        this.$customColor = this.createColorSample(this.options.defaultCustomColor);
        this.$selectedColor = this.createColorSample(this.color);

        $colorsList
            .append(
                $('<li />').addClass('with-text')
                           .text('Žádná')
                           .prepend(this.createColorSample())
                           .on('click', () => this.changeColor(null))
            )
            .append(
                $('<li />').addClass('with-text')
                           .text('Vlastní')
                           .prepend(this.$customColor)
                           .on('click', () => this.selectCustomColor())
            )
            .append($divider);

        this.options.colors.forEach(color => {
            $colorsList.append(
                $('<li />').append(
                    this.createColorSample(color)
                )
                    .on('click', () => this.changeColor(color))
            )
        });

        const $dropdown = $('<div />').addClass('dropdown color-picker')
            .append(
                $('<button />', {
                    'class': 'btn btn-default dropdown-toggle',
                    'type': 'button',
                    'data-toggle': 'dropdown'
                })
                    .append(this.$selectedColor)
                    .append($('<span />').addClass('caret'))
            )
            .append($colorsList);

        this.$input.after($dropdown);

        this.$colorInput = $('<input>', {
            type: 'color',
            value: this.options.defaultCustomColor
        }).hide().on('change', (event) => {
            this.changeColor(event.target.value);
            this.updateColorSample(this.$customColor, this.color);
        });

        this.$input.after(this.$colorInput);
    }

    /**
     * Select custom color trigger.
     */
    selectCustomColor () {
        this.$colorInput.click();
    }

    /**
     * Change color
     * @param {string|null} color
     */
    changeColor (color) {
        this.color = color;
        this.updateColorSample(this.$selectedColor, color);
        this.$input.val(this.color);
    }

    /**
     * Create color sample element.
     * @param {string} color
     * @return {jQuery}
     */
    createColorSample (color = null) {
        const $color = $('<span />')
            .addClass('sample');

        this.updateColorSample($color, color);

        return $color;
    }


    /**
     * Update color sample with specified color.
     * @param {jQuery} $sample
     * @param {string|null} color
     */
    updateColorSample ($sample, color) {
        if (color) {
            $sample.removeClass('none').css('backgroundColor', color);
        } else {
            $sample.addClass('none').css('backgroundColor', '');
        }

        this.$input.trigger('change', color || null);
    }
}