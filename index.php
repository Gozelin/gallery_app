<?php

include("./class/Gallery.Class.php");

cJsonHelper::$verbose = true;

// $i = new cImage(["name"=>"test1", "desc"=>"un petit test", "path"=>"image/default.png"]);

// $c = new cGallery(1);

// $c->addImage($i);

// $c->updateJson();

?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style/style.css">
		<script src="js/jquery-3.1.1.min.js"></script>
		<script src="js/gallery_app.js"></script>
	</head>
	<body>
		<div id="galleryApp">
		</div>
	</body>
</html>