<?php

// vérification de la présence des valeurs obligatoires d'un formulaire

function valideForm($method, $tabCles) {
	foreach ($tabCles as $cle) {
		if (!isset($method[$cle]))
			return FALSE;
		if (is_string($method[$cle]) && trim($method[$cle]) === "")
			return FALSE;
		if (is_array($method[$cle]) && empty($method[$cle]))
			return FALSE;
	}
	return TRUE;
}

// fonction vérifiant l'absence de tout contenu
// paramètre une référence sur le tableau $_GET ou $_POST
function videForm(&$method, $tabCles) {
	foreach ($tabCles as $cle) {
		if (isset($method[$cle]) and $method[$cle] !== "")
			return FALSE;
	}
	return TRUE;
}


// fabrication d'un input HTML de type text

function getInputText(string $nomVar, array $attributs=[]) : string {
	$inputHtml = "<input type='text' name='$nomVar' ";
	$inputHtml .= getInputValue($nomVar);
	if (!empty($attributs)) {
		foreach ($attributs as $attribut => $valeur)
		$inputHtml.= $attribut."='$valeur' ";
	}
	$inputHtml .= "/>";
	return $inputHtml;
}

// construction d'une liste HTML à partir d'un tableau
// usage de $_REQUEST comme dans getInputValue

function getFormResultIntoUL() : string {
	$resultat = "<ul>\n";
	foreach($_REQUEST as $var) {
		if (is_array($var)) {
			$resultat .= "<li><ul>";
			foreach ($var as $item) {
				$resultat .= "<li>$item</li>\n";
			}
			$resultat .= "</ul></li>\n";
		}
		else {
			$resultat .= "<li>$var</li>\n";
		}
	}
	return $resultat."</ul>\n";
}

// généralisation de getInputText pour un type d'input quelconque
// $attributs est un tableau associatif décrivant les attributs de l'élément HTML
// peut inclure la valeur par défaut (attribut value)

function getInputType(string $nomVar, string $typeInput, array $attributs=[]) : string {
	$inputHtml = "<input type='$typeInput' name='$nomVar' ";
	if (!empty($attributs)) {
		foreach ($attributs as $attribut => $valeur)
			$inputHtml.= $attribut."='$valeur' ";
	}
	$inputHtml .= "/>";
	return $inputHtml;
}

function getFormForCollectivite(array $assoc) : string
{
	// champ action du formulaire renvoyant sur le script appelant (seance5_exo2_2.php)
	$formUpdateCol = "<form action='" . $_SERVER['PHP_SELF'] . "' method='GET'>\n";

	// champ caché pour l'identifiant
	$attributsInput = [];
	if (isset($assoc['col_code']))
		$attributsInput['value'] = $assoc['col_code'];
	$inputColCode = getInputType("col_code", 'hidden', $attributsInput);
	$formUpdateCol .= $inputColCode . "<br />\n";

	// champ texte pour le nom
	$attributsInput = ['size' => '10', 'required'=>'required'];
	if (isset($assoc['col_nom']))
		$attributsInput['value'] = $assoc['col_nom'];
	$inputColNom = getInputType("col_nom", 'text', $attributsInput);
	$formUpdateCol .= intoBalise("label", "col_nom : $inputColNom") . "<br />\n";

	// champ number pour la population
	$attributsInput = ['size' => '10', 'required'=>'required'];
	if (isset($assoc['col_population']))
		$attributsInput['value'] = $assoc['col_population'];
	$inputColPop = getInputType("col_population", 'number', $attributsInput);
	$formUpdateCol .= intoBalise("label", "col_population : $inputColPop") . "<br />\n";

	// champ number pour la superficie
	$attributsInput = ['size' => '6', 'required'=>'required'];
	if (isset($assoc['col_superficie']))
		$attributsInput['value'] = $assoc['col_superficie'];
	$inputColSup = getInputType("col_superficie", 'number', $attributsInput);
	$formUpdateCol .= intoBalise("label", "col_superficie : $inputColSup") . "<br />\n";

	// champ text pour la région

	$attributsInput = ['size' => '16', 'required'=>'required'];
	if (isset($assoc['col_region']))
		$attributsInput['value'] = $assoc['col_region'];
	$inputColRegion = getInputType("col_region", 'text', $attributsInput);
	$formUpdateCol .= intoBalise("label", "col_region : $inputColRegion") . "<br />\n";

	// bouton d'envoi
	$formUpdateCol .= intoBalise("input", "", ['type' => 'submit', 'name' => 'go', 'value' => 'MAJ']);
	$formUpdateCol .= "</form>";
	return $formUpdateCol;
}

