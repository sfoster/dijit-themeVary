<?php
include_once("../inc/global.php");
require_once(LIB . "/Image.php");



$img = new Image(array(
	'filename' => "./example.png"
));

$img->invert();
$img->save('./invertedExample.png');
