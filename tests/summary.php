<?php
    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../config.php");
    $maxpoints = $_SESSION['maxpoints'];
    $score_cal = review();

    $points = round((($score_cal/$maxpoints)*100), 0, PHP_ROUND_HALF_UP);

    require_once("grade.php");
    
    $name = $_SESSION['name'].' '.$_SESSION['lastname'];
    $class = $_SESSION['class'];
    $test = $_SESSION['test'];

    if(isset($_SESSION['sid'])) {
        $sid = $_SESSION['sid'];
        require_once("scoresave.php");
    }

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
                <h2 class="mt-4"><?php echo "Udało ci się zdobyć <b>".$score_cal."</b> punktów, czyli <b>".$points."%</b>."; ?></h2>
                <h5>Twoja ocena:</h5>
                <?php
                    show_grade();
                ?>
                <div class="form-group mt-3">
                <a class="btn btn-primary" href='../index.php'>Powrót do strony głównej</a>
                </div>
            </div>
        </div>
    </body>
</html>

<?php
    setcookie (session_id(), "", time() - 3600);
    $_SESSION = array();
    session_destroy();
    session_write_close();
?>

