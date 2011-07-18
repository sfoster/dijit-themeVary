<?php 
define('LIB', realpath(dirname(__FILE__) . "/../inc"));

require_once(LIB . "/Color.php");

class ColorTest extends PHPUnit_Framework_TestCase  
{  
	public function setUp(){ }  
	public function tearDown(){ }  

	public function testEmptyConstructor()  
	{  
		// smoke test to ensure we can create a Color instance
		$color = new Color();
		
		$this->assertEquals("Color", get_class($color));  
		$this->assertTrue(method_exists($color, "setColor"));  
	}  
	
	public function testFromArray()  
	{  
		// test to ensure an array of [r,b,b] values is correctly handled
		$rgb = array(0, 51, 255);
		$color = Color::colorFromArray($rgb);
		print("testFromArray test\n");
		
		$this->assertTrue( $color && "Color" == get_class($color));  
		$this->assertEquals( 0, $color->r );  
		$this->assertEquals( 51, $color->g );  
		$this->assertEquals( 255, $color->b );  
	}  

	public function testFromObject()  
	{  
		// test to ensure an array of [r,b,b] values is correctly handled
		$rgb = (Object)array("r"=>0, "g"=>51, "b"=>255);
		$color = new Color($rgb);
		print("testFromArray test\n");
		
		$this->assertTrue( $color && "Color" == get_class($color));  
		$this->assertEquals( 0, $color->r );  
		$this->assertEquals( 51, $color->g );  
		$this->assertEquals( 255, $color->b );  
	}  
	
	public function testFromRgb()  
	{  
		// test to ensure a css rgb(n,n,n) color string is correctly parsed & handled
		print("testFromRgb test\n");
		$strRgb = 'rgb(0,51,   255)';
		$color = Color::colorFromRgb($strRgb);
		// print("color from rgb string: " . print_r($color, true) . "\n");
		
		$this->assertTrue( $color && "Color" == get_class($color));  
		$this->assertEquals( 0, $color->r );  
		$this->assertEquals( 51, $color->g );  
		$this->assertEquals( 255, $color->b );  
	}  
	
	public function testFromHex()  
	{  
		// test to ensure a 6-char hex color string is correctly parsed & handled
		print("testFromHex test\n");
		$strHex = '#0033ff';
		$color = Color::colorFromHex($strHex);
		// print("color from hex string: " . print_r($color, true) . "\n");
		
		$this->assertTrue( $color && "Color" == get_class($color));  
		$this->assertEquals( 0, $color->r );  
		$this->assertEquals( 51, $color->g );  
		$this->assertEquals( 255, $color->b );  
	}  
	public function testFromShortHex()  
	{  
		// test to ensure a 3-char hex color string is correctly parsed & handled
		print("testFromShortHex test\n");
		$strHex = '#03f';
		$color = Color::colorFromHex($strHex);
		// print("color from hex string: " . print_r($color, true) . "\n");
		
		$this->assertTrue( $color && "Color" == get_class($color));  
		$this->assertEquals( 0, $color->r );  
		$this->assertEquals( 51, $color->g );  
		$this->assertEquals( 255, $color->b );  
	}  
	
	public function testFromString()  
	{  
		// test to ensure string inputs are correctly parsed & handled
		print("testFromString test\n");
		$strHex = '#03f';
		$strRgb = 'rgb(0,51,   255)';
		$arColor = array(0,51,255);
		$colors = array($strHex, $strRgb, $arColor); 
		// $colors = array($strRgb); 

		foreach($colors as $val){
			// print("test with value: $val\n");
			$color = new Color($val);
			// print("..made Color instance: " . print_r($color, true) . "\n");

			$this->assertTrue( $color && "Color" == get_class($color));  
			$this->assertEquals( 0, $color->r );
			$this->assertEquals( 51, $color->g );
			$this->assertEquals( 255, $color->b );
			// print("/test\n");
		}
	}  

	public function testToOutput()  
	{  
		// test to ensure string inputs are correctly parsed & handled
		print("testToOutput test\n");
		$arColor = array(0,51,255);
		$color = new Color($arColor);
		print("toOutput test\n");

		foreach(array("toRgb") as $outMethod){
			print("test with outMethod: $outMethod\n");
			// print("..made Color instance: " . print_r($color, true) . "\n");

			$ret = $color->$outMethod();
			print("toOutput test: $ret\n");
			$this->assertFalse( null == $ret );  
			$this->assertEquals( 0, $color->r );
			$this->assertEquals( 51, $color->g );
			$this->assertEquals( 255, $color->b );
			print("/test\n");
		}
	}  

	public function testFromHsv()  
	{  
		print("testFromHsv test\n");
		$grey = 	new Color( (Object)array("r"=>128, 	"g"=>128,	"b"=>128) );
		$red = 		new Color( (Object)array("r"=>255, 	"g"=>0,		"b"=>0) );
		$green = 	new Color( (Object)array("r"=>0, 	"g"=>255,	"b"=>0) );
		$blue = 	new Color( (Object)array("r"=>0,	"g"=>0,		"b"=>255) );
		$yellow = 	new Color( (Object)array( "r"=>255, "g"=>255, "b"=>0) );

		//	fromHsv
		$this->assertEquals(
			$grey,
			Color::fromHsv( (Object)array("h"=>0, "s"=>0, "v"=>50) )
		);
		$this->assertEquals(
			$red,
			Color::fromHsv( (Object)array( "h"=>0, "s"=>100, "v" =>100) )
		);
		$this->assertEquals(
			$green,
			Color::fromHsv( (Object)array("h" =>120, "s"=>100, "v" =>100))
		);
		$this->assertEquals(
			$blue,
			Color::fromHsv( (Object)array("h" =>240, "s"=>100, "v" =>100))
		);
		$this->assertEquals(
			$yellow,
			Color::fromHsv( (Object)array( "h" =>60, "s"=>100, "v" =>100) )
		);
	}  

