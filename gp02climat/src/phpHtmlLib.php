<?php

function test(array $t) {
	echo "<ul>";
	foreach($t as $cle => $val) {
		echo "<li>";
		if (is_array($val)) {
			echo "$cle : ";
			echo "<ul>";
			foreach($val as $v) {
				echo "<li>$v</li>";
			}
			echo "</ul>";
		} else {
			echo "$cle : $val";
		}
		echo "</li>";
	}
	echo "</ul>";
}

function intoBalise3(string $nomElement, string $contenuElement): string {
	if ($contenuElement == "") {
		return "<$nomElement />";
	}
	return "<$nomElement>$contenuElement</$nomElement>";
}

function intoBalise(string $nomElement, string $contenuElement, array $params=null): string {
	if ($params == null) {
		return intoBalise3($nomElement, $contenuElement);
	}
	$reponse = "<".$nomElement;
	foreach ($params as $key => $value) {
		$reponse = $reponse." ".$key."='".$value."'";
	}
	$reponse .= ">".$contenuElement."</".$nomElement.">";
	return $reponse;
}

/**
 * ecritTableauModif()
 * @param array $t : un tableau
 * @param string $s : le nom de la table
 * @return : void
 * écrit le tableau $t sous forme de <table>
 */
function ecritTableauModif(array $t, string $s) : void {
	if (!is_array($t)) {
		echo intoBalise("h3", " l'entrée n'est pas un tableau", ["class" => "rouge"]);
		exit(1);
	}
	echo intoBalise("h3", " Modification de la table $s");
	echo boutonModifAjout($s);
	echo("<table>");
	$k = TRUE; // drapeau pour la ligne de titre
	foreach ($t as $ligne) {
		echo("<tr>");
		if ($k) {
			$k = FALSE;
			foreach($ligne as $colonne) {
				echo intoBalise("th", "$colonne");
			}
			echo intoBalise("th", "action");
		} else {
			foreach($ligne as $colonne) {
				echo intoBalise("td", "$colonne");
			}
			echo intoBalise("td", boutonModifMAJ($s, $ligne));
		}
		echo("</tr>\n");
	}
	echo("</table>");
	$nbLigne = count($t);
}

/**
 * boutonModifAjout()
 * @param string $s : le nom de la table
 * @return : string qui écrit le bouton ajouter
 */
function boutonModifAjout(string $s) : string {
	$reponse = "";
	$reponse .= "<form action='".$s."Form.php' method='get'>\n";
	$reponse .= "<div><input type='hidden' name='bdd' value='$s' /></div>\n";
	$reponse .= "<div><input type='submit' name='maj' size='12' value='insertion' /></div>\n";
	$reponse .= "</form>";
	return $reponse;
}

/**
 * boutonModifMAJ()
 * @param string $s : le nom de la table
 * @param array $l : la ligne de la table
 * @return : string qui écrit les boutons modifier / supprimer
 */
function boutonModifMAJ(string $s, array $l) : string {
	$reponse = "";
	if ($s=='p11_climat') {
		// test($l);
		$reponse .= "<form action='".$s."Form.php' method='get'>\n";
		$reponse .= "<div><input type='hidden' name='bdd' value='$s' /></div>\n";
		$ville = $l[0];
		$reponse .= "<div><input type='hidden' name='ville' value='$ville' /></div>\n";
		$date = $l[1];
		$reponse .= "<div><input type='hidden' name='date' value='$date' /></div>\n";
		$bouton1 = "<div><input class='vert' type='submit' name='maj'
			size='12' value='modification' />\n";
		$bouton2 = "<input class='rouge' type='submit' name='maj'
			size='12' value='suppression' /></div>\n";
		$reponse .= $bouton1 . " / " . $bouton2;
		$reponse .= "</form>";
		return $reponse;
	} elseif ($s=='p11_date') {
		$reponse .= "<form action='".$s."Form.php' method='get'>\n";
		$reponse .= "<div><input type='hidden' name='bdd' value='$s' /></div>\n";
		$date = $l[0];
		$reponse .= "<div><input type='hidden' name='date'
			value='$date' />\n";
		$bouton2 = "<input class='rouge' type='submit' name='maj'
			size='12' value='suppression' /></div>\n";
		// $reponse .= $bouton1 . " / " . $bouton2;
		$reponse .= $bouton2;
		$reponse .= "</form>";
		return $reponse;
	} elseif ($s=='p11_ville') {
		$reponse .= "<form action='".$s."Form.php' method='get'>\n";
		$reponse .= "<div><input type='hidden' name='bdd' value='$s' /></div>\n";
		$id = $l[0];
		$reponse .= "<div><input type='hidden' name='id' value='$id' /></div>\n";
		$bouton1 = "<div><input class='vert' type='submit' name='maj'
			size='12' value='modification' />\n";
		$bouton2 = "<input class='rouge' type='submit' name='maj'
			size='12' value='suppression' /></div>\n";
		$reponse .= $bouton1 . " / " . $bouton2;
		$reponse .= "</form>";
		return $reponse;
	}
	return $reponse;
}

/**
 * ecritTableau()
 * @param array $t : un tableau
 * @return : void
 * écrit le tableau $t sous forme de <table>
 */
