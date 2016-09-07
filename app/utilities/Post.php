<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 8:04 PM
 */
class Post
{
	public $posterProfile;
	public $time;
	public $location;
	public $content;
	public $comments;
	public $id;
	public $likes;
	public $tagged;
	public $image;
	public $groupID;
	public $group;
	public $Ff;
	public $age;
	public $relevance;
	public $postType;
	public $albumID;
	public $albumName;

	public function __construct()
	{
	}

	public function __toString()
	{
		return sprintf("%03d", $this->time);
	}

	public function initWithID($id) {
		global $conn;

		$this->id = $id;

		$post_query = $conn->prepare("SELECT posterID,content,postedTime,postedLocation,image,groupID,postType,albumID FROM posts WHERE id = ?");
		$post_query->bind_param("s", $id);
		$post_query->execute();
		$post_query->bind_result($t_posterID, $t_content, $t_postedTime, $t_location, $t_image,$t_groupID, $t_postType, $this->albumID);
		$post_query->store_result();
		$post_query->fetch();


		$this->time = $t_postedTime;
		$this->location = $t_location;
		$this->content = $t_content;
		$this->image = $t_image;
		$this->groupID = $t_groupID;
		$this->postType = $t_postType;

		$this->posterProfile = new Profile();
		$this->posterProfile->initWithID($t_posterID);

		//Get comments using our fancy Graph Data Abstracter (GDA)
		$this->comments = GraphManager::GetEntitiesForRelationshipType(ENTITY_COMMENT, ENTITY_POST, $id, RELATIONSHIP_COMMENT);
		$this->likes = GraphManager::GetEntitiesForRelationshipType(ENTITY_PROFILE, ENTITY_POST, $id, RELATIONSHIP_LIKE);
		$this->tagged = GraphManager::GetEntitiesForRelationshipType(ENTITY_PROFILE, ENTITY_POST, $id, RELATIONSHIP_TAGGED);

		if($this->groupID) {
			$this->group = new Group();
			$this->group->initWithID($this->groupID);
		}

		//Get the Ff

		$this->Ff = Friendliness::GetFriendlinessFactor(Profile::GetLoggedIn()->id, $this->posterProfile->id);

		$this->age = time() - $this->time;

		//Calculate the post relevance

		$ageDays = (time() - $this->time) / 86400;
		if((time() - $this->time) == 0) {
			$ageDays = 0.00001157407407; // 1/86400
		}
		$this->relevance = (1/$ageDays) * $this->Ff;

		$this->albumName = Album::IDToName($this->albumID);
	}

	public function save() {
		global $conn;

		$query = $conn->prepare("INSERT INTO posts (posterID,content,postedTime,image,groupID,postType,albumID) VALUES (?,?,?,?,?,?,?)");
		$query->bind_param("sssssss", $this->posterProfile->id, $this->content, $this->time, $this->image, $this->groupID, $this->postType, $this->albumID);
		$query->execute();
		$this->id = $query->insert_id;

		$query->close();
	}

	public function delete() {
		global $conn;

		$query = $conn->prepare("DELETE FROM posts WHERE id = ?");
		$query->bind_param("s", $this->id);
		$query->execute();
		$query->close();
	}
}