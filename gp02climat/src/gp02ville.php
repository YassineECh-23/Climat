<?php
$titrePage = "Table des Villes"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
echo modif($titrePage, "gp02villesModif.php");
echo ecritTableau(getAllVille());

echo getFinHTML();
?>