function ecritTableau(array $t) : void {
	if (!is_array($t)) {
		echo intoBalise("h3", " l'entrée n'est pas un tableau", ["class" => "rouge"]);
		exit(1);
	}
	echo("<table>");
	$k = TRUE; // drapeau pour la ligne de titre
	foreach ($t as $ligne) {
		echo("<tr>");
		if ($k) {
			$k = FALSE;
			foreach($ligne as $colonne) {
				echo intoBalise("th", "$colonne");
			}
		} else {
			foreach($ligne as $colonne) {
				echo intoBalise("td", "$colonne");
			}
		}
		echo("</tr>\n");
	}
	echo("</table>");
	$nbLigne = count($t);
}

function tabMul(int $taille = 10): string {
	$reponse = "<table> <tr> <th> x </th>";
	for ($i = 0; $i <= $taille; $i++) {
		$reponse .= "<th>$i</th>";
	}
	$reponse .= "</tr>";
	for ($i = 0; $i <= $taille; $i++) {
		$reponse .= "<tr><th>$i</th>";
		for ($j = 0; $j <= $taille; $j++) {
			$p = $i*$j;
			$reponse .= "<td>$p</td>";
		}
		$reponse .= "</tr>";
	}
	$reponse .= "</table>";
	return $reponse;
}

function listDep2(string $title = "Title content"): string {
	$reponse = "";
	$fichier = file($title);

	if ($fichier != 0) {
		$reponse .= "<ul>\n";

		// ligne de titre
		$celTitres = explode(",", array_shift($fichier));

		// les autres lignes
		foreach ($fichier as $numeroLigne => $contenu) {
			$contenus = explode(",", $contenu);

			$reponse .= "<li><b>".$contenus[1]."</b>\n";
			$reponse .= "<ul>\n";
			$reponse .= "<li><b>".$celTitres[0]."</b> : ".$contenus[0]."</li>\n";
			$reponse .= "<li><b>".$celTitres[4]."</b> : ".$contenus[4]."</li>\n";
			$reponse .= "<li><b>".$celTitres[3]."</b> : ".$contenus[3]."</li>\n";
			$reponse .= "<li><b>".$celTitres[2]."</b> : ".$contenus[2]."</li>\n";

			$reponse .= "</ul>\n";
			$reponse .= "</li>\n";
		}
	}

	$reponse .= "</ul>";
	return $reponse;
}

function listDep(string $title = ".txt", string $csv = ".csv"): string {
	$reponse = "";
	$fichier = file($title);
	$fichierCSV = file($csv);

	$compteur = 101; //première ligne pour les sous-prefectures

	if ($fichier != 0) {
		$reponse .= "<ul>\n";

		// ligne de titre
		$celTitres = explode(",", array_shift($fichier));

		// les autres lignes
		foreach ($fichier as $numeroLigne => $contenu) {
			$contenus = explode(",", $contenu);

			$reponse .= "<li><b>".$contenus[1]."</b>\n";
			$reponse .= "<ul>\n";
			$reponse .= "<li><b>".$celTitres[0]."</b> : ".$contenus[0]."</li>\n";
			$reponse .= "<li><b>".$celTitres[4]."</b> : ".$contenus[4]."</li>\n";
			$reponse .= "<li><b>".$celTitres[3]."</b> : ".$contenus[3]."</li>\n";
			$reponse .= "<li><b>".$celTitres[2]."</b> : ".$contenus[2]."</li>\n";

			// ajout des données du fichier CSV

			// ajout des préfectures
			$ligneCSV = $fichierCSV[$numeroLigne];
			$ligneCSV = explode(",", $ligneCSV);
			$reponse .= "<li><b> Préfecture </b> : ".$ligneCSV[1]."</li>\n";

			// ajout des sous-préfectures
			$reponse .= "<li><b> Sous-préfecture </b> : ";
			$ligneCSV = explode(",", $fichierCSV[$compteur]);
			while ($ligneCSV[4] == $numeroLigne + 1) {
				$reponse .= $ligneCSV[1].", ";
				$compteur = $compteur + 1;
				$ligneCSV = explode(",", $fichierCSV[$compteur]);
			}
			$reponse .= "</li>\n";      

			// fin de la liste
			$reponse .= "</ul>\n";
			$reponse .= "</li>\n";
		}
	}

	$reponse .= "</ul>";
	return $reponse;
}

function debutIndex(string $title = "Title content"): string {
	$reponse = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>' . $title . '</title>
		<!-- encodage utf-8 -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!--<style type="text/css"></style> -->
		<link rel="Stylesheet" href="CSS/P11_meteo.css"
			type="text/css" />
	</head>
	<body>
	<div id="titre">
		<h1>' . $title . '</h1>
	</div>' . "\n";
	return $reponse;
}

function getDebutHTML(string $title = "Title content"): string {
	$reponse = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>' . $title . '</title>
		<!-- encodage utf-8 -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<!--<style type="text/css"></style> -->
		<link rel="Stylesheet" href="../CSS/P11_meteo.css"
			type="text/css" />
	</head>
	<body>
	<div id="titre">
		<h1>' . $title . '</h1>
	</div>' . "\n";
	return $reponse;
}

function menuIndex(): string {
	return file_get_contents("HTML/menuIndex.html");
}

function getMenuHTML(): string {
	return file_get_contents("../HTML/menu.html");
}

function finIndex(): string {
	return file_get_contents("HTML/finKtmlIndex.html");
}

function getFinHTML(): string {
	return file_get_contents("../HTML/finKtml.html");
}

function contenuIndex(string $contenu): string {
	return file_get_contents($contenu);
}

function contenu(string $contenu): string {
	return file_get_contents($contenu);
}

?>