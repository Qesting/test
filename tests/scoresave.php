<?php

        $sql0 = "SELECT part FROM session WHERE id=${sid}";
        $res0 = mysqli_fetch_array(mysqli_query($link, $sql0));
        $ent = $res0[0]+1;

        $sql = "INSERT INTO s_entry (ent_id, session_id, name, class, perc, grade) VALUES ('${ent}', '${sid}', '${name}', '${class}', '${points}', '${grade}')";
        $res = mysqli_query($link, $sql);

        $sql1 = "UPDATE session SET part=part+1 WHERE id=${sid}";
        $res1 = mysqli_query($link, $sql1);

        $sql2 = "UPDATE test SET part=part+1 WHERE id=${test}";
        $res2 = mysqli_query($link, $sql2);
?>