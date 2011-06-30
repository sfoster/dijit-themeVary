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
		$this->transform("invert"); // support other transforms in the future like hue shifting?
	}

	function transform($method) {
		$img = $this->image;
		$img->$method();
		$img->save($this->path);
	}

	function log($msg) {
		print("$msg\n");
	}
}
