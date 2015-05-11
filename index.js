'use strict';

var DEBUG = function (value) {
    alert(JSON.stringify(value));
};

// -------- login --------

var login_page_hook = false;
// var login_user_id = undefined;
var login_user_id = -1; // hack: force update
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

var login_update = function (id, name) {
    if (id) {
        $('#text_name').text(name);
        login_ok_show();
    } else {
        login_need_show();
    }

    if (login_user_id !== id || login_name !== name) {
        login_user_id = id;
        login_name = name;

        if (login_page_hook) {
            content_update('#!home');
        } else {
            content_update();
        }
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
        if (xhr.status === 400) {
            tag_error('提交被拒绝');
        } else if (xhr.status === 401) {
            tag_error('登录失效');
            // ajax_auto_login();
        } else if (xhr.status === 500) {
            tag_error('服务器错误');
        } else {
            tag_error('通信错误');
        }
    }
});

var ajax_kill = function () {
    if (ajax_killing) {
        return;
    }

    ajax_killing = true;
    for (var i in ajax_list) {
        ajax_list[i].abort();
    }
    ajax_killing = false;
};

var ajax_image = function (image, handler) {
    if (image) {
        $.get(
            'upload/' + image,
            {},
            function (data) {
                handler(data);
            },
            'text'
        );
    }
};

