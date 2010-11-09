<?php 

require_once(LIB . "/CssTransform.php");

function invertHexColor( $color )
{
	$color       = trim($color);
	$prependHash = FALSE;
 
	if(strpos($color,'#')!==FALSE) {
		$prependHash = TRUE;
		$color       = str_replace('#',NULL,$color);
	}
 
	switch($len=strlen($color)) {
		case 3:
			$color=preg_replace("/(.)(.)(.)/","\\1\\1\\2\\2\\3\\3",$color);
			break;
		case 6:
			break;
		default:
			trigger_error("Invalid hex length ($len). Must be a minimum length of (3) or maxium of (6) characters", E_USER_ERROR);
	}
 
	if(!preg_match('/^[a-f0-9]{6}$/i',$color)) {
		$color = htmlentities($color);
		trigger_error( "Invalid hex string #$color", E_USER_ERROR );
	}
 
	$r = dechex(255-hexdec(substr($color,0,2)));
	$r = (strlen($r)>1)?$r:'0'.$r;
	$g = dechex(255-hexdec(substr($color,2,2)));
	$g = (strlen($g)>1)?$g:'0'.$g;
	$b = dechex(255-hexdec(substr($color,4,2)));
	$b = (strlen($b)>1)?$b:'0'.$b;
 
	return ($prependHash?'#':NULL).$r.$g.$b;
}

function invertRgbColorValue($val) {
	$val += 0;
	$val = max( min($val, 255), 0);
	return 255 - $val;
}
function invertRgbColor($str) {
	$rgba = array_map('trim', explode(',', $str));
	$rgba[0] = invertRgbColorValue($rgba[0]);
	$rgba[1] = invertRgbColorValue($rgba[1]);
	$rgba[2] = invertRgbColorValue($rgba[2]);
	return implode(', ', $rgba);
}

class CssInvert extends CssTransform
{
	function transformString($str) {
		$this->log("transforming CSS");
		$pathRe = '/url\(\s*[\'\"]?([^\'\"\)]+))/';
		$hexRe = '/#([0-9a-f]{3,6})/i';
		$rgbRe = '/(rgb|rgba)\(([^\(+]+)\)/i';
		
		// preg_replace(
		// 	"/(<\/?)(\w+)([^>]*>)/e", 
		//     "'\\1'.strtoupper('\\2').'\\3'", 
		//              $html_body);		

		$str = preg_replace($hexRe . 'e', 
			"'#' . invertHexColor('\\1')",
			$str);

		$str = preg_replace($rgbRe . 'e', 
			"'\\1(' . invertRgbColor('\\2') . ')'",
			$str);
		// $this->log("transformed: $str");
		return $str;
	}
}
