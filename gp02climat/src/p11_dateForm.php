<?php
$titrePage = "Modification de la table des dates"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
echo retour($titrePage, "gp02dateModif.php");
// echo test($_GET);

$type = $_GET['maj'];
if ($type == 'insertion') {
	echo formulaireInsertionDate();
} elseif ($type == 'suppression') {
	echo formulaireSuppressionDate();
}


echo getFinHTML();

/**
 * formulaireSuppressionDate()
 * @return : string : tout s'est bien passé
 */
function formulaireSuppressionDate() {
	echo intoBalise("h3",
		"Supression d'un enregistrement dans la table date",
		['class' => 'rouge']);
	$ptrDB = connexion();
	$query = "DELETE
		FROM p11_date
		WHERE date_date = $1";
	pg_prepare($ptrDB, "suppr", $query);
	$valeur = array();
	$valeur['date'] = $_GET['date'];
	$date = $_GET['date'];
	$ptrQuery = pg_execute($ptrDB, "suppr", $valeur);
	if (!$ptrQuery) {
		echo intoBalise("h3", "La supression de $date a échoué :
			cette date est encore présente
			dans la table climat !",
		['class' => 'rouge']);
	} else {
		echo intoBalise("h3", "La supression de $date
			a été effectuée avec succès !",
		['class' => 'vert']);
	}
}

/**
 * formulaireInsertionDate()
 * @return : string : le formulaire
 */
function formulaireInsertionDate() {
	$ptrDB = connexion();
	$query = "SELECT DISTINCT * FROM p11_ville";
	$ptrQuery = pg_query($ptrDB, $query);

	echo intoBalise("h3",
		"Ajout d'un enregistrement dans la table date");

	echo "<form action='p11_dateForm2.php' method='get'>\n";
	echo "<ul>";

	echo "<li><b>Entrez une date au format AAAA-MM : </b>";
	echo "<input type='text' name='date' value='2022-04' />\n";
	echo "</li>";

	echo "</ul>\n";
	echo "<p>";
	echo intoBalise("input", "", ['type' => 'submit', 'name' => 'maj', 'value' => 'ajout']);
	echo "</p>";
	echo "</form>";
}

?>