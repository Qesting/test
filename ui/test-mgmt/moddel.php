<?php
    session_start();
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    if($_SESSION['priv'] == 2) {

        $link = dbConnect();
        
        $id = $_SESSION['module_id'];

        $sql = $link->prepare("DELETE FROM module WHERE id=?");
        $sql->bind_param('i', $id);
        $sql->execute();

        $sql->close();
        $link->close();

        unset($_SESSION['module_id']);
        header('location: mod.php');

        $_SESSION['notice'] = "s-Pomyślnie usunięto moduł!";
    } else {
        $_SESSION['notice'] = "e-Nie masz wystarczającego poziomu uprawnień do wykonania tej operacji";
        header("location: test.php");
    }
?>