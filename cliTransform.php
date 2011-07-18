<?php 
	include_once("inc/global.php");
	include_once("inc/getopts.php");
	include_once(LIB . "/CssTransformApp.php");

	$cwd = dirname(__FILE__);
	$runArgs = getopt("", $longopts);
	$runArgs = getopts(array(
		'config' => array('switch' => array('c','config'),'type' => GETOPT_VAL),
		'dry' => array('switch' => array('d','dry-run', 'dryRun'),'type' => GETOPT_SWITCH),
		'config' => array('switch' => array('c','config'),'type' => GETOPT_VAL),
		'inDir' => array('switch' => array('inDir','indir', 'in'),'type' => GETOPT_VAL),
		'outDir' => array('switch' => array('outDir','outdir', 'out'),'type' => GETOPT_VAL),
		'inFile' => array('switch' => array('inFile','infile', 'file'),'type' => GETOPT_VAL),
	),$_SERVER['argv']);
	// default defaults
	$args = array(
		'inDir'				=> dirname(__FILE__) . "/claro",
		'outDir'			=> dirname(__FILE__) . "/out",
		'dryRun'			=> isset($runArgs['dry-run'])
	);
	
	$configFile = "$cwd/config.json.default"; 
	if(isset($runArgs['config'])){
		$configFile = $runArgs['config']; 
	} else if(file_exists("$cwd/config.json")) {
		$configFile = "$cwd/config.json";
	}
	if($configFile){
		// can pass empty config param to override defaults
		$userConfig = json_decode( "" . file_get_contents($configFile), true );
	}

	$args = array_merge($args, $userConfig);

	$app = new CssTransformApp( $args );
	$app->run();
