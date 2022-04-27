<?php

    /* Database credentials. Assuming you are running MySQL
    server with default setting*/
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'connect');
    define('DB_PASSWORD', '1234');
    define('DB_NAME', 'test');
    
    /* Attempt to connect to MySQL database */
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }


    require_once("function.php");
?>