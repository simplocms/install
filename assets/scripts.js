$(document).ready(function() {
    var step = 1;
    
    // check database connection
    $('#dbButton').on('click', function($e) {
        $e.preventDefault();

        var form = $('#dbForm');
        var dbResult = $('#dbResult');
        dbFormData = {};

        $.each(form.serializeArray(), function() {
            dbFormData[this.name] = this.value;
        });

        sendRequest(dbFormData, true)
        .done(function(data) {
            dbResult.text('Connection successful');
            $('#tab-2 .btn-next').prop('disabled', false);
        }).fail(function(data) {
            dbResult.text(data.responseText);
            $('#tab-2 .btn-next').prop('disabled', true);
        })
    });

    $('#admin_password').on('input', function() {
        checkAdminPassword($(this));
    });

    $('#admin_first_name').on('input', function() {
        checkName($(this));
    });

    $('#admin_last_name').on('input', function() {
        checkName($(this));
    });

    $('#admin_login').on('input', function() {
        checkName($(this));
    });

    $('#admin_email').on('input', function() {
        checkEmail($(this));
    });

    // check user and install
    $('#installButton').on('click', function(e) {
        e.preventDefault();

        var i = 0;

        if (! checkAdminSection()) {
            return;
        }

        var form = $('#dbForm');
        var formData = {};

        $.each(form.serializeArray(), function() {
            formData[this.name] = this.value;
        });

        var adminForm = $('#adminForm');
        var adminData = {};

        $.each(adminForm.serializeArray(), function() {
            adminData[this.name] = this.value;
        });

        var storeAdminData = $.extend({}, adminData, formData);
        storeAdminData['store_admin'] = true;

        var unzipData = $.extend({}, {}, formData);
        unzipData['unzip_file'] = true;

        sendRequest(unzipData, false)
        .done(function(data) {
            console.log(data)
        }).fail(function(data) {
            console.log(data)
        })

        sendRequest({migrate: true}, false)
        .done(function(data) {
            console.log(data);
        }).fail(function(data) {
            console.log(data)
        })

        sendRequest({db_seed: true}, false)
        .done(function(data) {
            console.log(data)
        }).fail(function(data) {
            console.log(data)
        })

        sendRequest(storeAdminData, false)
        .done(function(data) {
            console.log(data)
        }).fail(function(data) {
            console.log(data)
        })
    });

    $('.btn-next').click(function (e) {
        e.preventDefault();
        hideEl($('#tab-' + step++));
        showEl($('#tab-' + step));
    });

    $('.btn-prev').click(function (e) {
        e.preventDefault();
        hideEl($('#tab-' + step--));
        showEl($('#tab-' + step));
    });
});

function sendRequest(sendData, sendAsync, sendBeforeSend = null)
{
    return $.ajax({
        url: window.location.pathname,
        method: 'POST',
        data: sendData,
        async: sendAsync,
        beforeSend: sendBeforeSend
    });
}

function hideEl(el) {
    el.removeClass('show');
    el.addClass('d-none');
}

function showEl(el) {
    el.removeClass('d-none');
    el.addClass('show');
}

function checkAdminSection()
{
    var err = false;
    
    checkName($('#admin_first_name'));
    checkName($('#admin_last_name'));
    checkName($('#admin_login'));
    checkEmail($('#admin_email'));
    checkAdminPassword($('#admin_password'));
    checkPasswordConfirm($('#admin_password_confirm'));

    return ! err;
}

function checkName(el)
{
    if (! el.val().length) {
        el.siblings('.error').html('First name is required');
        return false;
    } else {
        el.siblings('.error').html('');
        return true;
    }
}

function checkEmail(el)
{
    var err = false;
    if (! /[^\@]+\@[^\@]+\.[\w]+/.test(el.val())) {
        el.siblings('.error').html('Hodnota můsí odpovídat emailu');
        return false;
    } else {
        el.siblings('.error').html('');
        return true;
    }
}

function checkAdminPassword(el)
{
    var pwdErr = false;
    var pwdValue = el.val();
    var html = '';

    if (el.val().length < 6) {
        html += 'Need at least 6 characters<br>';
        pwdErr = true;
    }

    if (pwdValue.toLowerCase() == pwdValue) {
        html += 'Need at least one upper case letter<br>';
        pwdErr = true;
    }

    if (pwdValue.toUpperCase() == pwdValue) {
        html += 'Need at least one lower case letter<br>';
        pwdErr = true;
    }

    if (pwdErr) {
        $('#adminPasswordError').html(html);
        return false;
    } else {
        $('#adminPasswordError').html('');
        return true;
    }
}

function checkPasswordConfirm(el)
{
    if ($('#admin_password').val() != el.val()) {
        $('#adminPasswordConfirmError').html('Heslá musia byť rovnaké');
        return false;
    } else {
        $('#adminPasswordConfirmError').html('');
        return true;
    }
}