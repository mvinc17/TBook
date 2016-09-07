<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 21/7/16
 * Time: 7:35 PM
 */
class Group
{
	public $id;
	public $name;
	public $slug;
	public $description;
	public $coverImg;
	public $profileImg;
	public $adminProfileID;

	private $exists;

	public function __construct()
	{
	}

	public function initWithID($id) {
		global $conn;

		$query = $conn->prepare("SELECT id,`name`,slug,description,coverImg,profileImg,adminID FROM groups WHERE id = ?");
		$query->bind_param("s", $id);
		$query->execute();
		$query->bind_result($this->id, $this->name, $this->slug, $this->description, $this->coverImg, $this->profileImg, $this->adminProfileID);
		$query->fetch();
		$query->close();
		$this->exists = true;
	}

	public function initWithSlug($slug) {
		global $conn;

		$query = $conn->prepare("SELECT id,`name`,slug,description,coverImg,profileImg,adminID FROM groups WHERE slug = ?");
		$query->bind_param("s", $slug);
		$query->execute();
		$query->bind_result($this->id, $this->name, $this->slug, $this->description, $this->coverImg, $this->profileImg, $this->adminProfileID);
		$query->fetch();
		$query->close();
		$this->exists = true;
	}

	public function getPosts() {
		return Posts::GetPostsInGroup($this->id);
	}

	public function getAdminProfile() {
		$profile = new Profile();
		$profile->initWithID($this->adminProfileID);
		return $profile;
	}


	public static function GetAll($name) {
		global $conn;

		$t_name = "%".$name."%";

		$query = $conn->prepare("SELECT id FROM groups WHERE `name` LIKE ?");
		$query->bind_param("s", $t_name);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_gid);
		$groups = array();

		while($query->fetch()) {
			$group = new Group();
			$group->initWithID($t_gid);

			$groups[] = $group;
		}

		return $groups;
	}

	public function save() {
		global $conn;

		if($this->exists) {
			$query = $conn->prepare("UPDATE `groups` SET `name`=?, `slug`=?, `description`=?, `coverImg`=?, `profileImg`=?, `adminID`=? WHERE id = ?");
			$query->bind_param("sssssss", $this->name, $this->slug, $this->description, $this->coverImg, $this->profileImg, $this->adminProfileID, $this->id);
		} else {
			$query = $conn->prepare("INSERT INTO `groups` (`name`, `slug`, `description`, `coverImg`, `profileImg`, `adminID`) VALUES (?, ?, ?, ?, ?, ?);");
			$query->bind_param("ssssss", $this->name, $this->slug, $this->description, $this->coverImg, $this->profileImg, $this->adminProfileID);
		}

		$query->execute();
		$query->store_result();

		if(!$this->exists) {
			$this->id = $query->insert_id;
		}

		$query->close();
	}

	public static function CheckIfExists($slug) {
		global $conn;

		$query = $conn->prepare("SELECT id FROM groups WHERE slug = ?");
		$query->bind_param("s", $slug);
		$query->execute();
		$query->bind_result($t_id);
		$query->fetch();



		if($t_id > 0) {
			$query->close();
			return true;
		} else {
			$query->close();
			return false;
		}
	}
}