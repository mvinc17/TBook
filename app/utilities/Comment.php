<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 8:09 PM
 */
class Comment
{
	public $content;
	public $poster;
	public $posterID;
	private $existing = false;

	public function __construct()
	{
	}

	public function initWithID($commentID) {
		global $conn;

		$query = $conn->prepare("SELECT posterID,content FROM comments WHERE id = ?");
		$query->bind_param("s", $commentID);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_posterID, $t_content);
		$query->fetch();


		$this->posterID = $t_posterID;
		$this->poster = new Profile();
		$this->poster->initWithID($t_posterID);
		$this->content = $t_content;
		$this->existing = true;
	}

	public function save($postID) {
		global $conn;

		if($this->existing) {
			//Comments are read only, so don't to anything haha
		} else {
			$query = $conn->prepare("INSERT INTO comments (posterID,content) VALUES (?,?)");
			$query->bind_param("ss", $this->posterID, $this->content);
			$query->execute();
			$commentID = $query->insert_id;
			$query->close();

			GraphManager::CreateRelationship(ENTITY_COMMENT, $commentID, ENTITY_POST, $postID, RELATIONSHIP_COMMENT);
		}
	}
}