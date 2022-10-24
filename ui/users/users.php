<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $id = $_SESSION['id'];

    $link = dbConnect();

    $notice = $notice_class = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $uid = argStrip($_POST['uid']);
        $new_perm = argStrip($_POST['new_perm']);

        if (is_numeric($uid) && is_numeric($new_perm)) {
            $sql = $link->prepare($link, "UPDATE users SET priv=? WHERE id=?");
            $sql->bind_param('ii', $new_perm, $uid);
            $sql->execute();

            if($sql->affected_rows == 0) {
                $_SESSION['notice'] = "w-Uprawnienia użytkownika nie uległy zmianie.";
            } else {
                $_SESSION['notice'] = "s-Pomyślnie zmieniono uprawnienia użytkownika!";
            }

            $sql->close();
        } else {
            $_SESSION['notice'] = "e-Wystąpił błąd. Spróbuj ponownie później.";
        }

        header("location: users.php");
        exit;
    }

    showNot();

    $sql = $link->prepare("SELECT id, username, created_at, priv FROM users WHERE id!=?");
    $sql->bind_param('i', $id);
    $sql->execute();
    $res = $sql->get_result();
    $sql->close();
    $link->close();
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Zarządzanie użytkownikami</title>
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
                        <a class="nav-item nav-link active" href='../userpage.php'><i class='bi-person-circle'></i> Strona użytkownika</a>
                        <a class="nav-item nav-link active" href='/index.php'><i class='bi-house-fill'></i> Strona główna</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <h1 class="mt-5">Zarządzanie użytkownikami</h1>
            <div class="container">
                <div class="container mt-3"><?php echo "<p id=\"notice\" class=\"${notice_class}\">${notice}</p>"; ?></div>
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
                            while ($row = $res->fetch_assoc()) {
                                if ($row['priv'] == 0) {
                                    $sel1 = "selected";
                                    $sel2 = "";
                                    $sel3 = "";
                                    $sel4 = "";
                                } else if ($row['priv'] == 1) {
                                    $sel1 = "";
                                    $sel2 = "selected";
                                    $sel3 = "";
                                    $sel4 = "";
                                } else if ($row['priv'] == 2) {
                                    $sel1 = "";
                                    $sel2 = "";
                                    $sel3 = "selected";
                                    $sel4 = "";
                                } else if ($row['priv'] == 3) {
                                    $sel1 = "";
                                    $sel2 = "";
                                    $sel3 = "";
                                    $sel4 = "selected";
                                }


                                echo '<tr>
                                <td>'.$row['username'].'</td>
                                <td>'.$row['created_at'].'</td>
                                <td>
                                <form method="post">
                                <input type="hidden" name="uid" value="'.$row['id'].'">
                                <div class="btn-group">
                                <select name="new_perm" class="form-control">
                                <option value="0" '.$sel1.'>Oczekujący</option>
                                <option value="1" '.$sel2.'>Piszący</option>
                                <option value="2" '.$sel3.'>Użytkownik</option>
                                <option value="3" '.$sel4.'>Administrator</option>
                                </select>
                                <button type="submit" class="btn btn-outline-primary btn-sm">Zatwierdź</button>
                                </div></form></td>
                                </tr>';
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>