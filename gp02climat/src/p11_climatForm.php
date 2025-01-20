<?php
$titrePage = "Modification de la table climat"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
echo retour($titrePage, "gp02climatModif.php");
// echo test($_GET);

if (isset($_GET['maj'])) {
	$type = $_GET['maj'];
} else {
	$type = "pb";
}





if ($type == 'insertion') {
	echo formulaireInsertionClimat();
} elseif ($type == 'suppression') {
	echo formulaireSuppressionClimat();
} elseif ($type == 'modification') {
	$ville = $_GET['ville'];
	$date = $_GET['date'];
	echo formulaireModificationClimat($ville, $date);
} else {
	echo intoBalise("h3", "Oups !");
}


echo getFinHTML();


/**
 * formulaireModificationClimat()
 * @return : string : le formulaire
 */
function formulaireModificationClimat($ville, $date) {
	// echo intoBalise("p", "$ville $date");
	$ptrDB = connexion();
	$query = "SELECT tmin, tmax, precipitations, ensoleillement
	FROM p11_climat NATURAL JOIN p11_ville
	WHERE ville_nom = $1
	AND date_date = $2";
	pg_prepare($ptrDB, "maj", $query);
	$valeur = array();
	$valeur['ville'] = $ville;
	$valeur['date'] = $date;

	$ptrQuery = pg_execute($ptrDB, "maj", $valeur);
	$ligne = pg_fetch_row($ptrQuery);

	// récupération éventuelle des valeurs
	if (isset($_GET['tmin'])) {
		$tmin = $_GET['tmin'];
	} else {
		$tmin = $ligne[0];;
	}
	if (isset($_GET['tmax'])) {
		$tmax = $_GET['tmax'];
	} else {
		$tmax = $ligne[1];
	}
	if (isset($_GET['precipitations'])) {
		$precipitations = $_GET['precipitations'];
	} else {
		$precipitations = $ligne[2];
	}
	if (isset($_GET['ensoleillement'])) {
		$ensoleillement = $_GET['ensoleillement'];
	} else {
		$ensoleillement = $ligne[3];
	}

	echo intoBalise("h3",
		"Modification d'un enregistrement dans la table climat");
	echo intoBalise("p", "Pour la ville de ".$ville
		." et pour la date ".$date);


	echo "<form action='p11_climatForm2.php' method='get'>\n";
	echo "<ul>";

	echo "<li><b>Entrez une température minimale : </b>";
	echo "<input type='hidden' name='ville' value='$ville' />\n";
	echo "<input type='hidden' name='date' value='$date' />\n";
	echo "<input type='text' name='tmin'
		value='$tmin' />\n";
	echo "</li>";
	echo "<li><b>Entrez une température maximale : </b>";
	echo "<input type='text' name='tmax'
	value='$tmax' />\n";
	echo "</li>";
	echo "<li><b>Entrez la précipitation mensuelle (en mm) : </b>";
	echo "<input type='text' name='precipitations'
	value='$precipitations' />\n";
	echo "</li>";
	echo "<li><b>Entrez la durée mensuelle
		d'ensoleillement (en heures) : </b>";
	echo "<input type='text' name='ensoleillement'
	value='$ensoleillement' />\n";
	echo "</li>";
	echo "</ul>\n";
	echo "<p>";
	echo intoBalise("input", "", ['type' => 'submit', 'name' => 'maj', 'value' => 'modif']);
	echo "</p>";
	echo "</form>";
}

/**
 * formulaireSuppressionClimat()
 * @return : string : tout s'est bien passé
 */
function formulaireSuppressionClimat() {
	echo intoBalise("h3",
		"Suppression d'un enregistrement dans la table climat",
		['class' => 'rouge']);
	$ptrDB = connexion();
	$query = "DELETE
		FROM p11_climat
		WHERE ville_code = (SELECT DISTINCT ville_code
			FROM p11_ville
			WHERE ville_nom = $1)
		AND date_date = $2";
	pg_prepare($ptrDB, "suppr", $query);
	$valeur = array();
	$valeur['ville'] = $_GET['ville'];
	$valeur['date'] = $_GET['date'];
	$ptrQuery = pg_execute($ptrDB, "suppr", $valeur);
	if (!$ptrQuery) {
		echo intoBalise("h3", "La suppression a échoué",
		['class' => 'rouge']);
	} else {
		echo intoBalise("h3", "La suppression
				a été effectuée avec succès !",
		['class' => 'vert']);
	}
}

