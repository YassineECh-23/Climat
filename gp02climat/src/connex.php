<?php

include 'envBD.php'; // déclaration des variables dans le tableau associatif $_ENV

function connexion() {
	/** TODO renseigner $strConnex */
	$strConnex = 'host='.$_ENV['host'].' dbname='. $_ENV['database'].' user='. $_ENV['user'].' password='.$_ENV['password'];
	$ptrDB = pg_connect($strConnex);
	if (!$ptrDB) {
		echo "<h1>Problème de connexion, vérifier vos paramètres dans $strConnex</h1>";
		exit(1); // fin du programme
	}
	return $ptrDB;
}
?>