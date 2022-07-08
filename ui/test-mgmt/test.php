<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $notice = $notice_class = "";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['name'])) {
            $sql = "SELECT MAX(id) FROM test";
            $res = mysqli_query($link, $sql);
            $data = mysqli_fetch_array($res);
            
            $id = $data[0] + 1;
            $mid = $_SESSION['module_id'];
            $name = argStrip($_POST['name']);
            $uid = $_SESSION['id'];
            
            $qry = "INSERT INTO test (id, module_id, name, owner) VALUES (?, ?, ?, ?)";
            $sql = mysqli_prepare($link, $qry);
            mysqli_stmt_bind_param($sql, 'iisi', $id, $mid, $name, $uid);
            mysqli_stmt_execute($sql);
    
            $sql = "SELECT MAX(id) FROM test";
            $res = mysqli_query($link, $sql);
            $data = mysqli_fetch_array($res);
    
            $_SESSION['test_id'] = $data[0];
            
            $_SESSION['notice'] = "s-Pomyślnie dodano test!";
            
            $_POST = [];

            $sql1 = "SELECT name, owner FROM test WHERE id=".$_SESSION['test_id'];
            $res1 = mysqli_query($link, $sql1);
            $data = mysqli_fetch_assoc($res1);
            
            $_SESSION['test_name'] = $data['name'];
            header("location: quest.php");
            exit;
        } else if (!empty($_POST['id'])) {
            $_SESSION['test_id'] = argStrip($_POST['id']);

            $sql1 = "SELECT name, owner FROM test WHERE id=".$_SESSION['test_id'];
            $res1 = mysqli_query($link, $sql1);
            $data = mysqli_fetch_assoc($res1);
            
            if ($data['owner'] != $_SESSION['id'] && $_SESSION['priv'] != 2) {
                unset($_SESSION['test_id']);
                $_SESSION['error'] = "e-Tylko właściciel testu może go modyfikować.";
                $_SESSION['error_class'] = ' class="alert alert-danger"';
            } else {
                $_SESSION['test_name'] = $data['name'];
                $_SESSION['notice'] = "s-Pomyślnie załadowano test!";
                header("location: quest.php");
                exit;
            }
        } else {
            $notice = "e-Musisz wybrać test!";
        } 
    }

    
    showNot();

    $sql = "SELECT name FROM module WHERE id=".$_SESSION['module_id'];
    $res = mysqli_query($link, $sql);
    $data = mysqli_fetch_assoc($res);
    $_SESSION['module_name'] = $data['name'];

    $sql1 = "SELECT * FROM test WHERE module_id=".$_SESSION['module_id'];
    $res1 = mysqli_query($link, $sql1);
    
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Wybór testu</title>
        <link rel="stylesheet" href="../../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
        </style>
    </head>
    <body id="body">
        <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
            <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='mod.php'>Powrót do wyboru modułu</a>
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <h1 class="my-5">Wybrany moduł: <?php echo $_SESSION['module_name']; ?></h1>
            <button class="btn btn-danger" id="del">Usuń bieżący moduł</button>
            <div class="container">
                <form method="post">
                    <h2 class="my-3">Wybierz test do modyfikacji...</h2>
                    <div class="form-group">
                        <select name="id" class="form-control">
                            <option value="">--Wybierz opcję--</option>
                            <?php
                                if(mysqli_num_rows($res1) > 0) {
                                    while ($row = mysqli_fetch_assoc($res1)) {
                                        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <h2 class="my-5">...lub stwórz nowy.</h2>
                    <div class="form-group">
                    <input type="text" class="form-control" id="name" name="name">
                    </div>      
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Zatwierdź">
                    </div>
                </form>
                <?php echo "<p id=\"notice\" class=\"${notice_class}\">${notice}</p>"; ?>
            </div>
        </div>
        <script>
            let del = document.getElementById("del");
            let notice = document.getElementById("notice");

            del.addEventListener("click", confirmDel);

            function confirmDel() {
                if (window.confirm("Usunięcie modułu usunie również wszystkie należące do niego testy.\nCzy chcesz kontynuować?")) {
                    window.location.replace("moddel.php");
                } else {
                    notice.innerHTML = "Operacja anulowana przez użytkownika";
                    notice.classList.add("alert", "alert-warning");
                }
            }
        </script>
    </body>
</html>
