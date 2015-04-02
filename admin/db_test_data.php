<?php
    require_once '../util/db_user.php';
    require_once '../util/db_cat.php';
    require_once '../util/db_book.php';
    require_once '../util/db_buy.php';

    db_user_add_admin(
        'hczhcz@example.com', 'hczhcz', user_pass('hczhcz', '123456'),
        'Shanghai', 'Room 101, No 1, Some Rd', 'Hi! I am HCZ.'
    );

    db_user_add(
        'zacks@example.net', 'zacks', user_pass('zacks', '123456'),
        'New York', 'Room 233, No 2, Another Rd', 'Hi! I am Zacks.'
    );

    db_user_add(
        'yexiao@example.org', '夜宵', user_pass('yexiao', '12345678'),
        '闵大荒', '东川路水上乐园', '你好，我是夜宵！'
    );

    db_user_add(
        'lzsd@example.info', '栗子书店', user_pass('lzsd', '1234567890'),
        '五角场', '李达三楼 四楼', '你好，我们只是举个栗子！'
    );

    db_cat_add(
        null,
        '科学技术', '这是科学技术类图书。'
    );

    db_cat_add(
        null,
        '休闲娱乐', '这是休闲娱乐类图书。'
    );

    db_cat_add(
        null,
        '医疗健康', '这是医疗健康类图书。'
    );

    db_cat_add(
        null,
        '外文图书', 'Books in English are listed here.'
    );

    db_cat_add(
        db_cat_get_name('科学技术')['cat_id'],
        '物理', '这是科学技术类中的物理类图书。'
    );

    db_cat_add(
        db_cat_get_name('科学技术')['cat_id'],
        '化学', '这是科学技术类中的化学类图书。'
    );

    db_cat_add(
        db_cat_get_name('科学技术')['cat_id'],
        '计算机', '这是科学技术类中的计算机类图书。'
    );

    db_book_add(
        db_user_get_name('hczhcz')['user_id'], db_cat_get_name('物理')['cat_id'],
        '《费曼物理讲义》', '这是一本书。', 'RMB 100.00', 1
    );

    db_book_add(
        db_user_get_name('hczhcz')['user_id'], db_cat_get_name('计算机')['cat_id'],
        '《PHP是世界上最好的语言》', '这本书卖断货了。', 'RMB 1000.00', 0
    );

    db_book_add(
        db_user_get_name('zacks')['user_id'], db_cat_get_name('计算机')['cat_id'],
        '《黑客与画家》', '这是由个人售出的一本书。', 'RMB 50.00', 5
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('计算机')['cat_id'],
        '《黑客与画家》', '这是由书店售出的一本书。', 'RMB 49.80', 200
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('计算机')['cat_id'],
        '《编程珠玑》', '这是一本计算机类图书。', 'RMB 59.80', 300
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('医疗健康')['cat_id'],
        '《颈椎病康复指南》', '这是一本医疗健康类图书。', 'RMB 29.80', 1200
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('科学技术')['cat_id'],
        '《井字棋必胜策略》', '这是一本免费的科学技术类图书。', 'Free', 100000
    );

    db_book_add(
        db_user_get_name('栗子书店')['user_id'], db_cat_get_name('外文图书')['cat_id'],
        'Database System Concepts', 'This is a book.', 'USD 20.00', 100
    );

    // db_buy_add(
    //     $buy_book_id, $buyer_user_id,
    //     $address
    // );
?>
