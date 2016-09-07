<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 21/7/16
 * Time: 9:51 AM
 */
class GlobalVar
{
	private static $vars;

	public static function Set($key, $val) {
		GlobalVar::$vars[$key] = $val;
	}

	public static function Get($key) {
		return GlobalVar::$vars[$key];
	}
}