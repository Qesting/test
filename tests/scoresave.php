<?php

        $link = dbConnect();

        $ent = $link->query("SELECT part FROM session WHERE id=${sid}")->fetch_array()[0] + 1;

        $link->query("INSERT INTO s_entry (ent_id, session_id, name, class, perc, grade) VALUES ('${ent}', '${sid}', '${name}', '${class}', '${points}', '${grade}')");

        $link->query("UPDATE session SET part=part+1 WHERE id=${sid}");

        $link->query("UPDATE test SET part=part+1 WHERE id=${test}");

        $link->close();
?>