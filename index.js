'use strict';

// -------- login --------

var login_page_hook = false;
var login_user_id = undefined;
var login_name = undefined;

// calculate 1-layer hashed password
var crypt_password = function (name, password_raw) {
    var SHA = new jsSHA(name + ':' + password_raw, 'TEXT');
    return SHA.getHash('SHA-256', 'HEX');
};

// calculate 2-layer (basic + salted) hashed password
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

    if (login_page_hook) {
        content_update('#!home');
    }
};

// -------- ajax --------

var ajax_list = [];
var ajax_killing = false;

$(document).ajaxSend(function (event, xhr) {
    ajax_list.push(xhr);
});

$(document).ajaxStop(function () {
    ajax_list = [];
});

$(document).ajaxError(function (event, xhr) {
    if (!ajax_killing) {
        if (xhr.status === 403) {
            tag_error('提交被拒绝');
        } else {
            tag_error('通信错误');
        }
    }
});

var ajax_kill = function () {
    ajax_killing = true;
    for (var i in ajax_list) {
        ajax_list[i].abort();
    }
    ajax_killing = false;
};

var ajax_auto_login = function () {
    $.post(
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
};

var ajax_login = function (i_name, i_pass) {
    $.post(
        'ajax/auth_gen.php',
        {},
        function (data) {
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
                            tag_error('密码错误');
                        } else {
                            tag_error('用户不存在');
                        }
                    }
                },
                'json'
            );
        },
        'json'
    );
};

var ajax_logout = function () {
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
                tag_error('内部错误');
            }
        },
        'json'
    );
};

var ajax_auth_reg = function (args) {
    $.post(
        'ajax/auth_reg.php',
        args,
        function (data) {
            if (data['auth_success']) {
                login_user_id = data['auth_user_id'];
                login_name = data['auth_name'];

                login_update();
            } else {
                tag_error('注册失败');
            }
        },
        'json'
    );
};

var ajax_auth_edit = function (args) {
    $.post(
        'ajax/auth_gen.php',
        {},
        function (data) {
            args['login_password'] = crypt_salt(
                args['login_password'],
                data['auth_salt']
            );

            $.post(
                'ajax/auth_edit.php',
                args,
                function (data) {
                    if (data['auth_success']) {
                        login_user_id = data['auth_user_id'];
                        login_name = data['auth_name'];

                        login_update();
                    } else {
                        if (data['auth_user_id']) {
                            tag_error('修改失败');
                        } else if (data['auth_name']) {
                            tag_error('密码错误');
                        } else {
                            tag_error('用户未登录');
                        }
                    }
                },
                'json'
            );
        },
        'json'
    );
};

var ajax_self_info = function () {
    $.post(
        'ajax/user_self.php',
        {},
        function (data) {
            intro_show(
                data['image'],
                data['name'],
                data['detail']
                    + '\n\n**用户ID：**' + data['user_id']
                    + '\n\n**邮箱：**' + data['mail']
                    + '\n\n**身份：**' + (data['is_admin'] === '1' ? '管理员' : '用户')
                    + '\n\n**地区：**' + data['location']
                    + '\n\n**地址：**' + data['address']
                    + '\n\n**已购买：**' + data['bought_count'] + '本'
                    + '\n\n**在售图书：**' + data['book_count'] + '种'
                    + '\n\n**已销售：**' + data['sold_count'] + '本'
                    + '\n\n**注册日期：**' + data['date_create']
                    + '\n\n**上次登录：**' + data['date_login'],
                [{
                    href: '#!edituser',
                    click: function () {},
                    text: '编辑信息',
                },]
            );
        },
        'json'
    );
};

var ajax_self_info_async = function (handler) {
    $.post(
        'ajax/user_self.php',
        {},
        function (data) {
            data['login_name'] = data['name'];
            handler(data);
        },
        'json'
    );
};

var ajax_user_info = function (id) {
    $.post(
        'ajax/user_any.php',
        {
            user_id: id,
        },
        function (data) {
            if (data['get_success']) {
                page_switch(data['name']);
                intro_show(
                    data['image'],
                    data['name'],
                    data['detail']
                        + '\n\n**用户ID：**' + data['user_id']
                        + '\n\n**地区：**' + data['location']
                        + '\n\n**在售图书：**' + data['book_count'] + '种'
                        + '\n\n**已销售：**' + data['sold_count'] + '本',
                    []
                );
            } else {
                tag_error('此用户不存在');
            }
        },
        'json'
    );
};

