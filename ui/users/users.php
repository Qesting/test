<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once("../../config.php");

    $id = $_SESSION['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $uid = argStrip($_POST['uid']);
        $new_perm = argStrip($_POST['new_perm']);

        if (is_numeric($uid) && is_numeric($new_perm)) {
            $sql = mysqli_prepare($link, "UPDATE users SET priv=? WHERE id=?");
            mysqli_stmt_bind_param($sql, 'ii', $new_perm, $uid);
            mysqli_stmt_execute($sql);

            if(mysqli_stmt_affected_rows($sql) == 0) {
                $_SESSION['notice'] = "w-Uprawnienia użytkownika nie uległy zmianie.";
            } else {
                $_SESSION['notice'] = "s-Pomyślnie zmieniono uprawnienia użytkownika!";
            }
        } else {
            $_SESSION['notice'] = "e-Wystąpił błąd. Spróbuj ponownie później.";
        }

        header("location: users.php");
        exit;
    }

    $sql = mysqli_prepare($link,"SELECT id, username, created_at, priv FROM users WHERE id!=?");
    mysqli_stmt_bind_param($sql, 'i', $id);
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
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <h1 class="mt-5">Zarządzanie użytkownikami</h1>
            <div class="container">
                <div class="container mt-3"><p <?php echo $_SESSION['error_class']; ?>><?php echo $_SESSION['error']; unset($_SESSION['error'], $_SESSION['error_class']); ?></p></div>
                <div class="card card-body mt-3">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nazwa</th>
                                <th>Data utworzenia</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>    
                        <tbody>
                        <?php
                            while ($row = mysqli_fetch_assoc($res)) {
                                if ($row['priv'] == 0) {
                                    $sel1 = "selected";
                                    $sel2 = "";
                                    $sel3 = "";
                                } else if ($row['priv'] == 1) {
                                    $sel1 = "";
                                    $sel2 = "selected";
                                    $sel3 = "";
                                } else if ($row['priv'] == 2) {
                                    $sel1 = "";
                                    $sel2 = "";
                                    $sel3 = "selected";
                                }


                                echo '<tr><form action="usermod.php" method="post">
                                <td>'.$row['username'].'</td>
                                <td>'.$row['created_at'].'</td>
                                <td>
                                <input type="hidden" name="uid" value="'.$row['id'].'">
                                <div class="btn-group">
                                <select name="new_perm" class="form-control">
                                <option value="0" '.$sel1.'>Oczekujący</option>
                                <option value="1" '.$sel2.'>Użytkownik</option>
                                <option value="2" '.$sel3.'>Administrator</option>
                                </select>
                                <input type="submit" value="Zatwierdź" class="btn btn-outline-primary btn-sm">
                                </div></td>
                                </form></tr>';
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>