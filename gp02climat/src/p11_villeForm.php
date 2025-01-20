<?php
$titrePage = "Modification de la table ville"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
echo retour($titrePage, "gp02villesModif.php");
// echo test($_GET);

$type = $_GET['maj'];
if ($type == 'insertion') {
	echo formulaireInsertionVille();
} elseif ($type == 'suppression') {
	echo formulaireSuppressionVille();
} elseif ($type == 'modification') {
	$id = $_GET['id'];
	echo formulaireModificationVille($id);
}


echo getFinHTML();


/**
 * formulaireModificationVille()
 * @return : string : le formulaire
 */
function formulaireModificationVille($id) {
	$ptrDB = connexion();
	$query = "SELECT ville_altitude, ville_latitude,
	ville_longitude, ville_nom
	FROM p11_ville
	WHERE ville_code = $1";
	pg_prepare($ptrDB, "maj", $query);
	$valeur = array();
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	}
	$valeur['id'] = $id;

	$ptrQuery = pg_execute($ptrDB, "maj", $valeur);
	$ligne = pg_fetch_row($ptrQuery);

	// récupération des valeurs
	if (isset($_GET['ville'])) {
		$ville = str_replace("%20", " ", $_GET['ville']);
	} else {
		$ville = $ligne[3];
	}
	if (isset($_GET['altitude'])) {
		$altitude = $_GET['altitude'];
	} else {
		$altitude = $ligne[0];
	}
	if (isset($_GET['latitude'])) {
		$latitude = $_GET['latitude'];
	} else {
		$latitude = $ligne[1];
	}
	if (isset($_GET['longitude'])) {
		$longitude = $_GET['longitude'];
	} else {
		$longitude = $ligne[2];
	}
	
	echo intoBalise("h3",
		"Modification d'un enregistrement dans la table ville");
	echo intoBalise("p", "Pour la ville de ".$ville);

	// str_replace sinon les villes commes Le Havre boguent
	$ville = str_replace(" ", "%20", $ligne[3]);

	echo "<form action='p11_villeForm2.php' method='get'>\n";
	echo "<ul>";

	echo "<li><b>Entrez l'altitude de la ville (en m) : </b>";

	echo "<input type='hidden' name='id' value='$id' />\n";
	echo "<input type='hidden' name='ville' value='".$ville."' />\n";
	echo "<input type='text' name='altitude'
		value='$altitude' />\n";
	echo "</li>";
	echo "<li><b>Entrez la latitude (en °): </b>";
	echo "<input type='text' name='latitude' value='$latitude' />\n";
	echo "</li>";
	echo "<li><b>Entrez la longitude (en °) : </b>";
	echo "<input type='text' name='longitude' value='$longitude' />\n";
	echo "</li>";
	echo "</ul>\n";
	echo "<p>";
	echo intoBalise("input", "",
		['type' => 'submit', 'name' => 'maj', 'value' => 'modif']);
	echo "</p>";
	echo "</form>";
}

/**
 * formulaireSuppressionVille()
 * @return : string : tout s'est bien passé
 */
function formulaireSuppressionVille() {
	echo intoBalise("h3",
		"Supression d'un enregistrement dans la table ville",
		['class' => 'rouge']);
	$ptrDB = connexion();

	$valeur = array();
	$valeur['id'] = $_GET['id'];

	// recherche du nom de la ville
	$query2 = "SELECT DISTINCT ville_nom
	FROM p11_ville
	WHERE ville_code = $1";
	pg_prepare($ptrDB, "suppr2", $query2);
	$ptrQuery2 = pg_execute($ptrDB, "suppr2", $valeur);
	$ligne = pg_fetch_row($ptrQuery2);
	$ville = $ligne[0];

	$query = "DELETE
		FROM p11_ville
		WHERE ville_code = $1";
	pg_prepare($ptrDB, "suppr", $query);
	$ptrQuery = pg_execute($ptrDB, "suppr", $valeur);
	if (!$ptrQuery) {
		echo intoBalise("h3", "La suppression a échoué :
			la ville de $ville possède
			encore des enregistrements
			dans la table des climats !",
		['class' => 'rouge']);
	} else {
		echo intoBalise("h3", "La suppression
			a été effectuée avec succès !",
			['class' => 'vert']);
	}
}

/**
 * formulaireInsertionVille()
 * @return : string : le formulaire
 */
function formulaireInsertionVille() {
	echo intoBalise("h3",
		"Ajout d'un enregistrement dans la table ville");
	// récupération des valeurs
	$ville = 'Grosville';
	if (isset($_GET['altitude'])) {
		$altitude = $_GET['altitude'];
	} else {
		$altitude = '56';
	}
	if (isset($_GET['latitude'])) {
		$latitude = $_GET['latitude'];
	} else {
		$latitude = '49.5000';
	}
	if (isset($_GET['longitude'])) {
		$longitude = $_GET['longitude'];
	} else {
		$longitude = '1.7500';
	}

	echo "<form action='p11_villeForm2.php' method='get'>\n";
	echo "<ul>";

	echo "<li><b>Nom de la ville : </b>";
	echo "<input type='text' name='ville' value='$ville' />\n";
	echo "</li>";

	echo "<li><b>Entrez l'altitude de la ville (en m) : </b>";
	echo "<input type='text' name='altitude'
		value='$altitude' />\n";
	echo "</li>";
	echo "<li><b>Entrez la latitude (en °): </b>";
	echo "<input type='text' name='latitude' value='$latitude' />\n";
	echo "</li>";
	echo "<li><b>Entrez la longitude (en °) : </b>";
	echo "<input type='text' name='longitude' value='$longitude' />\n";
	echo "</li>";
	echo "</ul>\n";
	echo "<p>";
	echo intoBalise("input", "", ['type' => 'submit', 'name' => 'maj', 'value' => 'ajout']);
	echo "</p>";
	echo "</form>";
}

?>