<?php
    session_start();
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $link = dbConnect();

    $cl = $ct = $vert = "";
    
    $test = unserialize($_SESSION['edit_test']);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['save'])) {
            $name = argStrip($_POST['name']);

            $ct = (!empty($_POST['ct'])) ? 1 : 0;
            $cl = (!empty($_POST['cl'])) ? 1 : 0;
            $vert = (!empty($_POST['vert'])) ? 1 : 0;

            $time = argStrip($_POST['time']);

            try {

                $test->updateTest($time, $vert, $cl, $ct);
                
                if ($name != $test->testName) $test->setName($name);

            } catch (Exception $e) {

                echo $e->getMessage();

            }

            $_SESSION['edit_test'] = serialize($test);

            header("location: ${_SERVER['PHP_SELF']}");
            exit;
        }
    }

    if ($test->testCT) {
        $ct = "checked";
    }
    if ($test->testLAA) {
        $cl = "checked";
    }
    if ($test->testVert) {
        $vert = "checked";
    }

    $time = $test->testTime;
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Modyfikacja testu</title>
        <link rel="stylesheet" href="../../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
            body > .wrapper form input.form-control[type="text"] {
                text-align: center;
            }
        </style>
    </head>
    <body>
    <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
            <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='quest.php'>Powr??t do modyfikacji pyta??</a>
                        <a class="nav-item nav-link active" href='../userpage.php'>Powr??t do strony u??ytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powr??t do strony g????wnej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <h1 class="my-5">Wybrany test: <?php echo $test->testName; ?></h1>
            <div class="container">
                <form method="post">
                    <h2 class="my-5">Zmie?? w??a??ciwo??ci testu:</h2>
                    <div class="form-group">
                        <label for="name">Nazwa testu:</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $test->testName; ?>">
                    </div>
                    <div class="form-group">
                        <label for="time">Ilo???? czasu/pytanie (sekundy)</label>
                        <input type="number" name="time" id="time" class="form-control" max="60" min="30" value='<?php echo $time; ?>'>                        
                    </div>
                    <div class="form-group">
                        <input name="ct" value="1" type="checkbox" id="ct" <?php echo $ct; ?> class="">
                        <label for="ct">Czy mo??na podchodzi?? do testu poza sesjami?</label>
                    </div>
                    <div class="form-group">
                        <input name="vert" value="1" type="checkbox" id="vert" <?php echo $vert; ?> class="">
                        <label for="vert">Test przewijany?</label>
                    </div>
                    <div class="form-group">
                        <input name="cl" value="1" type="checkbox" id="cl" <?php echo $cl; ?> class="">
                        <label for="cl">Czy mo??na podgl??da?? odpowiedzi po zako??czeniu testu?</label>
                    </div>
                    </div>      
                    <div class="form-group">
                        <input type="submit" name="save" class="btn btn-primary" value="Zatwierd??">
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>