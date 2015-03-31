<?php
    require_once '../util/db.php';

    function db_cat_init() {
        // catalogs
        mysql_query('
            create table cat (
                cat_id          int             primary key,

                parent_cat_id   int,
                name            varchar(64)     unique,
                detail          text
            );
        ');
    }
?>
