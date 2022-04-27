<?php
    session_start();
    require_once("../config.php");

    $_SESSION['question'] += 1;

    $rand = array_rand($_SESSION['quest_arr'], 1);
    $qid = $_SESSION['quest_arr'][$rand];
    array_splice($_SESSION['quest_arr'], $rand, 1);

    if ($_SESSION['question'] > 1) {
        saveAns();
    }

    if ($_SESSION['question'] > $_SESSION['quest_num']) {
        header("location: summary.php");
    }
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pytanie
            <?php echo $_SESSION['question']; ?>
        </title>
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
                        <a class="nav-item" id="timer"></a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container">
                <div class="card mt-5">
                    <?php
                        require_once("question_sel.php");
                    ?>
                </div>
            </div>
        </div>
        <script>
            let finish = new Date(<?php echo $_SESSION['finish_time']; ?>);
            finish = Math.round(finish.getTime());
            let x = setInterval(function() {
                let now = Math.round(new Date().getTime() / 1000);
                let dist = finish - now;

                let minutes = Math.floor((dist % (60 * 60)) / 60);
                let seconds = Math.floor(dist % 60);

                document.getElementById("timer").innerHTML = "Pozostały czas: " + minutes + " minut, " + seconds + " sekund.";

                if (dist <= 0) {
                    window.location.replace("summary.php");
                }
            }, 1000);
                
            
        </script>
        <script>
            function redir() {
                window.location.replace("summary.php");
            }
            window.addEventListener("blur", redir);
        </script>
    </body>
</html>