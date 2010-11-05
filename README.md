dijit-themeVary
===============

Dijit's new Claro theme is well suited to programatic generation of theme variations. This project is provides a means of cloning and transforming the contents of the theme. 
The initial goal is to generate a light-on-dark theme by simply inverting all colors. A similar effort early ~0.9 called this 'noir' and that's what I'm calling the output here

The architecture should make other transformations relatively trivial however, making it possible to hue-shift to produce green/blue/whatever tinted Claro variants.

Status
------

* Pre-alpha, CSS transformations and output generation works (tested OSX Snow Leopard, PHP 5.3.2). 
* Image transforms are not yet implemented

Usage
-----

	$ php ./cliTransform.php --config myconfig.json

If you provide no --config param, it will try config.json, then config.json.default

Configuration
-------------

The following config keys can be defined: 
	
	// outputThemeName: String
	//		e.g. 'noir'. The name that's used in filenames, css prefixes etc.
	'outputThemeName'	: 'themeName',	

	// inputThemeName: String
	//		e.g. 'claro'. The name you want used in filenames, css prefixes etc.
	'inputThemeName'	: 'themeName'	// 

	// svnThemeUrl: String?
	//		e.g. 'noir'. 
	// 		The name that's used in filenames, css prefixes etc.
	'svnThemeUrl'		: 'http://host/svn/repo/trunk/dijit/themes/themeName'	// valid URL to the 