var ajax_list_user = function (title, mode) {
    $.post(
        'ajax/list_user.php',
        {
            mode: mode,
            begin: 0,
        },
        function (data) {
            var idata = [];

            for (var i in data['data']) {
                var user_info = data['data'][i];

                idata.push({
                    href: '#!user-' + user_info['user_id'],
                    click: function () {},
                    textl: user_info['name'],
                    textr: {
                        'new': '',
                        book: user_info['book_count'] + '种',
                        sold: user_info['sold_count'] + '本',
                    }[mode],
                    textmore: '', // TODO: add this feature (detailed info)
                });
            }

            view_lists_insert(title, idata);
        },
        'json'
    );
};

var ajax_list_book = function (title, mode) {
    $.post(
        'ajax/list_book.php',
        {
            mode: mode,
            begin: 0,
        },
        function (data) {
            var idata = [];

            for (var i in data['data']) {
                var book_info = data['data'][i];

                idata.push({
                    href: '#!book-' + book_info['book_id'],
                    click: function () {},
                    textl: book_info['name'],
                    textr: {
                        'new': '',
                        sold: book_info['sold_count'] + '本',
                        newsold: book_info['sold_count'] + '本',
                    }[mode],
                    textmore: '', // TODO: add this feature (detailed info)
                });
            }

            view_lists_insert(title, idata);
        },
        'json'
    );
};

var ajax_user_book = function (id) {
    $.post(
        'ajax/user_book.php',
        {
            user_id: id,
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
                    text: book_info['detail']
                        + '\n\n价格 ' + book_info['price']
                        + '\n\n库存 ' + book_info['inventory'] + '本'
                        + '\n\n已销售 ' + book_info['sold_count'] + '本',
                });
            }

            view_isotope_insert(idata);
        },
        'json'
    );
};

var ajax_cat_info = function (id) {
    $.post(
        'ajax/cat.php',
        {
            cat_id: id,
        },
        function (data) {
            if (data['get_success']) {
                page_switch(data['name']);
                intro_show(
                    data['image'],
                    data['name'],
                    data['detail']
                        // cat_id
                        + '\n\n**子目录：**' + data['cat_count'] + '个'
                        + '\n\n**在售图书：**' + data['tot_book_count'] + '种'
                        + '\n\n**当前目录：**' + data['book_count'] + '种',
                    [{
                        href: '#!cat-' + data['parent_cat_id'],
                        click: function () {},
                        text: '返回上层',
                    },
                    {
                        href: '#!addbook-' + id,
                        click: function () {},
                        text: '我要卖书',
                    },]
                );
            } else {
                tag_error('此目录不存在');
            }
        },
        'json'
    );
};

var ajax_book_info = function (id) {
    $.post(
        'ajax/book.php',
        {
            book_id: id,
        },
        function (data) {
            if (data['get_success']) {
                page_switch(data['name']);
                intro_show(
                    data['image'],
                    data['name'],
                    data['detail']
                        // book_id
                        + '\n\n**价格：**' + data['price']
                        + '\n\n**库存：**' + data['inventory'] + '本'
                        + '\n\n**已销售：**' + data['sold_count'] + '本'
                        + '\n\n**上架日期：**' + data['date_create'],
                    [{
                        href: '#!cat-' + data['parent_cat_id'],
                        click: function () {},
                        text: '返回目录',
                    },
                    {
                        href: '#!user-' + data['owner_user_id'],
                        click: function () {},
                        text: '查看卖家',
                    },
                    {
                        href: undefined, // href: window.location.hash,
                        click: function () {
                            cart_add(id, '#!book-' + id, data['name'], data['price']);
                        },
                        text: '加入购物车',
                    },]
                );
            } else {
                tag_error('此书不存在');
            }
        },
        'json'
    );
};

var ajax_cat_cat = function (id) {
    $.post(
        'ajax/cat_cat.php',
        {
            cat_id: id,
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
                    text: cat_info['detail']
                        + '\n\n在售 ' + cat_info['tot_book_count'] + '种',
                });
            }

            view_isotope_insert(idata);
        },
        'json'
    );
};

