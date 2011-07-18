<?php 

function isNaN($thing=null){
	$type = gettype($thing);
	return ($type == "integer" || $type == "double");
}
class Color
{
	function __construct($color=null) {
		// summary:
		//		(named string, hex string)
		//		Takes a array of rgb or rgba values,
		//		an object with r, g, b, and a properties, or another `dojo.Color` object
		//		and creates a new Color instance to work from.
		//
		if($color){ 
			$this->setColor($color); 
		}
	}

	
	static $named = array(
		"black" => array(0,0,0),
		"silver" => array(192,192,192),
		"gray" => array(128,128,128),
		"white" => array(255,255,255),
		"maroon" => array(128,0,0),
		"red" => array(255,0,0),
		"purple" => array(128,0,128),
		"fuchsia" => array(255,0,255),
		"green" => array(0,128,0),
		"lime" => array(0,255,0),
		"olive" => array(128,128,0),
		"yellow" => array(255,255,0),
		"navy" => array(0,0,128),
		"blue" => array(0,0,255),
		"teal" => array(0,128,128),
		"aqua" => array(0,255,255),
		"transparent" => array(255,255,255) //  dojo.config.transparentColor
	);

	public $r = 255;
	public $g = 255; 
	public $b = 255; 
	public $a = 1;
	
	function _set($r, $g, $b, $a){
		$this->r = $r; 
		$this->g = $g; 
		$this->b = $b; 
		$this->a = $a;
	}
	
	function setColor(/*Array|String|Object*/ $color){
		// summary:
		//		Takes a named string, hex string, array of rgb or rgba values,
		//		an object with r, g, b, and a properties, or another `dojo.Color` object
		//		and sets this color instance to that value.
		//
		// example:
		//	|	var c = new dojo.Color(); // no color
		//	|	c.setColor("#ededed"); // greyish
		if("string" == gettype($color)){
			Color::colorFromString($color, $this);
		}else if("array" == gettype($color)){
			Color::colorFromArray($color, $this);
		}else{
			$this->_set($color->r, $color->g, $color->b, $color->a);
			if( "Color" != get_class($color) ){ 
				$this->sanitize();
			}
		}
		// echo "after from/sanitize: " . print_r($this, true) . "\n";
		return $this;	// dojo.Color
	}
	function sanitize(){
		// summary:
		//		Ensures the object has correct attributes
		// description:
		//		the default implementation does nothing, include dojo.colors to
		//		augment it with real checks
		
		// return $this;	// dojo.Color
	}

	function toRgb(){
		// summary:
		//		Returns 3 component array of rgb values
		// example:
		//	|	var c = new dojo.Color("#000000");
		//	|	console.log(c.toRgb()); // [0,0,0]
		return array(
			$this->r,
			$this->g,
			$this->b
		);
	}
	
	function toRgba(){
		// summary:
		//		Returns a 4 component array of rgba values from the color
		//		represented by this object.
		return array(
			$this->r,
			$this->g,
			$this->b,
			$this->a
		);
	}

	function toHex(){
		// summary:
		//		Returns a CSS color string in hexadecimal representation
		// example:
		//	|	console.log(new dojo.Color([0,0,0]).toHex()); // #000000
		$arr = array();
		foreach(explode("","rgb") as $x){
			// 
			$s = dechex($this->$x);
			array_push($arr, strlen($s) < 2 ? "0".$s : $s);
		}
		return "#" + implode("", $arr);	// String
	}
	
	function toCss(/*Boolean?*/ $includeAlpha=false){
		// summary:
		//		Returns a css color string in rgb(a) representation
		// example:
		//	|	var c = new dojo.Color("#FFF").toCss();
		//	|	console.log(c); // rgb('255','255','255')
		$rgb = $this->r . ", " . $this->g . ", " . $this->b;
		$css = "";
		if($includeAlpha){
			$css = "rgba(" . $rgb . ", " . $this->a;
		} else {
			$css = "rgb(". $rgb .")";
		}
	}
	function toString(){
		// summary:
		//		Returns a visual representation of the color
		return $this.toCss(true); // String
	}

	static function blendColors(
		/*dojo.Color*/ $start,
		/*dojo.Color*/ $end,
		/*Number*/ $weight,
		/*dojo.Color?*/ $obj=null
	){
		// summary:
		//		Blend colors end and start with weight from 0 to 1, 0.5 being a 50/50 blend,
		//		can reuse a previously allocated dojo.Color object for the result
		$t = (null==$obj) ? new Color() : $obj;
		foreach(explode("", "rgba") as $x){
			$t->$x = $start->$x + ($end->$x - $start->$x) * $weight;
			if($x != "a"){ 
				$t->$x = round($t->$x); 
			}
		};
		return $t->sanitize();	// dojo.Color
	}
	
