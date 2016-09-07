<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 16/7/16
 * Time: 8:05 AM
 */


function view($viewName = null) {
	$viewCompiler = new View_Compiler();
	$viewCompiler->compile($viewName);
//	if($viewName) {
//		if(file_exists(APP_PATH.'/views/'.$viewName.'.view')) {
//
//		} else {
//			throw_error("View not found.");
//		}
//	} else {
//
//	}
}
function control($controlName, $controlArg = null) {
	$control = new $controlName();
	if($controlArg) {
		return $control->view($controlArg);
	} else {
		return $control->view();
	}
}