var ajax_cat_book = function (id) {
    $.post(
        'ajax/cat_book.php',
        {
            cat_id: id,
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
                    text: book_info['detail']
                        + '\n\n价格 ' + book_info['price']
                        + '\n\n库存 ' + book_info['inventory'] + '本'
                        + '\n\n已销售 ' + book_info['sold_count'] + '本',
                });
            }

            view_isotope_insert(idata);
        },
        'json'
    );
};

var ajax_book_feedback = function (id) {
    $.post(
        'ajax/book_feedback.php',
        {
            book_id: id,
            begin: 0,
        },
        function (data) {
            var idata = [];

            for (var i in data['data']) {
                var buy_info = data['data'][i];

                idata.push({
                    big: buy_info['feedback'].length >= 24,
                    href: undefined,
                    click: function () {},
                    title: '',
                    text: buy_info['feedback']
                        + '\n\n日期 ' + buy_info['date_done'],
                });
            }

            view_isotope_insert(idata);
        },
        'json'
    );
};

var ajax_list_order = function (title, mode) {
    $.post(
        'ajax/user_buy_sell.php',
        {
            mode: mode,
            begin: 0,
        },
        function (data) {
            var idata = [];

            for (var i in data['data']) {
                var buy_info = data['data'][i];

                // TODO:
                // 'buy_id'
                // 'seller_user_id'
                // 'address'

                idata.push({
                    href: '#!book-' + buy_info['buy_book_id'],
                    click: function () {},
                    textl: buy_info['book_name'],
                    textr: [
                        '未确认',
                        '已确认',
                        '已完成',
                    ][buy_info['bool_accept'] + buy_info['bool_done']],
                    textmore: '', // TODO: add this feature (detailed info)
                });
            }

            view_lists_insert(title, idata);
        },
        'json'
    );
};

// -------- header --------

// add event handlers
$(function () {
    $('#btn_logout').click(function () {
        ajax_logout();
    });

    $('#input_name').keypress(function (e) {
        if (e.which === 13) {
            $('#input_password').focus();
        }
    });

    $('#input_password').keypress(function (e) {
        if (e.which === 13) {
            $('#btn_login').click();
        }
    });

    $('#btn_login').click(function () {
        var i_name = $('#input_name').val();
        var i_pass = $('#input_password').val();
        $('#input_password').val('');

        ajax_login(i_name, i_pass);
    });
});

// -------- navigation & page --------

var tag_now = -1;
var tag_list = [];

var navigation_update = function () {
    $('#navigation_tail').removeAttr('id');

    var last = $('#navigation a:last-child');
    if (!last.attr('id')) {
        last.attr('id', 'navigation_tail');
    }

    while (tag_list.length > 6) {
        tag_list.shift().remove();
        tag_now -= 1;
    }
};

var tag_page = function (title) {
    // find exist tag
    var tag_old = undefined;

    // remove errors
    $('.button_nav_error').click();

    for (var i in tag_list) {
        if (tag_list[i].attr('href') === window.location.hash) {
            // the same tag exists
            tag_old = tag_list[i];
            tag_list.splice(i, 1);

            break;
        }
    }

    // create a tag
    var tag = $('<a />')
        .addClass('button_nav_current')
        .attr('href', window.location.hash)
        .text(title);

    if (tag_old) {
        tag_old.after(tag);
        tag_old.remove();
    } else if (tag_now >= 0) {
        tag_list[tag_now].after(tag);
    } else {
        $('#navigation').append(tag);
    }

    // update the tag list
    tag_list.push(tag);
    tag_now = tag_list.length - 1;

    navigation_update();
};

var tag_error = function (message) {
    // create a tag
    var tag = $('<a />')
        .addClass('button_nav_error')
        .click(function () {
            for (var i in tag_list) {
                if (tag_list[i] === tag) {
                    // remove self if clicked
                    tag.remove();
                    tag_list.splice(i, 1);

                    if (tag_now >= i) {
                        tag_now -= 1;
                    }

                    break;
                }
            }

            navigation_update();
        })
        .text(message);

    if (tag_now >= 0) {
        tag_list[tag_now].after(tag);
    } else {
        $('#navigation').append(tag);
    }

    // update the tag list
    tag_list.push(tag);
    tag_now = tag_list.length - 1;

    navigation_update();
};

