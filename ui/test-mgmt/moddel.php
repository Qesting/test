<?php
    session_start();
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    if($_SESSION['priv'] == 2) {
        
        $id = $_SESSION['module_id'];

        $sql = mysqli_prepare($link, "DELETE FROM module WHERE id=?");
        mysqli_stmt_bind_param($sql, 'i', $id);
        mysqli_stmt_execute($sql);

        unset($_SESSION['module_id']);
        header('location: mod.php');

        $_SESSION['notice'] = "s-Pomyślnie usunięto moduł!";
    } else {
        $_SESSION['notice'] = "e-Nie masz wystarczającego poziomu uprawnień do wykonania tej operacji";
        header("location: test.php");
    }
?>