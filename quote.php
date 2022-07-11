<?php

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        require_once("config/config.php");

        $link = dbConnect();

        $result = $link->query("SELECT quote FROM quotes order by RAND() limit 1");

        //header("Content-Type: text/plain; charset=utf-8");
        echo $result->fetch_array()[0];

        $link->close();
    }

?>