var reqError = false;
$(document).ready(function() {
    var step = 1;
    
    checkRequirements();

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

        var msgEl = $('#installMessages');

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

        var envData = $.extend({}, {}, formData);
        envData['create_env'] = true;

        var requests = [
            {
                call: function() {
                    return sendRequest({unzip_file: true}, true, function () {
                        hideEl($('#tab-' + step++));
                        showEl($('#tab-' + step));
                        msgEl.append('Unzipping cms');
                    })
                },
                message: null,
                weight: 33
            },
            {
                call: function() { return sendRequest(envData, true) },
                message: 'Creating env',
                weight: 0.8
            },
            {
                call: function() { return sendRequest({migrate: true}, true) },
                message: 'Creating tables',
                weight: 64
            },
            {
                call: function() { return sendRequest({db_seed: true}, true) },
                message: 'Seeding data',
                weight: 1.7
            },
            {
                call: function() { return sendRequest(storeAdminData, true) },
                message: 'Creating user',
                weight: 0.5
            }
        ];

        var successful = chainRequestsInstall(requests, 0, 0);

        console.log('install was ' + successful);
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

function chainRequestsInstall(requests, index, progress)
{
    var msgEl = $('#installMessages');

    if (typeof requests[index] !== 'undefined') {
        msgEl.html(requests[index].message);

        $.when(requests[index].call()).then(function( data, textStatus, jqXHR ) {
            msgEl.html(jqXHR.responseText);
            if (jqXHR.status == 200) {
                progress += requests[index].weight;

                $('#installProgress').attr('aria-valuenow', progress).css('width', progress + '%');
                return chainRequestsInstall(requests, index + 1, progress);
            }

            return false;
        })
    }

    return true;
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
    var isGood = true;
    var err = false;

    isGood = checkName($('#admin_first_name'));
    err = err ? err : ! isGood;
    isGood = checkName($('#admin_last_name'));
    err = err ? err : ! isGood;
    isGood = checkName($('#admin_login'));
    err = err ? err : ! isGood;
    isGood = checkEmail($('#admin_email'));
    err = err ? err : ! isGood;
    isGood = checkAdminPassword($('#admin_password'));
    err = err ? err : ! isGood;
    isGood = checkPasswordConfirm($('#admin_password_confirm'));
    err = err ? err : ! isGood;

    return ! err;
}

function checkName(el)
{
    if (el.val().length < 3) {
        el.siblings('.error').html('Field requires at least 3 characters');
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
        el.siblings('.error').html('Hodnota musí odpovídat emailu');
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
        html += 'Field requires at least 6 characters<br>';
        pwdErr = true;
    }

    if (pwdValue.toLowerCase() == pwdValue) {
        html += 'Field requires at least one upper case letter<br>';
        pwdErr = true;
    }

    if (pwdValue.toUpperCase() == pwdValue) {
        html += 'Field requires at least one lower case letter<br>';
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

function checkRequirements()
{
    var obligatory = {
        php: 'PHP >= 7.1.3',
        mysql: 'Mysql database ^5.6',
        pdo: 'PDO PHP Extension',
        tokenizer: 'Tokenizer PHP Extension',
        mbstring: 'Mbstring PHP Extension',
        openssl: 'OpenSSL PHP Extension',
        xml: 'XML PHP Extension',
        ctype: 'Ctype PHP Extension',
        gd: 'GD PHP Extension',
        json: 'JSON PHP Extension',
        bcmath: 'BCMath PHP Extension',
        curl: 'cURL PHP Extension',
        zip: 'ZipArchive PHP Library is required'
    };

    var optional = {
        imagick: 'Imagick PHP Extension',
        optim: 'JpegOptim',
        opti: 'Optipng',
        quant: 'Pngquant 2',
        svgo: 'SVGO',
        gif: 'Gifsicle'
    };

    var requests = [];

    $.each(obligatory, function (key, value) {
        var data = {};
        data['ob_check'] = key;
        requests.push({
            call: function() { return sendRequest(data, true) },
            message: value,
            ul: $('#obReq'),
            ob: true
        });
    });

    $.each(optional, function (key, value) {
        var data = {};
        data['op_check'] = key;
        requests.push({
            call: function() { return sendRequest(data, true) },
            message: value,
            ul: $('#opReq'),
            ob: false
        });
    });

    var errObj = {
        err: false
    };

    $('#obReq').html('');
    $('#opReq').html('');

    var retval = chainRequestsReq(requests, 0, errObj, true);
}

function chainRequestsReq(requests, index, errObj, canContinue)
{
    var promises = [];
    requests.forEach(element => {
        promises.push(element.call())
    });

    $.when(...promises).then(function () {
        var error = false;
        for(var key in arguments) {
            requests[key].ul.append('<li>' + requests[key].message + ' ' + arguments[key][0] + '</li>');

            if (requests[key].ob && arguments[key][0] == 'neexistuje') {
                error = true;
            }
        }

        $('#reqBtnNext').prop('disabled', error);
    });
}