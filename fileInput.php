<?php

include("./class/Gallery.Class.php");

// cJsonHelper::$verbose = true;

?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style/style.css">
		<script src="js/jquery-3.1.1.min.js"></script>
		<!-- <script src="js/gallery_app.js"></script> -->
	</head>
	<body>
		<form method="post" enctype="multipart/form-data">
			<input class="fileInput" name="test_file" type="file">
			<!-- <input type="submit"> -->
			<h2 class="btn">btn</h2>
		</form>
	</body>
</html>

<script>
	$(".btn").on("click", function(){
		console.log($(".fileInput").prop('files')[0]);
	});
</script>