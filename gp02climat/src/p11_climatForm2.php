<?php
$titrePage = "Modification de la table climat"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();

$type = $_GET['maj'];

if ($type == 'ajout') {
	// echo test($_GET);
	// if (insertOK($_GET)) {
		insere();
	// }
} elseif ($type == 'modif') {
	// echo test($_GET);
	// if (insertOK($_GET)) {
		modifie();
	// }	
}

echo getFinHTML();



function modifie() {
	$ptrDB = connexion();

	// requête pour récupérer l'id de la ville
	$query2 = "SELECT DISTINCT ville_code
			FROM p11_climat NATURAL JOIN p11_ville
			WHERE ville_nom=$1";
	pg_prepare($ptrDB, "reqprep2", $query2);
	$valeur = array();
	$valeur['ville_code'] = $_GET['ville'];
	$ville = $_GET['ville'];
	$ptrQuery2 = pg_execute($ptrDB, "reqprep2", $valeur);
	$resultat = pg_fetch_all($ptrQuery2);
	foreach ($resultat as $ligne) {
		foreach ($ligne as $colonne) {
			$id = $colonne;
		}
	}
	// requête principale
	$query = "UPDATE p11_climat
		SET tmin = $3,
		tmax = $4,
		precipitations = $5,
		ensoleillement = $6
		WHERE ville_code = $1
		AND date_date = $2";

	pg_prepare($ptrDB, "reqprep", $query);
	$valeur = array();
	$valeur['ville_code'] = "$id";
	$valeur['date_date'] = $_GET['date'];
	$date = $_GET['date'];
	$valeur['tmin'] = $_GET['tmin'];
	$valeur['tmax'] = $_GET['tmax'];
	$valeur['precipitations'] = $_GET['precipitations'];
	$valeur['ensoleillement'] = $_GET['ensoleillement'];
	$ptrQuery = pg_execute($ptrDB, "reqprep", $valeur);

	// on sauvegarde les valeurs du formulaire précédent
	$tmin = $valeur['tmin'];
	$tmax = $valeur['tmax'];
	$precipitations = $valeur['precipitations'];
	$ensoleillement = $valeur['ensoleillement'];

	if (!$ptrQuery) {
		echo intoBalise("h3", "La modification a échoué !",
		['class' => 'rouge']);
		if (! insertOK($_GET)) {
			echo intoBalise("h3", "Problème dans les températures !",
			['class' => 'rouge']);
			echo intoBalise("p", "Vous avez entré
				une température minimale : ".$tmin."
				qui est supérieure à la température
				maximale : ".$tmax." !",
				['class' => 'rouge']);
		}
	} else {
		echo intoBalise("h3", "La modification
			a été effectuée avec succès !",
		['class' => 'vert']);
		
		pg_free_result($ptrQuery);
	}
	pg_free_result($ptrQuery2);
	pg_close($ptrDB);

	echo "<form action='p11_climatForm.php'>\n";
	echo "<p>";
	echo "<input type='hidden' name='maj' value='modification'/>\n";
	echo "<input type='hidden' name='ville' value='$ville'/>\n";
	echo "<input type='hidden' name='date' value='$date'/>\n";
	echo "<input type='hidden' name='tmin' value='$tmin'/>\n";
	echo "<input type='hidden' name='tmax' value='$tmax'/>\n";
	echo "<input type='hidden' name='precipitations'
		value='$precipitations'/>\n";
	echo "<input type='hidden' name='ensoleillement'
		value='$ensoleillement'/>\n";
	echo "<input type='submit' value='retour'/>\n";
	echo "</p>";
	echo "</form>";
}

