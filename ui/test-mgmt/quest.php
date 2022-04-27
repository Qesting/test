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

    $notice = $notice_class = "";

    $atab = (isset($_SESSION['tab'])) ? $_SESSION['tab'] : 0;
    $atc = "";
    
    $tid = $_SESSION['test_id'];

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $id = argStrip($_POST['qid']);
        $atab = argStrip($_POST['qnum']);
        $_SESSION['tab'] = $atab;

        $qc = argStrip($_POST['content']);

        if (isset($_POST['qSub'])) {
            if (!empty($qc)) {
                if (!empty($_POST['type'])) {
                    $type = argStrip($_POST['type']);
        
                    if ($type == "add") {
                        $num = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(id) FROM question"));
                        $num = $num[0]+1;
        
                        $ntype = argStrip($_POST['ntype']);

                        if($ntype > 0 && $ntype < 4 ) {
                            $sql = mysqli_prepare($link, "INSERT INTO question (id, test_id, content, quest_type) VALUES (?, ?, ?, ?)");
                            mysqli_stmt_bind_param($sql, 'iisi', $num, $tid, $qc, $ntype);
                            mysqli_stmt_execute($sql);

                            if ($ntype != 3) {
                                $sql = mysqli_prepare($link, "INSERT INTO answer (quest_id, ans_id) VALUES (?, ?)");
                                for ($i = 1; $i <= 2; $i++) {
                                    mysqli_stmt_bind_param($sql, 'ii', $num, $i);
                                    mysqli_stmt_execute($sql);
                                }
                            }
                            $_SESSION['tab'] = $_SESSION['maxtab'] + 1;
                            header("location: ${SERVER['PHP_SELF']}");
                            exit;
                        } else {
                            $notice = "e-Musisz wybrać typ pytania!";
                        }
                    } else if ($type == 1) {
                        $ans = argStrip($_POST['ans']);
                        $ac = argstrip($_POST['ac']);
                        $points = argStrip($_POST['points']);
            
                        $sql = mysqli_prepare($link, "UPDATE question SET content=?, ans=?, points=? WHERE id=?");
                        mysqli_stmt_bind_param($sql, 'siii', $qc, $ans, $points, $id,);
                        mysqli_stmt_execute($sql);
            
                        $sql = mysqli_prepare($link, "UPDATE answer SET content=? WHERE quest_id=? AND ans_id=?");
                        foreach ($ac as $key=>$cac) {
                            $aid = $key+1;
                            mysqli_stmt_bind_param($sql, 'sii', $cac, $id, $aid);
                            mysqli_stmt_execute($sql);
                        }
                    } else if ($type == 2) {
                        $ans = concArray($_POST['ans']);
                        $ac = argstrip($_POST['ac']);
                        $points = argStrip($_POST['points']);
            
                        $sql = mysqli_prepare($link, "UPDATE question SET content=?, ans=?, points=? WHERE id=?");
                        mysqli_stmt_bind_param($sql, 'siii', $qc, $ans, $points, $id);
                        mysqli_stmt_execute($sql);
            
                        $sql = mysqli_prepare($link, "UPDATE answer SET content=? WHERE quest_id=? AND ans_id=?");
                        foreach ($ac as $key=>$cac) {
                            $aid = $key+1;
                            mysqli_stmt_bind_param($sql, 'sii', $cac, $id, $aid);
                            mysqli_stmt_execute($sql);
                        }
                    } else if ($type == 3) {
                        $anst = argStrip($_POST['ans_text']);
                        $points = argStrip($_POST['points']);
            
                        $sql = mysqli_prepare($link, "UPDATE question SET content=?, ans_text=?, points=? WHERE id=?");
                        mysqli_stmt_bind_param($sql, 'ssi', $qc, $anst, $points, $id);
                        mysqli_stmt_execute($sql);
                    } else {
                        $notice = "e-Identyfikator typu pytania ma niepoprawną formę!";
                    }
                
                    if (empty($notice && empty(mysqli_stmt_error($sql)))) {
                        $notice = "s-Pomyślnie zaktualizowano pytanie!";
                    }
            
                    mysqli_stmt_close($sql);
                } else {
                    
                }
            } else {
                $notice = "e-Treść pytania nie może być pusta!";
            }
        } else if (isset($_POST['qDel'])) {
            $sql = mysqli_prepare($link, "DELETE FROM question WHERE id=?");
            mysqli_stmt_bind_param($sql, 'i', $id);
            mysqli_stmt_execute($sql);

            $_SESSION['tab'] = $_SESSION['maxtab'] - 1;
            $_SESSION['notice'] = "s-Pomyślnie usunięto pytanie!";
            header("location: ${SERVER['PHP_SELF']}");
            exit;
        } else if (isset($_POST["aDel3"]) || isset($_POST["aDel4"]) || isset($_POST["aDel4"]) || isset($_POST["aDel5"]) || isset($_POST["aDel6"])) {
            // niepotrzebnie długi blok kodu, ale inaczej nie umiem
            if (isset($_POST['aDel3'])) {
                $num = 3;
            } else if (isset($_POST['aDel4'])) {
                $num = 4;
            } else if (isset($_POST['aDel5'])) {
                $num = 5;
            } else if (isset($_POST['aDel6'])) {
                $num = 6;
            }
            $sql = mysqli_prepare($link, "DELETE FROM answer WHERE quest_id=? AND ans_id=?");
            mysqli_stmt_bind_param($sql, 'ii', $id, $num);
            mysqli_stmt_execute($sql);

            $_SESSION['notice'] = "s-Pomyślnie usunięto odpowiedź!";
            header("location: ${SERVER['PHP_SELF']}");
            exit;
        } else if (isset($_POST['aAdd'])) {

            $sql = mysqli_prepare($link, "SELECT COUNT(id) FROM answer WHERE quest_id=?");
            mysqli_stmt_bind_param($sql, 'i', $id);
            mysqli_stmt_execute($sql);
            $res = mysqli_stmt_get_result($sql);
            $res = mysqli_fetch_array($res);
            $num = $res[0]+1;
            if ($num <= 6) {
            $sql = mysqli_prepare($link, "INSERT INTO answer (quest_id, ans_id) VALUES (?, ?)");
            mysqli_stmt_bind_param($sql, 'ii', $id, $num);
            mysqli_stmt_execute($sql);

            $_SESSION['notice'] = "s-Pomyślnie dodano odpowiedź!";
            } else {
                $_SESSION['notice'] = "e-Pytanie nie może mieć więcej niż 6 możliwych odpowiedzi!";
            }
            header("location: ${SERVER['PHP_SELF']}");
            exit;
        } else if (isset($_POST['img'])) {
            $_SESSION['qid'] = $id;
            header("location: imgadd.php");
            exit;
        }
    }

    $qry = "SELECT * FROM question WHERE test_id=?";
    $sql = mysqli_prepare($link, $qry);
    mysqli_stmt_bind_param($sql, 'i', $tid);
    mysqli_stmt_execute($sql);
    $res = mysqli_stmt_get_result($sql);

    $n = mysqli_num_rows($res);
    $_SESSION['quest_num'] = $n;


    showNot();

