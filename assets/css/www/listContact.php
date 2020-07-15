<?php

    session_start();

    if(isset($_SESSION['login'])){
        if($_SESSION['login'] != 1){
            header('Location: login.php');
        }
    }else{
        header('Location: login.php');
    }

    include 'application/db_connection.php';

    $query = $pdo->prepare(
        "SELECT
            *
        FROM
            contacts
        "
    );
    $query->execute();
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    $template = 'listContact';
    include 'layout.phtml';

?>