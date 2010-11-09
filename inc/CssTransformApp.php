<?php 

require_once(LIB . "/ImageInvert.php");
require_once(LIB . "/CssInvert.php");

function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}

class CssTransformApp
{
	public $inDir = "";
	public $outDir = "";
	public $svnThemeUrl = "";
	public $inputThemeName = "";
	public $outputThemeName = "";
	public $dryRun = true;
	
	function __construct($args) {
		$this->cwd = getcwd();
		if(is_array($args)) {
			foreach($args as $name => $value) {
				$this->$name = $value;
			}
		}

		if(!$this->inputThemeName) {
			throw new Exception("No input theme name provided: ". $this->inputThemeName);
		}
		if(!$this->outputThemeName) {
			throw new Exception("No output theme name provided: ". $this->outputThemeName);
		}

		$this->renameThemeFn = implode('', array(
			'$str = str_replace(\''. $this->inputThemeName . '\', \''. $this->outputThemeName . '\', $str);',
			'$str = str_replace(\''. ucfirst($this->inputThemeName) . '\', \''. ucfirst($this->outputThemeName) . '\', $str);',
			'return $str;'
		));
	}
	
	function log($msg) {
		print("$msg\n");
	}
	
	function run() {
		$imgTransform = $this->imgTransform = new ImageInvert();
		$cssTransform = $this->cssTransform = new CssInvert();
		
		if(file_exists($this->outDir)) {
			$this->log("Removing existing out dir: " . $this->outDir);
			rmdir($this->outDir);
		}

		if($this->inDir && is_dir($this->inDir)) {
			$this->log("Using input directory: " . $this->inDir);
			recurse_copy($this->inDir, $this->outDir);
			$outDir = $this->outDir;
		} else {
			$this->fetchTheme();
			$outDir = $this->outDir;
			$this->log("Theme fetched, next up... (todo)");
		}
		
		$filterFn = 'return preg_match(\'/'. $this->inputThemeName . '/i\', $name);';
		$this->log("filterFn: " . $filterFn);
		$filter = create_function('$name', $filterFn);

		$cssFilterFn = 'return preg_match(\'/\\.css$/\', $name);';
		$this->log("cssFilterFn: " . $cssFilterFn);
		$cssFilter = create_function('$name', $cssFilterFn);

		$this->log("process fn: " . $this->renameThemeFn);
		$process = create_function('$str', $this->renameThemeFn);

		$this->log("processFn created, renaming files in directory: $outDir");
		$this->renameDirectory($outDir, $filter, $process);

		$this->log("Processing files in directory: $outDir");

		$this->processDirectory($outDir, $cssFilterFn, $process);
	}
	
	function fetchTheme() {
		$repo = $this->svnThemeUrl;
		if(!$repo) {
			throw new Exception("Bad repo url: " . $repo);
		}
		$destination = $this->outDir;
		$checkout = "svn export --force " . $this->svnThemeUrl . " " . $this->outDir;
		if($this->dryRun) {
			$this->log("(dry run, doesn't actually do anything)");
			$checkout = "echo $checkout";
		}

		$this->log("Fetching theme from svn: $repo");

		if (substr(php_uname(), 0, 7) == "Windows") {
			pclose(popen("start /B ". $checkout, "r"));
		} else {
			$this->log("Exec cmd");
			$result = exec($checkout); //  . " > /dev/null &"
			$this->log("Result: " . $result);
		}
		$this->log("/Fetched theme from svn");
	}
	
	function renameDirectory($path="", $filter, $transform) {

		//using the opendir function
		$dir_handle = @opendir($path) or die("Unable to open $path");

		//running the while loop
		$count = 0;

		while ($name = readdir($dir_handle)) 
		{
			// echo "dir loop: $path/$name\n";
			if(strpos($name, ".") === 0) {
				// skipping dot file
				continue;
			}
			// run the item through our filter
			if($filter && !$filter($name)) {
				continue;
			} else {
				// transform it
				$newName = $transform($name);
				$this->log("$name transformed to $newName");
				if($this->dryRun) {
					$this->log("(dry run) rename $path/$name to $path/$newName");
				} else {
					$result = rename("$path/$name", "$path/$newName");
					if(!$result) {
						throw new Exception("Failed to rename: $path/$name to $path/$newName");
					}
				}
				$name = $newName;
			}
			
			if(is_dir("$path/$name")) {
				$this->renameDirectory("$path/$name", $filter, $transform);
			}
			$count++;
		}
		closedir($dir_handle);

		// return array
		return $items;
	}

	function transformCssFile($path) {
		if($this->dryRun) {
			$this->log("(dry run) transform css file: $path");
		} else {
			$str = file_get_contents($path);
			$process = create_function('$str', $this->renameThemeFn);
			$newStr = $process($str);
			$invertedStr = $this->cssTransform->transformString($newStr);
			file_put_contents($path, $invertedStr);
		}
	}

	function transformImageFile($path) {
		$this->log("transforming img: $path");
		if($this->dryRun) {
			$this->log("(dry run) transform image file: $path");
		} else {
			$this->log("transforming image file: $path");
			$result = $this->imgTransform->transformFile($path);
		}
	}

	function isCssFile($path) {
		return preg_match('/\.css$/', $path);
	}
	function isImageFile($path) {
		return preg_match('/\.(png|gif|jpg|jpeg)$/', $path);
	}

	function processDirectory($path="", $filter, $transform) {

		//using the opendir function
		$dir_handle = @opendir($path) or die("Unable to open $path");

		//running the while loop
		$count = 0;

		while ($name = readdir($dir_handle)) 
		{
			$pathname = "$path/$name";
			// echo "dir loop: $path/$name\n";
			if(strpos($name, ".") === 0) {
				// skipping dot file
				continue;
			}
			
			if(is_dir($pathname)) {
				$this->processDirectory($pathname, $filter, $transform);
			} else if(is_file($pathname)) {
				if($this->isCssFile($pathname)) {
					$this->transformCssFile($pathname);
				}
				else if($this->isImageFile($pathname)) {
					$this->transformImageFile($pathname);
				}
			}
			$count++;
		}
		closedir($dir_handle);
	}
}
