<form method="post" id="quest" action="question.php"> 
    <div class="card-header"><h2>Pytanie <?php echo $_SESSION['question']; ?>:</h2></div>
    <div class="card-body px-5">
    <div class="form-group">

    <?php

        $sql = mysqli_prepare($link, "SELECT points, quest_type, img_path FROM question WHERE id=?");
        mysqli_stmt_bind_param($sql, 'i', $qid);
        mysqli_stmt_execute($sql);
        $result = mysqli_stmt_get_result($sql);
        $data = mysqli_fetch_assoc($result);

        $_SESSION['p'] = $data['points'];

        $sql = mysqli_prepare($link, "SELECT COUNT(id) AS num FROM answer WHERE quest_id=?");
        mysqli_stmt_bind_param($sql, 'i', $qid);
        mysqli_stmt_execute($sql);
        $res = mysqli_stmt_get_result($sql);
        $a = mysqli_fetch_assoc($res);

        $_SESSION['t'] = $data['quest_type'];

        if (is_null($data['img_path'])) {
            
        } else {
            echo '<img class="rounded w" src="./../media/'.$data['img_path'].'">';
        }

        switch ($data['quest_type']) {
            case 1:

                $query = "SELECT ans, content FROM question WHERE id=?";
                $sql = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($sql, 'i', $qid);

                mysqli_stmt_execute($sql);
                $result = mysqli_stmt_get_result($sql);
                $data2 = mysqli_fetch_assoc($result);

                echo '<div class="form-group"><h5 class="mb-3 font-weight-bold">'.$data2['content'].'</h5></div>'; 

                $_SESSION['c'] = $data2['ans'];

                $query = "SELECT content FROM answer WHERE quest_id=? AND ans_id=?";
                for ($i = 1; $i <= $a['num']; $i++) {

                    echo '<div class="form-group"><input type="radio" id="'.$i.'" name="ans" value="'.$i.'">';
                    echo '<label class="form-label ml-2" for="'.$i.'">';

                    $sql = mysqli_prepare($link, $query);
                    mysqli_stmt_bind_param($sql, 'ii', $qid, $i);

                    mysqli_stmt_execute($sql);
                    $result = mysqli_stmt_get_result($sql);
                    $data3 = mysqli_fetch_assoc($result);
                    echo $data3['content'];

                    echo '</label></div>';
                }

                break;
            

            case 2:

                $query = "SELECT ans, content FROM question WHERE id=?";
                $sql = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($sql, 'i', $qid);

                mysqli_stmt_execute($sql);
                $result = mysqli_stmt_get_result($sql);
                $data2 = mysqli_fetch_assoc($result);

                echo '<div class="form-group"><h5 class="mb-3 font-weight-bold">'.$data2['content'].'</h5></div>';  

                $_SESSION['c'] = $data2['ans'];

                $query = "SELECT content FROM answer WHERE quest_id=? AND ans_id=?";
                for ($i = 1; $i <= $a['num']; $i++) {

                    echo '<div class="form-group"><input type="checkbox" id="'.$i.'" name="ans[]" value="'.$i.'">';
                    echo '<label class="form-label ml-2" for="'.$i.'">';

                    $sql = mysqli_prepare($link, $query);
                    mysqli_stmt_bind_param($sql, 'ii', $qid, $i);

                    mysqli_stmt_execute($sql);
                    $result = mysqli_stmt_get_result($sql);
                    $data3 = mysqli_fetch_assoc($result);
                    echo $data3['content'];

                    echo '</label></div>';
                }

                break;

            case 3:

                $query = "SELECT ans_text, content FROM question WHERE id=?";
                $sql = mysqli_prepare($link, $query);
                mysqli_stmt_bind_param($sql, 'i', $qid);

                mysqli_stmt_execute($sql);
                $result = mysqli_stmt_get_result($sql);
                $data2 = mysqli_fetch_assoc($result);

                echo '<div class="form-group"><h5 class="mb-3 font-weight-bold">'.$data2['content'].'</h5></div>'; 

                $_SESSION['c'] = $data2['ans_text'];

                echo '<div class="form-group"><label class="form-label">Odpowiedź:</label>';
                echo '<input type="text" class="form-control" name="ans"></div>';

                break;
        }
    ?>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Następne pytanie">
    </div>
    </div>
</form>