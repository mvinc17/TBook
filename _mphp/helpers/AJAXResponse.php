<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 6:33 PM
 */
class AJAXResponse
{
	public static function Success($data = null, $respond = true) {
		$string = json_encode(array(
			"success" => true,
			"data"    => $data
		));

		if($respond) {
			echo $string;
			die();
		} else {
			return $string;
		}
	}

	public static function Failure($data = null, $respond = true) {
		$string = json_encode(array(
			"success" => false,
			"data"    => $data
		));

		if($respond) {
			echo $string;
			die();
		} else {
			return $string;
		}
	}
}