?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Edycja pytań</title>
        <link rel="stylesheet" href="../../style/main.css">
        <style>
            body{ font: 14px sans-serif; text-align: center;}
        </style>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </head>
    <body>
    <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
            <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class="nav-item nav-link active" href='test.php'>Powrót do wyboru testu</a>
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <h1 class="mt-5 mb-3">Wybrany test: <?php echo $_SESSION['test_name'].' ('.$_SESSION['quest_num'].' pytań)'; ?></h1>
            <div class="btn-group mb-2">
                <button onclick="window.location.href='testmod.php'" class="btn btn-primary">Modyfikuj test</button>
                <button id="del" class="btn btn-danger">Usuń bieżący test</button>
            </div>
            <div class="container">
                <nav class="nav nav-tabs">
                    <?php 
                        $atc = ($atab == 0) ? " active" : "" ;
                    ?>
                    <a class="nav-item tablink nav-link bi-bookmark-plus-fill <?php echo $atc; ?>" href="#ansadd" data-bs-toggle="tab"></a>
                    <?php
                        $i = 0;
                        while ($i < $n) {
                            $i++;
                            $atc = ($i == $atab) ? " active" : "";
                            echo "<a class=\"nav-item tablink nav-link${atc}\" href=\"#q${i}\" data-bs-toggle=\"tab\">${i}</a>";
                        }
                        $_SESSION['maxtab'] = $i;
                    ?>
                </nav>
                <div class="tab-content">
                    <?php 
                        $atc = (empty($atab)) ? " show active" : "" ;
                    ?>
                    <div class="tab-pane fade<?php echo $atc; ?>" id="ansadd">
                        <form method="post" class="card mt-4">
                            <input type='hidden' name='qid' value='0'>
                            <input type='hidden' name='qnum' value='0'>
                            <input type='hidden' name='type' value='add'>
                            <div class="card-header">
                                <h4 class="mt-4">Dodaj pytanie</h4> 
                            </div>
                            <div class="card-body pt-4 px-4">
                                <div class="form-group">
                                    <label class="form-label">Treść pytania</label>
                                    <input placeholder="Treść pytania" type="text" class="form-control" name="content">
                                </div>
                                <div class="form-group">
                                <label class="form-label">Typ pytania</label>
                                    <select class="form-control" name="ntype">
                                        <option selected>-- Wybierz opcję --</option>
                                        <option value="1">Jednokrotnego wyboru</option>
                                        <option value="2">Wielokrotnego wyboru</option>
                                        <option value="3">Otwarte</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="submit" name="qSub" value="Zatwierdź" class="btn btn-primary">
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php
                        $j = 0;
                        $sql2 = mysqli_prepare($link, "SELECT content, ans_id FROM answer WHERE quest_id=?");
                        while ($row = mysqli_fetch_assoc($res)) {

                            mysqli_stmt_bind_param($sql2, 'i', $row['id']);
                            mysqli_stmt_execute($sql2);
                            $res2 = mysqli_stmt_get_result($sql2);

                            $j++;

                            $atc = ($j == $atab) ? " active show" : "";

                            echo "<div class=\"tab-pane fade${atc}\" id=\"q${j}\">
                            <form method=\"post\" class=\"card mt-4\">
                            <input type='hidden' name='qid' value=\"${row['id']}\">
                            <input type='hidden' name='qnum' value='${j}'>
                            <input type='hidden' name='type' value=\"${row['quest_type']}\">
                            <div class=\"card-header\"><h4 class=\"mt-4\">Pytanie ${j}</h4></div>
                            <div class=\"card-body pt-4 px-4\">
                            <div class=\"form-group\">
                            <label class=\"form-label\">Treść pytania</label>
                            <input value=\"${row['content']}\" type=\"text\" class=\"form-control\" name=\"content\">
                            </div>
                            <div class='form-group'>
                            <label class='form-label'>Wartość punktowa odpowiedzi:</label>
                            <input type='number' class='form-control' name='points' min='0' value='${row['points']}'>
                            </div>
                            <div class=\"form-group\">
                            <label class='form-label'>Odpowiedzi:</label>";

                            switch ($row['quest_type']) {
                                case 1:
                                    $i = 0;
                                    while ($row2 = mysqli_fetch_assoc($res2)) {
                                        $i++;
                                        $ans = ($row['ans'] == $i) ? "checked" : "";
                                        $del = ($i > 2) ? "<button type='submit' name='aDel${i}' class='btn btn-danger'><span class='bi-dash-square'></span></button>" : "<span class='bi-square btn btn-secondary disabled'></span>";

                                        echo "<div class='input-group mb-2'>
                                        <span class='input-group-text'><input ${ans} type='radio' name='ans' value=\"${row2['ans_id']}\"></span>
                                        <input type='text' value=\"${row2['content']}\" class='form-control' name='ac[]'>${del}
                                        </div>";
                                    }
                                    echo "<div class='input-group'>
                                    <button type='submit' name='aAdd' class='form-control btn btn-success'><span class='bi-plus-square'></span> Dodaj odpowiedź</button>
                                    </div>";
                                    break;
                                case 2:
                                    $i = 0;
                                    while ($row2 = mysqli_fetch_assoc($res2)) {
                                        $i++;
                                        if (!empty($row['ans'])) {
                                            $ans = str_contains($row['ans'], $i) ? "checked" : "";
                                        } else {
                                            $ans = "";
                                        }
                                        $del = ($i > 2) ? "<button type='submit' name='aDel${i}' class='btn btn-danger'><span class='bi-dash-square'></span></button>" : "<span class='bi-square btn btn-secondary disabled'></span>";

                                        echo "<div class='input-group mb-2'>
                                        <span class='input-group-text'><input ${ans} type='checkbox' name='ans[]' value=\"${row2['ans_id']}\"></span>
                                        <input type='text' value=\"${row2['content']}\" class='form-control' name='ac[${i}]'>${del}
                                        </div>";
                                    }
                                    echo "<div class='input-group'>
                                    <button type='submit' name='aAdd' class='form-control btn btn-success'><span class='bi-plus-square'></span> Dodaj odpowiedź</button>
                                    </div>";
                                    break;
                                case 3:
                                    echo "<input type='text' class='form-control' name='ans_text' value=\"${row['ans_text']}\">";
                                    break;
                            }
                            echo "</div>
                            <div class='btn-group'>
                            <input type='submit' name='qDel' value='Usuń pytanie' class='btn btn-danger'>
                            <input type='submit' name='qSub' value='Zatwierdź' class='btn btn-primary'>
                            <input type='submit' name='img' value='Dodaj obraz' class='btn btn-secondary'>
                            </div></div></form></div>";
                        }
                    ?>
                </div>
                <?php echo "<p id=\"notice\" class=\"mt-3 ${notice_class}\">${notice}</p>"; ?>
            </div>
        </div>
    </body>

    <script>
        $(document).ready(function(){
            $(".tablink").click(function(e){
                e.preventDefault();
                $(this).tab("show");

            });
        });
    </script>

    <script>
        /*history.replaceState && history.replaceState(
            null, '', location.pathname + location.search.replace(/[\?&]pref=[^&]+/, '').replace(/^&/, '?')
        );*/
    </script>

    <script>
            let del = document.getElementById("del");
            let error = document.getElementById("error");

            del.addEventListener("click", confirmDel);

            function confirmDel() {
                if (window.confirm("Usunięcie testu jest operacją nieodwracalną.\nCzy chcesz kontynuować?")) {
                    window.location.replace("testdel.php");
                } else {
                    error.innerHTML = "Operacja anulowana przez użytkownika";
                    error.classList.add("alert", "alert-warning");
                }
            }
    </script>
        
</html>
