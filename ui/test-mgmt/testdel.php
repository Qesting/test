<?php
    session_start();
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: ../login.php");
        exit;
    }
    require_once('../../config/config.php');

    $id = $_SESSION['test_id'];

    $sql = mysqli_prepare($link, "DELETE FROM test WHERE id=?");
    mysqli_stmt_bind_param($sql, 'i', $id);
    mysqli_stmt_execute($sql);

    $_SESSION['error'] = "Pomyślnie usunięto test!";
    $_SESSION['error_class'] = 'class="alert alert-success"';

    unset($_SESSION['test_id']);
    header('location: test.php');
?>