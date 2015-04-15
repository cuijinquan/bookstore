<?php
    require_once 'db.php';

    // db actions of catalogs

    function db_cat_init() {
        global $db_conn;

        // catalogs
        //     parent_cat_id: top level catalog if null
        //     cat_count: no more than 50
        return $db_conn->query('
            create table cat (
                cat_id          bigint          auto_increment  primary key,
                parent_cat_id   bigint,

                foreign key (parent_cat_id) references cat (cat_id)
                                on delete cascade on update cascade,

                name            varchar(64)     unique,
                image           varchar(64),
                detail          text,

                cat_count       bigint          not null,
                tot_book_count  bigint          not null,
                book_count      bigint          not null
            ) ENGINE = InnoDB;
        ');
    }

    function db_cat_drop() {
        global $db_conn;

        return $db_conn->query('
            drop table cat;
        ');
    }

    function db_cat_add(
        $parent_cat_id,
        $name, $image, $detail
    ) {
        return db_insert(
            'cat',
            null, $parent_cat_id,
            $name, $image, $detail,
            0, 0, 0
        ) && ($parent_cat_id === null || db_update(
            'cat',
            'cat_id', $parent_cat_id,
            'cat_count = cat_count + 1'
        ));
    }

    function db_cat_get($cat_id) {
        return db_select('cat', 'cat_id', $cat_id)->fetch_assoc();
    }

    function db_cat_get_name($name) {
        return db_select('cat', 'name', $name)->fetch_assoc();
    }

    function db_cat_list_parent(
        $cat_id,
        $begin, $count = 50
    ) {
        if ($cat_id === 0) {
            // top level
            return db_select_cond(
                'cat', 'parent_cat_id is null', array(),
                'cat_id', false, $begin, $count
            );
        } else {
            // not top level
            return db_select(
                'cat', 'parent_cat_id', $cat_id,
                'cat_id', false, $begin, $count
            );
        }
    }

    function db_cat_set_totinc($cat_id) {
        if ($cat_id === null) {
            return true;
        }

        return db_update(
            'cat',
            'cat_id', $cat_id,
            'tot_book_count = tot_book_count + 1'
        ) && (
            $cat_info = db_cat_get($cat_id)
        ) && db_cat_set_totinc($cat_info['parent_cat_id']);
    }

    // function db_cat_set($data) {
    //     return db_write('cat', $data, true);
    // }
?>
