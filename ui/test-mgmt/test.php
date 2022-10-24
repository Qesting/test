<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $notice = $notice_class = "";

    $link = dbConnect();
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['name'])) {
            $mid = $_SESSION['module_id'];
            $name = argStrip($_POST['name']);
            $uid = $_SESSION['id'];
            
            $test = test::set($mid, $name, $uid);
            $_SESSION['edit_test'] = serialize($test);
            
            $_SESSION['notice'] = "s-Pomyślnie dodano test!";
            
            $_POST = array();

            header("location: quest.php");
            exit;
        } else if (!empty($_POST['id'])) {
            $tid = argStrip($_POST['id']);

            $sql = $link->prepare("SELECT owner FROM test WHERE id=?");
            $sql->bind_param('i', $tid);
            $sql->execute();
            $data = $sql->get_result()->fetch_assoc();

            $sql->close();

            $test = test::get($tid);
            
            if ($data['owner'] != $_SESSION['id'] && $_SESSION['priv'] != 3) {
                $_SESSION['notice'] = "e-Tylko właściciel testu może go modyfikować.";
            } else {
                $_SESSION['notice'] = "s-Pomyślnie załadowano test!";

                $_SESSION['edit_test'] = serialize($test);
                
                header("location: quest.php");
                exit;
            }
        } else {
            $notice = "e-Musisz wybrać test!";
        } 
    }

    showNot();

    $sql = $link->prepare("SELECT name FROM module WHERE id=?");
    $sql->bind_param('i', $_SESSION['module_id']);
    $sql->execute();
    $res = $sql->get_result();

    $sql->close();

    $data = $res->fetch_assoc();
    $_SESSION['module_name'] = $data['name'];

    $sql = $link->prepare("SELECT * FROM test WHERE module_id=?");
    $sql->bind_param('i', $_SESSION['module_id']);
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
        <title>Wybór testu</title>
        <link rel="stylesheet" href="../../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
            #test {
                margin-left: auto;
                margin-right: auto;
            }
            @media screen and (min-width: 768px) {
                #test {
                    width: 80%;
                }
            }
            @media screen and (min-width: 992px) {
                #test {
                    width: 60%;
                }
            }
        </style>
    </head>
    <body id="body">
        <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
            <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='mod.php'><i class='bi-card-list'></i> Moduł</a>
                        <a class="nav-item nav-link active" href='../userpage.php'><i class='bi-person-circle'></i> Strona użytkownika</a>
                        <a class="nav-item nav-link active" href='/index.php'><i class='bi-house-fill'></i> Strona główna</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <h1 class="mt-5 mb-3">Wybrany moduł: <?php echo $_SESSION['module_name']; ?></h1>
            <button class="btn btn-danger mb-2" id="del"><i class='bi-trash'></i> Usuń bieżący moduł</button>
            <div class="container">
                <h2 class="my-3">Wybierz test do modyfikacji...</h2>
                <form method="post" id='test'>
                    <div class="form-group">
                        <select name="id" class="form-control">
                            <option value="">--Wybierz opcję--</option>
                            <?php
                                if($res->num_rows > 0) {
                                    while ($row = $res->fetch_assoc()) {
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
                    <button type="submit" class="btn btn-primary"><i class='bi-check'></i> Wybierz</button>
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
                    notice.textContent = "Operacja anulowana przez użytkownika";
                    notice.classList.add("alert", "alert-warning");
                }
            }
        </script>
    </body>
</html>
