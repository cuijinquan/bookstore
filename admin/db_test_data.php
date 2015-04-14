<?php
    require_once '../util/db_user.php';
    require_once '../util/db_cat.php';
    require_once '../util/db_book.php';
    require_once '../util/db_buy.php';

    // truncate all tables in the database
    // then add test data

    db_buy_drop();
    db_book_drop();
    db_user_drop();
    db_cat_drop();

    db_user_init();
    db_cat_init();
    db_book_init();
    db_buy_init();

    // add users

    db_user_add_admin(
        'hczhcz@example.com', 'hczhcz', null, 'Hi! I am HCZ.',
        crypt_password('hczhcz', '123456'),
        'Shanghai', 'Room 101, No 1, Some Rd'
    );

    db_user_add(
        'zacks@example.net', 'zacks', null, 'Hi! I am Zacks.',
        crypt_password('zacks', '123456'),
        'New York', 'Room 233, No 2, Another Rd'
    );

    db_user_add(
        'yexiao@example.org', '夜宵', null, '你好，我是夜宵！',
        crypt_password('yexiao', '12345678'),
        '闵大荒', '东川路水上乐园'
    );

    db_user_add(
        'lzsd@example.info', '栗子书店', null, '你好，我们只是举个栗子！',
        crypt_password('lzsd', '1234567890'),
        '五角场', '李达三楼 四楼'
    );

    // add catalogs

    db_cat_add(
        null,
        '科学技术', '13dd167159a652a278fe0860a0abf468', '这是科学技术类图书。'
    );

    db_cat_add(
        null,
        '休闲娱乐', '1155b9cf0f24f8d2c2da854e62a4bbef', '这是休闲娱乐类图书。'
    );

    db_cat_add(
        null,
        '医疗健康', '59d9e827ccb1947002ac8acd50e38e17', '这是医疗健康类图书。'
    );

    db_cat_add(
        null,
        '外文图书', '720b75954179401f4f80bc8a91bc90e7', 'Books in English are listed here.'
    );

    db_cat_add(
        db_cat_get_name('科学技术')['cat_id'],
        '物理', '2f861d02192743a1c1cf2f32adec97b8', '这是科学技术类中的物理类图书。'
    );

    db_cat_add(
        db_cat_get_name('科学技术')['cat_id'],
        '化学', '732e1de1c5a204f76d869059462e8481', '这是科学技术类中的化学类图书。'
    );

    db_cat_add(
        db_cat_get_name('科学技术')['cat_id'],
        '计算机', 'ae2e4dbc9b2a993cd9ee98f0a37e8319', '这是科学技术类中的计算机类图书。'
    );

    // add books

    db_book_add(
        db_user_get_name('hczhcz')['user_id'], db_cat_get_name('物理')['cat_id'],
        '《费曼物理讲义》', 'b8d6c33eba8faac86a6ce07a045885e0', '这是一本书。', 'RMB 100.00', 1
    );

    db_book_add(
        db_user_get_name('hczhcz')['user_id'], db_cat_get_name('计算机')['cat_id'],
        '《PHP是世界上最好的语言》', null, '这本书卖断货了。', 'RMB 1000.00', 0
    );

    db_book_add(
        db_user_get_name('zacks')['user_id'], db_cat_get_name('计算机')['cat_id'],
        '《黑客与画家》', '7ca2b1d775467ae1dee4d3e5c32e1917', '这是由个人售出的一本书。', 'RMB 50.00', 5
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('计算机')['cat_id'],
        '《黑客与画家》', '39d2a2e26c02253612f4a55573c19482', '这是由书店售出的一本书。', 'RMB 49.80', 200
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('计算机')['cat_id'],
        '《编程珠玑》', '8bf06d05f26022537a1ff66a54a98349', '这是一本计算机类图书。', 'RMB 59.80', 300
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('医疗健康')['cat_id'],
        '《颈椎病康复指南》', null, '这是一本医疗健康类图书。', 'RMB 29.80', 1200
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('科学技术')['cat_id'],
        '《井字棋必胜策略》', null, '这是一本免费的科学技术类图书。', 'Free', 100000
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('外文图书')['cat_id'],
        'Database System Concepts', 'f2a70b0b3a0306eb050d566c2a458a6b', 'This is a book.', 'USD 20.00', 100
    );

    // add ordering actions

    // db_buy_add(
    //     $buy_book_id, $buyer_user_id,
    //     $address
    // );

    // TODO: add other actions
?>