var ajax_auto_login = function () {
    $.post(
        'ajax/auth_get.php',
        {},
        function (data) {
            if (data['auth_user_id']) {
                login_update(data['auth_user_id'], data['auth_name']);
            } else {
                login_update(undefined, undefined);
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
                        login_update(data['auth_user_id'], data['auth_name']);
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
            login_user_id: login_user_id,
        },
        function (data) {
            login_update(undefined, undefined);
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
                login_update(data['auth_user_id'], data['auth_name']);
                content_update('#!home');
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
            args['login_user_id'] = login_user_id;
            args['login_password'] = crypt_salt(
                args['login_password'],
                data['auth_salt']
            );

            $.post(
                'ajax/auth_edit.php',
                args,
                function (data) {
                    if (data['auth_success']) {
                        login_update(data['auth_user_id'], data['auth_name']);
                        content_update('#!my');
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
        {
            login_user_id: login_user_id,
        },
        function (data) {
            intro_show(
                data['image'],
                data['name'],
                data['detail']
                    + '\n\n**用户ID：**' + data['user_id']
                    + '\n\n**邮箱：**' + data['mail']
                    + '\n\n**身份：**' + (data['is_admin'] === '1' ? '管理员' : '用户')
                    + '\n\n**地区：**' + data['location']
                    + '\n\n**收货地址：**' + data['address']
                    + '\n\n**已购买：**' + data['bought_count'] + '本'
                    + '\n\n**在售图书：**' + data['book_count'] + '种'
                    + '\n\n**已销售：**' + data['sold_count'] + '本'
                    + '\n\n**注册日期：**' + data['date_create']
                    + '\n\n**上次登录：**' + data['date_login'],
                [
                    {
                        href: '#!edituser',
                        click: function () {},
                        text: '编辑信息',
                    },
                ]
            );
        },
        'json'
    );
};

var ajax_self_info_async = function (handler) {
    $.post(
        'ajax/user_self.php',
        {
            login_user_id: login_user_id,
        },
        function (data) {
            data['login_name'] = data['name'];
            handler(data);
        },
        'json'
    );
};

var ajax_user_info = function (id, simple) {
    $.post(
        'ajax/user_any.php',
        {
            user_id: id,
        },
        function (data) {
            if (data['get_success']) {
                if (!simple) {
                    page_switch(data['name']);
                }

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

var ajax_cat_info = function (id, simple) {
    $.post(
        'ajax/cat.php',
        {
            cat_id: id,
        },
        function (data) {
            if (data['get_success']) {
                if (!simple) {
                    page_switch(data['name']);
                }

                intro_show(
                    data['image'],
                    data['name'],
                    data['detail']
                        // cat_id
                        + '\n\n**子目录：**' + data['cat_count'] + '个'
                        + '\n\n**在售图书：**' + data['tot_book_count'] + '种'
                        + '\n\n**当前目录：**' + data['book_count'] + '种',
                    simple ? [] : [
                        login_user_id ? {
                            href: '#!addbook-' + id,
                            click: function () {},
                            text: '我要卖书',
                        } : undefined,
                        {
                            href: '#!cat-' + data['parent_cat_id'],
                            click: function () {},
                            text: '返回上层',
                        },
                    ]
                );
            } else {
                tag_error('此目录不存在');
            }
        },
        'json'
    );
};

var ajax_book_info = function (id, simple) {
    $.post(
        'ajax/book.php',
        {
            book_id: id,
        },
        function (data) {
            if (data['get_success']) {
                if (!simple) {
                    page_switch(data['name']);
                }

                intro_show(
                    data['image'],
                    data['name'],
                    data['detail']
                        // book_id
                        + '\n\n**价格：**' + data['price']
                        + '\n\n**库存：**' + data['inventory'] + '本'
                        + (
                            data['inventory'] <= 5 ?
                            '\n\n**本书库存较少，可能会延迟发货**' : ''
                        )
                        + '\n\n**已销售：**' + data['sold_count'] + '本'
                        + '\n\n**上架日期：**' + data['date_create'],
                    simple ? [] : [
                        (login_user_id === data['owner_user_id']) ? {
                            href: '#!editbook-' + id,
                            click: function () {},
                            text: '编辑信息',
                        } : undefined,
                        {
                            href: window.location.hash,
                            click: function () {
                                cart_add(
                                    id,
                                    '#!book-' + id,
                                    data['name'],
                                    data['price']
                                );
                            },
                            text: '加入购物车',
                        },
                        {
                            href: '#!user-' + data['owner_user_id'],
                            click: function () {},
                            text: '查看卖家',
                        },
                        {
                            href: '#!cat-' + data['parent_cat_id'],
                            click: function () {},
                            text: '返回目录',
                        },
                    ]
                );
            } else {
                tag_error('此书不存在');
            }
        },
        'json'
    );
};

var ajax_book_info_async = function (id, handler) {
    $.post(
        'ajax/book.php',
        {
            book_id: id,
        },
        function (data) {
            handler(data);
        },
        'json'
    );
};

var ajax_book_add = function (args) {
    args['login_user_id'] = login_user_id;
    $.post(
        'ajax/book_add.php',
        args,
        function (data) {
            if (data['set_success']) {
                content_update('#!book-' + data['book_id']);
            } else {
                tag_error('添加失败');
            }
        },
        'json'
    );
};

var ajax_book_edit = function (args) {
    args['login_user_id'] = login_user_id;
    $.post(
        'ajax/book_edit.php',
        args,
        function (data) {
            if (data['set_success']) {
                content_update('#!book-' + data['book_id']);
            } else {
                if (data['book_id']) {
                    tag_error('修改失败');
                } else {
                    tar_error('此书不存在');
                }
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
            login_user_id: login_user_id,
            mode: mode,
            begin: 0,
        },
        function (data) {
            var idata = [];

            for (var i in data['data']) {
                var buy_info = data['data'][i];

                var handler = function (buy_info) {
                    var show = function (book_info) {
                        var submit = false;
                        var extra_button = undefined;

                        if (
                            mode[0] === 's'
                            && !buy_info['bool_accept']
                            // && !buy_info['bool_done']
                        ) {
                            extra_button = {
                                href: '#!orders',
                                click: function () {
                                    ajax_buy_accept(buy_info['buy_id']);
                                },
                                text: '确认订单',
                            };
                        }

                        if (
                            mode[0] === 'b'
                            && buy_info['bool_accept']
                            && !buy_info['bool_done']
                        ) {
                            submit = true;

                            extra_button = {
                                href: '#!orders',
                                click: function () {
                                    ajax_buy_done(
                                        buy_info['buy_id'],
                                        $('#intro_submit_input').val()
                                    );
                                },
                                text: '完成收货',
                            };
                        }

                        intro_show(
                            undefined,
                            book_info['name'],
                            book_info['detail']
                                + '\n\n**价格：**' + book_info['price']
                                + '\n\n**库存：**' + book_info['inventory'] + '本'
                                + (
                                    book_info['inventory'] <= 5 ?
                                    '\n\n**本书库存较少，可能会延迟发货**' : ''
                                )
                                // + '\n\n**已销售：**' + book_info['sold_count'] + '本'
                                // + '\n\n**上架日期：**' + book_info['date_create']
                                + '\n\n**收货地址**：' + buy_info['address'],
                            [
                                extra_button,
                                {
                                    href: '#!book-' + buy_info['buy_book_id'],
                                    click: function () {},
                                    text: '查看图书',
                                },
                                {
                                    href: '#!user-' + buy_info['seller_user_id'],
                                    click: function () {},
                                    text: '查看卖家',
                                },
                            ],
                            submit ? (
                                $('<div />')
                                    .attr('id', 'intro_submit')
                                    .append(
                                        $('<p />')
                                            .attr('id', 'intro_submit_title')
                                            .text('订单评价')
                                    )
                                    .append(
                                        $('<textarea />')
                                            .addClass('input_body')
                                            .attr('id', 'intro_submit_input')
                                    )
                            ) : undefined
                        );
                    };

                    return function () {
                        ajax_book_info_async(buy_info['buy_book_id'], show);
                    };
                } (buy_info);

                idata.push({
                    // href: '#!book-' + buy_info['buy_book_id'],
                    href: undefined,
                    click: handler,
                    textl: buy_info['book_name'],
                    textr: [
                        '未确认',
                        '未完成',
                        '已完成',
                    ][buy_info['bool_accept'] + buy_info['bool_done']],
                });
            }

            view_lists_insert(title, idata);
        },
        'json'
    );
};

var ajax_buy = function (cart, address) {
    if (cart.length > 0) {
        var item = cart.shift();

        $.post(
            'ajax/buy_add.php',
            {
                login_user_id: login_user_id,
                book_id: item['id'],
                address: address,
            },
            function (data) {
                if (data['set_success']) {
                    cart_remove(item['id']);
                    ajax_buy(cart, address);
                } else {
                    tag_error('购买失败');
                }
            },
            'json'
        );
    } else {
        content_update('#!orders');
    }
};

var ajax_buy_accept = function (id) {
    $.post(
        'ajax/buy_accept.php',
        {
            login_user_id: login_user_id,
            buy_id: id,
        },
        function (data) {
            if (data['set_success']) {
                content_update('#!orders');
            } else {
                tag_error('操作失败');
            }
        },
        'json'
    );
};

var ajax_buy_done = function (id, feedback) {
    $.post(
        'ajax/buy_done.php',
        {
            login_user_id: login_user_id,
            buy_id: id,
            feedback: feedback,
        },
        function (data) {
            if (data['set_success']) {
                content_update('#!orders');
            } else {
                tag_error('操作失败');
            }
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

    $('#input_name').keypress(function (event) {
        if (event.which === 13) {
            $('#input_password').focus();
        }
    });

    $('#input_password').keypress(function (event) {
        if (event.which === 13) {
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

var cart_init = function () {
    if (!window['localStorage']) {
        window['localStorage'] = {};
    }

    if (!localStorage['bookstore_cart']) {
        localStorage['bookstore_cart'] = JSON.stringify([]);
    }

    cart_update();
    setInterval(cart_update, 1000); // auto refresh
};

var cart_get = function () {
    return JSON.parse(localStorage['bookstore_cart']);
};

var cart_get_raw = function () {
    return localStorage['bookstore_cart'];
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

var cart_remove = function (id) {
    var cart = cart_get();

    for (var i in cart) {
        if (cart[i]['id'] === id) {
            cart.splice(i, 1);
            break;
        }
    }

    cart_set(cart);
};

var cart_clear = function () {
    cart_set([]);
};

var cart_old_data = undefined;

var cart_update = function () {
    var raw = cart_get_raw();

    if (raw === cart_old_data) {
        return;
    }

    cart_old_data = raw;
    var cart = JSON.parse(raw);

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
                                .click(function (i) {
                                    return function () {
                                        cart_remove(cart[i]['id']);
                                    };
                                } (i))
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

// -------- intro --------

var intro_hide = function () {
    $('#intro').css('display', 'none');
};

var intro_show = function (image, title, text, buttons, extra) {
    $('#intro_image').css('display', 'none');

    ajax_image(
        image,
        function (data) {
            $('#intro_image').attr('src', data);
            $('#intro_image').css('display', 'block');
        }
    );

    $('#intro_title').text(title);
    $('#intro_text').html(markdown.toHTML(text));

    $('#intro_tool').empty();

    if (extra) {
        $('#intro_tool')
            .append(extra);
    }

    for (var i in buttons) {
        if (buttons[i]) {
            $('<a />')
                .addClass('button_float')
                .attr('href', buttons[i]['href'])
                .click(buttons[i]['click'])
                .text(buttons[i]['text'])
                .appendTo('#intro_tool');
        }
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

var view_submit_upload = function (input) {
    var refresh = function (value) {
        input.val(value);
        upload.remove();
        view_submit_upload(input);
    };

    var active = false;

    // init components

    var upload_image = $('<img />')
        .css({
            margin: '0 0 8px 0',
        });

    var upload_file = $('<input />')
        .attr('type', 'file')
        .attr('name', 'file')
        .attr('required', 'required');

    var upload_submit = $('<input />')
        .attr('type', 'submit')
        .val('提交')
        .click(function () {
            active = true;
        });

    var upload_clear = $('<input />')
        .attr('type', 'button')
        .val('清除')
        .click(function () {
            refresh('');
        });

    var upload_form = $('<form />')
        .css({
            margin: '0',
        })
        .attr('action', 'ajax/upload_image.php')
        .attr('method', 'post')
        .attr('enctype', 'multipart/form-data')
        .append(upload_file)
        .append(upload_submit)
        .append(upload_clear);

    // build the iframe

    var upload = $('<iframe />')
        .addClass('submit_input')
        .addClass('input_body')
        .on('load', function () {
            var body = upload.contents().find('body');

            if (active) {
                var data = JSON.parse(body.html());

                if (data['upload_success']) {
                    refresh(data['upload_hash']);
                } else {
                    tag_error('上传失败');
                    // refresh(input.val());
                }
            } else {
                body
                    .css({
                        margin: '0',
                        padding: '8px',
                    })
                    .append(upload_image)
                    .append(upload_form);

                upload.height(body.height() + 16);
            }
        });

    input.after(upload);

    // load image

    ajax_image(
        input.val(),
        function (data) {
            var body = upload.contents().find('body');

            upload_image.attr('src', data);
            upload.height(body.height() + 16); // TODO: on iframe change
        }
    );
};

var view_submit = function (rows, values, handler) {
    $('#submit_table').empty();
    $('#submit_button').attr('href', window.location.hash);

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
        } else if (rows[i]['type'] === 'image') {
            input = $('<input />')
                .attr('type', 'hidden');
        } else {
            input = $('<input />')
                .attr('type', rows[i]['type']);
        }

        input
            .addClass('submit_input')
            .addClass('input_body')
            .attr('id', 'submit_input_' + idname)
            // .keypress(function () {}) // change focus on key 13?
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

        if (rows[i]['type'] === 'image') {
            view_submit_upload(input);
        }
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
            var idname = (rows[i]['key'] ? rows[i]['key'] : i);
            var target = $('#submit_input_' + idname);

            if (rows[i]['key']) {
                var text = target.val();

                if (rows[i]['generator']) {
                    args[rows[i]['key']] = rows[i]['generator'](text, i);
                } else {
                    args[rows[i]['key']] = text;
                }
            }

            if (rows[i]['type'] === 'password') {
                target.val('');
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
            case '#!my': // view: isotope, args: n/a
                view_isotope_reset();
                view_switch('isotope');

                page_switch('我的', true);

                ajax_self_info();

                // TODO: load more than 50 books
                ajax_user_book(login_user_id);

                break;
            case '#!user': // view: isotope, args: user_id
                var user_id = parseInt(args[1]);

                view_isotope_reset();
                view_switch('isotope');

                ajax_user_info(user_id);

                // TODO: load more than 50 books
                ajax_user_book(user_id);

                break;
            case '#!cat': // view: isotope, args: cat_id
                var cat_id = parseInt(args[1]);

                view_isotope_reset();
                view_switch('isotope');

                ajax_cat_info(cat_id);

                // TODO: load more than 50 catalogs & books
                ajax_cat_cat(cat_id);
                ajax_cat_book(cat_id);

                break;
            case '#!book': // view: isotope, args: book_id
                var book_id = parseInt(args[1]);

                view_isotope_reset();
                view_switch('isotope');

                ajax_book_info(book_id);

                ajax_book_feedback(book_id);

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
            case '#!register': // view: submit, args: n/a
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
                            var target = $(
                                '#submit_' + (parseInt(i) + 1) + ' .submit_input'
                            );

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
                            var target = $(
                                '#submit_' + (parseInt(i) - 1) + ' .submit_input'
                            );
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
                        key: 'image',
                        name: '头像',
                        type: 'image',
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

                page_switch('注册', true, true);

                break;
            case '#!edituser': // view: submit, args: n/a
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
                            var target = $(
                                '#submit_' + (parseInt(i) + 1) + ' .submit_input'
                            );

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
                            var target = $(
                                '#submit_' + (parseInt(i) - 1) + ' .submit_input'
                            );
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
                        key: 'image',
                        name: '头像',
                        type: 'image',
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

                page_switch('修改个人信息', true, true);

                break;
            case '#!addbook': // view: submit, args: parent_cat_id
                var cat_id = parseInt(args[1]);

                view_submit(
                    [{
                        key: 'name',
                        name: '书名 *',
                        type: 'text',
                        checker: function (value, i) {
                            if (value.length !== 0) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('书名不能为空！');
                            }
                        },
                    },
                    {
                        key: 'price',
                        name: '价格 *',
                        type: 'text',
                        checker: function (value, i) {
                            if (value.length !== 0) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('价格不能为空！');
                            }
                        },
                    },
                    {
                        key: 'inventory',
                        name: '库存 *',
                        type: 'number',
                        checker: function (value, i) {
                            if (value.length !== 0 && !isNaN(parseInt(value))) {
                                if (parseInt(value) < 0) {
                                    $('#submit_input_inventory').val(0);
                                }

                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('库存应为数字！');
                            }
                        },
                        generator: function (value, i) {
                            return parseInt(value);
                        },
                    },
                    {
                        key: 'image',
                        name: '图片',
                        type: 'image',
                    },
                    {
                        key: 'detail',
                        name: '内容介绍',
                        type: 'textarea',
                    },],
                    {
                        inventory: '1',
                    },
                    function (args) {
                        // hack
                        args['cat_id'] = cat_id;
                        ajax_book_add(args);
                    }
                );
                view_switch('submit');

                page_switch('上架新书', true, true);

                ajax_cat_info(cat_id, true);

                break;
            case '#!editbook': // view: submit, args: book_id
                var book_id = parseInt(args[1]);

                view_submit_async(
                    [{
                        key: 'name',
                        name: '书名 *',
                        type: 'readonly',
                    },
                    {
                        key: 'price',
                        name: '价格 *',
                        type: 'text',
                        checker: function (value, i) {
                            if (value.length !== 0) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('价格不能为空！');
                            }
                        },
                    },
                    {
                        key: 'inventory',
                        name: '库存 *',
                        type: 'number',
                        checker: function (value, i) {
                            if (value.length !== 0 && !isNaN(parseInt(value))) {
                                if (parseInt(value) < 0) {
                                    $('#submit_input_inventory').val(0);
                                }

                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('库存应为数字！');
                            }
                        },
                        generator: function (value, i) {
                            return parseInt(value);
                        },
                    },
                    {
                        key: 'image',
                        name: '图片',
                        type: 'image',
                    },
                    {
                        key: 'detail',
                        name: '内容介绍',
                        type: 'textarea',
                    },],
                    function (handler) {
                        ajax_book_info_async(book_id, handler);
                    },
                    function (args) {
                        // hack
                        args['book_id'] = book_id;
                        delete args['name'];
                        ajax_book_edit(args);
                    }
                );
                view_switch('submit');

                page_switch('修改图书信息', true, true);

                break;
            case '#!buy': // view: submit, args: n/a
                var cart = cart_get();

                // cart_hide();

                view_submit_async(
                    [{
                        key: 'name',
                        name: '用户名 *',
                        type: 'readonly',
                    },
                    {
                        key: 'location',
                        name: '所在地区 *',
                        type: 'readonly',
                    },
                    {
                        key: 'address',
                        name: '收货地址 *',
                        type: 'textarea',
                        checker: function (value, i) {
                            if (value.length !== 0) {
                                return '';
                            } else {
                                return $('<p />')
                                    .addClass('red')
                                    .text('地址不能为空！');
                            }
                        },
                    },],
                    ajax_self_info_async,
                    function (args) {
                        ajax_buy(cart, args['address']);
                    }
                );
                view_switch('submit');

                page_switch('下单', true, true);

                var cart_str = '';
                for (var i in cart) {
                    // TODO: markdown tokens filtering?
                    cart_str += (
                        (parseInt(i) + 1) + ' - '
                        + '**' + cart[i]['textl'] + '** '
                        + cart[i]['textr'] + '\n\n'
                    );
                }

                intro_show(undefined, '购物车', cart_str, []);

                break;
            default:
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
        content_update(window.location.hash); // refresh
    });

    // hide objects
    $('.login_need').css('display', 'none');
    $('.login_ok').css('display', 'none');

    // init
    cart_init();
    view_isotope_init();
    view_submit_init();

    // content_update();

    // ajax
    ajax_auto_login();
    setInterval(ajax_auto_login, 60000); // auto refresh
});
