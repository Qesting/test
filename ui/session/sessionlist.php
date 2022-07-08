<?php
    session_start();

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $owner = $_SESSION['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $session = argStrip($_POST['session']);
        if (isset($_POST['open'])) {
            $started = date("Y-m-d H:i:s");

            $sql = mysqli_prepare($link, "UPDATE session SET is_open=1, started=? WHERE id=?");
            mysqli_stmt_bind_param($sql, 'si', $started, $session);
            mysqli_stmt_execute($sql);
            header("location: sessionlist.php");
            exit;
        } else if (isset($_POST['close'])) {
            $closed = date("Y-m-d H:i:s");

            $sql = mysqli_prepare($link, "UPDATE session SET is_open=0, closed=? WHERE id=?");
            mysqli_stmt_bind_param($sql, 'si', $closed, $session);
            mysqli_stmt_execute($sql);
            header("location: sessionlist.php");
            exit;
        }
    }

    $sql = mysqli_prepare($link, "SELECT session.id, session.can_laa, session.code, test.name AS tname, test.id AS tid, module.name AS mname, session.is_open, session.started, session.closed, session.part FROM session, test, module WHERE session.test_id=test.id AND test.module_id=module.id AND session.owner=? ORDER BY session.id DESC");
    mysqli_stmt_bind_param($sql, 'i', $owner);
    mysqli_stmt_execute($sql);
    $res = mysqli_stmt_get_result($sql);
?>

<!DOCTYPE html>
<html lang="pl-PL">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sesje</title>
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
                        <a class="nav-item nav-link active" href='sessions.php'>Powrót do strony sesji</a>
                        <a class="nav-item nav-link active" href='../userpage.php'>Powrót do strony użytkownika</a>
                        <a class="nav-item nav-link active" href='../../index.php'>Powrót do strony głównej</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class="container-fluid">
                <h2 class="my-5">Twoje sesje</h2>
                    <table class="table">
                        <thead>
                            <th>Kod sesji</th>
                            <th>Nazwa testu</th>
                            <th>Status</th>
                            <th>Podglądanie odpowiedzi</th>
                            <th>Ilość zarejestrowanych podejść</th>
                            <th colspan='2'></th>
                        </thead>
                        <tbody>
                            <?php
                                while ($row = mysqli_fetch_assoc($res)) {
                                    if ($row['is_open'] == 0 && !empty($row['closed'])) {
                                        $status = "Zakończona";
                                        $btn_vis = "hidden";
                                    } else if ($row['is_open'] == 0) {
                                        $status = "Zamknięta";
                                        $btn_text = "Otwórz sesję";
                                        $act = "open";
                                        $btn_vis = "";
                                    } else {
                                        $status = "Otwarta";
                                        $btn_text = "Zamknij sesję";
                                        $act = "close";
                                        $btn_vis = "";
                                    }

                                    if (empty($row['part'])) {
                                        $part = 0;
                                    } else {
                                        $part = $row['part'];
                                    }

                                    if ($row['can_laa'] == 0) {
                                        $cl = "Nie";
                                    } else {
                                        $cl = "Tak";
                                    }
                                    echo '<tr>';
                                    echo '<td><form method="post"><input type="hidden" value="'.$row['id'].'" name="session"><input type="submit" formaction="s_entry.php" value="'.$row['code'].'" class="btn btn-link btn-sm"></form></td>
                                        <td>'.$row['tname'].' - '.$row['mname'].'</td>
                                        <td>'.$status.'</td>
                                        <td>'.$cl.'</td>
                                        <td>'.$part.'</td>';

                                    if ($btn_vis != "hidden") {
                                        echo "<td><button type='button' class='btn btn-sm btn-secondary' id='copy' onclick='copyLink(\"${row['tid']}\", \"${row['code']}\")'><span class='bi-link-45deg'></span></button></td>";
                                        echo '<td>
                                        <form method="post">
                                            <input type="hidden" value="'.$row['id'].'" name="session">
                                            <input type="submit" name='.$act.' value="'.$btn_text.'" '.$act.' class="btn btn-primary btn-sm">
                                            </form>
                                        </td>';
                                    }
                                    echo '</tr>';
                                }
                            ?>
                        </tbody>
                    </table>
            </div>

        </div>
        
        <script>
            function checkIfMobile() {
                let check = false;
                (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
                return check;
            };


            function copyLink(testId, sessionCode) {

                let text = "<?php echo $_SERVER['SERVER_NAME']; ?>" + "?test=" + testId + "&s=" + sessionCode;

                if (checkIfMobile()) {
                    const elem = document.createElement('textarea');
                    elem.value = text;
                    document.body.appendChild(elem);
                    elem.select();
                    document.execCommand('copy');
                    document.body.removeChild(elem);
                } else {
                    navigator.clipboard.writeText(text);
                }
            }
        </script>
    </body>
</html>