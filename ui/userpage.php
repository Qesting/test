<?php
    session_start();

    /*ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        exit;
    }
    require_once("../config.php");

    $owner = $_SESSION['id'];

    $sql = mysqli_prepare($link, "SELECT module.name AS module, test.name, created_at, part FROM test, module WHERE module.id=test.module_id AND test.owner=? ORDER BY test.id DESC LIMIT 5");
    mysqli_stmt_bind_param($sql, 'i', $owner);
    mysqli_stmt_execute($sql);
    $res = mysqli_stmt_get_result($sql);

    mysqli_stmt_close($sql);

    $sql1 = mysqli_prepare($link, "SELECT test.name AS test, code, is_open, session.part, closed FROM session, test WHERE test.id=session.test_id AND session.owner=? ORDER BY session.id DESC LIMIT 5");
    mysqli_stmt_bind_param($sql1, 'i', $owner);
    mysqli_stmt_execute($sql1);
    $res1 = mysqli_stmt_get_result($sql1);

    mysqli_stmt_close($sql1);

    mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Strona użytkownika</title>
        <link rel="stylesheet" href="../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center; }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <h1 class="my-5">Witaj, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></h1>
            <?php
                    if ($_SESSION['priv'] == 2) {
                        echo "<div class=\"form-group\"><button onclick=\"window.location.href='users/users.php'\" class=\"btn btn-primary\"><span class=\"bi-file-person\"></span> Zarządzaj użytkownikami</button></div>";
                    }
                ?>
            <div class="form-group">
                <button onclick="window.location.href='reset-password.php'" class="btn btn-warning"><span class="bi-key"></span> Zresetuj hasło</button>
                <button onclick="window.location.href='logout.php'" class="btn btn-danger"><span class="bi-door-open"></span> Wyloguj</button>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="card card-body">
                        <button onclick="window.location.href='./test-mgmt/mod.php'" class="btn btn-primary"><span class="bi-card-text"></span> Zarządzaj testami</button>
                        <?php
                            if (mysqli_num_rows($res) == 0) {
                                echo '<h5 class="my-5">Wygląda na to, że nie masz jeszcze żadnych testów. Stwórz jakiś!</h5>';
                            } else {
                                echo '<table class="table">';
                                echo '<thead><tr>
                                    <th>Nazwa testu</th>
                                    <th>Data utworzenia</th>
                                    <th>Zarejestrowane podejścia</th>
                                    </thead></tr><tbody>';

                                while ($row = mysqli_fetch_assoc($res)) {
                                    echo '<tr>
                                    <td>'.$row['module'].' - '.$row['name'].'</td>
                                    <td>'.$row['created_at'].'</td>
                                    <td>'.$row['part'].'</td>
                                    </tr>';
                                }
                                echo '</tbody></table>';
                            }
                        ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card card-body">
                        <button onclick="window.location.href='./session/sessions.php'" class="btn btn-primary"><span class="bi-calendar-week"></span> Zarządzaj sesjami</button>
                        <?php
                            if (mysqli_num_rows($res1) == 0) {
                                echo '<h5 class="my-5">Wygląda na to, że nie masz jeszcze żadnych sesji. Stwórz jakąś!</h5>';
                            } else {
                                echo '<table class="table">';
                                echo '<thead><tr>
                                    <th>Nazwa testu</th>
                                    <th>Kod sesji</th>
                                    <th>Status</th>
                                    <th>Zarejestrowane podejścia</th>
                                    </thead></tr><tbody>';

                                while ($row1 = mysqli_fetch_assoc($res1)) {
                                    if ($row1['is_open'] == 1 && empty($row1['closed'])) {
                                        $open = "Otwarta";
                                    } else if (empty($row1['closed'])) {
                                        $open = "Zamknięta";
                                    } else {
                                        $open = "Zakończona";
                                    }

                                    echo '<tr>
                                    <td>'.$row1['test'].'</td>
                                    <td>'.$row1['code'].'</td>
                                    <td>'.$open.'</td>
                                    <td>'.$row1['part'].'</td>
                                    </tr>';
                                }
                                echo '</tbody></table>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>