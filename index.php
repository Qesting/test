<?php
    session_start();
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    require_once('config/config.php');

    if (isset($_SESSION['question'])) {
        require_once("tests/unset.php");
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['test'])) {

        $testArgs = "t=${_GET['test']}";
        $testArgs .= (isset($_GET['s'])) ? "&s=${_GET['s']}" : "";
        header("location: ./tests/start.php?${testArgs}");
        exit;

    }

    $link2 = dbConnect();

    $result = $link2->query("SELECT * FROM quotes order by RAND() limit 1");
    $res = $result->fetch_array();
    $q = $res[1];
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
                        <?php
                            if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
                                echo "<a class='nav-item nav-link active' href='ui/login.php'>Zaloguj się</a>
                                <a class='nav-item nav-link active' href='ui/register.php'>Zarejestruj się</a>";
                            } else {
                                echo "<span class='nav-item nav-link active'>Witaj, <b>${_SESSION['username']}</b></span>
                                <a class='nav-item nav-link active' href='ui/userpage.php'><u>Strona użytkownika</u></u></a>";
                            }
                        ?>
                    </div>
                </div>
                <div class="text-right pl-3"></div>
            </div>
            </div>
        </nav>
        <div class="wrapper">
            <h2 class="my-5">TESTOPOL</h2>
            <p>Witamy w systemie testów TESTOPOL.</p>
            <p>Wybierz test</p>
            <div class="container-fluid">
            <?php
                $res1 = $link2->query("SELECT * FROM module");

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
         <script>
            var qv = false;
            $(".navbar-brand").css('cursor', 'pointer');
            $(".navbar-brand").click(function() {
                if (!qv) {
                    qv = true;
                    $(".navbar-brand").css('cursor', 'auto');

                    $("<div class='container'><p class='alert alert-info' id='q'></p></div>").insertBefore(".wrapper > :first-child");
                
                    $("#q").text('<?php echo $q; ?>');
                    $("#q").delay(2000).fadeOut(400);
                    
                    setTimeout(function() {
                        qv = false;
                        $("#q").remove();
                        $(".navbar-brand").css('cursor', 'pointer');
                    }, 2500);
                }
            });
         </script>
    </body>
</html>