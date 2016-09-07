<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 8:09 PM
 */
class Profile
{
	public $username;
	public $name;
	public $location;
	public $bio;
	public $profilePic;
	public $coverPic;
	public $id;

	public function __construct()
	{
	}

	public function initWithID($id) {
		global $conn;

		$query = $conn->prepare("SELECT username,`name`,location,coverImg,profileImg,bio FROM profiles WHERE id = ?");
		$query->bind_param("s", $id);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_uname, $t_name, $t_location, $t_coverImg, $t_profileImg, $t_bio);
		$query->fetch();

		$this->username = $t_uname;
		$this->name = $t_name;
		$this->location = $t_location;
		$this->coverPic = $t_coverImg;
		$this->profilePic = $t_profileImg;
		$this->bio = $t_bio;
		$this->id = $id;
	}

	public function initWithUsername($username) {
		global $conn;

		$query = $conn->prepare("SELECT id,username,`name`,location,coverImg,profileImg,bio FROM profiles WHERE username = ?");
		$query->bind_param("s", $username);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_id, $t_uname, $t_name, $t_location, $t_coverImg, $t_profileImg, $t_bio);
		$query->fetch();

		$this->username = $t_uname;
		$this->name = $t_name;
		$this->location = $t_location;
		$this->coverPic = $t_coverImg;
		$this->profilePic = $t_profileImg;
		$this->bio = $t_bio;
		$this->id = $t_id;
	}

	public static function GetLoggedIn() {
		$profile = new Profile();
		$profile->initWithUsername(Session::GetUsername());
		return $profile;
	}

	public static function UsernameToID($username) {
		global $conn;

		$query = $conn->prepare("SELECT id FROM profiles WHERE username = ?");
		$query->bind_param("s", $username);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_id);
		$query->fetch();

		return $t_id;
	}

	public static function IsInGroup($userID, $groupID) {
		return GraphManager::CheckRelationshipExists(ENTITY_PROFILE, $userID, ENTITY_GROUP, $groupID, RELATIONSHIP_IN_GROUP);
	}

	public static function GetGroups($userID) {
		return GraphManager::ReverseGetEntitiesForRelationshipType(ENTITY_PROFILE, ENTITY_GROUP, $userID, RELATIONSHIP_IN_GROUP);
	}

	public static function GetGroupIDs($userID) {
		$groups = Profile::GetGroups($userID);

		$ids = array();

		foreach($groups as $group) {
			$ids[] = $group->id;
		}

		return $ids;
	}

	public function CreateNew($password) {
		global $conn;

		$hashed = password_hash($password, PASSWORD_DEFAULT);

		$query = $conn->prepare("INSERT INTO `profiles` (`username`, `name`, `coverImg`, `profileImg`, `bio`) VALUES (?,?,?,?,?)");
		$query->bind_param("sssss", $this->username, $this->name, $this->coverPic, $this->profilePic, $this->bio);
		$query->execute();

		$this->id = $query->insert_id;
		$query->close();

		$login_query = $conn->prepare("INSERT INTO logins (username,passwd,profileID) VALUES (?,?,?)");
		$login_query->bind_param("sss", $this->username, $hashed, $this->id);
		$login_query->execute();
		$login_query->close();

		return $this;

	}
}