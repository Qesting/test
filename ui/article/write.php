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

if (isset($_GET['a']) && is_numeric($_GET['a'])) {
    try {
        $art = Article::getById($_GET['a']);
    } catch (Exception $e) {
        header('location: articles.php');
        exit;
    }
} else {
    $art = new Article;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $art->storePostData($_POST);
    if (isset($_POST['save'])) {
        if (!is_null($art->id)) $art->update(); 
        else $art->insert($_SESSION['id']); 
    } else if (isset($_POST['publish'])) {
        $art->publish($_SESSION['id']);
    }
}

?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Artykuł | Materiały testopol</title>
        <link rel="stylesheet" href="../../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center; margin-bottom: 5rem;}
            #toolbar {display: flex; flex-wrap: wrap; padding-left: 2rem}
            #toolbar > div {display: flex; justify-content: center; align-items: center; margin-right: 1rem; margin-bottom: 1rem;}
            #toolbar > div > * {margin-right: .5rem}
            #header {display: flex; justify-content: center; align-items: start;}
            #header > div {margin-right: 1rem;}
            .hidden {display: none !important}
            form .form-group {width: 80%; margin-left: auto; margin-right: auto;}
            form > :first-child, form > :nth-child(2) {width: 60%; margin-left: auto; margin-right: auto;}
            form input, form textarea{text-align: left;}
            .left textarea {
                min-height: 15rem;
                height: auto;
            }
            .input-group-text {width: 6rem; justify-content: center;}
            #error {width: 60%; margin-left: auto; margin-right: auto};
            #cont {
                margin-top: 2rem;
                position: sticky;
                top: 2rem;
                left: 0;
                right: 0;
                margin-left: auto;
                margin-right: auto;
                z-index: 20;
            }
            form .btn-group {
                position: fixed;
                bottom: 1rem;
                left: 0;
                right: 0;
                margin-left: auto;
                margin-right: auto;
                z-index: 20;
                width: fit-content;
            }
            .container:not(#cont) {margin-top: 3rem;}
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='articlelist.php'><i class='bi-list'></i> Lista artykułów</a>
                        <a class="nav-item nav-link active" href='../userpage.php'><i class='bi-person-circle'></i> Strona użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'><i class='bi-house-fill'></i> Strona główna</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class='container' id='cont'>
                <div class='card'>
                    <div class='card-header' id='header'>
                        <div>
                            <input type='checkbox' id='help' />
                            <label for='help' class='form-label'>Pasek narzędzi</label>
                        </div>
                        <div>
                            <a href='https://www.markdownguide.org/basic-syntax/' target='_blank' class='link-info'>Formatowanie w markdown</a>
                        </div>
                    </div>
                    <div class='hidden card-body' id='toolbar'>
                        <div>
                            <h6>Nagłówki</h6>
                            <div class='btn-group'>
                                <btn id='h1' data-count='1' class='btn btn-secondary'>1</btn>
                                <btn id='h2' data-count='2' class='btn btn-secondary'>2</btn>
                                <btn id='h3' data-count='3' class='btn btn-secondary'>3</btn>
                                <btn id='h4' data-count='4' class='btn btn-secondary'>4</btn>
                                <btn id='h5' data-count='5' class='btn btn-secondary'>5</btn>
                                <btn id='h6' data-count='6' class='btn btn-secondary'>6</btn>
                            </div>
                        </div>
                        <div>
                            <h6>Stylowanie</h6>
                            <div class='btn-group'>
                                <btn data-template='**$**' id='bold' class='btn btn-secondary'><i class='bi-type-bold'></i></btn>
                                <btn data-template='*$*' id='italic' class='btn btn-secondary'><i class='bi-type-italic'></i></btn>
                                <btn data-template='~~$~~' id='strike' class='btn btn-secondary'><i class='bi-type-strikethrough'></i></btn>
                                <btn id='quote' class='btn btn-secondary'><i class='bi-blockquote-left'></i></btn>
                            </div>
                        </div>
                        <div>
                            <h6>Listy</h6>
                            <div class='btn-group'>
                                <btn id='ol' class='btn btn-secondary'><i class='bi-list-ol'></i></btn>
                                <btn id='ul' class='btn btn-secondary'><i class='bi-list-ul'></i></btn>
                            </div>
                        </div>
                        <div>
                            <h6>Wstawianie</h6>
                            <div class='btn-group'>
                                <btn id='img' class='btn btn-secondary'><i class='bi-card-image'></i></btn>
                                <btn id='lnk' class='btn btn-secondary'><i class='bi-link-45deg'></i></btn>
                                <btn id='code' class='btn btn-secondary'><i class='bi-code'></i></btn>
                                <btn id='hr' class='btn btn-secondary'><i class='bi-dash'></i></btn>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class='container-fluid'>
                    <form method='post' class='mt-5 left'>
                        <div class='form-group'>
                            <div class='input-group mb-3'>
                                <input class='form-control' type='text' name='title' id='title' placeholder='Tytuł...' value='<?php echo $art->title ?>'/>
                                <div class='input-group-append'>
                                    <span class='input-group-text' id='title-count' data-target='title' data-limit='255'></span>
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            <input type='text' name='thumbnail' placeholder='Adres miniatury...' class='form-control' value='<?php echo $art->thumbnail ?>' />
                        </div>
                        <div class='form-group'>
                            <div class='input-group mb-3'>
                                <textarea class='form-control' id='summary' placeholder='Wstęp...' name='summary'><?php echo $art->summary ?></textarea>
                                <div class='input-group-append'>
                                    <span class='input-group-text' id='summary-count' data-target='summary' data-limit='2048'></span>
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            <div class='input-group mb-3'>
                                <textarea class='form-control' id='content' placeholder='Rozwinięcie...' name='content'><?php echo $art->content ?></textarea>
                                <div class='input-group-append'>
                                    <span class='input-group-text' id='content-count' data-target='content' data-limit='64535'></span>
                                </div>
                            </div>
                        </div>
                        <div class='form-group mt-5'>
                            <p class='' id='error'></p>
                        </div>
                        <div class='form-group'>
                            <div class='btn-group'>
                                <button class='btn btn-secondary' type='submit' name='save'><i class='bi-save'></i> Zapisz</button>
                                <button class='btn btn-primary' type='submit' name='publish'><i class='bi-save'></i> Zapisz i opublikuj</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src='../../js/write.js'></script>
    </body>
</html>