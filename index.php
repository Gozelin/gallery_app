<?php

include("./class/Gallery.Class.php");
include("./class/Image.Class.php");

cJsonHelper::$verbose = true;

$arr = ["name"=>"GALLERY_NAME", "desc"=>"GALLERY_DESC"];

$g = new cGallery($arr);

$arr= ["name"=>"IMAGE_NAME", "desc"=>"IMAGE_DESC", "path"=>"/images/this.png"];

$i = new cImage($arr);

$g->addImage($i);

// $g->importJson(2);
// $g->insertJson();

// echo('<pre>');
// var_dump($g);
// echo('</pre>');

?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style/style.css">
		<script src="js/jquery-3.1.1.min.js"></script>
		<script src="js/gallery_app.js"></script>
	</head>
	<body>
		<div id="galleryApp">
			<?php buildGallery(1); ?>
		</div>
	</body>
</html>