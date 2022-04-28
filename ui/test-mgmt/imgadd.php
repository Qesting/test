<?php

    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once("../../config.php");

    $notice = $notice_class = "";

    function fUp() {
        // weryfikacja nazwy

        global $notice;
        global $link;
        global $id;

        $dir = "../../usermedia/";
        $fup = $_FILES['img']['name'];
        $ext = pathinfo($fup, PATHINFO_EXTENSION);
    
        if(!empty(pathinfo($fup, PATHINFO_FILENAME))) {
            if (!empty($_POST['fname'])) {
                $new_fname = trim(htmlspecialchars($_POST['fname']));
            } else {
                $new_fname = pathinfo($fup, PATHINFO_FILENAME);
            }
        } else {
            $notice = "e-Musisz wybrać plik!";
            return false;
            $_POST = array();
        }
        
    
        // składanie nazwy pliku
        
        $file = $new_fname.'.'.$ext;
    
        // czy plik istnieje
    
        if (file_exists("./../../media/".$file)) {
            $notice = "e-Plik o podanej nazwie i typie już istnieje!";
            return false;
        }
    
        // czy plik nie jest za duży
    
        if($_FILES['img']['size'] > 1000000) {
            $notice = "e-Maksymalny rozmiar pliku wynosi 1 MiB!";
            return false;
        }
    
        $exp = "/jpg|jpeg|png/";
        if(!preg_match($exp, $ext)) {
            $notice = "e-Plik nie ma właściwego formatu!";
            return false;
        }
    
        if (copy($_FILES['img']['tmp_name'], "./../../media/".$file)) {
        
            $sql = mysqli_prepare($link, "UPDATE question SET img_path=? WHERE id=?");
            mysqli_stmt_bind_param($sql, 'si', $file, $id);
            mysqli_stmt_execute($sql);
    
            if(empty(mysqli_stmt_error($sql))) {
                $notice = "s-Plik został przekazany na serwer!";
                return true;
            } else {
                $notice = "e-Wystąpił błąd, spróbuj ponownie później.";
                return false;
            }
        } else {
            $notice = "e-Wystąpił błąd, spróbuj ponownie później.";
            return false;
        }
    }
    
    $id = $_SESSION['qid'];

    if ($_SERVER['REQUEST_METHOD'] == "POST") {


        if (isset($_POST['imgAdd'])) {
            if(fUp()) {
                $_SESSION['notice'] = $notice;
                //header("location: ${_SERVER['PHP_SELF']}");
            } 
        } else if (isset($_POST['imgDel'])) {

            $sql = mysqli_prepare($link,"SELECT img_path FROM question WHERE id=?");
            mysqli_stmt_bind_param($sql, 'i', $id);
            mysqli_stmt_execute($sql);
            $res = mysqli_stmt_get_result($sql);
            $data = mysqli_fetch_array($res);
            $file = $data[0];

            unlink("./../../media/${file}");

            $sql = mysqli_prepare($link, "UPDATE question SET img_path=NULL WHERE id=?");
            mysqli_stmt_bind_param($sql, 'i', $qid);
            mysqli_stmt_execute($sql);
            header("location: ${_SERVER['PHP_SELF']}");
        }
    }

    showNot();

    $sql = mysqli_prepare($link, "SELECT img_path FROM question WHERE id=?");
    mysqli_stmt_bind_param($sql, 'i', $id);
    mysqli_stmt_execute($sql);
    $res = mysqli_stmt_get_result($sql);
    $data = mysqli_fetch_array($res);

    $ex = file_exists("./../../media/".$data['img_path']);

?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dodawanie obrazu</title>
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
                        <a class="nav-item nav-link active" href='quest.php'>Powrót do modyfikacji pytania</a>
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container">
                <h2 class="my-5">Dodawanie obrazu</h2>
                <?php 
                    if (empty($data['img_path']) || $ex === false) {
                        echo '<form method="post" enctype="multipart/form-data">

                        <div class="form-group">
                            <label for="fname" class="form-label">Nazwa dla obrazu</label>
                            <input id="fname" type="text" name="fname" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="file" class="form-label">Obraz do przesłania</label>
                            <input style="text-align: center;" name="img" type="file" id="file" class="form-control" accept="image/*">
                        </div>
                        <div class="form-group btn-group">
                            <input type="submit" name="imgAdd" value="Zatwierdź" class="btn btn-primary">
                        </div>
                        </form>';
                    } else {
                        echo '<div class="card card-body">
                        <form method="post">
                        <h4 class="my-5">To pytanie zawiera już obraz</h4>
                        <input type="submit" name="imgDel" value="Usuń obraz" class="btn btn-danger">
                        </form>
                        </div>';
                    }
                ?>
                <?php echo "<p id=\"notice\" class=\"${notice_class}\">${notice}</p>"; ?>
            </div>
        </div>
    </body>
</html>
