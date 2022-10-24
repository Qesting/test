<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['session'])) {
            $sid = $_POST['session'];
            $_POST = array();
        }
    }

    $link = dbConnect();
    
    $sql = $link->prepare("SELECT code, is_open, closed FROM session WHERE id=?");
    $sql->bind_param('i', $sid);
    $sql->execute();;
    $res = $sql->get_result();

    $sql->close();

    $data = $res->fetch_assoc();

    if ($data['is_open'] == 0 && !empty($data['closed'])) {
        $status = "Zakończona";
    } else if ($data['is_open'] == 0) {
        $status = "Zamknięta";
    } else {
        $status = "Otwarta";
    }

    $sql = $link->prepare("SELECT s_entry.ent_id, s_entry.name, s_entry.class, s_entry.perc, grade.str AS grade FROM s_entry, grade WHERE grade.id=s_entry.grade AND s_entry.session_id=?");
    $sql->bind_param('i', $sid);
    $sql->execute();
    $res1 = $sql->get_result();

    $sql->close();
    $link->close();

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
                        <a class="nav-item nav-link active" href='sessionlist.php'><i class='bi-journal-list'></i> Test</a>
                        <a class="nav-item nav-link active" href='../userpage.php'><i class='bi-person-circle'></i> Strona użytkownika</a>
                        <a class="nav-item nav-link active" href='/index.php'><i class='bi-house-fill'></i> Strona główna</a>
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
                            while ($row = $res1->fetch_assoc()) {
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