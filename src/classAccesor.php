<?php

include("../class/Gallery.class.php");
include("../src/AppController.php");

cGallery::$verbose = true;

$arr = ["action"=>"getForm", "class"=>"cGallery", "data"=>2];

$AC = new cAppController($arr);

$AC->execQuery();

if (!isset($_POST)) { 
    exit(0);
}

echo('<pre>');
var_dump($AC);
echo('</pre>');

?>