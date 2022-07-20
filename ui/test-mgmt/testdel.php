<?php
    session_start();
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $id = $_SESSION['test_id'];

    $link = dbConnect();

    $sql = $link->prepare("DELETE FROM test WHERE id=?");
    $sql->bind_param('i', $id);
    $sql->execute();

    $sql->close();
    $link->close();

    $_SESSION['notice'] = "s-Pomyślnie usunięto test!";

    unset($_SESSION['test_id']);
    header('location: test.php');
?>