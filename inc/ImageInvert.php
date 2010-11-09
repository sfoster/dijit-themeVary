<?php 

require_once(LIB . "/ImageTransform.php");
require_once(LIB . "/Image.php");

class ImageInvert extends ImageTransform
{
	function transform() {
		$this->image->invert();
		$this->image->save($this->path);
	}
}
