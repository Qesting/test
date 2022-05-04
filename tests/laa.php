<?php

    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once('../config.php');

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
        <script src="js/layout.js"></script>
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
                    
                        $sql = mysqli_prepare($link, "SELECT content FROM question WHERE id=?");
                        
                        foreach ($_SESSION['qOrder'] as $key => $val) {

                            echo "<section class='card card-body'>";

                            mysqli_stmt_bind_param($sql, 'i', $val);
                            mysqli_stmt_execute($sql);
                            $res = mysqli_stmt_get_result($sql);
                            $data = mysqli_fetch_array($res);

                            $ans = $_SESSION['ans'][$key];
                            $qo = $key + 1;
                            echo "<h4>Pytanie ${qo}:</h4>";
                            echo "<h5 class='mb-3'>${data[0]}</h5>";

                            
                            switch ($ans['type']) {
                                case 1:
                                    $sqla = mysqli_prepare($link, "SELECT content FROM answer WHERE quest_id=?");
                                    mysqli_stmt_bind_param($sqla, 'i', $val);
                                    mysqli_stmt_execute($sqla);
                                    $res = mysqli_stmt_get_result($sqla);
                                    $content = array();
                                    $i = 1;
                                    while($row = mysqli_fetch_array($res)) {
                                        $content[$i] = $row[0];
                                        $i++;
                                    }
                                    mysqli_stmt_close($sqla);

                                    $a = (!empty($ans['answer'])) ? $content[$ans['answer']] : "brak";
                                    $c = $content[$ans['correct']];

                                    if($ans['answer'] == $ans['correct']) {
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
                                    $sqla = mysqli_prepare($link, "SELECT content FROM answer WHERE quest_id=?");
                                    mysqli_stmt_bind_param($sqla, 'i', $val);
                                    mysqli_stmt_execute($sqla);
                                    $res = mysqli_stmt_get_result($sqla);
                                    $content = array();
                                    $i = 1;
                                    while($row = mysqli_fetch_array($res)) {
                                        $content[$i] = $row[0];
                                        $i++;
                                    }
                                    mysqli_stmt_close($sqla);

                                    $a = $c = array();

                                    $arra = str_split($ans['answer']);
                                    foreach ($arra as $k => $v) {

                                        $a[$k] = (!empty($v)) ? $content[$v] : "brak";
                                    }

                                    $arrc = str_split($ans['correct']);
                                    foreach ($arrc as $k => $v) {

                                        $c[$k] = $content[$v];
                                    }
                                    
                                    if ($ans['correct'] == $ans['answer']) {
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
                                    $a = (!empty($ans['answer'])) ? $ans['answer'] : "brak";
                                    $c = $ans['correct'];

                                    if($ans['answer'] == $ans['correct']) {
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
                <a href='/index.php' class='btn btn-primary'>Powrót do strony głównej</a>
            </footer>
        </div>
    </body>
</html>

<?php
    require_once("unset.php");
?>