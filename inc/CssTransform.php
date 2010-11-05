<?php 

class CssTransform
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
	function transformFile($pathname, $args = array()) {
		$str = file_get_contents($pathname);
		if($str) {
			$this->log("(todo: transform: ". $pathname .")");
			$newStr = $this->transformString($str);
			if(isset($args['overwrite']) && $args['overwrite']) {
				return file_put_contents($pathname, $newStr);
			} else {
				return $newStr;
			}
		} else {
			throw new Exception("Unable to read file: $pathname");
		}
	}

	function transformString() {
		$this->log("(todo)");
	}

	function log($msg) {
		print("$msg\n");
	}
}
