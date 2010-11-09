<?php 

class Image
{
	public $fh = null;
	public $filename = "";
	
	function __construct($args=array()) {
		if(is_array($args)) {
			foreach($args as $name => $value) {
				$this->$name = $value;
			}
		}
	}
	
	function _do($name, $args=array()) {
		$name = "_$name";
		// print("_do: $name\n");
		// print("_do on: " . $this->filename);

		$infile = $this->filename;
		$info = getimagesize($infile);
		$image = null;
		
		//Create the image depending on what kind of file it is.
		switch($info['mime']) {
			case 'image/png' : $image = imagecreatefrompng($infile); break;
			case 'image/jpeg': $image = imagecreatefromjpeg($infile); break;
			case 'image/gif' : 
				$old_id = imagecreatefromgif($infile); 
				$image  = imagecreatetruecolor($info[0],$info[1]); 
				imagecopy($image,$old_id,0,0,0,0,$info[0],$info[1]); 
				break;
			default: break;
		}
		$this->info		= $info;
		$this->width	= imagesx($image);
		$this->height	= imagesy($image);
		$this->image	= $this->org_image = $image;

		$this->$name($args);
	}
	
	function invert($args = array()) {
		return $this->_do("invert", $args);
	}

	/**
	 * Inverts the colors in the given image
	 * E.g.: $img = new Image("some/image.png"); $img->invert();
	 */
	function _invert($args) {
		if(!$this->image) {
			return false;
		}
		
		$imgdest= imagecreatetruecolor($this->width, $this->height);
		$imgsrc	= $this->image;
		$height	= $this->height;
		$width	= $this->width;

		for( $x=0 ; $x<$width ; $x++ )
		{
			imagecopy($imgdest, $imgsrc, $width-$x-1, 0, $x, 0, 1, $height);

			$rowBuffer = imagecreatetruecolor($width, 1);
			for( $y=0 ; $y<($height/2) ; $y++ ) {
				imagecopy($rowBuffer, $imgdest  , 0, 0, 0, $height-$y-1, $width, 1);
				imagecopy($imgdest  , $imgdest  , 0, $height-$y-1, 0, $y, $width, 1);
				imagecopy($imgdest  , $rowBuffer, 0, $y, 0, 0, $width, 1);
			}

			imagedestroy( $rowBuffer );
		}
		
		$this->image = $imgdest;
		return $this;
	}

	function save($toPathname, $destroy = true) {
		if(!$this->image) {
			return false;
		}
		
		$ext = strtolower( pathinfo( $toPathname, PATHINFO_EXTENSION ) );
		$result = null;
		switch($ext) {
			case 'png' : 
				$result = imagepng($this->image, $toPathname); 
				break;
			case 'jpeg': 
			case 'jpg' : 
				$result = imagejpeg($this->image, $toPathname); 
				break;
			case 'gif' : 
				$result = imagegif($this->image, $toPathname); 
				break;
			default: break;
		}
		if($destroy) {
			$this->destroy();
		}
		return false;
	}

	/**
	 * Display the image and then destroy it.
	 * Example: $img->show();
	 */
	function show($destroy = true) {
		if(!$this->image) return false;
		
		header("Content-type: ".$this->info['mime']);
		switch($this->info['mime']) {
			case 'image/png' : imagepng($this->image); break;
			case 'image/jpeg': imagejpeg($this->image); break;
			case 'image/gif' : imagegif($this->image); break;
			default: break;
		}
		if($destroy) $this->destroy();
		
		return $this;
	}
		
	/**
	 * Destroy the image to save the memory. Do this after all operations are complete.
	 */
	function destroy() {
		if($this->image) {
			imagedestroy($this->image);
		}
		if($this->org_image) {
			imagedestroy($this->org_image);
		}
	}
	
	function log($msg) {
		print("$msg\n");
	}
}

// from: 
