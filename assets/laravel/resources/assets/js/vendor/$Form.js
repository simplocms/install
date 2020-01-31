window.Form = {};

/**
 * Shows error on input field
 *
 * @param {Element|jQuery} elem
 * @param {String} message
 */
Form.addError = function(elem, message) {
    if (elem.focus) {
        elem.focus();
    }

    var $elem = $(elem);
    var $group = $elem.closest('.form-group').addClass("has-error");
    let $helpBlock = null;

    if (message) {
        var name = $elem.attr('name');

        if(name) {
            $helpBlock = $group.find('.help-block[data-for="' + name + '"]');
        }

        if (!$helpBlock || !$helpBlock.length) {
            $helpBlock = $group.find('.help-block').first();
        }

        if ($helpBlock.length) {
            $helpBlock.text(message).show();
        }
    }

    return $helpBlock && $helpBlock.length;
};

/**
 * Removes error from input element
 *
 * @param {HTMLInputElement} elem
 */
Form.removeError = function(elem) {
    var $elem = $(elem);
    var $group = $elem.closest(".form-group");

    $group.removeClass("has-error").find('.help-block').text('').hide();
};


/**
 * Removes all errors from form
 *
 * @param {HTMLFormElement|jQuery} form
 */
Form.removeAllErrors = function(form) {
    var $form = $(form);
    $form.find(':input').each(function(){
        Form.removeError(this);
    });

    var $errorsPanel = $form.find('.form-errors');
    if($errorsPanel.length){
        $errorsPanel.html('').hide();
    }
};


/**
 * Adds errors to form fields
 *
 * @param {HTMLFormElement|jQuery} form
 * @param {Object} errors
 */
Form.addErrors = function(form, errors){
    var $form = $(form);
    if(!$form.length) return;

    var $errorsPanel = $form.find('.form-errors').first();

    // Go through all errors
    for(var field in errors){

        // Check if prototype exists
        if(!Object.prototype.hasOwnProperty.call(errors, field)) continue;

        // Try to find field for an error
        var $field = $form.find(':input[name="' + field + '"]');

        // Compose message of an error
        var message = '';
        for(var mi in errors[field]){
            if(!Object.prototype.hasOwnProperty.call(errors[field], mi)) continue;

            var msg = errors[field][mi];
            message += (typeof msg === 'string' ? msg : msg.message) + '\n';
        }

        // If field was not found
        if(!$field.length) {
            var splitField = field.split('.');
            if (splitField.length > 1) {
                $field = $form.find(':input[name="' + splitField[0] + '[]"]').eq(parseInt(splitField[1]));
            }
        }

        let success = false;

        // If manually mapped array field exists
        if($field.length) {
            success = Form.addError($field, message);
        }

        // Otherwise write errors to error panel
        if($errorsPanel.length && !success){
            $errorsPanel.append(
                $('<li />').addClass('list-group-item list-group-item-danger')
                    .append('<i class="fa fa-warning"></i> ')
                    .append(message)
            );
            $errorsPanel.show();
        }
    }
};
