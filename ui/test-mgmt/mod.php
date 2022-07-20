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

    $notice = $notice_class = "";

    $link = dbConnect();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['name'])) {
            $name = argStrip($_POST['name']);
            
            $res = $link->query("SELECT MAX(id) FROM module");
            
            $id = $res->fetch_array()[0] + 1;
            
            $sql = $link->prepare("INSERT INTO module VALUES (?, ?)");
            $sql->bind_param('is', $id, $name);
            $sql->execute();
    
            $_SESSION['module_id'] = $sql->insert_id;

            $sql->close();
            
            $_SESSION['notice'] = "s-Pomyślnie dodano moduł!";
            $_POST = array();
            header("location: test.php");
            exit;
            
        } else if(!empty($_POST['id'])) {
            $_SESSION['module_id'] = argStrip($_POST['id']);
            $_SESSION['notice'] = "s-Pomyślnie załadowano moduł!";
            header("location: test.php");
            exit;
            
        } else {
            $notice = "e-Musisz wybrać moduł!";
        }
    }

    showNot();
    
    $result = $link->query("SELECT * FROM module");

    $link->close();
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Wybór modułu</title>
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
            <div class="container">
                <form method="post">
                    <h2 class="my-5">Wybierz moduł do modyfikacji...</h2>
                    <div class="form-group">
                        <select name="id" class="form-control">
                            <option value="">--Wybierz opcję--</option>
                            <?php
                                if($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <h2 class="my-5">...lub stwórz nowy.</h2>
                    <div class="form-group">
                        <label for="#name">Nazwa modułu:</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>      
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Zatwierdź">
                    </div>
                </form>
                <?php echo "<p id=\"notice\" class=\"${notice_class}\">${notice}</p>"; ?>
            </div>
        </div>
        
    </body>
</html>