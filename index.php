<?php

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['test'])) {

        $testArgs = "t=${_GET['test']}";
        $testArgs .= (isset($_GET['s'])) ? "&s=${_GET['s']}" : "";
        header("location: ./tests/start.php?${testArgs}");
        exit;

    }

?>

<!DOCTYPE html>
<html lang='pl-Pl'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Indeks</title>
        <link rel="stylesheet" href="style/main.css">
        <style>
            body{
                font: 14px sans-serif;
                margin-bottom: 120px;
            }
            .row { margin-top: -20px; }
            .row > * { margin-top: 20px; }
            
            @media screen and (min-width: 768px) {
                h4 + div > p {height: 8rem}
            }
            @media screen and (min-width: 992px) {
                h4 + div > p {height: 6rem}
            }
            @media screen and (min-width: 1200px) {
                h4 + div > p {height: 4rem}
            }
        </style>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    </head>
    <body>
        <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <?php if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
                            <a class='nav-item nav-link active' href='ui/login.php'><i class='bi-person-check-fill'></i> Zaloguj się</a>
                            <a class='nav-item nav-link active' href='ui/register.php'><i class='bi-person-plus-fill'></i> Zarejestruj się</a>
                        <?php else: ?>
                            <span class='nav-item nav-link active'>Witaj, <b><?php echo $_SESSION['username'] ?></b></span>
                            <a class='nav-item nav-link active' href='ui/userpage.php'><i class='bi-person-circle'></i> Strona użytkownika</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right pl-3"></div>
            </div>
        </nav>
        <div class="wrapper">
            <div class='container my-3'>
                <div class='text-center'>
                    <h1 class='text-uppercase'>Testopol</h1>
                    <p>Testopol to hobbystycznie stworzony system dla testów, który zrodził się z potrzeby znalezienia zamiennika dla innego, dużo bardziej popularnego serwisu podobnego typu.</p>
                    <p>Tworzony jest od kwietnia 2022 i od tego czasu przeszedł już kilka etapów, za każdym razem zwiększając ilość i jakość swoich funkcjonalności, a także poziom bezpieczeństwa. </p>
                </div>
            </div>
            <div class='container text-center text-white'>
                <div class='row'>
                    <div class='col-md-6 p-3 bg-primary' id='container-test'>
                        <h4 class='mb-4'><span class='bi-card-text'></span> Testy</h4>
                        <div>
                            <p>Główna część systemu Testopol. System posiada wszystkie funkcjonalności potrzebne do tworzenia prostych testów sprawdzających, a także mechanizmy blokowania testu, sesji i udostępniania odpowiedzi po rozwiązaniu.</p>
                            <a class='btn btn-secondary' href='tests'>Zobacz listę testów</a>
                        </div>
                        <div class='mt-5'>
                            <p>Nie udało ci się znaleźć odpowiedniego testu? <u><a class='text-white text-underline' href='ui/userpage.php'>Stwórz własny.</a></u></p>
                        </div>
                    </div>
                    <div class='col-md-6 p-3 bg-secondary' id='container-article'>
                        <h4 class='mb-4'>Materiały <span class='bi-journal-text'></span></h4>
                        <div>
                            <p>Materiały do nauki, czyli system tworzenia i przeglądania artykułów. Formatowanie oparte jest na języku Markdown.</p>
                            <a class='btn btn-primary' href='article'>Zobacz materiały</a>
                        </div>
                        <div class='mt-5'>
                            <p>Nie udało ci się znaleźć odpowiednich materiałów? <u><a class='text-white text-underline' href='ui/userpage.php'>Stwórz własne.</a></u></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class='bg-primary text-light text-center fixed-bottom'>
            <div class='container p-4 pb-0'>
                <div class='mb-3'>
                    <p>Trafiłeś/aś na jakiś błąd w działaniu aplikacji? <a class='text-light' href='https://github.com/Qesting/test/issues' target='_blank'><i class='bi-envelope-fill'></i> Skontaktuj się z nami</a></p>
                </div>
            </div>
        </footer> 
        <script src='js/quote.js'></script>
    </body>
</html>