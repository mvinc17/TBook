<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 2:22 PM
 */
class Authentication
{
	private static $isLoggedIn;
	private static $userName;

	public function __construct()
	{
		if(isset($_SESSION[KEY_SESSION_ID])) {
			//There is a session
			if($this->verifySession($_SESSION[KEY_SESSION_ID])) {
				Authentication::setIsLoggedIn(true);
				$session = new Session();
				$session->Make($_SESSION[KEY_SESSION_ID]);
			} else {
				Authentication::setIsLoggedIn(false);
			}
		} else {
			//There is not a session
			Authentication::setIsLoggedIn(false);
			if(!Request::IsAjax() && Request::GetPath() != '/account/register') {
				Request::Redirect('/account/login');
			}
		}
	}

	public static function getUsername() {
		return Authentication::$userName;
	}

	public static function setIsLoggedIn($state) {
		Authentication::$isLoggedIn = $state;
	}

	public static function isLoggedIn() {
		return Authentication::$isLoggedIn;
	}

	public static function Logout($silent = false) {
		global $conn;
		if(Authentication::isLoggedIn()) {
			$query = $conn->prepare("UPDATE sessions SET valid = 0 WHERE sessions.sessionID = ?");
			$query->bind_param("s", $_SESSION[KEY_SESSION_ID]);
			$query->execute();
			$query->close();
			if(!$silent) {
				Request::Redirect('/account/login');
			}
		}
	}

	public static function Login($username, $password) {
		global $conn;

		$hashQuery = $conn->prepare("SELECT passwd FROM logins WHERE username = ?");
		$hashQuery->bind_param("s", $username);
		$hashQuery->execute();
		$hashQuery->store_result();
		$hashQuery->bind_result($t_passwd_hash);
		$hashQuery->fetch();

		if(strlen($t_passwd_hash) > 0) {
			if(password_verify($password, $t_passwd_hash)) {
				//Create a new session and save it in the database
				$session = new Session();
				$session->Create($username);
				AJAXResponse::Success();
			} else {
				AJAXResponse::Failure(array(
					"reason" => "Invalid username and/or password"
				));
			}
		} else {
			AJAXResponse::Failure(array(
				"reason" => "Invalid username and/or password"
			));
		}


	}

	private function verifySession($sessionID) {
		global $conn;

		$query = $conn->prepare("SELECT logins.id,logins.username,sessions.ip,sessions.start,sessions.end,sessions.valid FROM sessions RIGHT JOIN logins ON sessions.username = logins.username WHERE sessions.sessionID = ?");
		$query->bind_param("s", $sessionID);
		$query->execute();
		$query->bind_result($t_login_id, $t_login_username, $t_session_ip, $t_session_start, $t_session_end, $t_valid);
		$query->fetch();

		if(
			$_SESSION[KEY_SESSION_IP] == $t_session_ip &&
			$_SESSION[KEY_SESSION_START] == $t_session_start &&
			$_SESSION[KEY_SESSION_EXPIRE] == $t_session_end &&
			$t_valid == 1
		) {
			//The session has been verified! Hooray
			Authentication::$userName = $t_login_username;
			return true;
		} else {
			//The session is not valid
			return false;
		}
	}
}