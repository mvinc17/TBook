<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 9:45 PM
 */

function pluralise($text, $count) {
	if($count == 1) {
		return $text;
	} else {
		return $text."s";
	}
}


function timeify($time) {
	$timestamp = time() - $time;
	switch(true) {
		case($timestamp < 120):
			return "just now";
		break;
		case($timestamp >=120 && $timestamp < 2700):
			return floor($timestamp / 60)." minutes ago";
		break;
		case($timestamp >= 2700 && $timestamp < 5400):
			return "1 hour ago";
		break;
		case($timestamp >= 5400 && $timestamp < 9000):
			return "2 hours ago";
		break;
		case($timestamp >= 9000 && $timestamp < 12600):
			return "3 hours ago";
		break;
		case($timestamp >= 12600 && $timestamp < 86400):
			return floor($timestamp / (60*60))." hours ago";
		break;
		case($timestamp >= 86400):
			$days = floor($timestamp / 86400);
			return $days." " . pluralise("day", $days) . " ago";
		break;
	}
}