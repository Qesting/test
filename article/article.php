<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('../config/config.php');
    require_once('../config/classArticle.php');

    session_start();

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
        try {
            $art = Article::getById((int) $_GET['id'])->parse();
        } catch (Exception $e) {
            header("location: .");
            exit;
        }
    } else {
        header("location: .");
        exit;
    }
?>

<!DOCTYPE html>
<html lang='pl-PL'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $art->title ?> | Materiały testopol</title>
        <link rel="stylesheet" href="../style/main.css">
        <style>
            ul {
                list-style-type: none;
            }
            body {
                text-align: center;
                font-size: 14px;
            }
            .card-body > :first-child {
            }
            img {
                margin-right: 2rem;
                width: 30%;
                float: left;
                margin-bottom: 1rem;
            }
            hr {
                clear: both;
            }
            .card-body > :first-child div {
                margin-right: 2rem;
            }
            .card-body {
                text-align: left;
                margin-left: 2rem;
                margin-right: 2rem;
            }
        </style>
    </head>
    <body class="bootstrap-dark">
        <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class='nav-item nav-link active' href='..'>
                            <i class='bi-house-fill'></i> Strona główna
                        </a>
                        <a class='nav-item nav-link active' href='.'>
                            <i class='bi-journal-text'></i> Materiały
                        </a>
                        <?php if(!isset($_SESSION["loggedin"]) || !$_SESSION["loggedin"]): ?>
                            <a class='nav-item nav-link active' href='../ui/login.php'>
                                <i class='bi-person-check-fill'></i> Zaloguj się
                            </a>
                            <a class='nav-item nav-link active' href='../ui/register.php'>
                                <i class='bi-person-plus-fill'></i> Zarejestruj się
                            </a>
                        <?php else: ?>
                            <span class='nav-item nav-link active'>Witaj, <b><?php echo $_SESSION['username'] ?></b></span>
                            <a class='nav-item nav-link active' href='../ui/userpage.php'>
                                <i class='bi-person-circle'></i> <u>Strona użytkownika</u>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right pl-3"></div>
            </div>
            </div>
        </nav>
        <div class="wrapper mt-5">
            <div class='container'>
                <div class='card'>
                    <div class='card-header'>
                        <h1><?php echo $art->title ?></h1>
                        <p class='text-dark'>
                            <b><?php echo $art->publicationDate ?></b> przez <b><?php echo $art->author ?></b>
                        </p>
                    </div>
                    <div class='card-body'>
                        <div>
                            <?php if (!is_null($art->thumbnail)): ?>
                                <img src='<?php echo $art->thumbnail ?>' class='rounded' />
                            <?php endif; ?>
                            <div>
                                <?php echo $art->summary ?>
                            </div>
                        </div>
                        <hr />
                        <div>
                            <?php echo $art->content ?>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </body>
</html>