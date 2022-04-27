<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once("../../config.php");

    $owner = $_SESSION['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $session = argStrip($_POST['session']);
        if (isset($_POST['open'])) {
            $started = date("Y-m-d H:i:s");

            $sql = mysqli_prepare($link, "UPDATE session SET is_open=1, started=? WHERE id=?");
            mysqli_stmt_bind_param($sql, 'si', $started, $session);
            mysqli_stmt_execute($sql);
            header("location: sessionlist.php");
            exit;
        } else if (isset($_POST['close'])) {
            $closed = date("Y-m-d H:i:s");

            $sql = mysqli_prepare($link, "UPDATE session SET is_open=0, closed=? WHERE id=?");
            mysqli_stmt_bind_param($sql, 'si', $closed, $session);
            mysqli_stmt_execute($sql);
            header("location: sessionlist.php");
            exit;
        }
    }

    $sql = mysqli_prepare($link, "SELECT session.id, session.can_laa, session.code, test.name AS tname, module.name AS mname, session.is_open, session.started, session.closed, session.part FROM session, test, module WHERE session.test_id=test.id AND test.module_id=module.id AND session.owner=? ORDER BY session.id DESC");
    mysqli_stmt_bind_param($sql, 'i', $owner);
    mysqli_stmt_execute($sql);
    $res = mysqli_stmt_get_result($sql);
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
                        <a class="nav-item nav-link active" href='sessions.php'>Powrót do strony sesji</a>
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container-fluid">
                <h2 class="my-5">Twoje sesje</h2>
                    <table class="table">
                        <thead>
                            <th>Kod sesji</th>
                            <th>Nazwa testu</th>
                            <th>Status</th>
                            <th>Podglądanie odpowiedzi</th>
                            <th>Data otwarcia</th>
                            <th>Data zamknięcia</th>
                            <th>Ilość zarejestrowanych podejść</th>
                            <th></th>
                        </thead>
                        <tbody>
                            <?php
                                while ($row = mysqli_fetch_assoc($res)) {
                                    if ($row['is_open'] == 0 && !empty($row['closed'])) {
                                        $status = "Zakończona";
                                        $btn_vis = "hidden";
                                    } else if ($row['is_open'] == 0) {
                                        $status = "Zamknięta";
                                        $btn_text = "Otwórz sesję";
                                        $act = "open";
                                        $btn_vis = "";
                                    } else {
                                        $status = "Otwarta";
                                        $btn_text = "Zamknij sesję";
                                        $act = "close";
                                        $btn_vis = "";
                                    }

                                    if (empty($row['started'])) {
                                        $started = "-";
                                    } else {
                                        $started = $row['started'];
                                    }

                                    if (empty($row['closed'])) {
                                        $closed = "-";
                                    } else {
                                        $closed = $row['closed'];
                                    }

                                    if (empty($row['part'])) {
                                        $part = 0;
                                    } else {
                                        $part = $row['part'];
                                    }

                                    if ($row['can_laa'] == 0) {
                                        $cl = "Nie";
                                    } else {
                                        $cl = "Tak";
                                    }
                                    echo '<tr>';
                                    echo '<td><form method="post"><input type="hidden" value="'.$row['id'].'" name="session"><input type="submit" formaction="s_entry.php" value="'.$row['code'].'" class="btn btn-link btn-sm"></form></td>
                                        <td>'.$row['tname'].' - '.$row['mname'].'</td>
                                        <td>'.$status.'</td>
                                        <td>'.$cl.'</td>
                                        <td>'.$started.'</td>
                                        <td>'.$closed.'</td>
                                        <td>'.$part.'</td>';

                                    if ($btn_vis != "hidden") {
                                        echo '<td><form method="post">
                                            <input type="hidden" value="'.$row['id'].'" name="session">
                                            <input type="submit" name='.$act.' value="'.$btn_text.'" '.$act.' class="btn btn-primary btn-sm">
                                            </form>
                                        </td>';
                                    }
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                    </table>
            </div>

        </div>
        
    </body>
</html>