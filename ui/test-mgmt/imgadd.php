<?php

    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);


    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $notice = $notice_class = "";

    $link = dbConnect();

    $test = unserialize($_SESSION['edit_test']);

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['mode'])) {

        if ($_POST['mode'] == "add") {

            $num = argStrip($_POST['num']);

            if ($num <= 0 || $num > count($test->testQuestions)) {

                $_SESSION['notice'] = "e-Musisz wybrać pytanie!";
                header('location: imgadd.php');
                exit;

            }

            $file = $_FILES['file'];

            $fname = trim($_POST['fname']);

            if (preg_match("[#%&\{\}\\<>*?\/\$\!'\":;@+\`|=]", $fname)) {

                $_SESSION['notice'] = "e-Nazwa zawiera niedozwolone znaki!";
                header('location: imgadd.php');
                exit;

            }

            $ext = strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION));

            $target = "../../usermedia/{$fname}.{$ext}";

            if (is_uploaded_file($file['tmp_name'])) {

                $mime = mime_content_type($file['tmp_name']);
                $allowedMime = ['image/png', 'image/jpeg', 'image/webp'];

                if (!in_array($mime, $allowedMime)) {

                    $_SESSION['notice'] = "e-Niedozwolony typ pliku!";
                    header('location: imgadd.php');
                    exit;
                    
                }

                if ($file['size'] > pow(1024, 2)) {

                    $_SESSION['notice'] = "e-Plik jest zbyt duży, maksymalny rozmiar to 1 MiB!";
                    header('location: imgadd.php');
                    exit;

                }

                if (move_uploaded_file($file['tmp_name'], $target)) {
                    
                    if ($test->testQuestions[$num - 1]->setPath("{$fname}.{$ext}")) {

                        $_SESSION['notice'] = "s-Plik został zapisany!";
                        $_SESSION['edit_test'] = serialize($test);
                        header('location: imgadd.php');
                        exit;

                    } else {

                        unlink($target);

                        $_SESSION['notice'] = "e-Wystąpił błąd. Spróbuj ponownie później.";
                        header('location: imgadd.php');
                        exit;

                    }

                    $_SESSION['notice'] = "e-Wystąpił błąd. Spróbuj ponownie później.";
                    header('location: imgadd.php');
                    exit;

                }

            } else {

                $_SESSION['notice'] = "e-Wystąpił błąd.";
                header('location: imgadd.php');
                exit;

            }

        } else if ($_POST['mode'] == "del") {

            $num = argStrip($_POST['num']);

            if ($num <= 0 || $num > count($test->testQuestions)) {

                $_SESSION['notice'] = "e-Wystąpił błąd. Spróbuj ponownie później.";
                header('location: imgadd.php');
                exit;

            }

            $fname = $test->testQuestions[$num - 1]->questionImgPath;

            if (unlink("../../usermedia/{$fname}")) {

                if ($test->testQuestions[$num - 1]->setPath("")) {

                    $_SESSION['notice'] = "s-Plik został usunięty!";
                    $_SESSION['edit_test'] = serialize($test);
                    header('location: imgadd.php');
                    exit;

                } else {

                    $_SESSION['notice'] = "e-Wystąpił błąd. Spróbuj ponownie później.";
                    header('location: imgadd.php');
                    exit;

                }

                $_SESSION['notice'] = "e-Wystąpił błąd. Spróbuj ponownie później.";
                header('location: imgadd.php');
                exit;

            }

        }
        
    }

    showNot();

?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dodawanie obrazu</title>
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
                        <a class="nav-item nav-link active" href='quest.php'><i class='bi-question-lg'></i> Pytania</a>
                        <a class="nav-item nav-link active" href='../userpage.php'><i class='bi-person-circle'></i> Strona użytkownika</a>
                        <a class="nav-item nav-link active" href='/index.php'><i class='bi-house-fill'></i> Strona główna</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container">
                <h2 class="my-5">Dodawanie obrazu</h2>
                <form method="post" enctype="multipart/form-data">
                    <input type='hidden' name='mode' id='mode'/>
                    <div class='form-group'>
                        <label class='form-label'>Pytanie</label>
                        <select id='question' name='num' class='form-control'>
                            <option value='0'>-- Wybierz pytanie --</option>
                            <?php 
                                for ($i = 0; $i < count($test->testQuestions); $i++) {

                                    $question  = $test->testQuestions[$i];
                                    $j = $i + 1;

                                    echo "<option value='{$j}' data-type='{$question->questionType}' data-imgpath='{$question->questionImgPath}'>{$j} - {$question->questionContent}</option>";

                                }
                            ?>
                        </select>
                        <div class='alert alert-info mt-4'>
                                <p><b>Typ pytania:</b> <span id='type'></span></p>
                                <p><b>Posiada obraz?</b> <span id='hasimg'></span></p>
                        </div>
                        <div class='btn-group'>
                            <button type='button' class='btn btn-secondary' id='prev'><span class='bi-caret-left-fill'></span> Poprzednie pytanie</button>
                            <button type='button' class='btn btn-outline-secondary disabled'></button>
                            <button type='button' class='btn btn-secondary' id='next'>Poprzednie pytanie <span class='bi-caret-right-fill'></span></button>
                        </div>
                    </div>
                    <div id='options'></div>
                    <div class="form-group btn-group">
                        <button type="button" name="imgAdd" class="btn btn-primary d-none" id='btnsubmit'><span class='bi-file-earmark-plus'></span> Dodaj obraz</button>
                    </div>
                </form>
                <?php echo "<p id='error' class='{$notice_class}'>{$notice}</p>" ?>
            </div>
        </div>
        <script src='../../js/imgChange.js'></script>
    </body>
</html>
