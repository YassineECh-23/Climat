<?php
$titrePage = "Table des Climats"; // mettre le titre ici
include 'connex.php';
include ("phpHtmlLib.php");
include ("phpFormLib.php");
echo getDebutHTML($titrePage);
echo getMenuHTML();
echo modif($titrePage, "gp02climatModif.php");

echo ecritTableau(getAllClimat());

echo getFinHTML();
?>