	// continued port
	

	static function colorFromRgb(/*String*/ $color, /*dojo.Color?*/ $obj=null){
		// summary:
		//		Returns a `dojo.Color` instance from a string of the form
		//		"rgb(...)" or "rgba(...)". Optionally accepts a `dojo.Color`
		//		object to update with the parsed value and return instead of
		//		creating a new object.
		// returns:
		//		A dojo.Color object. If obj is passed, it will be the return value.
		echo "handling str: $color with colorFromRgb\n";
		$color = strtolower($color); 
		$matches = array();
		if(
			preg_match('/^rgba?\(([\s\.,0-9]+)\)/', $color, $matches)
		) {
			$values = array_map('trim', explode(",", $matches[1]));
			echo "matched rgb: " . print_r($values, true) . "\n";
			
			return Color::colorFromArray($values, $obj); // pass r,g,b,a as array
		}
		return null;
	}

	static function colorFromHex(/*String*/ $color, /*dojo.Color?*/ $obj=null){
		// summary:
		//		Converts a hex string with a '#' prefix to a color object.
		//		Supports 12-bit #rgb shorthand. Optionally accepts a
		//		`dojo.Color` object to update with the parsed value.
		//
		// returns:
		//		A dojo.Color object. If obj is passed, it will be the return value.
		//
		// example:
		//	 | var thing = dojo.colorFromHex("#ededed"); // grey, longhand
		//
		// example:
		//	| var thing = dojo.colorFromHex("#000"); // black, shorthand
		echo "handling str $color with colorFromHex\n";
		$t = (null==$obj) ? new Color() : $obj;
		$bits = (strlen($color) == 4) ? 4 : 8;
		$mask = (1 << $bits) - 1;
		$color = hexdec(substr($color, 1));
		echo "hexdec gives us: $color\n";

		foreach(array("b", "g", "r") as $x){
			$c = $color & $mask;
			$color >>= $bits;
			echo "shifted $color\n";
			if($bits == 4) {
				$t->$x = 17 * $c;
			} else {
				$t->$x = $c;
			}
		}
		$t->a = 1;
		return $t;	// dojo.Color
	}

	static function colorFromArray(/*Array*/ $arColor, /*dojo.Color?*/ $obj=null){
		// summary:
		//		Builds a `dojo.Color` from a 3 or 4 element array, mapping each
		//		element in sequence to the rgb(a) values of the color.
		// example:
		//		| var myColor = dojo.colorFromArray([237,237,237,0.5]); // grey, 50% alpha
		// returns:
		//		A dojo.Color object. If obj is passed, it will be the return value.
		echo "colorFromArray: handling array " . print_r($arColor, true) . "\n";
		echo "colorFromArray: obj " . print_r($obj) ."\n";
		$t = (null==$obj) ? new Color() : $obj;
		$r = intval($arColor[0]); 
		$g = intval($arColor[1]); 
		$b = intval($arColor[2]); 
		$a = isset($arColor[3]) ? intval($arColor[3]) : 1;
		echo "calling _set with $r, $g, $b, $a\n";
		$t->_set($r, $g, $b, $a);

		$t->sanitize();	// dojo.Color
		return $t;
	}

	static function colorFromString(/*String*/ $str, /*dojo.Color?*/ $obj){
		// summary:
		//		Parses `str` for a color value. Accepts hex, rgb, and rgba
		//		style color values.
		// description:
		//		Acceptable input values for str may include arrays of any form
		//		accepted by dojo.colorFromArray, hex strings such as "#aaaaaa", or
		//		rgb or rgba strings such as "rgb(133, 200, 16)" or "rgba(10, 10,
		//		10, 50)"
		// returns:
		//		A dojo.Color object. If obj is passed, it will be the return value.
		$color = null;
		$a = @Color::$named[$str];
		if($a) {
			$color = Color::colorFromArray($a, $obj);
			if($color){
				return $color;
			}
		}
		$color = Color::colorFromRgb($str, $obj);
		if($color){
			return $color;
		}
		$color = Color::colorFromHex($str, $obj);
		if($color){
			return $color;
		}
		return null;
	}
}
