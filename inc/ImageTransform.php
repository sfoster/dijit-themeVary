<?php 

class ImageTransform
{
	public $fh = null;
	public $dryRun = true;
	
	function __construct($args=array()) {
		if(is_array($args)) {
			foreach($args as $name => $value) {
				$this->$name = $value;
			}
		}
	}
	function transformFile($pathname) {
		$this->path = $pathname;
		$this->image = new Image(array(
			"filename" => $pathname
		));
		// $fh  = fopen($pathname, "r");
		$this->transform($fh);
		// fclose($fh);
	}

	function transform($fh) {
		$this->log("(todo: transform: ". $this->path .")");
	}

	function log($msg) {
		print("$msg\n");
	}
}
