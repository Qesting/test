<?php
    session_start();
    require_once("../config.php");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $notice = $notice_class = "";

    if (isset($_GET['t'])) {
        $_SESSION['test'] = argStrip($_GET['t']);
        $tid = $_SESSION['test'];

        $sql = mysqli_prepare($link, "SELECT module.name AS mn, test.name AS tn FROM module, test WHERE module.id=test.module_id AND test.id=?");
        mysqli_stmt_bind_param($sql, 'i', $tid);
        mysqli_stmt_execute($sql);
        $res = mysqli_stmt_get_result($sql);
        $tname = mysqli_fetch_assoc($res);
        
        $sql = mysqli_prepare($link, "SELECT id, points, quest_type, ans FROM question WHERE test_id=?");
        mysqli_stmt_bind_param($sql, 'i', $tid);
        mysqli_stmt_execute($sql);
        $result = mysqli_stmt_get_result($sql);
        
        $i = 0;
        $j = 0;
        $arr;
        while ($row = mysqli_fetch_assoc($result)) {
            $arr[$i] = $row['id'];
            $i ++;
            if ($row['quest_type'] == 2) {
                $num = strlen($row['ans']);

                $j += ($num * $row['points']);
            } else {
                $j += $row['points'];
            }
        }
        $_SESSION['quest_num'] = $i;
        $_SESSION['maxpoints'] = $j;

        $_SESSION['quest_arr'] = $arr;
        mysqli_stmt_close($sql);

        $_SESSION['ans'] = array();
    }
    
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if(isset($_POST['start'])) {
            $code = argStrip($_POST['s']);

            $sql = mysqli_prepare($link, "SELECT can_take, vert, time FROM test WHERE id=?");
            mysqli_stmt_bind_param($sql, 'i', $_SESSION['test']);
            mysqli_stmt_execute($sql);
            $result = mysqli_stmt_get_result($sql);
            $dt = mysqli_fetch_assoc($result);

            if ($dt['can_take'] == 0) {

                if (!empty($code)) {
                    $sql = mysqli_prepare($link, "SELECT id, can_laa, is_open, closed FROM session WHERE code=?");
                    mysqli_stmt_bind_param($sql, 's', $code);
                    mysqli_stmt_execute($sql);
                    $res1 = mysqli_stmt_get_result($sql);
        
                    if (mysqli_num_rows($res1) == 0) {
                        $notice = "e-Taki kod sesji nie istnieje!";
                        unset($_SESSION['question']);
                    } else {
                        $data = mysqli_fetch_assoc($res1);
                        if ($data['is_open'] == 0 && !empty($data['closed'])) {
                            $notice = "e-Sesja się już zakończyła!";
                            unset($_SESSION['question']);
                        } else if ($data['is_open'] == 0) {
                            $notice = "e-Sesja się jeszcze nie rozpoczęła!";
                            unset($_SESSION['question']);
                        } else {
                            $_SESSION['laa'] = $data['can_laa'];
                            $_SESSION['vert'] = $dt['vert'];

                            $_SESSION['sid'] = $data['id'];
    
                            $_SESSION['name'] = argStrip($_POST['n']);
                            $_SESSION['lastname'] = argStrip($_POST['l']);
                            $_SESSION['class'] = argStrip($_POST['c']);
    
                            $_SESSION['finish_time'] = time() + ($_SESSION['quest_num'] * $dt['time']);
    
                            $_SESSION['ans'] = array();
                            header("location: question.php");
                            exit;
                        }
                    }
                } else {
                    $notice = "e-Ten test można rozwiązać tylko w ramach sesji!";
                    unset($_SESSION['question']);
                }  
            } else if (!empty($code)) {
                $sql = mysqli_prepare($link, "SELECT id, can_laa, is_open, closed FROM session WHERE code=?");
                mysqli_stmt_bind_param($sql, 's', $code);
                mysqli_stmt_execute($sql);
                $result = mysqli_stmt_get_result($sql);

                if (mysqli_num_rows($result) == 0) {
                    $notice = "e-Taki kod sesji nie istnieje!";
                    unset($_SESSION['question']);
                } else {
                    $data = mysqli_fetch_assoc($result);
                    if ($data['is_open'] == 0 && !empty($data['closed'])) {
                        $notice = "e-Sesja się już zakończyła!";
                        unset($_SESSION['question']);
                    } else if ($data['is_open'] == 0) {
                        $notice = "e-Sesja się jeszcze nie rozpoczęła!";
                        unset($_SESSION['question']);
                    } else {
                        $_SESSION['laa'] = $data['can_laa'];
                        $_SESSION['vert'] = $dt['vert'];

                        $_SESSION['sid'] = $data['id'];

                        $_SESSION['name'] = argStrip($_POST['n']);
                        $_SESSION['lastname'] = argStrip($_POST['l']);
                        $_SESSION['class'] = argStrip($_POST['c']);

                        $_SESSION['finish_time'] = time() + ($_SESSION['quest_num'] * $dt['time']);

                        $_SESSION['ans'] = array();
                        header("location: question.php");
                        exit;
                    }
                }
            } else {
                $_SESSION['vert'] = $dt['vert'];

                $_SESSION['name'] = argStrip($_POST['n']);
                $_SESSION['lastname'] = argStrip($_POST['l']);
                $_SESSION['class'] = argStrip($_POST['c']);
    
                $_SESSION['finish_time'] = time() + ($_SESSION['quest_num'] * $dt['time']);

                $sql = mysqli_prepare($link, "SELECT can_laa FROM test WHERE id=?");
                mysqli_stmt_bind_param($sql, 'i', $_SESSION['test']);
                mysqli_stmt_execute($sql);
                $res = mysqli_stmt_get_result($sql);
                mysqli_stmt_close($sql);
                $data = mysqli_fetch_assoc($res);

                $_SESSION['laa'] = $data['can_laa'];
    
                $_SESSION['ans'] = array();
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

    $_SESSION['question'] = 0;

    mysqli_close($link)

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
                <p><?php echo "${_SESSION['quest_num']} pytań, ${_SESSION['maxpoints']} punktów"; ?></p>
                <form method="post">
                    <h4>Zasady:</h4>
                    <ul id="rules" class="alert alert-info">
                        <li>Jeżeli spróbujesz cofnąć się do poprzedniej strony, system przekieruje cię do podsumowania.</li>
                        <li>Jeżeli skończy ci się czas, odpowiedź na ostatnie pytanie nie zostanie zapisana, a system przekieruje cię do podsumowania.</li>
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
                        <input type="text" name="s" id="s" placeholder="Kod sesji" class="form-control">
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