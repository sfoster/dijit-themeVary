<?php 

require_once(LIB . "/ImageTransform.php");

class ImageInvert extends ImageTransform
{
	function transform() {
		$this->log("(todo: transform: ". $this->path .")");
	}
}
