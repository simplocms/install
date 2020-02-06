var reqError = false;
var step = 1;

$(document).ready(function() {
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

        chainRequestsInstall(requests, 0, 0);
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
            progress += requests[index].weight;

            $('#installProgress').attr('aria-valuenow', progress).css('width', progress + '%');
            chainRequestsInstall(requests, index + 1, progress);
        }, function (data) {
            hideEl($('#tab-' + step));
            showEl($('#tab-installError'));
        });
    } else {
        hideEl($('#tab-' + step));
        showEl($('#tab-installSuccess'));
    }
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
        php: {
            label: 'PHP >= 7.1.3',
            link: '#'
        },
        mysql: {
            label: 'Mysql database ^5.6',
            link: '#'
        },
        pdo: {
            label: 'PDO PHP Extension',
            link: '#'
        },
        tokenizer: {
            label: 'Tokenizer PHP Extension',
            link: '#'
        },
        mbstring: {
            label: 'Mbstring PHP Extension',
            link: '#'
        },
        openssl: {
            label: 'OpenSSL PHP Extension',
            link: '#'
        },
        xml: {
            label: 'XML PHP Extension',
            link: '#'
        },
        ctype: {
            label: 'Ctype PHP Extension',
            link: '#'
        },
        gd: {
            label: 'GD PHP Extension',
            link: '#'
        },
        json: {
            label: 'JSON PHP Extension',
            link: '#'
        },
        bcmath: {
            label: 'BCMath PHP Extension',
            link: '#'
        },
        curl: {
            label: 'cURL PHP Extension',
            link: '#'
        },
        zip: {
            label: 'ZipArchive PHP Library is required',
            link: '#'
        }
    };

    var optional = {
        imagick: {
            label: 'Imagick PHP Extension',
            link: '#'
        },
        optim: {
            label: 'JpegOptim',
            link: '#'
        },
        opti: {
            label: 'Optipng',
            link: '#'
        },
        quant: {
            label: 'Pngquant 2',
            link: '#'
        },
        svgo: {
            label: 'SVGO',
            link: '#'
        },
        gif: {
            label: 'Gifsicle',
            link: '#'
        }
    };

    var requests = [];

    $.each(obligatory, function (key, value) {
        var data = {};
        data['ob_check'] = key;
        requests.push({
            call: function() { return sendRequest(data, true) },
            message: value,
            ul: $('#obReq'),
            ob: true,
            key: key
        });
    });

    requests.push({
        call: function() { return sendRequest({perm_check: true}, true) },
        message: 'Installation directory must be writable',
        ul: $('#folderPerm'),
        ob: true,
        key: 'folderPerm'
    });

    $.each(optional, function (key, value) {
        var data = {};
        data['op_check'] = key;
        requests.push({
            call: function() { return sendRequest(data, true) },
            message: value,
            ul: $('#opReq'),
            ob: false,
            key: key
        });
    });

    $('#obReq').html('');
    $('#opReq').html('');
    $('#folderPerm').html('');

    $('#reqBtnNext').prop('disabled', false);
    chainRequestsReq(requests, 0);
}

function chainRequestsReq(requests, index)
{
    if (typeof requests[index] !== 'undefined') {
        $.when(requests[index].call()).then(function( data, textStatus, jqXHR ) {
            var message = typeof requests[index].message == 'string' ? requests[index].message : requests[index].message.label;

            requests[index].ul.append('<li>' + message + ' Success</li>');
            return chainRequestsReq(requests, index + 1);
        }, function(data) {
            var message = typeof requests[index].message == 'string' ? requests[index].message : requests[index].message.label;

            if (requests[index].ob) {
                $('#reqBtnNext').prop('disabled', true);
            }

            if (typeof requests[index].message == 'string') {
                requests[index].ul.append('<li>X ' + message + ' ' + data.responseText + '</li>');
            } else {
                requests[index].ul.append('<li>' + message + ' ' + '<a href="' + requests[index].message.link + '" target="_blank">Find more</a></li>');
            }

            return chainRequestsReq(requests, index + 1);
        });
    }
}