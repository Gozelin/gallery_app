<?php

include("../class/Gallery.class.php");
include("../class/AppController.Class.php");

cGallery::$verbose = true;

if (!isset($_POST)) { 
	exit(0);
}

// echo '<pre>';
// var_dump($_POST);
// var_dump($_FILES);
// echo '</pre>';

$AC = new cAppController();

$AC->prepareQuery($_POST);
$AC->execQuery();

// var_dump($AC);

?>