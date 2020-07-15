<?php

    include 'application/db_connection.php';
    /*if($_SERVER['HTTP_REFERER'] === 'http://myportfolio.alwaysdata.net/index.html') {*/
        if(isset($_POST) && $_POST) {
            $query = $pdo->prepare
                (
                    "INSERT INTO
                        `contacts`(`lastName`, `firstName`, `email`, `message`, `date`)
                    VALUES
                        (?, ?, ?, ?, NOW())"
                );
        
                $query->execute([$_POST['lastName'], $_POST['firstName'], $_POST['email'], $_POST['message']]);
                echo "Je vous contacte dans les plus bref délai, merci";
                header('location: index.php');
        } else {
            echo "Veuillez contacter le propriétaire du site: ykemouche@gmail.com, merci";
            header('location: index.php');
        }
    /*}*/

?>