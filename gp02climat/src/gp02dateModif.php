<?php
$titrePage = "Table des Dates"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
echo retour($titrePage, "gp02date.php");
echo ecritTableauModif(getAllDate(), "p11_date");

echo getFinHTML();
?>