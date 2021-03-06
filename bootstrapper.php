<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 15/7/16
 * Time: 6:52 PM
 */

global $startTime;
$startTime = microtime(true);
session_start();

define("mPHP_PATH", '/Users/Michael/Documents/Dev/ipt/Term3/mPHP');
define("mPHP_MODE", "PROD"); //Change this to "PROD" when in production

//Do not edit code below this line

define("APP_PATH", mPHP_PATH.'/app');
define("PUB_PATH", mPHP_PATH.'/public');

//Core Functions
function throw_error($msg, $errorType = mPHP_ERROR_500) {
	global $_mphp_loaded_libs, $_mphp_included_files, $_mphp_loaded_controls;

	http_response_code($errorType);

	echo '<pre>';

	echo PHP_EOL.'----------------------------------------------------------------------------------------------------';

	echo PHP_EOL;
	echo '            _____  _    _ _____    ______                     '.PHP_EOL;
	echo '           |  __ \| |  | |  __ \  |  ____|                    '.PHP_EOL;
	echo '  _ __ ___ | |__) | |__| | |__) | | |__   _ __ _ __ ___  _ __ '.PHP_EOL;
	echo ' | \'_ ` _ \|  ___/|  __  |  ___/  |  __| | \'__| \'__/ _ \| \'__|'.PHP_EOL;
	echo ' | | | | | | |    | |  | | |      | |____| |  | | | (_) | |   '.PHP_EOL;
	echo ' |_| |_| |_|_|    |_|  |_|_|      |______|_|  |_|  \___/|_|   '.PHP_EOL;
	echo '                                                              '.PHP_EOL;


	echo PHP_EOL.PHP_EOL.PHP_EOL;
	echo "LOADED FILES:".PHP_EOL;

	foreach($_mphp_included_files as $file) {
		echo "  LOADED FILE: ".$file.PHP_EOL;
	}

	echo PHP_EOL.PHP_EOL.PHP_EOL;
	echo "LOADED LIBS:".PHP_EOL;

	foreach($_mphp_loaded_libs as $lib) {
		echo "  LOADED LIB: ".$lib->name." with files:".PHP_EOL;
		foreach($lib->files as $file) {
			echo "      -".$file.PHP_EOL;
		}
	}

	echo PHP_EOL.PHP_EOL.PHP_EOL;

	echo "LOADED CONTROLS:".PHP_EOL;

	foreach($_mphp_loaded_controls as $control) {
		echo "  LOADED CONTROL: ".$control->name." with files:".PHP_EOL;
		foreach($control->files as $file) {
			echo "      -".$file.PHP_EOL;
		}
	}

	echo PHP_EOL.PHP_EOL.PHP_EOL;

	echo 'mPHP Error: '.$msg.PHP_EOL;

	echo PHP_EOL.'----------------------------------------------------------------------------------------------------';

	echo '</pre>';
	die(); 
}

function php_error($errno, $errstr, $errfile, $errline) {
	if($errno != E_NOTICE && $errno != E_WARNING) {
		throw_error("$errstr in $errfile on $errline");
	} else {
		
	}
}

set_error_handler("php_error", E_ALL ^ E_NOTICE);

function shutdown_handler() {
	if(Request::IsAjax()) {
		die();
	}
	global $_mphp_loaded_libs, $_mphp_included_files, $_mphp_loaded_controls;
	if(mPHP_MODE == "DEV") {
		echo '<!--'.PHP_EOL;
		echo '----------------------------------------------------------------------------------------------------'.PHP_EOL;
		echo ' _____ _____ _____ _____    __    _____ _____ '.PHP_EOL;
		echo '|     |  _  |  |  |  _  |  |  |  |     |   __|'.PHP_EOL;
		echo '| | | |   __|     |   __|  |  |__|  |  |  |  |'.PHP_EOL;
		echo '|_|_|_|__|  |__|__|__|     |_____|_____|_____|'.PHP_EOL;
		echo PHP_EOL;
		echo 'POWERED BY mPHP'.PHP_EOL;
		echo 'https://github.com/mvinc17/mPHP'.PHP_EOL;

		echo '----------------------------------------------------------------------------------------------------'.PHP_EOL;

		echo PHP_EOL.PHP_EOL.PHP_EOL;
		echo "LOADED FILES:".PHP_EOL;

		foreach($_mphp_included_files as $file) {
			echo "  LOADED FILE: ".$file.PHP_EOL;
		}

		echo PHP_EOL.PHP_EOL.PHP_EOL;
		echo "LOADED LIBS:".PHP_EOL;

		foreach($_mphp_loaded_libs as $lib) {
			echo "  LOADED LIB: ".$lib->name." with files:".PHP_EOL;
			foreach($lib->files as $file) {
				echo "      -".$file.PHP_EOL;
			}
		}

		echo PHP_EOL.PHP_EOL.PHP_EOL;

		echo "LOADED CONTROLS:".PHP_EOL;

		foreach($_mphp_loaded_controls as $control) {
			echo "  LOADED CONTROL: ".$control->name." with files:".PHP_EOL;
			foreach($control->files as $file) {
				echo "      -".$file.PHP_EOL;
			}
		}

		echo '----------------------------------------------------------------------------------------------------'.PHP_EOL.'-->';
	}
}


register_shutdown_function('shutdown_handler');

//Load mPHP Files
require mPHP_PATH.'/_mphp/autoload.php';