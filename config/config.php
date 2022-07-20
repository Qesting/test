<?php

    require_once("credentials.php");    // poświadczenia do logowania w bazie jako stałe
                                        // dbServer, dbUser, dbPasswd, dbName

    require_once("function.php");       // funkcje
    require_once("classQuestion.php");  // plik klasy "question"
    require_once("classTest.php");      // plik klasy "test"

    // połączenie z bazą
    function dbConnect() {
        return new mysqli(dbServer, dbUser, dbPasswd, dbName);
    }

    /* Attempt to connect to MySQL database */
    $link = mysqli_connect(dbServer, dbUser, dbPasswd, dbName);
    
    // Check connection
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }


?>