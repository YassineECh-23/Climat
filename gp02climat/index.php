<?php
$titrePage = "Accueil"; // mettre le titre ici
$contenuPage = "HTML/accueil.html"; // mettre la page de contenu ici
include ("src/phpHtmlLib.php");
echo debutIndex($titrePage);
echo menuIndex();
echo contenu($contenuPage);
echo finIndex();
?>