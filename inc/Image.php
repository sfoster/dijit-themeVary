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
	function _is_gifanim($filename) {
		return (bool)preg_match('/\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)/s', file_get_contents($filename), $m);
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
				if($this->_is_gifanim($infile)) {
					// split into frames, treat each seperately and recompose to animation
					
				} else {
					
				}
				$old_id = imagecreatefromgif($infile);
				$image  = imagecreatetruecolor($info[0],$info[1]); 
				imagecopy($image,$old_id,0,0,0,0,$info[0],$info[1]); 
				// $transparent_index = imagecolortransparent($image); /* gives the index of current transparent color or -1 */
				// if($transparent_index!=(-1)) {
				// 	$transparent_color = imagecolorsForindex($img,$transparent_index);	
				// }
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
		
		$keepcolor = true;
		$imgsrc	= $this->image;
		$height	= $this->height;
		$width	= $this->width;
		
		$imgdest= imageistruecolor($imgsrc) ? 
			imagecreatetruecolor($this->width, $this->height) :
			imagecreate($this->width, $this->height);
			
		imagesavealpha($imgdest, true);
		imagealphablending($imgdest, false);

		// image dimensions
		$x = imagesx($imgdest);
		$y = imagesy($imgdest);
		// copy over all the image data into the new image
		imagecopy ($imgdest, $imgsrc, 0, 0, 0, 0, $x, $y);

		// rows loop
		// see: http://stackoverflow.com/questions/1890409/change-hue-of-an-image-with-php-gd-library
		for($i=0; $i<$y; $i++)
		{
			// cols loop
			for($j=0; $j<$x; $j++)
			{
				$rgba = imagecolorat($imgsrc, $j, $i); // position of color in the palette
				$r = ($rgba >> 16) & 0xFF;
				$g = ($rgba >> 8) & 0xFF;
				$b = $rgba & 0xFF;
				$alpha = ($rgba & 0x7F000000) >> 24;
				
				$col = imagecolorallocatealpha($imgdest, 255-$r, 255-$g, 255-$b, $alpha);
				// $this->log("$rgb: " . print_r($col, true) . "\n");
				imagesetpixel($imgdest, $j, $i, $col);
			}
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
	
	// function log($msg) {
	// 	print("$msg\n");
	// }
	function log($msg) {
		$logfile = dirname(__FILE__) . "/image.log";
		error_log($msg . "\n", 3, $logfile);
		print($msg . "\n");
	}
}