var page_prepare = function () {
    document.title = 'Yet Another Bookstore';

    $('.button_nav_current')
        .removeClass('button_nav_current')
        .addClass('button_nav');
};

var page_switch = function (title, hook, notag) {
    login_page_hook = (hook ? true : false);
    if (title) {
        document.title = title + ' - Yet Another Bookstore';
        if (!notag) {
            tag_page(title);
        }
    } else {
        document.title = 'Yet Another Bookstore';
        if (!notag) {
            tag_page('主页');
        }
    }
};

// -------- cart --------

var cart_show = function () {
    // $('#cart_list').stop().slideDown(200);
    $('#cart_list').css('display', 'block');
    $('#cart_buy').css('display', 'block');
    $('#cart_title').click(cart_hide);
};

var cart_hide = function () {
    // $('#cart_list').stop().slideUp(200);
    $('#cart_list').css('display', 'none');
    $('#cart_buy').css('display', 'none');
    $('#cart_title').click(cart_show);
};

var cart_update = function () {
    var cart = cart_get();

    $('#cart_title')
        .text('购物车 (' + cart.length + ')');

    $('#cart_list').empty();

    if (cart.length > 0) {
        for (var i in cart) {
            $('<tr />')
                .append(
                    $('<td />')
                        .addClass('button_cart')
                        .append(
                            $('<a />')
                                .addClass('red')
                                .click(function () {
                                    cart_remove(i);
                                })
                                .text('❌')
                        )
                )
                .append(
                    $('<td />')
                        .addClass('button_cart')
                        .append(
                            $('<a />')
                                .attr('href', cart[i]['href'])
                                .text(cart[i]['textl'])
                        )
                )
                .append(
                    $('<td />')
                        .addClass('button_cart')
                        .append(
                            $('<a />')
                                .attr('href', cart[i]['href'])
                                .text(cart[i]['textr'])
                        )
                )
                .appendTo('#cart_list');
        }

        cart_show();
    } else {
        $('<tr />')
                .append(
                    $('<td />')
                        .addClass('button_cart')
                        .text('（空）')
                )
                .appendTo('#cart_list');

        cart_hide();
    }
};

var cart_init = function () {
    if (!window['localStorage']) {
        window['localStorage'] = {};
    }

    if (!localStorage['bookstore_cart']) {
        localStorage['bookstore_cart'] = JSON.stringify([]);
    }

    cart_update();
};

var cart_get = function () {
    return JSON.parse(localStorage['bookstore_cart']);
};

var cart_set = function (cart) {
    localStorage['bookstore_cart'] = JSON.stringify(cart);
    cart_update();
};

var cart_add = function (id, href, textl, textr) {
    var cart = cart_get();

    for (var i in cart) {
        if (cart[i]['id'] === id) {
            return;
        }
    }

    cart.push({
        id: id,
        href: href,
        textl: textl,
        textr: textr,
    });

    cart_set(cart);
};

var cart_remove = function (index) {
    var cart = cart_get();

    cart.splice(index, 1);

    cart_set(cart);
};

var cart_clear = function () {
    cart_set([]);
};

// -------- intro --------

var intro_hide = function () {
    $('#intro').css('display', 'none');
};

var intro_show = function (image, title, text, buttons) {
    $('#intro_image').css('display', 'none');

    if (image) {
        $.get(
            'upload/' + image,
            {},
            function (data) {
                $('#intro_image').attr('src', data);
                $('#intro_image').css('display', 'block');
            },
            'text'
        );
    }

    $('#intro_title').text(title);
    $('#intro_text').html(markdown.toHTML(text));

    $('#intro_tool').empty();
    for (var i in buttons) {
        $('<a />')
            .addClass('button_float')
            .attr('href', buttons[i]['href'])
            .click(buttons[i]['click'])
            .text(buttons[i]['text'])
            .appendTo('#intro_tool');
    }

    $('#intro').css('display', 'block');
};

// -------- view --------

var view_hide = function (action) {
    $('#content').stop().animate({
        opacity: 0,
    }, 200, function () {
        $('#content').css('display', 'none');
        $('#content').scrollTop(0);
        action();
    });
};

