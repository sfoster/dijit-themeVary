<?php 
	include_once("inc/global.php");
	include_once(LIB . "/CssTransformApp.php");

	defined('THEME_URL') 
		|| define('THEME_URL', "http://svn.dojotoolkit.org/src/dijit/trunk/themes/claro");
	defined('DOJO_DIR') 
		|| define('DOJO_DIR', "/Users/sfoster/dev/dojo/trunk");


	$cwd = dirname(__FILE__);
	$longopts  = array( "config::", "dry-run" );
	$runArgs = getopt("", $longopts);
	
	$args = array(
		'inDir'				=> dirname(__FILE__) . "/claro",
		'outDir'			=> dirname(__FILE__) . "/out",
		'dryRun'			=> isset($runArgs['dry-run'])
	);
	
	$configFile = isset($runArgs['config']) ? 
		$runArgs['config'] : file_exists("$cwd/config.json") ? 
			"$cwd/config.json" : "$cwd/config.json.default";

	$userConfig = json_decode( "" . file_get_contents($configFile), true );
	
	$args = array_merge($args, $userConfig);
	$app = new CssTransformApp( $args );
	$app->run();
