<?php
    require_once('config.php');


    /*$sql = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($sql, 'iii', $module, $test, $question);

    $module = $_SESSION['module'];
    $test = $_SESSION['test'];
    $question = $_SESSION['question'];

    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    $data = mysqli_fetch_assoc($result);
    echo $data['content'];*/


?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Indeks</title>
        <link rel="stylesheet" href="style/main.css">
        <style>
            ul {
                list-style-type: none;
            }
            body{ font: 14px sans-serif; text-align: center;}
        </style>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    </head>
    <body class="bootstrap-dark">
        <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='ui/login.php'>Zaloguj się</a>
                        <a class="nav-item nav-link active" href='ui/register.php'>Zarejestruj się</a>
                        
                    </div>
                </div>
                <div class="text-right pl-3"><?php require_once("losu2.php"); ?></div>
            </div>
            </div>
        </nav>
        <div class="wrapper">
            <h2 class="my-5">TESTOPOL</h2>
            <p>Witamy w systemie testów TESTOPOL.</p>
            <p>Wybierz test</p>
            <div class="container-fluid">
            <?php
                $sql1 = "SELECT * FROM module";
                $res1 = mysqli_query($link, $sql1);

                $sql = mysqli_prepare($link, "SELECT id, name FROM test WHERE module_id=?");

                while ($row1 = mysqli_fetch_assoc($res1)) {
                    if (($row1['id']%3) == 1) {
                        echo '<div class="row">';
                    }
                    echo '<div class="col-md-4 mb-3">';
                    echo '<button class="btn btn-primary btn-lg btn-block" data-toggle="collapse" data-target="#mod'.$row1['id'].'" aria-expanded="false" aria-controls="collapseExample">'.$row1['name'].'</button>';

                    $mid = $row1['id'];
                    mysqli_stmt_bind_param($sql, 'i', $mid);
                    mysqli_stmt_execute($sql);
                    $res2 = mysqli_stmt_get_result($sql);

                    echo '<ul id="mod'.$row1['id'].'" class="collapse card card-body">';
                    while ($row2 = mysqli_fetch_assoc($res2)) {
                        echo "<li class=\"card card-body mb-2\">
                        <p>${row2['name']}</p>
                            <a href='tests/start.php?t=${row2['id']}' class='btn btn-secondary btn-sm'>Start</a>
                        </li>";
                    }

                    echo "</ul></div>";

                    if (($row1['id']%3) == 0) {
                        echo '</div>';
                    }
                }
                echo '<div class="row"></div>';

                mysqli_stmt_close($sql);
                mysqli_close($link);
            ?>
            </div>
            
        </div>
         
    </body>
</html>