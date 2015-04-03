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
    $('.login_need').css('display', 'block');
    $('.login_ok').css('display', 'none');
};

var login_ok_show = function () {
    $('.login_need').css('display', 'none');
    $('.login_ok').css('display', 'block');
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
    $('#btn_logout').click(function () {
        $.post(
            'ajax/auth_logout.php',
            {
                user_id: login_user_id,
            },
            function (data) {
                if (data['auth_success']) {
                    login_user_id = undefined;
                    login_name = undefined;

                    login_update();
                    content_update();
                } else {
                    // TODO: internal error
                }
            },
            'json'
        );
    });

    $('#input_name').keypress(function (e) {
        if (e.which == 13) {
            $('#input_password').focus();
        }
    });

    $('#input_password').keypress(function (e) {
        if (e.which == 13) {
            $('#btn_login').click();
        }
    });

    $('#btn_login').click(function () {
        $.get(
            'ajax/auth_gen.php',
            {},
            function (data) {
                var i_name = $('#input_name').val();
                var i_pass = $('#input_password').val();
                $('#input_password').val('');

                $.post(
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
                            content_update();
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

// -------- view --------

var view_hide = function (action) {
    $('#content').stop().animate({
        opacity: 0,
    }, 200, function () {
        $('#content').css('display', 'none');
        action();
    });
}

var view_switch = function (name) {
    $('.view').css('display', 'none');
    $('#view_' + name).css('display', 'block');
    $('#content').css('display', 'block');

    $('#content').stop().animate({
        opacity: 1,
    }, 200);
}

var view_isotope_init = function () {
    $('#view_isotope').isotope({
        layoutMode: 'masonry',
        itemSelector: '.isotope, .isotope_big',
        transitionDuration: '0.2s',
    });
}

var view_isotope_reset = function () {
    $('#view_isotope').isotope('destroy');
    $('#view_isotope').empty();

    view_isotope_init();
};

var view_isotope = function (data) {
    for (var i in data) {
        $('#view_isotope').isotope('insert',
            $('<div />')
                .addClass(data[i]['big'] ? 'isotope_big' : 'isotope')
                .append(
                    $('<a />')
                        .addClass('isotope_inner')
                        .attr('href', data[i]['href'])
                        .click(data[i]['click'])
                        .append(
                            $('<div />')
                                .addClass('isotope_title')
                                .html(markdown.toHTML(data[i]['title']))
                        )
                        .append(
                            $('<div />')
                                .addClass('isotope_text')
                                .html(markdown.toHTML(data[i]['text']))
                        )
                )
                .appendTo('#view_isotope')
        )
    }
};

// -------- content --------

var content_update = function () {
    if (!window.location.hash) {
        window.location.hash = '#!home';
    }

    view_hide(function () {
        switch (window.location.hash) {
            case '#!home':
                view_isotope_reset();
                view_switch('isotope');

                $.get(
                    'ajax/cat_cat.php',
                    {
                        cat_id: 0,
                        begin: 0,
                    },
                    function (data) {
                        var idata = [];

                        for (var i in data['data']) {
                            var cat_info = data['data'][i];

                            idata.push({
                                big: false,
                                href: '#!cat-' + cat_info['cat_id'],
                                click: function () {},
                                title: cat_info['name'],
                                text: cat_info['detail'],
                            });
                        }

                        view_isotope(idata);
                    },
                    'json'
                );

                break;
            case '#!explore':
                view_switch('isotope');

                break;
            case '#!stores':
                view_switch('isotope');

                break;
            case '#!orders':
                // view_switch('isotope');

                break;
            case '#!my':
                // view_switch('isotope');

                break;
            default:
                var arg = window.location.hash.split('-');

                switch (arg[0]) {
                    case '#!user':
                        // view_switch('isotope');

                        break;
                    case '#!cat':
                        view_isotope_reset();
                        view_switch('isotope');

                        // TODO: load more than 20 catalogs & books

                        $.get(
                            'ajax/cat_cat.php',
                            {
                                cat_id: parseInt(arg[1]),
                                begin: 0,
                            },
                            function (data) {
                                var idata = [];

                                for (var i in data['data']) {
                                    var cat_info = data['data'][i];

                                    idata.push({
                                        big: false,
                                        href: '#!cat-' + cat_info['cat_id'],
                                        click: function () {},
                                        title: cat_info['name'],
                                        text: cat_info['detail'],
                                    });
                                }

                                view_isotope(idata);
                            },
                            'json'
                        );

                        $.get(
                            'ajax/cat_book.php',
                            {
                                book_id: parseInt(arg[1]),
                                begin: 0,
                            },
                            function (data) {
                                var idata = [];

                                for (var i in data['data']) {
                                    var book_info = data['data'][i];

                                    idata.push({
                                        big: true,
                                        href: '#!book-' + book_info['book_id'],
                                        click: function () {},
                                        title: book_info['name'],
                                        text: book_info['detail'] + '\n\n**价格：**'
                                            + book_info['price'] + '\n\n**已售：**'
                                            + book_info['sold'] + '\n\n**库存：**'
                                            + book_info['inventory'],
                                    });
                                }

                                view_isotope(idata);
                            },
                            'json'
                        );

                        break;
                    case '#!book':
                        // view_switch('isotope');

                        break;
                    default:
                        // TODO: bad hashbang

                        break;
                }
                break;
        }
    });
};

$(window).on('hashchange', content_update);

// -------- page init --------

$(function () {
    view_isotope_init();

    $.get(
        'ajax/auth_get.php',
        {},
        function (data) {
            if (data['auth_user_id']) {
                login_user_id = data['auth_user_id'];
                login_name = data['auth_name'];

                login_update();
                content_update();
            } else {
                login_update();
                content_update();
            }
        },
        'json'
    );
});
