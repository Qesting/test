<?php 
    $sql = mysqli_prepare($link, "SELECT points, quest_type, img_path, ans FROM question WHERE id=?");
    mysqli_stmt_bind_param($sql, 'i', $qid);
    mysqli_stmt_execute($sql);
    $result = mysqli_stmt_get_result($sql);
    $data = mysqli_fetch_assoc($result);
    
    $points = $data['points'];
    
    $sql = mysqli_prepare($link, "SELECT COUNT(id) AS num FROM answer WHERE quest_id=?");
    mysqli_stmt_bind_param($sql, 'i', $qid);
    mysqli_stmt_execute($sql);
    $res = mysqli_stmt_get_result($sql);
    $a = mysqli_fetch_assoc($res);

    $point_show = ($data['quest_type'] == 2) ? $points * strlen($data['ans']) : "$points";
?>

<div class='card-header'>
    <h2>Pytanie <?php echo $qnum; ?>:</h2>
    <p><i><b><?php echo $point_show; ?></b> punktów</i></p>
</div>
<div class='card-body px-5'>
    <div class='form-group'>

        <?php
        
        $type = $data['quest_type'];
        
        if (is_null($data['img_path'])) {
            
        } else {
            echo '<img class="rounded w" src="./../usermedia/'.$data['img_path'].'">';
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
                
                $corr = $data2['ans'];
                
                $query = "SELECT content FROM answer WHERE quest_id=? AND ans_id=?";
                for ($i = 1; $i <= $a['num']; $i++) {
                    
                    echo "<div class='form-group'><input type='radio' id='a${i}' name='${iter}[ans]' value='${i}'>";
                    echo '<label class="form-label ml-2" for="a'.$i.'">';
                    
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
                
                $corr = $data2['ans'];
                
                $query = "SELECT content FROM answer WHERE quest_id=? AND ans_id=?";
                for ($i = 1; $i <= $a['num']; $i++) {
                    
                    echo "<div class='form-group'><input type='checkbox' id='a${i}' name='${iter}[ans][]'' value='$i'>";
                    echo '<label class="form-label ml-2" for="a'.$i.'">';
                    
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
                
                $corr = $data2['ans_text'];
                
                echo '<div class="form-group"><label class="form-label">Odpowiedź:</label>';
                echo "<input type='text' class='form-control' name='${iter}[ans]'></div>";
                
                break;
            }
        ?>
    </div>
</div>