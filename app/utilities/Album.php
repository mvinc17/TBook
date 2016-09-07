<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 1/9/16
 * Time: 9:08 AM
 */
class Album
{
	public $id;
	public $name;
	public $description;
	public $ownerProfile;

	public $updated;
	public $created;

	public $posts;

	private $exists;

	public function __construct()
	{

	}

	public function initWithID($id) {
		global $conn;

		$this->id = $id;

		$query = $conn->prepare("SELECT ownerID,title,description,updated,created FROM albums WHERE id=?");
		$query->bind_param("s", $id);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_ownerID, $this->name, $this->description, $this->updated, $this->created);
		$query->fetch();

		$this->ownerProfile = new Profile();
		$this->ownerProfile->initWithID($t_ownerID);

		$this->posts = Posts::GetPostsInAlbum($id);

		$this->exists = true;
	}

	public static function GetAllForUser($userID) {
		global $conn;

		$query = $conn->prepare("SELECT id FROM albums WHERE ownerID = ?");
		$query->bind_param("s", $userID);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_id);

		$albums = array();

		while($query->fetch()) {
			$album = new Album();
			$album->initWithID($t_id);
			$albums[] = $album;
		}

		return $albums;
	}

	public static function IDToName($albumID) {
		global $conn;

		$query = $conn->prepare("SELECT title FROM albums WHERE id=?");
		$query->bind_param("s", $albumID);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_name);
		$query->fetch();
		$query->close();

		return $t_name;
	}

	public function save() {
		global $conn;

		$this->updated = time();

		if($this->exists) {
			$query = $conn->prepare("UPDATE albums SET title=?,description=?,updated=? WHERE id = ?");
			$query->bind_param("ssss", $this->name, $this->description, $this->updated, $this->id);
			$query->execute();
		} else {
			$this->created = time();
			$query = $conn->prepare("UPDATE albums SET title=?,description=?,updated=?,created=?,ownerID=? WHERE id = ?");
			$query->bind_param("ssssss", $this->name, $this->description, $this->updated, $this->created, $this->ownerProfile->id);
			$query->execute();
			$this->id = $query->insert_id;
		}


		$query->close();
	}
}