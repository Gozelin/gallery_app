<?php

include("../class/Gallery.class.php");
include("../class/AppController.Class.php");

cGallery::$verbose = true;

if (!isset($_POST)) { 
	exit(0);
}

$AC = new cAppController();

$data = $AC->prepareData($_POST);

echo '<pre>';
// var_dump($_POST);
// var_dump($_FILES);
// var_dump($data);
echo '</pre>';


// $gallery = new cGallery(data); 

$AC->prepareQuery($data);
$AC->execQuery();

// var_dump($AC);

?>