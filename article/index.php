<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('../config/config.php');
    require_once('../config/classArticle.php');

    session_start();

    $p = 0;
    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['p'])) $p = $_GET['p'];
    $arts = Article::getList(10, $p);
?>

<!DOCTYPE html>
<html lang='pl-PL'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Materiały testopol</title>
        <link rel="stylesheet" href="../style/main.css">
        <style>
            ul {
                list-style-type: none;
            }
            body {
                text-align: center;
                font-size: 14px;
            }
            .card {
                text-align: left;
                margin-top: 2rem;
            }

            .card-header {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
            .card-header a {
                color: black;
            }
            .card-header > :first-child {
                display: flex;
                align-items: center;

            }
            img {
                margin-right: 1rem;
                height: 3.75rem;
            }
            .collapsible {
                position: relative;
                padding-bottom: 0.5em;
            }
            .collapsible:not(.open) > :not(:first-child):not(div) {
                display: none;
            }

            .collapsible > .toggler {
                position: absolute;
                left: 0;
                bottom: 0;
                display: block;
                width: 100%;
                background: #fff;
                text-align: center;
                cursor: pointer;
            }
            .collapsible > .toggler:focus, .collapsible > .toggler:hover {
                background: #eee;
            }
            .collapsible > .toggler::after {
                content: "\25bc";
            }
            .collapsible.open > .toggler::after {
                content: "\25b2";
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
                                <i class='bi-person-circle'></i> Strona użytkownika
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right pl-3"></div>
            </div>
        </nav>
        <div class="wrapper mt-5">
            <div class='container'>
                <h1>Wybierz materiał</h1>
                <a class='text-dark' href='../ui/userpage.php'>Lub stwórz własny</a>
                <div class="container-fluid">
                    <?php foreach ($arts as $art): ?>
                    <?php $art->parse() ?>
                        <div class='card'>
                            <div class='card-header'>
                                <div>
                                    <?php if (!is_null($art->thumbnail)): ?>
                                        <img src='<?php echo $art->thumbnail ?>' class='rounded' />
                                    <?php endif; ?>
                                    <a href='<?php echo $art->id ?>'><h3><?php echo $art->title ?></h3></a>
                                </div>
                                <div><b><?php echo $art->publicationDate ?></b> przez <b><?php echo $art->author ?></b></div>
                            </div>
                            <div class='card-body collapsible'>
                                <?php echo $art->summary ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>            
        </div>
        <script>
            document.querySelectorAll(".collapsible").forEach(function(current) {

                if (current.childElementCount > 1) {
                    let toggler = document.createElement("div");
                    toggler.className = "toggler";
                    current.appendChild(toggler);

                    toggler.addEventListener("click", function(e) {
                    current.classList.toggle("open");
                    }, false);
                }

            });
        </script>
    </body>
</html>