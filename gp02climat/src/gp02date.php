<?php
$titrePage = "Table des Dates"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
echo modif($titrePage, "gp02dateModif.php");

echo ecritTableau(getAllDate());

echo getFinHTML();
?>