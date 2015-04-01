<?php
    require_once 'db.php';

    function db_cat_init() {
        global $db_conn;

        // catalogs
        return $db_conn->query('
            create table cat (
                cat_id          bigint          auto_increment  primary key,
                parent_cat_id   bigint          not null,

                name            varchar(64)     unique,
                detail          text
            );
        ');
    }

    function db_cat_get($cat_id) {
        return db_select('cat', 'cat_id', $cat_id)->fetch_assoc();
    }
?>