var view_switch = function (name) {
    $('.view').css('display', 'none');

    $('#view_' + name).css('display', 'block');
    $('#content').css('display', 'block');

    $('#content').stop().animate({
        opacity: 1,
    }, 200);
};

var view_isotope_init = function () {
    $('#view_isotope').isotope({
        layoutMode: 'masonry',
        itemSelector: '.isotope, .isotope_big',
        transitionDuration: '0.2s',
    });
};

var view_isotope_reset = function () {
    $('#view_isotope').isotope('destroy');
    $('#view_isotope').empty();

    view_isotope_init();
};

var view_isotope_insert = function (data) {
    for (var i in data) {
        $('#view_isotope').isotope('insert',
            $('<div />')
                .addClass(data[i]['big'] ? 'isotope_big' : 'isotope')
                .append(
                    $('<a />')
                        .addClass('isotope_inner')
                        .addClass('frame_body')
                        .attr('href', data[i]['href'])
                        .click(data[i]['click'])
                        .append(
                            $('<div />')
                                .addClass('title')
                                .text(data[i]['title'])
                        )
                        .append(
                            $('<div />')
                                .addClass('normal')
                                .html(markdown.toHTML(data[i]['text']))
                        )
                )
                .appendTo('#view_isotope')
        );
    }
};

var view_lists_reset = function () {
    $('#view_lists').empty();
};

var view_lists_insert = function (title, data) {
    if (data.length === 0) {
        return;
    }

    var list = $('<table />')
        .addClass('lists_list')
        .addClass('frame_body')
        .append(
            $('<div />')
                .addClass('lists_title')
                .addClass('title')
                .text(title)
        )
        .appendTo('#view_lists');

    for (var i in data) {
        $('<tr />')
            .append(
                $('<td />')
                    .addClass('lists_item_l')
                    .append(
                        $('<a />')
                            .attr('href', data[i]['href'])
                            .click(data[i]['click'])
                            .text(data[i]['textl'])
                    )
            )
            .append(
                $('<td />')
                    .addClass('lists_item_r')
                    .text(data[i]['textr'])
            )
            .appendTo(list);
    }
};

// click event handler of view_submit
var submit_func = undefined;

var view_submit_init = function () {
    $('#submit_button').click(function () {
        submit_func();
    });
};

var view_submit = function (rows, values, handler) {
    $('#submit_table').empty();

    for (var i in rows) {
        var idname = (rows[i]['key'] ? rows[i]['key'] : i);

        var checker = function (i, idname) {
            if (rows[i]['checker']) {
                return function () {
                    var target = $('#submit_input_' + idname);

                    $('#submit_' + i)
                        .find('.submit_hint')
                        .empty()
                        .append(
                            rows[i]['checker'](target.val(), i)
                        );
                };
            } else {
                return function () {};
            }
        } (i, idname);

        var name = $('<p />')
            .addClass('submit_name')
            .html(rows[i]['name']);

        var input = undefined;
        if (rows[i]['type'] === 'textarea') {
            input = $('<textarea />');
        } else if (rows[i]['type'] === 'readonly') {
            input = $('<input />')
                .attr('type', 'text')
                .attr('readonly', 'readonly');
        } else {
            input = $('<input />')
                .attr('type', rows[i]['type']);
        }

        input
            .addClass('submit_input')
            .attr('id', 'submit_input_' + idname)
            .keypress(function () {
                var target = $('#submit_' + (parseInt(i) + 1) + ' .submit_input');

                if (target) {
                    target.focus();
                }
            })
            .change(checker);

        if (values[idname]) {
            input.val(values[idname]);
        }

        var hint = $('<p />')
            .addClass('submit_hint');

        $('<tr />')
            .attr('id', 'submit_' + i)
            .append($('<td />').append(name))
            .append($('<td />').append(input))
            .append($('<td />').append(hint))
            .appendTo('#submit_table');
    }

    // set the handler
    submit_func = function () {
        // check error again
        $('#submit_table .submit_input').change();

        if ($('#submit_table .red').length > 0) {
            tag_error('无法提交');
            return;
        }

        var args = {};

        for (var i in rows) {
            if (rows[i]['key']) {
                var target = $('#submit_input_' + rows[i]['key']);
                var text = target.val();

                if (rows[i]['generator']) {
                    args[rows[i]['key']] = rows[i]['generator'](text, i);
                } else {
                    args[rows[i]['key']] = text;
                }
            }
        }

        handler(args);
    }
};

