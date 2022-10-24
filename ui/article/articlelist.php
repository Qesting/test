<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

require_once("../../config/config.php");
require_once("../../config/classArticle.php");

$arts = Article::getByOwner($_SESSION['id']);

?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lista | Materiały testopol</title>
        <link rel="stylesheet" href="../../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
            .card-header > div {display: flex; justify-content: space-between; align-items: center; }
        </style>
    </head>
    <body>
    <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
            <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='articles.php'><i class='bi-journal-text'></i> Materiały</a>
                        <a class="nav-item nav-link active" href='../userpage.php'><i class='bi-person-circle'></i> Strona użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'><i class='bi-house-fill'></i> Strona główna</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container">
                <h2 class="my-5">Lista twoich materiałów</h2>
                <div class='container-fluid'>
                    <?php if (count($arts) === 0): ?>
                        <h3>Wygląda na to, że nie masz jeszcze żadnych materiałów</h3>
                    <?php else: ?>
                        <?php foreach ($arts as $art): ?>
                            <div class='card mb-3'>
                                <div class='card-header'>
                                    <div>
                                        <h4><?php echo $art->title ?></h4>
                                        <div><b><?php echo $art->publicationDate ?></b></div>
                                    </div>
                                    <?php if ($art->published === 0): ?>
                                        <div><span><id>Nieopublikowany</i></span></div>
                                    <?php endif ?>
                                </div>
                                <div class='card-body'>
                                    <div class='btn-group'>
                                        <a class='btn btn-danger' href='articledel.php?a=<?php echo $art->id ?>'><i class='bi-trash-fill'></i> Usuń</a>
                                        <a class='btn btn-primary' href='write.php?a=<?php echo $art->id ?>'><i class='bi-pencil-square'></i> Edytuj</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                    <a class='btn btn-primary' href='write.php'><i class='bi-pencil-square'></i> Stwórz artykuł</a>
                </div>
            </div>
        </div>
        <script>
            document.querySelectorAll('.btn-danger').forEach(n => {
                n.addEventListener('click', e => {
                        if(!confirm("Czy na pewno chcesz usunąć ten artykuł? Ta operacja jest nieodwracalna.")) {
                            e.preventDefault();
                    }
                });
            });
        </script>
    </body>
</html>