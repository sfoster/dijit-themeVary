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
		$testImplemented = false;
		$this->assertTrue( $testImplemented );  
	}  

	public function testFromHsl()  
	{  
		print("testFromHsl test\n");
		$testImplemented = false;
		$this->assertTrue( $testImplemented );  
	}  

}
