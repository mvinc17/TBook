<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 6:44 PM
 */
class Session
{
	private static $username;
	private static $profileID;

	public function __construct()
	{
	}

	public function Create($username) {
		global $conn;

		$sessionID = uniqid("tbook_sess_");
		$startTime = time();
		//Sessions expire after 4 hours
		$expireTime = time() + (60*60*4);
		$sessionIP = $_SERVER["REMOTE_ADDR"];

		$_SESSION[KEY_SESSION_ID] = $sessionID;
		$_SESSION[KEY_SESSION_START] = $startTime;
		$_SESSION[KEY_SESSION_EXPIRE] = $expireTime;
		$_SESSION[KEY_SESSION_IP] = $sessionIP;

		$insertQuery = $conn->prepare("INSERT INTO sessions (sessionID,username,ip,start,`end`) VALUES (?,?,?,?,?)");
		$insertQuery->bind_param("sssss", $sessionID, $username, $sessionIP, $startTime, $expireTime);
		$insertQuery->execute();
		$insertQuery->close();

		Session::$username = $username;
	}

	public function Make($sessionID) {
		global $conn;

		$query = $conn->prepare("SELECT username,ip,start,`end` FROM sessions WHERE sessionID = ?");
		$query->bind_param("s", $sessionID);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_username, $t_ip, $t_start, $t_end);
		$query->fetch();

		Session::$username = $t_username;
	}

	public static function GetUsername() {
		return Session::$username;
	}
}