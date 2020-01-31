import ColorPicker from './core';

(function ($) {

        $.fn.colorPicker = function () {
            /** @type {Array.<*>} */
            const coreArguments = Array.prototype.slice.call(arguments);

            this.each(function () {
                let ColorPickerInstance = $(this).data('ColorPicker');

                if (!ColorPickerInstance) {
                    ColorPickerInstance = new ColorPicker(
                        this, coreArguments[0]
                    );
                    $(this).data('ColorPicker', ColorPickerInstance);
                } else if (typeof coreArguments[0] === 'string' &&
                           ColorPickerInstance[coreArguments[0]]
                ) {
                    ColorPickerInstance[coreArguments[0]].call(
                        ColorPickerInstance, ...coreArguments.slice(1)
                    );
                }
            });

            return this;
        };
    }
)(jQuery);