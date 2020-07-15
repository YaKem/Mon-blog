<?php

    session_start();

    if(isset($_SESSION['login'])){
        if($_SESSION['login'] != 1){
            header('Location: login.php');
        }
    }else{
        header('Location: login.php');
    }

    if(!array_key_exists('id', $_GET) OR !ctype_digit($_GET['id']))
    {
        header('Location: listContact.php');
        exit();
    }

    include 'application/db_connection.php';

    $query = $pdo->prepare(
        "DELETE FROM
            contacts
        WHERE
            id = ?"
    );
    $query->execute([$_GET['id']]);
    
    header('Location: listContact.php');
    exit();

?>
