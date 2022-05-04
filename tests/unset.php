<?php
    if (is_null($_SESSION)) session_start();
    $keys = array(
        0 => 'test',
        1 => 'quest_num',
        2 => 'maxpoints',
        3 => 'quest_arr',
        4 => 'ans',
        5 => 'question',
        6 => 'vert',
        7 => 'name',
        8 => 'lastname',
        9 => 'class',
        10 => 'finish_time',
        11 => 'qOrder',
        12 => 'sid',
        13 => 'laa'
    );

    
    foreach ($keys as $val) {
        if (isset($_SESSION[$val])) unset($_SESSION[$val]);
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (isset($_GET['index'])) {
            header("location: ../index.php");
            exit;
        }
    }
?>