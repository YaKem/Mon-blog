<?php

	//	Connexion à la base de données
	try {
		$pdo = new PDO
		(
			'mysql:host=mysql-myportfolio.alwaysdata.net;dbname=myportfolio_portfolio',
			'186469',
			'mdpAD51.',
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			]
		);
		$pdo->exec('SET NAMES UTF8');
	} catch(PDOException $e) {
		echo $e->getMessage();
	}
	
?>