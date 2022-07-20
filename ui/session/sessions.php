<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sesje</title>
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
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container">
                <h2 class="my-5">Tutaj znajdują się wszystkie twoje sesje</h2>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card card-body">
                            <h4 class="my-5">Lista twoich sesji</h4>
                            <a class="btn btn-primary" href="sessionlist.php">Rozpocznij</a>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-body">
                            <h4 class="my-5">Stwórz nową sesję</h4>
                            <a class="btn btn-primary" href="sessionadd.php">Rozpocznij</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
    </body>
</html>