function insere() {
	$ptrDB = connexion();

	// on récupère l'id de la ville
	$query2 = "SELECT DISTINCT ville_code
			FROM p11_ville
			WHERE ville_nom=$1";
	pg_prepare($ptrDB, "reqprep2", $query2);
	$valeur = array();
	$valeur['ville_nom'] = $_GET['ville'];
	$ville = $_GET['ville'];
	$ptrQuery2 = pg_execute($ptrDB, "reqprep2", $valeur);
	$resultat = pg_fetch_all($ptrQuery2);
	foreach ($resultat as $ligne) {
		if (is_array($ligne)) {
			foreach ($ligne as $colonne) {
				$id = $colonne;
			}
		} else {
			echo $ligne;
		}
	}
	// on essaie d'insérer
	$query = "INSERT INTO p11_climat(ville_code,
		date_date,
		tmin,
		tmax,
		precipitations,
		ensoleillement)
		VALUES($1, $2, $3, $4, $5, $6)";
	pg_prepare($ptrDB, "reqprep", $query);
	$valeur = array();
	$valeur['ville_code'] = "$id";
	$valeur['date_date'] = $_GET['date'];
	$date = $_GET['date'];
	$valeur['tmin'] = $_GET['tmin'];
	$valeur['tmax'] = $_GET['tmax'];
	$valeur['precipitations'] = $_GET['precipitations'];
	$valeur['ensoleillement'] = $_GET['ensoleillement'];

	// on sauvegarde les valeurs du formulaire précédent
	$tmin = $valeur['tmin'];
	$tmax = $valeur['tmax'];
	$precipitations = $valeur['precipitations'];
	$ensoleillement = $valeur['ensoleillement'];

	// on vérifie si l'enregistrement n'est pas déjà présent
	$query3 = "SELECT (ville_code, date_date)
	FROM p11_climat
	WHERE ville_code = $1
	AND date_date = $2";
	$valeur3 = array();
	$valeur3['ville_code'] = "$id";
	$valeur3['date_date'] = $_GET['date'];
	pg_prepare($ptrDB, "reqprep3", $query3);
	$ptrQuery3 = pg_execute($ptrDB, "reqprep3", $valeur3);
	$ligne = pg_fetch_row($ptrQuery3);

	if ($ligne) { // s'il est déjà présent
		echo intoBalise("h3", "L'insertion a échoué :
			le climat de la ville de $ville
			pour la date $date
			est déjà présent dans la table !",
		['class' => 'rouge']);

		echo "<form action='p11_climatForm.php'>\n";
		echo "<p>";
		echo "<input type='hidden' name='ville' value='$ville'/>\n";
		echo "<input type='hidden' name='date' value='$date'/>\n";
		echo "<input type='hidden' name='maj' value='insertion'/>\n";
		echo "<input type='hidden' name='tmin' value='$tmin'/>\n";
		echo "<input type='hidden' name='tmax' value='$tmax'/>\n";
		echo "<input type='hidden' name='precipitations'
			value='$precipitations'/>\n";
		echo "<input type='hidden' name='ensoleillement'
			value='$ensoleillement'/>\n";
		echo "<input type='submit' value='retour'/>\n";
		echo "</p>";
		echo "</form>";
	}
	pg_free_result($ptrQuery3);

	$ptrQuery = pg_execute($ptrDB, "reqprep", $valeur);
	if (! $ptrQuery) { // si l'insertion échoue
		if (insertOK($_GET) && ! $ligne) {
			echo intoBalise("h3", "L'insertion a échoué !
				Le problème n'est pas répertorié.
				Contactez le concepteur du site.",
			['class' => 'rouge']);

			echo "<form action='p11_climatForm.php'>\n";
			echo "<p>";
			echo "<input type='hidden' name='maj' value='insertion'/>\n";
			echo "<input type='hidden' name='ville' value='$ville'/>\n";
			echo "<input type='hidden' name='date' value='$date'/>\n";
			echo "<input type='hidden' name='tmin' value='$tmin'/>\n";
			echo "<input type='hidden' name='tmax' value='$tmax'/>\n";
			echo "<input type='hidden' name='precipitations'
				value='$precipitations'/>\n";
			echo "<input type='hidden' name='ensoleillement'
				value='$ensoleillement'/>\n";
			echo "<input type='submit' value='retour'/>\n";
			echo "</p>";
			echo "</form>";
		}
		if (! insertOK($_GET) && ! $ligne) {
			echo intoBalise("h3", "Problème dans la température !",
				['class' => 'rouge']);
			$tmin = (int)$_GET['tmin'];
			$tmax = (int)$_GET['tmax'];
			echo intoBalise("p", "Vous avez entré
				une température minimale : ".$tmin."
				qui est supérieure à la température
				maximale : ".$tmax." !",
				['class' => 'rouge']);
			echo "<form action='p11_climatForm.php'>\n";
			echo "<p>";
			echo "<input type='hidden' name='maj' value='insertion'/>\n";
			echo "<input type='hidden' name='ville'
				value='".$_GET['ville']."'/>\n";
			echo "<input type='hidden' name='date'
				value='".$_GET['date']."'/>\n";
			echo "<input type='hidden' name='tmin' value='$tmin'/>\n";
			echo "<input type='hidden' name='tmax' value='$tmax'/>\n";
			echo "<input type='hidden' name='precipitations'
				value='$precipitations'/>\n";
			echo "<input type='hidden' name='ensoleillement'
				value='$ensoleillement'/>\n";
			echo "<input type='submit' value='retour'/>\n";
			echo "</p>";
			echo "</form>";
		}
	} else {
		echo intoBalise("h3", "L'insertion du climat
			pour la ville de $ville
			et pour la date $date
			a été effectuée avec succès !",
		['class' => 'vert']);

		echo "<form action='p11_climatForm.php'>\n";
		echo "<p>";
		echo "<input type='hidden' name='maj' value='insertion'/>\n";
		echo "<input type='hidden' name='ville' value='$ville'/>\n";
		echo "<input type='hidden' name='date' value='$date'/>\n";
		echo "<input type='hidden' name='tmin' value='$tmin'/>\n";
		echo "<input type='hidden' name='tmax' value='$tmax'/>\n";
		echo "<input type='hidden' name='precipitations'
			value='$precipitations'/>\n";
		echo "<input type='hidden' name='ensoleillement'
			value='$ensoleillement'/>\n";
		echo "<input type='submit' value='retour'/>\n";
		echo "</p>";
		echo "</form>";
	}
	pg_free_result($ptrQuery);
	pg_free_result($ptrQuery2);
	pg_close($ptrDB);
}

function insertOK(array $t) : bool {
	if ((int)$t['tmin'] > (int)$t['tmax']) {
		return false;
	}
	return true;
}

?>