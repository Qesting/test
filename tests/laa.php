<?php

    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once('../config/config.php');

    if (!isset($_SESSION['question'])) {
        header("location: ../index.php");
    }

?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Odpowiedzi</title>
        <link rel="stylesheet" href="../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
        </style>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="../js/layout.js"></script>
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
            <header>
                <h3 class="mt-5 mb-3">Odpowiedzi dla testu</h3>
            </header>    
            <main class="container">
                <article>
                    <?php 

                        $quest = unserialize($_SESSION['test'])->testQuestions;
                        
                        for ($i = 0; $i < count($quest); $i++) {

                            echo "<section class='card card-body'>";

                            $ans = $_SESSION['ans'][$i];
                            $q = $quest[$i];
                            $qo = $i + 1;
                            echo "<h4>Pytanie ${qo}:</h4>";
                            echo "<h5 class='mb-3'>".$q->questionContent."</h5>";

                            
                            switch ($q->questionType) {
                                case 1:
                                    $a = (!empty($ans)) ? $q->questionAnsList[$ans - 1] : "brak";
                                    $c = $q->questionAnsList[$q->questionAnswer - 1];

                                    if($ans == $q->questionAnswer) {
                                        echo "<p>Twoja odpowiedź:</p>";
                                        echo "<p class='alert alert-success'>${a}</p>";
                                    } else {
                                        echo "<p>Twoja odpowiedź:</p>";
                                        echo "<p class='alert alert-danger'>${a}</p>";

                                        echo "<p>Poprawna odpowiedź:</p>";
                                        echo "<p class='alert alert-success'>${c}</p>";
                                    }
                                    break;
                                case 2:

                                    $a = $c = array();

                                    $arra = str_split($ans);
                                    foreach ($arra as $k => $v) {

                                        $a[$k] = (!empty($v)) ? $q->questionAnsList[$v - 1] : "brak";
                                    }

                                    $arrc = str_split($q->questionAnswer);
                                    foreach ($arrc as $k => $v) {

                                        $c[$k] = $q->questionAnsList[$v - 1];
                                    }
                                    
                                    if ($ans == $q->questionAnswer) {
                                        echo "<p>Twoja odpowiedź:</p>";
                                        foreach ($a as $k => $v) {
                                            echo "<p class='alert alert-success'>${v}</p>";
                                        } 
                                    } else {
                                        echo "<p>Twoja odpowiedź:</p>";
                                        foreach ($a as $k => $v) {
                                            echo "<p class='alert alert-danger'>${v}</p>";
                                        }

                                        echo "<p>Poprawna odpowiedź:</p>";
                                        foreach ($c as $k => $v) {
                                            echo "<p class='alert alert-success'>${v}</p>";
                                        }
                                    }
                                    break;
                                case 3:
                                    $a = (!empty($ans)) ? $ans : "brak";
                                    $c = $q->questionAnswer;

                                    mb_internal_encoding('UTF-8');
                                    if(mb_strtolower($ans) == mb_strtolower($q->questionAnswer)) {
                                        echo "<p>Twoja odpowiedź:</p>";
                                        echo "<p class='alert alert-success'>${a}</p>";
                                    } else {
                                        echo "<p>Twoja odpowiedź:</p>";
                                        echo "<p class='alert alert-danger'>${a}</p>";

                                        echo "<p>Poprawna odpowiedź:</p>";
                                        echo "<p class='alert alert-success'>${c}</p>";
                                    }

                                    break;
                            }

                            echo "</section>";
                        }
                    ?>
                </article>
            </main>
            <footer class='pb-5 px-5'>
                <a href='../' class='btn btn-primary'><i class='bi-house-fill'></i> Strona główna</a>
                <a href='.' class='btn btn-primary'><i class='bi-house-fill'></i> Lista testów</a>
            </footer>
        </div>
    </body>
</html>

<?php
    require_once("unset.php");
?>