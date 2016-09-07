<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 20/7/16
 * Time: 10:52 AM
 */
class HTTP
{
	private static $hasCleaned = false;
	private static $POST = array();
	private static $GET = array();

	private static function Clean() {
		if(!HTTP::$hasCleaned) {
			foreach($_POST as $key=>$value) {
				HTTP::$POST[$key] = strip_tags($value);
			}

			foreach($_GET as $key=>$value) {
				HTTP::$GET[$key] = strip_tags($value);
			}
			HTTP::$hasCleaned = true;
		}
	}

	public static function POST($key, $delimiter = null) {
		HTTP::Clean();
		if($delimiter) {
			$raw = $_POST[$key];
			$split = explode($delimiter, $raw);
			$result = array();
			foreach($split as $value) {
				$result[] = filter_var($value, FILTER_SANITIZE_STRING);
			}
			return $result;
		}
		return HTTP::$POST[$key];
	}

	public static function GET($key) {
		HTTP::Clean();
		return HTTP::$GET[$key];
	}
}