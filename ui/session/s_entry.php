<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once("../../config.php"); 

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['session'])) {
            $sid = $_POST['session'];
            $_POST = array();
        }
    }
    
    $sql = mysqli_prepare($link, "SELECT code, is_open, closed FROM session WHERE id=?");
    mysqli_stmt_bind_param($sql, 'i', $sid);
    mysqli_stmt_execute($sql);
    $res = mysqli_stmt_get_result($sql);
    $data = mysqli_fetch_assoc($res);

    if ($data['is_open'] == 0 && !empty($data['closed'])) {
        $status = "Zakończona";
    } else if ($data['is_open'] == 0) {
        $status = "Zamknięta";
    } else {
        $status = "Otwarta";
    }

    $sql1 = mysqli_prepare($link, "SELECT s_entry.ent_id, s_entry.name, s_entry.class, s_entry.perc, grade.str AS grade FROM s_entry, grade WHERE grade.id=s_entry.grade AND s_entry.session_id=?");
    mysqli_stmt_bind_param($sql1, 'i', $sid);
    mysqli_stmt_execute($sql1);
    $res1 = mysqli_stmt_get_result($sql1);

?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Podgląd sesji</title>
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
                        <a class="nav-item nav-link active" href='sessionlist.php'>Powrót do listy sesji</a>
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container">
                <h2 class="my-5">Lista wyników dla sesji <?php echo $data['code'].' ('.$status.')'; ?></h2>
                <table class="table" id="scores">
                    <thead>
                        <th>L.p.</th>
                        <th>Imię i nazwisko</th>
                        <th>Klasa</th>
                        <th>Wynik (%)</th>
                        <th>Ocena</th>
                    </thead>
                    <tbody>
                        <?php
                            while ($row = mysqli_fetch_assoc($res1)) {
                                echo '<tr>
                                    <td>'.$row['ent_id'].'</td>
                                    <td>'.$row['name'].'</td>
                                    <td>'.$row['class'].'</td>
                                    <td>'.$row['perc'].'</td>
                                    <td>'.$row['grade'].'</td>
                                    <td>'.$part.'</td></tr>';

                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>