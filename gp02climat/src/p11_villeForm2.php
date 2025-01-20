<?php
$titrePage = "Modification de la table ville"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
// echo retour($titrePage, "p11_villeForm.php");

$type = $_GET['maj'];

if ($type == 'ajout') {
	// echo test($_GET);
	// if (insertOK($_GET)) {
		insere();
	// }
} elseif ($type == 'modif') {
	// echo test($_GET);
	modifie();
}

echo getFinHTML();



function modifie() {
	$ptrDB = connexion();

	// récupération des valeurs
	$valeur = array();
	$valeur['id'] = $_GET['id'];
	$valeur['ville'] = str_replace("%20", " ", $_GET['ville']);
	$valeur['altitude'] = $_GET['altitude'];
	$valeur['latitude'] = $_GET['latitude'];
	$valeur['longitude'] = $_GET['longitude'];
	$id = $_GET['id'];
	$ville = $valeur['ville'];
	$altitude = $_GET['altitude'];
	$latitude = $_GET['latitude'];
	$longitude = $_GET['longitude'];

	// la requête
	$query = "UPDATE p11_ville
		SET ville_altitude = $3,
		ville_latitude = $4,
		ville_longitude = $5
		WHERE ville_code = $1
		AND ville_nom = $2";
	pg_prepare($ptrDB, "reqprep", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprep", $valeur);

	if ($ptrQuery) {
		echo intoBalise("h3", "La modification de la ville 
			de $ville a été effectuée avec succès !",
			['class' => 'vert']);
	} else {
		echo intoBalise("h3", "Problème lors
			de la modification de la ville 
			de $ville !",
			['class' => 'rouge']);
	}
		
	pg_free_result($ptrQuery);
	pg_close($ptrDB);

	$ville = $_GET['ville'];

	echo "<form action='p11_villeForm.php'>\n";
	echo "<p>";
	echo "<input type='hidden' name='maj' value='modification'/>\n";

	echo "<input type='hidden' name='id' value='$id'/>\n";
	echo "<input type='hidden' name='ville' value='$ville'/>\n";
	echo "<input type='hidden' name='altitude' value='$altitude'/>\n";
	echo "<input type='hidden' name='latitude' value='$latitude'/>\n";
	echo "<input type='hidden' name='longitude' value='$longitude'/>\n";
	echo "<input type='submit' value='retour'/>\n";
	echo "</p>";
	echo "</form>";
}

function insere() {
	$valeur = array();
	$ville = str_replace("%20", " ", $_GET['ville']);
	$valeur['ville_nom'] = $ville;
	$valeur['ville_altitude'] = $_GET['altitude'];
	$valeur['ville_latitude'] = $_GET['latitude'];
	$valeur['ville_longitude'] = $_GET['longitude'];
	insertOK($ville);

	$ptrDB = connexion();

	$query = "INSERT INTO p11_ville(
		ville_nom, ville_altitude,
		ville_latitude, ville_longitude)
		VALUES($1, $2, $3, $4)";
	pg_prepare($ptrDB, "reqprep", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprep", $valeur);

	if ($ptrQuery) {
		echo intoBalise("h3", "L'insertion de la ville 
			de $ville a été effectuée avec succès !",
			['class' => 'vert']);
	} else {
		echo intoBalise("h3", "Problème lors
			de l'insertion de la ville 
			de $ville !",
			['class' => 'rouge']);
	}

	pg_free_result($ptrQuery);
	pg_close($ptrDB);


	// str_replace sinon les villes commes Le Havre boguent
	$ville = str_replace(" ", "%20", $_GET['ville']);

	echo "<form action='p11_villeForm.php'>\n";
	echo "<p>";
	echo "<input type='hidden' name='maj' value='insertion'/>\n";
	echo "<input type='hidden' name='ville' value='$ville'/>\n";
	echo "<input type='hidden' name='altitude'
		value='".$_GET['altitude']."'/>\n";
	echo "<input type='hidden' name='latitude'
		value='".$_GET['latitude']."'/>\n";
	echo "<input type='hidden' name='longitude'
		value='".$_GET['longitude']."'/>\n";
	echo "<input type='submit' value='retour'/>\n";
	echo "</p>";
	echo "</form>";
}

/**
 * Teste si la ville $s
 * est déjà présente (même nom)
 * dans la table ville
 * et envoie un Warning le cas échéant
 **/
function insertOK(string $s) {
	$ptrDB = connexion();
	$query2 = "SELECT DISTINCT ville_nom
	FROM p11_ville
	WHERE ville_nom = $1";
	$valeur = array();
	$valeur['ville_nom'] = $s;
	pg_prepare($ptrDB, "reqprep2", $query2);
	$ptrQuery2 = pg_execute($ptrDB, "reqprep2", $valeur);
	$ligne = pg_fetch_row($ptrQuery2);
	if (isset($ligne[0])) {
		if ($ligne[0] == $s) {
			echo intoBalise("h3", "Avertissement !",
				['class' => 'rouge']);
			echo intoBalise("p", "Ce nom de ville
				$s existe déjà dans la table ville !
				Deux villes peuvent certes avoir le même nom.
				Si vous êtes sûr de vous, continuez
				en croisant les doigts !");
		}
	}

	pg_free_result($ptrQuery2);
	pg_close($ptrDB);
}

?>