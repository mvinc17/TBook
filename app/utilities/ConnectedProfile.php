<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 22/7/16
 * Time: 8:52 AM
 */

define("PROFILE_FACEBOOK", "profile.facebook");
define("PROFILE_TWITTER", "profile.twitter");
define("PROFILE_YOUTUBE", "profile.youtube");

class ConnectedProfile
{
	public $id;
	public $type;
	public $icon;
	public $url;
	public $profileID;
	public $username;

	private static $icons = array(
		PROFILE_FACEBOOK => array(
			"icon"  => "fa-facebook-official",
			"colour"=> "#3b5998"
		),
		PROFILE_TWITTER => array(
			"icon"  => "fa-twitter-square",
			"colour"=> "#4099FF"
		),
		PROFILE_YOUTUBE => array(
			"icon"  => "fa-youtube-square",
			"colour"=> "#e52d27"
		)
	);

	public function __construct()
	{
	}

	public function initWithID($id) {
		global $conn;

		$query = $conn->prepare("SELECT id,userID,`type`,url FROM connectedProfiles WHERE id = ?");
		$query->bind_param("s", $id);
		$query->execute();
		$query->store_result();
		$query->bind_result($this->id, $this->profileID, $this->type, $this->url);
		$query->fetch();

		$this->icon = $this->findIcon();
		$this->username = $this->findUsername();
	}

	private function findIcon() {
		return (object)ConnectedProfile::$icons[$this->type];
	}

	private function findUsername() {
		switch($this->type) {
			case(PROFILE_FACEBOOK):
				return str_replace("https://www.facebook.com/", "", $this->url);
			break;
			case(PROFILE_TWITTER):
				return str_replace("https://twitter.com/", "", $this->url);
			break;
			case(PROFILE_YOUTUBE):
				$metaTags = get_meta_tags($this->url);
				return $metaTags["title"];
			break;
		}
	}


	public function build() {
		$this->icon = $this->findIcon();
		$this->username = $this->findUsername();
	}


}