'use strict';

// -------- login --------

var login_user_id = undefined;
var login_name = undefined;

var crypt_password = function (name, password_raw) {
    var SHA = new jsSHA(name + ':' + password_raw, 'TEXT');
    return SHA.getHash('SHA-256', 'HEX');
};

var crypt_salt = function (password, salt) {
    var SHA = new jsSHA(password + salt, 'TEXT');
    return SHA.getHash('SHA-256', 'HEX');
};

var login_need_show = function () {
    $('.login_need').css('display', 'initial');
    $('.login_ok').css('display', 'none');
};

var login_ok_show = function () {
    $('.login_need').css('display', 'none');
    $('.login_ok').css('display', 'initial');
};

var login_update = function () {
    if (login_user_id) {
        $('#text_name').text(login_name);
        login_ok_show();
    } else {
        login_need_show();
    }
};

// -------- header --------

$(function () {
    $('#title').click(function () {
        window.location.hash = '';
    });

    $('#btn_explore').click(function () {
        window.location.hash = '!explore';
    });

    $('#btn_stores').click(function () {
        window.location.hash = '!stores';
    });

    $('#btn_orders').click(function () {
        window.location.hash = '!orders';
    });

    $('#btn_my').click(function () {
        window.location.hash = '!my';
    });

    $('#btn_logout').click(function () {
        $.post(
            'ajax/auth_logout.php',
            {
                user_id: 1
            },
            function (data) {
                if (data['auth_success']) {
                    login_user_id = undefined;
                    login_name = undefined;

                    login_update();
                } else {
                    // TODO: internal error
                }
            },
            'json'
        );
    });

    $('#btn_login').click(function () {
        $.post(
            'ajax/auth_gen.php',
            {},
            function (data) {
                var i_name = $('#input_name').val();
                var i_pass = $('#input_password').val();

                $.get(
                    'ajax/auth_login.php',
                    {
                        name: i_name,
                        password: crypt_salt(
                            crypt_password(i_name, i_pass),
                            data['auth_salt']
                        ),
                    },
                    function (data) {
                        if (data['auth_success']) {
                            login_user_id = data['auth_user_id'];
                            login_name = data['auth_name'];

                            login_update();
                        } else {
                            if (data['auth_name']) {
                                // TODO: wrong password
                            } else {
                                // TODO: wrong name
                            }
                        }
                    },
                    'json'
                );
            },
            'json'
        );
    });
});

// -------- page init --------

$(function () {
    login_update();
});
