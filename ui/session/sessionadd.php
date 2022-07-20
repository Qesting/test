<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $code = "";
    $owner = $_SESSION['id'];

    $notice = $notice_class = "";

    $link = dbConnect();

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if ($_POST['test'] != 0) {
            $exp = "/[0-9]{6}/";
            if (preg_match($exp, $_POST['code'])) {
                $code = argStrip($_POST['code']);
                $test = argStrip($_POST['test']);
            } else {
                $notice = "Kod nie zgadza się ze wzorem!";
            }
    
            $cl = (empty($_POST['cl'])) ? 0 : 1;
    
            $sql = $link->prepare("INSERT INTO session (code, test_id, can_laa, owner) VALUES (?, ?, ?, ?)");
            $sql->bind_param('siii', $code, $test, $cl, $owner);
            $sql->execute();
            if(empty($sql->error)) {
                $_SESSION['notice'] = "Pomyślnie dodano sesję!";
                header("location: sessionadd.php");
                exit;
            } else {
                $notice = "Wystąpił niespodziewany błąd.";
            }
        } else {
            $notice = "Proszę wybrać test!";
        }
    }

    do {
        for ($j = 0; $j < 6; $j++) {
            $code .= rand(0, 9);
        }

        $sql = $link->prepare("SELECT id FROM session WHERE code=?");
        $sql->bind_param('s', $code);
        $sql->execute();
        $num = $sql->num_rows;

        $num = (empty($num)) ? 0 : $num;
    } while ($num != 0);

    $sql = $link->query("SELECT test.id, module.name, test.name FROM test, module WHERE module.id=test.module_id");

    showNot();
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dodaj sesję</title>
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
                <h2 class="my-5">Tutaj możesz dodać sesję</h2>
                <form method="post" name="sessform">
                    <div class="form-group">
                        <label for="code" class="form-label">Kod sesji (unikalny, wybrany automatycznie)</label>
                        <input id="code" class="form-control" type="text" name="code" value="<?php echo $code; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="testsel" class="form-label">Wybierz test, którego sesja ma dotyczyć</label>
                        <select id="testsel" name="test" class="form-control">
                            <option value="0" selected>--Wybierz opcję--</option>
                            <?php 
                                while ($row = $res->fetch_array()) {
                                    echo '<option value="'.$row[0].'">'.$row[1].' - '.$row[2].'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="cl" value="1" id="cl">
                        <label for="cl" class="form-label">Czy można podejrzeć odpowiedzi po zakończeniu testu?</label>
                    </div>
                    <div class="form-group">
                        <div class="btn-group">
                            <input type="button" onclick="window.location.href='sessions.php'" class="btn btn-secondary" value="Powrót">
                            <input type="submit" class="btn btn-primary" value="Stwórz sesję">
                        </div>
                    </div>
                </form>
                <?php echo "<p id=\"notice\" class=\"${notice_class}\">${notice}</p>"; ?>
            </div>
        </div>
    </body>
</html>