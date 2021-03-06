<?php
    session_start();

    if (!isset($_SESSION['question'])) {
        header("location: ../index.php");
        exit;
    }

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('../config/config.php');
    $maxpoints = (isset($_SESSION['test'])) ? unserialize($_SESSION['test'])->testPoints : 1;
    $score_cal = review();

    $points = round((($score_cal/$maxpoints)*100), 0, PHP_ROUND_HALF_UP);

    require_once("grade.php");
    
    
    if(isset($_SESSION['sid'])) {
        $sid = $_SESSION['sid'];
        $name = $_SESSION['name'].' '.$_SESSION['lastname'];
        $class = $_SESSION['class'];
        $test = unserialize($_SESSION['test'])->testId;
        
        require_once("scoresave.php");
    }

    $btn = (unserialize($_SESSION['test'])->testLAA == 1) ? "<a class='btn btn-secondary' href='laa.php'>Zobacz odpowiedzi</a>" : "";

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Podsumowanie testu</title>

    </head>
    <body>
        <div>
            

        </div>
    </body>
</html>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Podsumowanie testu</title>
        <link rel="stylesheet" href="../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
            ul#rules {
                text-align: left;
            }
        </style>
    </head>
    <body>
    <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
            <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container">
                <h2 class="mt-4"><?php echo "Uda??o ci si?? zdoby?? <b>".$score_cal."</b> punkt??w, czyli <b>".$points."%</b>."; ?></h2>
                <h5>Twoja ocena:</h5>
                <?php
                    show_grade();
                ?>
                <div class="form-group mt-3">
                    <a class="btn btn-primary" href='unset.php?index=1'>Powr??t do strony g????wnej</a>
                    <?php echo $btn ?>
                </div>
            </div>
        </div>
    </body>
</html>