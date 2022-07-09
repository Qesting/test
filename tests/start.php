<?php
    session_start();
    require_once('../config/config.php');

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $notice = $notice_class = "";
    $sCode = "";

    $tname = array(
        'tn' => "",
        'mn' => ""
    );

    if (isset($_GET['t'])) {

        $sCode = (isset($_GET['s'])) ? $_GET['s'] : $sCode;
        $testId = argStrip($_GET['t']);

        $test = &$_SESSION['test'];
        $test = test::get($testId);

        $link = dbConnect();
        $sql = $link->prepare("SELECT module.name AS mn, test.name AS tn FROM module, test WHERE module.id=test.module_id AND test.id=?");
        $sql->bind_param('i', $testId);
        $sql->execute();
        $res = $sql->get_result();
        $tname = $res->fetch_assoc();

        $sql->close();
        $link->close();

    } 

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if(isset($_POST['start'])) {
            $code = argStrip($_POST['s']);

            if ($test->testCT == 0) {

                if (!empty($code)) {
                    $link = dbConnect();
                    $sql = $link->prepare("SELECT id, can_laa, is_open, closed FROM session WHERE code=?");
                    $sql->bind_param('s', $code);
                    $sql->execute();
                    $res = $sql->get_result();

                    $sql->close();
                    $link->close();
        
                    if ($res->num_rows == 0) {
                        $notice = "e-Taki kod sesji nie istnieje!";
                    } else {
                        $data = $res->fetch_assoc();
                        if ($data['is_open'] == 0 && !empty($data['closed'])) {
                            $notice = "e-Sesja się już zakończyła!";
                        } else if ($data['is_open'] == 0) {
                            $notice = "e-Sesja się jeszcze nie rozpoczęła!";
                        } else {
                            $_SESSION['laa'] = $data['can_laa'];

                            $_SESSION['sid'] = $data['id'];
    
                            $_SESSION['name'] = argStrip($_POST['n']);
                            $_SESSION['lastname'] = argStrip($_POST['l']);
                            $_SESSION['class'] = argStrip($_POST['c']);
    
                            $_SESSION['finish_time'] = time() + (count($test->testQuestions) * $test->testTime);
    
                            $_SESSION['ans'] = array();

                            $_SESSION['question'] = 0;

                            header("location: question.php");
                            exit;
                        }
                    }
                } else {
                    $notice = "e-Ten test można rozwiązać tylko w ramach sesji!";
                }  
            } else if (!empty($code)) {
                $link = dbConnect();
                    $sql = $link->prepare("SELECT id, can_laa, is_open, closed FROM session WHERE code=?");
                    $sql->bind_param('s', $code);
                    $sql->execute();
                    $res = $sql->get_result();

                    $sql->close();
                    $link->close();
        
                    if ($res->num_rows == 0) {
                        $notice = "e-Taki kod sesji nie istnieje!";
                    } else {
                        $data = $res->fetch_assoc();
                        if ($data['is_open'] == 0 && !empty($data['closed'])) {
                            $notice = "e-Sesja się już zakończyła!";
                        } else if ($data['is_open'] == 0) {
                            $notice = "e-Sesja się jeszcze nie rozpoczęła!";
                        } else {
                            $_SESSION['laa'] = $data['can_laa'];

                            $_SESSION['sid'] = $data['id'];
    
                            $_SESSION['name'] = argStrip($_POST['n']);
                            $_SESSION['lastname'] = argStrip($_POST['l']);
                            $_SESSION['class'] = argStrip($_POST['c']);
    
                            $_SESSION['finish_time'] = time() + (count($test->testQuestions) * $test->testTime);
    
                            $_SESSION['ans'] = array();

                            $_SESSION['question'] = 0;

                            header("location: question.php");
                            exit;
                        }
                }
            } else {

                $_SESSION['name'] = argStrip($_POST['n']);
                $_SESSION['lastname'] = argStrip($_POST['l']);
                $_SESSION['class'] = argStrip($_POST['c']);
    
                $_SESSION['finish_time'] = time() + (count($test->testQuestions) * $test->testTime);
    
                $_SESSION['ans'] = array();

                $_SESSION['question'] = 0;

                header("location: question.php");
                exit;
            }
        } 
    }

    showNot();


    if (isset($_SESSION['question'])) {
        header('location:summary.php');
        exit;
    }

?>


<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Rozpocznij test</title>
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
                        <a class="nav-item nav-link active" href='../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container">
                <h3 class="mt-5 mb-3"><?php echo "${tname['mn']} - ${tname['tn']}";?></h3>
                <p><?php echo count($test->testQuestions)." pytań, ".$test->testPoints." punktów"; ?></p>
                <form method="post">
                    <h4>Zasady:</h4>
                    <ul id="rules" class="alert alert-info">
                        <li>Jeżeli spróbujesz cofnąć się do poprzedniej strony, system przekieruje cię do podsumowania.</li>
                        <li>Jeżeli skończy ci się czas, system przekieruje również cię do podsumowania.</li>
                        <li>Przy każdym pytaniu widoczna jest ilość punktów, które jest ono warte.</li>
                        <li>Wartość pytania wielokrotnego wyboru to stawka punktowa * ilość odpowiedzi.</li>
                        <li>W pytaniach wielokrotnego wyboru punkty odejmowane są za udzielenie błędnej (również nadmiarowej) odpowiedzi, ale nie mogą zejść poniżej 0.</li>
                    </ul>
                    <div class="form-group">
                        <label for="n" class="form-label">Imię</label>
                        <input type="text" name="n" id="n" placeholder="Imię" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="l" class="form-label">Nazwisko</label>
                        <input type="text" name="l" id="l" placeholder="Nazwisko" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="c" class="form-label">Klasa</label>
                        <input type="text" name="c" id="c" placeholder="Klasa" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="s" class="form-label">Kod sesji</label>
                        <?php echo "<input type='text' name='s' id='s' placeholder='Kod sesji' class='form-control' value='${sCode}'>" ?>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="start" class="btn btn-primary" value="Zatwierdź">
                    </div>
                </form>
                <?php echo "<p id=\"notice\" class=\"${notice_class}\">${notice}</p>"; ?>
            </div>
        </div>
    </body>
</html>
