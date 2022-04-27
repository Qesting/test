<?php
  $result = mysqli_query($link,"SELECT * FROM quotes order by RAND() limit 1");
  
  $i=0;
  while($row = mysqli_fetch_array($result)) {
    echo"<p class='text-white text-right'>${row['quote']}</p>";

        
  $i++;
 }
?>