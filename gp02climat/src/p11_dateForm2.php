<?php
$titrePage = "Modification de la table climat"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();

$type = $_GET['maj'];

if ($type == 'ajout') {
	insere();
}

echo getFinHTML();


function insere() {
	$ptrDB = connexion();

	$query = "INSERT INTO p11_date
		VALUES($1)";
	pg_prepare($ptrDB, "reqprep", $query);
	$valeur = array();
	$date = dateTransform($_GET['date']);
	$valeur['date_date'] = $date;
	$ptrQuery = pg_execute($ptrDB, "reqprep", $valeur);
	if (!$ptrQuery) {
		echo intoBalise("h3", "L'insertion a échoué :
			la date $date est déjà présente dans la table !",
		['class' => 'rouge']);

		echo "<form action='p11_dateForm.php'>\n";
		echo "<p>";
		echo "<input type='hidden' name='maj' value='insertion'/>\n";
		echo "<input type='submit' value='retour'/>\n";
		echo "</p>";
		echo "</form>";
	} else {
		echo intoBalise("h3", "L'insertion de $date
			a été effectuée avec succès !",
			['class' => 'vert']);

		echo "<form action='p11_dateForm.php'>\n";
		echo "<p>";
		echo "<input type='hidden' name='maj' value='insertion'/>\n";
		echo "<input type='submit' value='retour'/>\n";
		echo "</p>";
		echo "</form>";
	}
	pg_free_result($ptrQuery);
	pg_close($ptrDB);
}

function dateTransform(string $date) : string {
	$t = explode("-", $date);
	$annee = $t[0];
	$mois = $t[1];
	// echo $date;
	$reponse = $annee."-01-".$mois;
	// echo $reponse;
	return $reponse;
}

?>