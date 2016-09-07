<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 6:01 PM
 */
class Request
{
	public function __construct()
	{
	}

	public static function Redirect($path)
	{
		if($path != Request::GetPath()) {
			header("Location: $path");
			die();
		}
		else {
			//The path is the same as the current page, this is to avoid redirect loops
			return false;
		}
	}

	public static function IsAjax() {
		return (strpos(Request::GetPath(), 'ajax') !== false);
	}

	public static function GetPath() {
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}
}