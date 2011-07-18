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
		$this->a = empty($a) ? 1 : $a;
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
			$this->_set($color->r, $color->g, $color->b, @$color->a);
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
		
		return $this;	// dojo.Color
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
			isset($this->a) ? $this->a : 1
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
		return $this->toCss(true); // String
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
		// echo "handling str: $color with colorFromRgb\n";
		$color = strtolower($color); 
		$matches = array();
		if(
			preg_match('/^rgba?\(([\s\.,0-9]+)\)/', $color, $matches)
		) {
			$values = array_map('trim', explode(",", $matches[1]));
			// echo "matched rgb: " . print_r($values, true) . "\n";
			
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
		// echo "handling str $color with colorFromHex\n";
		$t = (null==$obj) ? new Color() : $obj;
		$bits = (strlen($color) == 4) ? 4 : 8;
		$mask = (1 << $bits) - 1;
		$color = hexdec(substr($color, 1));
		// echo "hexdec gives us: $color\n";

		foreach(array("b", "g", "r") as $x){
			$c = $color & $mask;
			$color >>= $bits;
			// echo "shifted $color\n";
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
		//echo "colorFromArray: handling array " . print_r($arColor, true) . "\n";
		//echo "colorFromArray: obj " . print_r($obj) ."\n";
		$t = (null==$obj) ? new Color() : $obj;
		$r = intval($arColor[0]); 
		$g = intval($arColor[1]); 
		$b = intval($arColor[2]); 
		$a = isset($arColor[3]) ? intval($arColor[3]) : 1;
		// echo "calling _set with $r, $g, $b, $a\n";
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

	// (select) dojox/color/_base ports
	// 	don't need the cmyk stuff
	
	static function fromHsl(/* Object|Array|int */$hue, /* int */$saturation=null, /* int */$luminosity=null){
		//	summary
		//	Create a dojox.color.Color from an HSL defined color.
		//	hue from 0-359 (degrees), saturation and luminosity 0-100.

		if(is_array($hue)){
			$saturation=$hue[1];
			$luminosity=$hue[2];
			$hue=$hue[0];
		} else if(is_object($hue)){
			$saturation=$hue->s;
			$luminosity=$hue->l;
			$hue=$hue->h;
		}
		$saturation = $saturation/100;
		$luminosity = $luminosity/100;

		while($hue<0){ $hue+=360; }
		while($hue>=360){ $hue-=360; }
		
		$r; $g; $b;
		if($hue<120){
			$r=(120-$hue)/60; $g=$hue/60; $b=0;
		} else if($hue<240){
			$r=0; $g=(240-$hue)/60; $b=($hue-120)/60;
		} else {
			$r=($hue-240)/60; $g=0; $b=(360-$hue)/60;
		}
		
		$r=2*$saturation*min($r, 1)+(1-$saturation);
		$g=2*$saturation*min($g, 1)+(1-$saturation);
		$b=2*$saturation*min($b, 1)+(1-$saturation);
		if($luminosity<0.5){
			$r*=$luminosity; $g*=$luminosity; $b*=$luminosity;
		}else{
			$r=(1-$luminosity)*$r+2*$luminosity-1;
			$g=(1-$luminosity)*$g+2*$luminosity-1;
			$b=(1-$luminosity)*$b+2*$luminosity-1;
		}
		return new Color(array(
			round($r*255), 		// r
			round($g*255),		// g
			round($b*255)		// b
		));
	}
	
	static function fromHsv(/* Object|Array|int */$hue, /* int */$saturation=null, /* int */$value=null){
		//	summary
		//	Create a dojox.color.Color from an HSV defined color.
		//	hue from 0-359 (degrees), saturation and value 0-100.
		if(is_array($hue)){
			$saturation=$hue[1];
			$value=$hue[2];
			$hue=$hue[0];
		} else if (is_object($hue)){
			$saturation=$hue->s;
			$value=$hue->v;
			$hue=$hue->h;
		}
		
		if($hue==360){ $hue=0; }
		$saturation = $saturation/100;
		$value = $value/100;
		
		$r; $g; $b;
		if($saturation==0){
			$r=$value;
			$b=$value;
			$g=$value;
		}else{
			$hTemp=$hue/60;
			$i=floor($hTemp);
			$f=$hTemp-$i;
			$p=$value*(1-$saturation);
			$q=$value*(1-($saturation*$f));
			$t=$value*(1-($saturation*(1-$f)));
			switch($i){
				case 0:{ $r=$value; $g=$t; $b=$p; break; }
				case 1:{ $r=$q; $g=$value; $b=$p; break; }
				case 2:{ $r=$p; $g=$value; $b=$t; break; }
				case 3:{ $r=$p; $g=$q; $b=$value; break; }
				case 4:{ $r=$t; $g=$p; $b=$value; break; }
				case 5:{ $r=$value; $g=$p; $b=$q; break; }
			}
		}
		return new Color(array(
			round($r*255), 		// r
			round($g*255), 		// g
			round($b*255)		// b
		));
	}

	function toHsl(){
		//	summary
		//	Convert this Color to an HSL definition.
		$r=$this->r/255;
		$g=$this->g/255;
		$b=$this->b/255;
		$min = min($r, $b, $g);
		$max = max($r, $g, $b);
		$delta = $max-$min;
		$h=0; $s=0; $l=($min+$max)/2;
		if($l>0 && $l<1){
			$s = $delta/(($l<0.5)?(2*$l):(2-2*$l));
		}
		if($delta>0){
			if($max==$r && $max!=$g){
				$h+=($g-$b)/$delta;
			}
			if($max==$g && $max!=$b){
				$h+=(2+($b-$r)/$delta);
			}
			if($max==$b && $max!=$r){
				$h+=(4+($r-$g)/$delta);
			}
			$h*=60;
		}
		$hsl = (Object)array( 
			"h" => $h, 
			"s" => round($s*100), 
			"l" => round($l*100)
		);	
		return $hsl; //	Object
	}

	function toHsv(){
		//	summary
		//	Convert this Color to an HSV definition.
		$r=$this->r / 255;
		$g=$this->g / 255;
		$b=$this->b / 255;
		
		$min = min($r, $b, $g);
		$max = max($r, $g, $b);
		$delta = $max-$min;
		$h = null;
		$s = ($max==0)?0:($delta/$max);
		if($s==0){
			$h = 0;
		}else{
			if($r==$max){
				$h = 60*($g-$b)/$delta;
			}else if($g==$max){
				$h = 120 + 60*($b-$r)/$delta;
			}else{
				$h = 240 + 60*($r-$g)/$delta;
			}

			if($h<0){ $h+=360; }
		}
		$hsv = (Object)array( 
			"h"=> $h, 
			"s"=> round($s*100), 
			"v"=> round($max*100)
		);	
		return $hsv; //	Object
	}
	
}
