<?php
    require_once 'util/config.php';
    require_once 'util/session.php';
    require_once 'util/db.php';

    echo $site_name;

    // db_write('buy', array('address' => 'aaa','buy_id' => '123')， false);
    // $r = db_select('buy', 'address', 'aaa');
    // $r1 = $r->fetch_assoc();
    // $r1['feedback'] = $r1['feedback'] . 'a';
    // db_write('buy', $r1, true);
    // db_delete('buy', 'address', 'aaa');
    // db_write('buy', array('address' => 'aaab', 'buy_id' => 1), true);
?>
