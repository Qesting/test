<?php 
    $question = $test->testQuestions[$i];
    
    $points = $question->questionPoints;

    $point_show = ($question->questionType == 2) ? $points * strlen($question->questionAnswer) : "$points";
?>

<div class='card-header'>
    <h2>Pytanie <?php echo $qnum; ?>:</h2>
    <p><i><b><?php echo $point_show; ?></b> punktów</i></p>
</div>
<div class='card-body px-5'>
    <div class='form-group'>

        <?php
        
        if (is_null($question->questionImgPath)) {
            
        } else {
            echo '<img class="rounded w" src="./../usermedia/'.$question->questionImgPath.'">';
        }

        switch ($question->questionType) {
            case 1:
                
                echo '<div class="form-group"><h5 class="mb-3 font-weight-bold">'.$question->questionContent.'</h5></div>'; 
                
                for ($j = 1; $j <= count($question->questionAnsList); $j++) {
                    
                    echo "<div class='form-group'><input type='radio' id='a${j}' name='${i}[ans]' value='${j}'>";
                    echo '<label class="form-label ml-2" for="a'.$i.'">';
                    
                    echo $question->questionAnsList[$j-1].'</label></div>';
                
                }
                
                break;
                
                
            case 2:
                
                echo '<div class="form-group"><h5 class="mb-3 font-weight-bold">'.$question->questionContent.'</h5></div>'; 
                
                for ($j = 1; $j <= count($question->questionAnsList); $j++) {
                    
                    echo "<div class='form-group'><input type='checkbox' id='a${j}' name='${i}[ans][]' value='${j}'>";
                    echo '<label class="form-label ml-2" for="a'.$i.'">';
                    
                    echo $question->questionAnsList[$j-1].'</label></div>';
                
                }
                
                break;
                
                break;
                
            case 3:

                echo '<div class="form-group"><h5 class="mb-3 font-weight-bold">'.$question->questionContent.'</h5></div>'; 
                                
                echo '<div class="form-group"><label class="form-label">Odpowiedź:</label>';
                echo "<input type='text' class='form-control' name='${i}[ans]'></div>";
                
                break;
            }
        ?>
    </div>
</div>