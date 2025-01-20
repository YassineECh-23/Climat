<?php
$titrePage = "Table des Climats"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
echo retour($titrePage, "gp02climat.php");
echo ecritTableauModif(getAllClimat(), "p11_climat");

echo getFinHTML();
?>