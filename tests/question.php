<?php
    session_start();
    require_once('../config/config.php');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if ($_SESSION['question'] === 0) {
        $_SESSION['question'] += 1;
    } else {
        if ($_SERVER['REQUEST_METHOD'] == "POST") { // nie wychodź, jeżeli przesyłane są dane do sprawdzenia
            $count = count($_SESSION['ans']);
            for ($i = 0; $i < $count; $i++) { // zbierz wszystkie odpowiedzi
                $test = (isset($_POST[$i])) ? argStrip($_POST[$i]['ans']) : "";
                $ans = (is_array($test)) ? concArray($test) : $test;
    
                $_SESSION['ans'][$i]['answer'] = $ans;        
            }
    
            header("location: summary.php");
            exit;
        } else {
            header("location: summary.php"); // jeżeli strona została po prostu odświeżona, wyjdź
            exit;
        }
    }

    $prevNext = $script = $sub = "";

    $qnum = 0;
    $qcount = $_SESSION['quest_num'];

    if (!isset($_SESSION['qOrder'])) {
        $_SESSION['qOrder'] = $_SESSION['quest_arr'];
        shuffle($_SESSION['qOrder']);
    }

    $rand = $_SESSION['qOrder'];

    $btn = "<div class='form-group mt-3' style='text-align: center;'>
                <div class='btn-group'>
                    <button type='button' class='btn btn-secondary' id='prev'>Poprzednie pytanie</button>
                    <span class='btn btn-outline-secondary disabled'></span>
                    <button type='button' class='btn btn-secondary' id='next'>Następne pytanie</button>
                </div>
            </div>";

    $prevNext = ($_SESSION['vert'] == 0) ? $btn : "";
    $script = ($_SESSION['vert'] == 0) ? "<script src='js/questTabs.js'></script>" : "<script src='js/layout.js'></script>";
    $sub = ($_SESSION['vert'] != 0) ? "<input type='submit' class='btn btn-primary mt-3' value='Zatwierdź'>" : "";

    ?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Odpowiedz na pytania</title>
        <link rel="stylesheet" href="../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
            
            .hidden {
                display: none;
            }
        </style>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item" id="timer"></a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <article class="container">
                <form method='post' id='quest'>
                    <?php
                        $corr = $type = $points = 0;
                        
                        for ($iter = 0; $iter < $qcount; $iter++) {
                            $qnum++;
                            
                            $qid = $rand[$iter];
                            
                            echo "<section class='card mt-5' id='q${iter}'>"; 
                            require("question_sel.php");
                            echo "</section>";
                            
                            $_SESSION['ans'][$iter] = array(
                                "correct" => $corr,
                                "type" => $type,
                                "points" => $points
                            );
                        }
                        $sub = ($iter == $qcount - 1) ? "<input type='submit' class='btn btn-primary mt-3' value='Zatwierdź'>" : $sub;
                    ?>
                    <?php echo $prevNext; ?>
                    <section class='form-group' id='sub-grp' style="text-align: center;">
                        <?php echo $sub; ?>
                    </section>
                </form>
                <p id="alert"></p>
            </article>
        </div>
        <script>
            let finish = new Date(<?php echo $_SESSION['finish_time']; ?>);
            finish = Math.round(finish.getTime());
            let x = setInterval(function() {
                let now = Math.round(new Date().getTime() / 1000);
                let dist = finish - now;

                let minutes = Math.floor((dist % (60 * 60)) / 60);
                let seconds = Math.floor(dist % 60);

                $('#timer').text("Pozostały czas: " + minutes + " minut, " + seconds + " sekund.");

                if (dist <= 0) {
                    $('#quest').submit();
                }
            }, 1000);
        </script>
        <?php echo $script; ?>
    </body>
</html>