/**
 * formulaireInsertion()
 * @param String $t : nom de la base de donnée
 * @return : String : un lien vers la page précédente
 */
function formulaireInsertion(string $t) {
	$ptrDB = connexion();
	$tab = pg_meta_data($ptrDB, $t, false);

	echo intoBalise("h3", "Ajout d'un enregistrement dans $t");
	$formUpdateCol = "<form action='insertion.php' method='GET'>\n";
	echo "<ul>";
	foreach ($tab as $key => $val) {
		echo "<li><b>Entrez $key</b> : ";
		if ($val['not null'] == 1) {
			$notNull = "required";
		} else {
			$notNull = "";
		}
		if ($val['type'] == 'date') {
			$pourDate = "placeholder='yyyy-dd-mm'";
		} else {
			$pourDate = "";
		}
		// if not auto-increment ?
		echo intoBalise("input", "", ['type' => $val['type'], 'name' => '$key', $notNull, $pourDate]);
		echo "</li>";
	}
	echo "</ul><br />\n";
	echo intoBalise("input", "",
		['type' => 'submit', 'name' => 'go', 'value' => 'ajout']);
	echo "</form>";

	pg_close($ptrDB);
}


// function formulaireSuppression($table, $ligne);

// function formulaireModification($table, $ligne);


/**
 * retour()
 * @param String $s1 : le titre de la page en cours
 * @param String $s : le nom de la page précédente
 * @return : String : un lien vers la page précédente
 */
function retour($s1, $s) : string {
	$reponse = "";
	$reponse .= intoBalise("h2", $s1);
	$lien = intoBalise("a", "Cliquez ici", ["href" => $s]);
	$reponse .= intoBalise("p", $lien . " pour revenir à la page précédente.");
	return $reponse;
}

/**
 * modif()
 * @param String $s1 : le titre de la page
 * @param String $s : le nom de la page du formulaire
 * @return : String : un lien vers une page où l'on peut modifier la table
 */
function modif($s1, $s) : string {
	$reponse = "";
	$reponse .= intoBalise("h2", $s1);
	$lien = intoBalise("a", "Cliquez ici", ["href" => $s]);
	$reponse .= intoBalise("p", $lien . " pour modifier cette page.");
	return $reponse;
}

/**
 * getClimatById
 * @param array $t : tableau (couple) ville_code, date_date
 * @return array tableau associatif associé au climat
 */
