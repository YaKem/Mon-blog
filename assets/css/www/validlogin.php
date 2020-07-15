<?php 

 	// Ma clé privée
    $secret = "6LcDnqwUAAAAAFAenqCPFKKx9ztb265JFfw6vLfv";
    // Paramètre renvoyé par le recaptcha
    $response = $_POST['g-recaptcha-response'];
    // On récupère l'IP de l'utilisateur
    $remoteip = $_SERVER['REMOTE_ADDR'];
     
    $api_url = "https://www.google.com/recaptcha/api/siteverify?secret=" 
        . $secret
        . "&response=" . $response
        . "&remoteip=" . $remoteip ;
     
    $decode = json_decode(file_get_contents($api_url), true);
     
    if ($decode['success'] == true) {
        // C'est un humain
        session_start(); // on accède aux variables de sessions

        include 'application/db_connection.php';

        $login = $_POST['login'];
        $password = $_POST['password'];

        $query = $pdo->prepare
        (
            "select * from access where login= ?"
        );

        $query->execute([$login]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if(password_verify($password, $result['password'])){
            $_SESSION['login'] = 1;
            header('Location: listContact.php');
        } 
        else{
            header('Location: login.php');
        }
    }else{
		// C'est un robot ou le code de vérification est incorrecte
		header('Location: login.php');
		//echo "pb captcha";
    }

?>