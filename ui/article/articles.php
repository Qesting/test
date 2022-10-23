<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Materiały | Materiały testopol</title>
        <link rel="stylesheet" href="../../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
        </style>
    </head>
    <body>
    <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
            <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='../userpage.php'><i class='bi-person-circle'></i> Strona użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'><i class='bi-house-fill'></i> Strona główna</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container">
                <h2 class="my-5">Tutaj znajdują się wszystkie twoje materiały</h2>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card card-body">
                            <h4 class="my-5">Lista twoich materiałów</h4>
                            <a class="btn btn-primary" href="articlelist.php">Rozpocznij</a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-body">
                            <h4 class="my-5">Stwórz nowy artykuł</h4>
                            <a class="btn btn-primary" href="write.php">Rozpocznij</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>