function getClimatById(array $t) : array {
	$ptrDB = connexion();
	$query = "SELECT * FROM p11_climat
		WHERE col_code = $1";
	$ptrQuery = pg_prepare($ptrDB, "reqprep", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprep", array($id));
	if (isset($ptrQuery)) {
		$resu = pg_fetch_assoc($ptrQuery);
		if (empty($resu)) {
			$resu =  array("message" => "Identifiant de collectivité non valide : $id");
		}
	}
	pg_free_result($ptrQuery);
	pg_close($ptrDB);
	return $resu;
}

/**
 * getAllClimat()
 * @return array tableau associatif associé au climat
 */
function getAllClimat() : array {
	$ptrDB = connexion();
	$query = "SELECT ville_nom, date_date, tmin, tmax, precipitations, ensoleillement
		FROM p11_climat NATURAL JOIN p11_ville
		ORDER BY ville_nom, date_date";
	pg_prepare($ptrDB, "reqprep", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprep", array());
	$query2 = "SELECT COUNT(*)
		FROM p11_climat NATURAL JOIN p11_ville";
	$ptr2 = pg_query($ptrDB, $query2);
	$kMax = pg_fetch_row($ptr2, 0);
	$resu = array();
	if (isset($ptrQuery)) {
		array_push($resu, titreClimat());
		for ($k=0; $k < $kMax[0]; $k++) { // k = numéro de la ligne
			$ligne = pg_fetch_row($ptrQuery,$k);
			array_push($resu, $ligne);
		}
	}
	pg_free_result($ptrQuery);
	pg_free_result($ptr2);
	pg_close($ptrDB);
	return $resu;
}

// /**
//  * getAllClimatModif()
//  * @return array tableau associatif associé au climat
//  */
// function getAllClimatModif() : array {
// 	$ptrDB = connexion();
// 	$query = "SELECT ville_nom, date_date, tmin, tmax, precipitations, ensoleillement
// 		FROM p11_climat NATURAL JOIN p11_ville";
// 	pg_prepare($ptrDB, "reqprep", $query);
// 	$ptrQuery = pg_execute($ptrDB, "reqprep", array());
// 	$resu = array();
// 	if (isset($ptrQuery)) {
// 		$kMax = pg_NumRows($ptrQuery);
// 		array_push($resu, titreClimatModif());
// 		for ($k=0; $k < $kMax; $k++) { // k = numéro de la ligne
// 			$ligne = pg_fetch_row($ptrQuery,$k);
// 			array_push($resu, $ligne);
// 		}
// 	}
// 	pg_free_result($ptrQuery);
// 	pg_close($ptrDB);
// 	return $resu;
// }

// /**
//  * titreClimatModif()
//  * @return array tableau associatif --> titre de la table climat
//  */
// function titreClimatModif() : array {
// 	$ligne = array();
// 	array_push($ligne, 'ville_code');
// 	array_push($ligne, 'Date');
// 	array_push($ligne, 'Température Maximale (en °)');
// 	array_push($ligne, 'Température Minimale (en °)');
// 	array_push($ligne, 'Precipitations (en mm)');
// 	array_push($ligne, 'Ensoleillement mensuel (en heures)');
// 	array_push($ligne, 'Action');
// 	return $ligne;
// }

/**
 * titreClimat()
 * @return array tableau associatif --> titre de la table climat
 */
function titreClimat() : array {
	$ligne = array();
	array_push($ligne, 'Ville');
	array_push($ligne, 'Date');
	array_push($ligne, 'Température Maximale (en °)');
	array_push($ligne, 'Température Minimale (en °)');
	array_push($ligne, 'Precipitations (en mm)');
	array_push($ligne, 'Ensoleillement mensuel (en heures)');
	return $ligne;
}

/**
 * getAllVille()
 * @return array tableau associatif associé aux villes
 */
function getAllVille() : array {
	$ptrDB = connexion();
	$query = "SELECT * FROM p11_ville
		ORDER BY ville_nom";
	$query2 = "SELECT COUNT(*) FROM p11_ville";
	pg_prepare($ptrDB, "reqprep", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprep", array());
	$ptr2 = pg_query($ptrDB, $query2);
	$kMax = pg_fetch_row($ptr2, 0);	
	$resu = array();
	if (isset($ptrQuery)) {
		array_push($resu, titreVille());
		for ($k=0; $k < $kMax[0]; $k++) { // k = numéro de la ligne
			$ligne = pg_fetch_row($ptrQuery,$k);
			array_push($resu, $ligne);
		}
	}
	pg_free_result($ptrQuery);
	pg_free_result($ptr2);
	pg_close($ptrDB);
	return $resu;
}

// /**
//  * getAllVilleModif()
//  * @return array tableau associatif associé aux villes
//  */
// function getAllVilleModif() : array {
// 	$ptrDB = connexion();
// 	$query = "SELECT * FROM p11_ville";
// 	pg_prepare($ptrDB, "reqprep", $query);
// 	$ptrQuery = pg_execute($ptrDB, "reqprep", array());
// 	$resu = array();
// 	if (isset($ptrQuery)) {
// 		$kMax = pg_NumRows($ptrQuery);
// 		array_push($resu, titreVilleModif());
// 		for ($k=0; $k < $kMax; $k++) { // k = numéro de la ligne
// 			$ligne = pg_fetch_row($ptrQuery,$k);
// 			array_push($resu, $ligne);
// 		}
// 	}
// 	pg_free_result($ptrQuery);
// 	pg_close($ptrDB);
// 	return $resu;
// }

/**
 * titreVille()
 * @return array tableau associatif --> titre de la table climat
 */
function titreVille() : array {
	$ligne = array();
	array_push($ligne, 'ville_code');
	array_push($ligne, 'Nom de la ville');
	array_push($ligne, 'Altitude (en m)');
	array_push($ligne, 'Latitude (en °)');
	array_push($ligne, 'Longitude (en °)');
	return $ligne;
}

// /**
//  * titreVilletModif()
//  * @return array tableau associatif --> titre de la table climat
//  */
// function titreVilletModif() : array {
// 	$ligne = array();
// 	array_push($ligne, 'ville_code');
// 	array_push($ligne, 'Nom de la ville');
// 	array_push($ligne, 'Altitude (en m)');
// 	array_push($ligne, 'Latitude (en °)');
// 	array_push($ligne, 'Longitude (en °)');
// 	array_push($ligne, 'Action');
// 	return $ligne;
// }

/**
 * getAllDate()
 * @return array tableau associatif associé aux villes
 */
function getAllDate() : array {
	$ptrDB = connexion();
	$query = "SELECT * FROM p11_date
		ORDER BY date_date";
	pg_prepare($ptrDB, "reqprep", $query);
	$query2 = "SELECT COUNT(*) FROM p11_date";
	$ptrQuery = pg_execute($ptrDB, "reqprep", array());
	$ptr2 = pg_query($ptrDB, $query2);
	$kMax = pg_fetch_row($ptr2, 0);
	$resu = array();
	if (isset($ptrQuery)) {
		array_push($resu, titreDate());
		for ($k=0; $k < $kMax[0]; $k++) { // k = numéro de la ligne
			$ligne = pg_fetch_row($ptrQuery,$k);
			array_push($resu, $ligne);
		}
	}
	pg_free_result($ptrQuery);
	pg_free_result($ptr2);
	pg_close($ptrDB);
	return $resu;
}

// /**
//  * getAllDateModif()
//  * @return array tableau associatif associé aux villes
//  */
// function getAllDateModif() : array {
// 	$ptrDB = connexion();
// 	$query = "SELECT * FROM p11_date";
// 	pg_prepare($ptrDB, "reqprep", $query);
// 	$ptrQuery = pg_execute($ptrDB, "reqprep", array());
// 	$resu = array();
// 	if (isset($ptrQuery)) {
// 		$kMax = pg_NumRows($ptrQuery);
// 		array_push($resu, titreDateModif());
// 		for ($k=0; $k < $kMax; $k++) { // k = numéro de la ligne
// 			$ligne = pg_fetch_row($ptrQuery,$k);
// 			array_push($resu, $ligne);
// 		}
// 	}
// 	pg_free_result($ptrQuery);
// 	pg_close($ptrDB);
// 	return $resu;
// }

/**
 * titreDate()
 * @return array tableau associatif --> titre de la table climat
 */
function titreDate() : array {
	$ligne = array();
	array_push($ligne, 'Date au format AAAA-JJ-MM');
	return $ligne;
}

// /**
//  * titreDatetModif()
//  * @return array tableau associatif --> titre de la table climat
//  */
// function titreDatetModif() : array {
// 	$ligne = array();
// 	array_push($ligne, 'Date au format AAAA-JJ-MM');
// 	array_push($ligne, 'Action');
// 	return $ligne;
// }

function insertCollectivite(array $collectivite) : array {
	$ptrDB = connexion();

	/* DONE? préparation et exécution de la requête INSERT ici */
	$query = "INSERT INTO collectivite(col_code,
		col_nom, col_population, col_superficie, col_region)
		VALUES($1, $2, $3, $4, $5)";
	pg_prepare($ptrDB, "reqprep", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprep", $collectivite);
	if (!$ptrQuery) {
		return array("message" => "L'insertion de la collectivité a échoué :
			$collectivite[0], $collectivite[1]");
	}

	pg_free_result($ptrQuery);
	pg_close($ptrDB);
	return getCollectiviteById($collectivite['col_code']);
}

function updateCollectivite(array $collectivite) : array {
	$ptrDB = connexion();

	/* DONE? préparation et exécution de la requête UPDATE ici */
	$query = "UPDATE collectivite SET col_nom=$2,
		col_population=$3, col_superficie=$4, col_region=$5
		WHERE col_code=$1";
	pg_prepare($ptrDB, "reqprep", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprep", $collectivite);
	if (!$ptrQuery) {
		return array("message" => "La MAJ de la collectivité a échoué :
			$collectivite[0], $collectivite[1]");
	}

	pg_free_result($ptrQuery);
	pg_close($ptrDB);
	return getCollectiviteById($collectivite['col_code']);
}

function deleteCollectivite(string $id) {
	$ptrDB = connexion();

	/* DONE? préparation et exécution de la requête DELETE ici */
	$query = "DELETE FROM collectivite
		WHERE col_code=$1";
	pg_prepare($ptrDB, "reqprep", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprep", array($id));
	if (!$ptrQuery) {
		return array("message" => "La supression de la collectivité $id a échoué.");
	}

	pg_free_result($ptrQuery);
	pg_close($ptrDB);
}

function getVilleById(string $id) : array {
	$ptrDB = connexion();
	$query = "SELECT * FROM ville WHERE vil_num = $1";
	$ptrQuery = pg_prepare($ptrDB, "reqprepVille", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprepVille", array($id));
	if (isset($ptrQuery)) {
		$resu = pg_fetch_assoc($ptrQuery);
		if (empty($resu)) {
			$resu =  array("message" => "Identifiant de ville non valide : $id");
		}
	}
	pg_free_result($ptrQuery);
	pg_close($ptrDB);
	return $resu;
}

function getAllVilles() : array {
	$ptrDB = connexion();
	$query = "SELECT * FROM ville";
	$ptrQuery = pg_prepare($ptrDB, "reqprepVille", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprepVille", array());
	$resu = array();
	if (isset($ptrQuery)) {
		$kMax = pg_NumRows($ptrQuery);
		for ($k=0; $k < $kMax; $k++) { // k = numéro de la ligne
			$ligne = pg_fetch_row($ptrQuery,$k);
			array_push($resu, $ligne);
		}
	}
	pg_free_result($ptrQuery);
	pg_close($ptrDB);
	return $resu;
}

function insertVille(array $collectivite) : array {
	$ptrDB = connexion();
	$query = "INSERT INTO ville(vil_num, 
		vil_nom, vil_population, vil_statut, col_code)
		VALUES($1, $2, $3, $4, $5)";
	$ptrQuery = pg_prepare($ptrDB, "reqprepVille", $query);
	// tester la clé primaire ?
	$ptrQuery = pg_execute($ptrDB, "reqprepVille", $collectivite);
	if (!$ptrQuery) {
		return array("message" => "L'insertion de la ville a échoué :
			$collectivite[0], $collectivite[1]");
	}
	pg_free_result($ptrQuery);
	pg_close($ptrDB);
	return getVilleById($collectivite['vil_num']);
}

function updateVille(array $collectivite) : array {
	$ptrDB = connexion();
	$query = "UPDATE ville SET vil_nom=$2,
		vil_population=$3, vil_statut=$4, col_code=$5
		WHERE vil_num=$1";
	$ptrQuery = pg_prepare($ptrDB, "reqprepVille", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprepVille", $collectivite);
	if (!$ptrQuery) {
		return array("message" => "La MAJ de la ville a échoué :
			$collectivite[0], $collectivite[1]");
	}

	pg_free_result($ptrQuery);
	pg_close($ptrDB);
	return getVilleById($collectivite['vil_num']);
}

function deleteVille(string $id) {
	$ptrDB = connexion();

	/* DONE? préparation et exécution de la requête DELETE ici */
	$query = "DELETE FROM ville
		WHERE vil_num=$1";
	$ptrQuery = pg_prepare($ptrDB, "reqprepVille", $query);
	$ptrQuery = pg_execute($ptrDB, "reqprepVille", array($id));
	if (!$ptrQuery) {
		return array("message" => "La supression de la ville $id a échoué.");
	}

	pg_free_result($ptrQuery);
	pg_close($ptrDB);
}

?>