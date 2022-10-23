<?php
    session_start();
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    require_once('../config/config.php');

    if (isset($_SESSION['question'])) {
        require_once("unset.php");
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['test'])) {

        $testArgs = "t=${_GET['test']}";
        $testArgs .= (isset($_GET['s'])) ? "&s=${_GET['s']}" : "";
        header("location: start.php?${testArgs}");
        exit;

    }

    $link = dbConnect();
?>

<!DOCTYPE html>
<html lang='pl-PL'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Indeks</title>
        <link rel="stylesheet" href="../style/main.css">
        <style>
            ul {
                list-style-type: none;
            }
            .container > .container-fluid {margin-top: 50px;}
            body{ font: 14px sans-serif; text-align: center;}
            .row {margin-bottom: 2rem}
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
                        <a class='nav-item nav-link active' href='..'>Do strony głównej</a>
                        <?php if(!isset($_SESSION["loggedin"]) || !$_SESSION["loggedin"]): ?>
                            <a class='nav-item nav-link active' href='../ui/login.php'>Zaloguj się</a>
                            <a class='nav-item nav-link active' href='../ui/register.php'>Zarejestruj się</a>
                        <?php else: ?>
                            <span class='nav-item nav-link active'>Witaj, <b><?php echo $_SESSION['username'] ?></b></span>
                            <a class='nav-item nav-link active' href='../ui/userpage.php'><u>Strona użytkownika</u></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right pl-3"></div>
            </div>
            </div>
        </nav>
        <div class="wrapper mt-5">
            <div class='container'>
                <h1>Wybierz test</h1>
                <a class='text-dark' href='../ui/userpage.php'>Lub stwórz własny</a>
                <div class="container-fluid">
                    <?php 
                    $mod = $link->query("SELECT * FROM module");
                    $stmt = $link->prepare("SELECT id, name FROM test WHERE module_id=?");
                    while($row = $mod->fetch_assoc()): 
                        if ($row['id']%3 === 1): ?>
                            <div class="row">
                        <?php endif; ?>
                        <div class='col-md-4 mb-3'>
                            <button class='btn btn-primary btn-lg btn-block' data-toggle='collapse' data-target='#mod<?php echo $row['id']; ?>' aria-expanded='false' aria-controls='collapseExample'><?php echo $row['name']; ?></button>
                            <ul id='mod<?php echo $row['id']; ?>' class='collapse card card-body'>
                            <?php
                            $stmt->bind_param('i', $row['id']);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row1 = $res->fetch_assoc()): ?>
                                <li class='card card-body mb-2'>
                                    <p><?php echo $row1['name']; ?></p>
                                    <a href='start.php?t=<?php echo $row1['id']; ?>' class='btn btn-secondary btn-sm'>Start</a>
                                </li>
                            <?php endwhile; ?>
                            </ul></div>
                        <?php if ($row['id']%3 === 0): ?>
                        </div>
                        <?php endif; ?>
                    <?php endwhile; 
                    $stmt->close();
                    $link->close();
                    ?>
                    <div class='row'></div>
                </div>
            </div>            
        </div>
    </body>
</html>