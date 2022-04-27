<?php
    if ($points == 100) {
        $grade_str = 'celujący';
        $grade = 6;
    } else if ($points < 100 && $points >= 90) {
        $grade_str = 'bardzo dobry';
        $grade = 5;
    } else if ($points < 90 && $points >= 75) {
        $grade_str = 'dobry';
        $grade = 4;
    } else if ($points < 75 && $points >= 50) {
        $grade_str = 'dostateczny';
        $grade = 3;
    } else if ($points < 50 && $points >= 35) {
        $grade_str = 'dopuszczający';
        $grade = 2;
    } else {
        $grade_str = 'niedostateczny';
        $grade = 1;
    }

    function show_grade() {
        global $grade_str;
        global $grade;
        $img = "";

        switch ($grade) {
            case 1:
                $img = '<img class="rounded w" src="./../media/life.jpg">';
                break;
            case 6:
                $img = '<img class="rounded w" src="./../media/science.jpg">';
                break;
        }

        echo '<div><h2>
        '.$grade_str.'
        </h2>.'.$img.'</div>';
    }
?>