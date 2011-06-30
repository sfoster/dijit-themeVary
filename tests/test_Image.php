<!DOCTYPE html>
<html><?php
include_once("../inc/global.php");
require_once(LIB . "/Image.php");

$srcimgs = array(
	"example.png", 
	"commonHighlight.png",
	"degas.jpg",
	"treeExpand_loading.gif"
);

foreach($srcimgs as $file){
	$img = new Image(array(
		'filename' => $file
	));
	$outputFilename = "inverted" . ucfirst($file);
	$img->invert();
	$img->save($outputFilename);
}
?>
<head>
	<meta charset="utf-8">
	<title>Page Title</title>
	<style>
		body {
			background: #000;
		}
	</style>
	<script>
		window.onload = function(){
			var colors = ["red", "green", "black", "white", "blue"];
			var idx = 0;
			document.addEventListener("click", function(evt){
				document.body.style.backgroundColor = colors[idx++];
				if(idx>=colors.length) {
					idx = 0;
				}
			});
		}
	</script>
</head>
<body>
<table>
<?php 
$now = time();
foreach($srcimgs as $filename){
	$outputFilename = "inverted" . ucfirst($filename);
	print implode("\n", array(
		'<tr>',
			'<td><img src="' .$filename. '?' . $now. '" style="border: 1px solid yellow"/></td>',
			'<td><img src="' .$outputFilename. '?' . $now. '" style="border: 1px solid yellow"/></td>',
		'</tr>'
	));

}?>
</table>
	
</body>
</html>