var view_submit_async = function (rows, source, handler) {
    source(function (values) {
        view_submit(rows, values, handler);
    });
};

// -------- content --------

// parse location.hash and load a page
var content_update = function (go) {
    if (go) {
        window.location.hash = go;
    }

    if (
        !window.location.hash
        || window.location.hash === '#!cat-0'
    ) {
        window.location.hash = '#!home';
        return;
    }

    // prepare
    ajax_kill();

    // load page
    view_hide(function () {
        // reset title
        page_prepare();
        intro_hide();

        var args = window.location.hash.split('-');
        switch (args[0]) {
            case '#!home': // view: isotope, args: n/a
                view_isotope_reset();
                view_switch('isotope');

                page_switch();

                // TODO: load more than 50 items
                ajax_cat_cat(0);

                break;
            case '#!explore': // view: lists, args: n/a
                view_lists_reset();
                view_switch('lists');

                page_switch('发现');

                ajax_list_book('最新上架', 'new');
                ajax_list_book('畅销图书', 'sold');
                ajax_list_book('热门新书', 'newsold');

                break;
            case '#!users': // view: lists, args: n/a
                view_lists_reset();
                view_switch('lists');

                page_switch('用户');

                ajax_list_user('新注册用户', 'new');
                ajax_list_user('最多在售', 'book');
                ajax_list_user('畅销卖家', 'sold');

                break;
            case '#!orders': // view: lists, args: n/a
                view_lists_reset();
                view_switch('lists');

                page_switch('订单', true);

                ajax_list_order('全部购买', 'b_all');
                ajax_list_order('未完成购买', 'b_ca');
                ajax_list_order('全部销售', 's_all');
                ajax_list_order('未确认销售', 's_c');
                ajax_list_order('未完成销售', 's_a');

                break;
            case '#!my': // view: isotope, args: n/a
                view_isotope_reset();
                view_switch('isotope');

                page_switch('我的', true);

                ajax_self_info();

                // TODO: load more than 50 books
                ajax_user_book(login_user_id);

                break;
            case '#!user': // view: isotope, args: user_id
                view_isotope_reset();
                view_switch('isotope');

                var user_id = parseInt(args[1]);

                ajax_user_info(user_id);

                // TODO: load more than 50 books
                ajax_user_book(user_id);

                break;
            case '#!cat': // view: isotope, args: cat_id
                view_isotope_reset();
                view_switch('isotope');

                var cat_id = parseInt(args[1]);

                ajax_cat_info(cat_id);

                // TODO: load more than 50 catalogs & books
                ajax_cat_cat(cat_id);
                ajax_cat_book(cat_id);

                break;
            case '#!book': // view: isotope, args: book_id
                view_isotope_reset();
                view_switch('isotope');

                var book_id = parseInt(args[1]);

                ajax_book_info(book_id);

                ajax_book_feedback(book_id);

                break;
            case '#!register': // view: submit, args: n/a
                page_switch('注册', true, true);

                view_submit(
                    [{
                        key: 'mail',
                        name: '邮箱 *',
                        type: 'text',
                        checker: function (value, i) {
                            if (value.match(/^[^ ;@]+@[^ ;@]+\.[^ ;@]*$/ig)) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('邮箱格式错误！');
                            }
                        },
                    },
                    {
                        key: 'name',
                        name: '用户名 *',
                        type: 'text',
                        checker: function (value, i) {
                            if (value.length !== 0) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('用户名不能为空！');
                            }
                        },
                    },
                    {
                        key: 'password',
                        name: '密码 *',
                        type: 'password',
                        checker: function (value, i) {
                            var target = $('#submit_' + (parseInt(i) + 1) + ' .submit_input');

                            if (value !== target.val()) {
                                target.val('');
                            }

                            if (value.length >= 6) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('密码过短！');
                            }
                        },
                        generator: function (value, i) {
                            var name = $('#submit_input_name');

                            return crypt_password(
                                name.val(),
                                value
                            );
                        },
                    },
                    {
                        key: '',
                        name: '确认密码 *',
                        type: 'password',
                        checker: function (value, i) {
                            var target = $('#submit_' + (parseInt(i) - 1) + ' .submit_input');
                            var value1 = target.val();

                            if (value1 === value) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('密码不一致！');
                            }
                        },
                    },
                    {
                        key: 'location',
                        name: '所在地区 *',
                        type: 'text',
                        checker: function (value, i) {
                            if (value.length !== 0) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('所在地区不能为空！');
                            }
                        },
                    },
                    {
                        key: 'address',
                        name: '收货地址',
                        type: 'textarea',
                    },
                    {
                        key: 'detail',
                        name: '自我介绍',
                        type: 'textarea',
                    },],
                    {
                        // no default value
                    },
                    ajax_auth_reg
                );
                view_switch('submit');

                break;
            case '#!edituser': // view: submit, args: n/a
                page_switch('修改个人信息', true, true);

                view_submit_async(
                    [{
                        key: 'login_name',
                        name: '原用户名 *',
                        type: 'readonly',
                    },
                    {
                        key: 'login_password',
                        name: '原密码 *',
                        type: 'password',
                        checker: function (value, i) {
                            if (value.length !== 0) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('原密码不能为空！');
                            }
                        },
                        generator: function (value, i) {
                            var name = $('#submit_input_login_name');

                            return crypt_password(
                                name.val(),
                                value
                            );
                        },
                    },
                    {
                        key: 'mail',
                        name: '邮箱 *',
                        type: 'text',
                        checker: function (value, i) {
                            if (value.match(/^[^ ;@]+@[^ ;@]+\.[^ ;@]*$/ig)) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('邮箱格式错误！');
                            }
                        },
                    },
                    {
                        key: 'name',
                        name: '用户名 *',
                        type: 'text',
                        checker: function (value, i) {
                            if (value.length !== 0) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('用户名不能为空！');
                            }
                        },
                    },
                    {
                        key: 'password',
                        name: '密码 *',
                        type: 'password',
                        checker: function (value, i) {
                            var target = $('#submit_' + (parseInt(i) + 1) + ' .submit_input');

                            // special case
                            if ($('#submit_input_login_password').val().length === 0) {
                                return '';
                            }

                            if (value.length === 0) {
                                value = $('#submit_input_login_password').val();

                                $('#submit_input_password').val(value);
                                target.val(value);
                            }

                            if (value !== target.val()) {
                                target.val('');
                            }

                            if (value.length >= 6) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('密码过短！');
                            }
                        },
                        generator: function (value, i) {
                            var name = $('#submit_input_name');

                            return crypt_password(
                                name.val(),
                                value
                            );
                        },
                    },
                    {
                        key: '',
                        name: '确认密码 *',
                        type: 'password',
                        checker: function (value, i) {
                            var target = $('#submit_' + (parseInt(i) - 1) + ' .submit_input');
                            var value1 = target.val();

                            if (value1 === value) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('密码不一致！');
                            }
                        },
                    },
                    {
                        key: 'location',
                        name: '所在地区 *',
                        type: 'text',
                        checker: function (value, i) {
                            if (value.length !== 0) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('所在地区不能为空！');
                            }
                        },
                    },
                    {
                        key: 'address',
                        name: '收货地址',
                        type: 'textarea',
                    },
                    {
                        key: 'detail',
                        name: '自我介绍',
                        type: 'textarea',
                    },],
                    ajax_self_info_async,
                    ajax_auth_edit
                );
                view_switch('submit');

                break;
            case '#!addbook': // view: submit(TODO), args: parent_cat_id
                // TODO

                break;
            case '#!addbuy': // view: submit(TODO), args: n/a
                // TODO

                break;
            default:
                // TODO: use a view?
                tag_error('此页不存在');

                break;
        }
    });
};

// -------- page init --------

$(function () {
    // bg image
    $.backstretch(['res/bg.jpg']);

    // content update
    $(window).on('hashchange', function () {
        content_update();
    });
    $('#navigation_head').click(function () {
        content_update(window.location.hash);
    });

    // hide objects
    $('.login_need').css('display', 'none');
    $('.login_ok').css('display', 'none');

    // init
    cart_init();
    view_isotope_init();
    view_submit_init();

    // ajax
    ajax_auto_login();
});
