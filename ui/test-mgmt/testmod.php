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

    $id = $_SESSION['test_id'];
    $cl = $ct = "";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['save'])) {
            $name = argStrip($_POST['name']);

            if (!empty($_POST['ct'])) {
                $ct = argStrip($_POST['ct']);
            } else {
                $ct = 0;
            }
            if (!empty($_POST['cl'])) {
                $cl = argStrip($_POST['cl']);
            } else {
                $cl = 0;
            }

            $time = argStrip($_POST['time']);

            $sql = mysqli_prepare($link, "UPDATE test SET name=?, can_take=?, can_laa=?, time=? WHERE id=?");
            mysqli_stmt_bind_param($sql, 'siiii', $name, $ct, $cl, $time, $id);
            mysqli_stmt_execute($sql);
            header("location: ${_SERVER['PHP_SELF']}");
        }
    }

    $sql = mysqli_prepare($link, "SELECT can_take, can_laa, time FROM test WHERE id=?");
    mysqli_stmt_bind_param($sql, 'i', $id);
    mysqli_stmt_execute($sql);
    $res = mysqli_stmt_get_result($sql);
    $res = mysqli_fetch_assoc($res);

    if ($res['can_take'] == 1) {
        $ct = "checked";
    }
    if ($res['can_laa'] == 1) {
        $cl = "checked";
    }

    $time = $res['time'];
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Modyfikacja testu</title>
        <link rel="stylesheet" href="../../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
            body > .wrapper form input.form-control[type="text"] {
                text-align: center;
            }
        </style>
    </head>
    <body>
    <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
            <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='quest.php'>Powrót do modyfikacji pytań</a>
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <h1 class="my-5">Wybrany test: <?php echo $_SESSION['test_name']; ?></h1>
            <div class="container">
                <form method="post">
                    <h2 class="my-5">Zmień właściwości testu:</h2>
                    <div class="form-group">
                        <label for="name">Nazwa testu:</label>
                        <input type="text" class="form-control" name="name" value="<?php echo $_SESSION['test_name']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="time">Ilość czasu/pytanie (sekundy)</label>
                        <input type="number" name="time" id="time" class="form-control" max="60" min="30" value='<?php echo $time; ?>'>                        
                    </div>
                    <div class="form-group">
                        <input name="ct" value="1" type="checkbox" id="ct" <?php echo $ct; ?> class="">
                        <label for="ct">Czy można podchodzić do testu poza sesjami?</label>
                    </div>
                    <div class="form-group">
                        <input name="cl" value="1" type="checkbox" id="cl" <?php echo $cl; ?> class="">
                        <label for="cl">Czy można podglądać odpowiedzi po zakończeniu testu?</label>
                    </div>
                    </div>      
                    <div class="form-group">
                        <input type="submit" name="save" class="btn btn-primary" value="Zatwierdź">
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>