	public function testFromHsl()  
	{  
		print("testFromHsl test\n");
		$grey = 	new Color( (Object)array("r"=>128, 	"g"=>128,	"b"=>128) );
		$red = 		new Color( (Object)array("r"=>255, 	"g"=>0,		"b"=>0) );
		$green = 	new Color( (Object)array("r"=>0, 	"g"=>255,	"b"=>0) );
		$blue = 	new Color( (Object)array("r"=>0,	"g"=>0,		"b"=>255) );
		$yellow = 	new Color( (Object)array( "r"=>255, "g"=>255, "b"=>0) );

		$this->assertEquals(
			$grey,
			Color::fromHsl((Object)array("h"=>0, "s"=>0, "l"=>50))
		);
		$this->assertEquals(
			$red,
			Color::fromHsl( (Object)array("h"=>0, "s"=>100, "l"=>50) ) 
		);
		$this->assertEquals(
			$green,
			Color::fromHsl( (Object)array("h"=>120, "s"=>100, "l"=>50) ) 
		);
		$this->assertEquals(
			$blue,
			Color::fromHsl((Object)array("h"=>240, "s"=>100, "l"=>50))
		);
		$this->assertEquals(
			$yellow,
			Color::fromHsl( (Object)array("h"=>60, "s"=>100, "l"=>50) )
		);

	}  
	public function testColorExtensions(){

		$grey=new Color((Object)array( "r"=>128, "g"=>128, "b"=>128 ));
		$red=new Color((Object)array( "r"=>255, "g"=>0, "b"=>0 ));
		$green=new Color((Object)array( "r"=>0, "g"=>255, "b"=>0 ));
		$blue=new Color((Object)array( "r"=>0, "g"=>0, "b"=>255 ));
		$yellow=new Color((Object)array( "r"=>255, "g"=>255, "b"=>0 ));

		// //	toCmy
		// $this->assertEquals($grey->toCmy(), (Object)array( "c"=>50, "m"=>50, "y"=>50 ));
		// $this->assertEquals($red->toCmy(), (Object)array( "c"=>0, "m"=>100, "y"=>100 ));
		// $this->assertEquals($green->toCmy(), (Object)array( "c"=>100, "m"=>0, "y"=>100 ));
		// $this->assertEquals($blue->toCmy(), (Object)array( "c"=>100, "m"=>100, "y"=>0 ));
		// $this->assertEquals($yellow->toCmy(), (Object)array( "c"=>0, "m"=>0, "y"=>100 ));
		// 
		// //	toCmyk
		// $this->assertEquals($grey->toCmyk(), (Object)array( "c"=>0, "m"=>0, "y"=>0, "b"=>50 ));
		// $this->assertEquals($red->toCmyk(), (Object)array( "c"=>0, "m"=>100, "y"=>100, "b"=>0 ));
		// $this->assertEquals($green->toCmyk(), (Object)array( "c"=>100, "m"=>0, "y"=>100, "b"=>0 ));
		// $this->assertEquals($blue->toCmyk(), (Object)array( "c"=>100, "m"=>100, "y"=>0, "b"=>0 ));
		// $this->assertEquals($yellow->toCmyk(), (Object)array( "c"=>0, "m"=>0, "y"=>100, "b"=>0 ));

		//	toHsl
		$this->assertTrue(method_exists($grey, "toHsl"));
		$this->assertEquals($grey->toHsl(), (Object)array( "h"=>0, "s"=>0, "l"=>50 ));
		$this->assertEquals($red->toHsl(), (Object)array( "h"=>0, "s"=>100, "l"=>50 ));
		$this->assertEquals($green->toHsl(), (Object)array( "h"=>120, "s"=>100, "l"=>50 ));
		$this->assertEquals($blue->toHsl(), (Object)array( "h"=>240, "s"=>100, "l"=>50 ));
		$this->assertEquals($yellow->toHsl(), (Object)array( "h"=>60, "s"=>100, "l"=>50 ));

		//	toHsv
		$this->assertTrue(method_exists($grey, "toHsv"));
		$this->assertEquals($grey->toHsv(), (Object)array( "h"=>0, "s"=>0, "v"=>50 ));
		$this->assertEquals($red->toHsv(), (Object)array( "h"=>0, "s"=>100, "v"=>100 ));
		$this->assertEquals($green->toHsv(), (Object)array( "h"=>120, "s"=>100, "v"=>100 ));
		$this->assertEquals($blue->toHsv(), (Object)array( "h"=>240, "s"=>100, "v"=>100 ));
		$this->assertEquals($yellow->toHsv(), (Object)array( "h"=>60, "s"=>100, "v"=>100 ));
	}

}
