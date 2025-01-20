<?php
$titrePage = "Table des Villes"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
echo retour($titrePage, "gp02ville.php");
echo ecritTableauModif(getAllVille(), "p11_ville");

echo getFinHTML();
?>