/**
 * formulaireInsertionClimat()
 * @return : string : le formulaire
 */
function formulaireInsertionClimat() {
	$ptrDB = connexion();
	$query = "SELECT DISTINCT * FROM p11_ville
		ORDER BY ville_nom";
	$ptrQuery = pg_query($ptrDB, $query);

	// récupération éventuelle des valeurs
	if (isset($_GET['ville'])) {
		$ville = $_GET['ville'];
	} else {
		$ligne = pg_fetch_row($ptrQuery);
		$ville = $ligne[1];
	}

	echo intoBalise("h3",
		"Ajout d'un enregistrement dans la table climat");

	echo "<form action='p11_climatForm2.php' method='get'>\n";
	echo "<ul>";

	echo "<li><b>Sélectionnez la ville : </b>";
	echo "<select name='ville'>";
	if ($ptrQuery) {
		$numLig = 0;
		while($ligne = pg_fetch_row($ptrQuery,$numLig)) {
			$numLig++;
			echo "<option ";
			if ($ville == $ligne[1]) {
				echo "selected='selected' ";
			}
			echo ">".$ligne[1]."</option>\n";
		}
	}
	echo "</select>\n";
	echo "</li>";

	// récupération éventuelle des valeurs
	if (isset($_GET['date'])) {
		$date = $_GET['date'];
	} else {
		$date = 'Paris';
	}

	echo "<li><b>Sélectionnez la date : </b>";
	echo "<select name='date'>";
	$query = "SELECT DISTINCT * FROM p11_date
		ORDER BY date_date";
	$ptrQuery = pg_query($ptrDB, $query);
	if ($ptrQuery) {
		$numLig = 0;
		while($ligne = pg_fetch_row($ptrQuery,$numLig)) {
			$numLig++;
			echo "<option ";
			if ($date == $ligne[0]) {
				echo "selected='selected' ";
			}
			echo ">".$ligne[0]."</option>\n";
		}
	}
	echo "</select>\n";
	echo "</li>";

	// récupération éventuelle des valeurs
	if (isset($_GET['tmin'])) {
		$tmin = $_GET['tmin'];
	} else {
		$tmin = 0;
	}
	if (isset($_GET['tmax'])) {
		$tmax = $_GET['tmax'];
	} else {
		$tmax = 40;
	}
	if (isset($_GET['precipitations'])) {
		$precipitations = $_GET['precipitations'];
	} else {
		$precipitations = 50.5;
	}
	if (isset($_GET['ensoleillement'])) {
		$ensoleillement = $_GET['ensoleillement'];
	} else {
		$ensoleillement = 144;
	}
	// début du formulaire

	echo "<li><b>Entrez une température minimale : </b>";
	echo "<input type='text' name='tmin' value='$tmin' />\n";
	echo "</li>";
	echo "<li><b>Entrez une température maximale : </b>";
	echo "<input type='text' name='tmax' value='$tmax' />\n";
	echo "</li>";
	echo "<li><b>Entrez la précipitation mensuelle (en mm) : </b>";
	echo "<input type='text' name='precipitations'
		value='$precipitations' />\n";
	echo "</li>";
	echo "<li><b>Entrez la durée mensuelle
		d'ensoleillement (en heures) : </b>";
	echo "<input type='text' name='ensoleillement'
		value='$ensoleillement' />\n";
	echo "</li>";
	echo "</ul>\n";
	echo "<p>";
	echo intoBalise("input", "",
		['type' => 'submit', 'name' => 'maj', 'value' => 'ajout']);
	echo "</p>";
	echo "</form>";
}

?>