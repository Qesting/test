<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

require_once("../../config/config.php");
require_once("../../config/classArticle.php");

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['a'])) {
    try {
        Article::getById($_GET['a'])->delete();
    } catch (Exception $e) {

    }
}

header('location: articlelist.php');
exit;