<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 27/8/16
 * Time: 2:23 PM
 */
class MessageGroup
{
	public $id;
	public $name;
	public $colour;
	public $emoji;

	public $members = array();
	public $messages = array();

	public function __construct()
	{
	}

	public function initWithID($id) {
		global $conn;

		$group_query = $conn->prepare("SELECT `name`,colour,emoji FROM messageGroups WHERE id = ?");
		$group_query->bind_param("s", $id);
		$group_query->execute();
		$group_query->store_result();
		$group_query->bind_result($this->name, $this->colour, $t_b64Emoji);
		$group_query->fetch();

		$this->emoji = base64_decode($t_b64Emoji);
		$group_query->close();

		$this->id = $id;

		$this->getMessages();
		$this->getMembers();
	}

	public function initWithNew($name, $colour, $emoji) {
		global $conn;

		$this->name = $name;
		$this->colour = $colour;
		$this->emoji = $emoji;

		$query = $conn->prepare("INSERT INTO messageGroups (`name`, colour, emoji) VALUES (?,?,?)");

		$b64Emoji = base64_encode($emoji);

		$query->bind_param("sss", $name, $colour, $b64Emoji);
		$query->execute();

		$this->id = $query->insert_id;

		$query->close();

		$this->addMember(Profile::GetLoggedIn()->id);
		$this->getMessages();
		$this->getMembers();
	}


	private function getMessages($limit = 100) {
		global $conn;

		$messages_query = $conn->prepare("SELECT id,senderID,content,`time`,media FROM messages WHERE groupID = ? ORDER BY id ASC LIMIT ?");
		$messages_query->bind_param("ss", $this->id, $limit);
		$messages_query->execute();
		$messages_query->store_result();
		$messages_query->bind_result($t_id, $t_senderID, $t_content, $t_time, $t_media);


		while($messages_query->fetch()) {
			$senderProfile = new Profile();
			$senderProfile->initWithID($t_senderID);
			$this->messages[] = [
				"id"        => $t_id,
				"sender"    => $senderProfile,
				"content"   => base64_decode($t_content),
				"time"      => $t_time,
				"media"     => $t_media
			];
		}
	}

	public function postMessage($senderID, $content, $media) {
		global $conn;

		$b64Content = base64_encode($content);
		$time = time();

		$query = $conn->prepare("INSERT INTO messages (senderID,groupID,content,`time`,media) VALUES (?,?,?,?,?)");
		$query->bind_param("sssss", $senderID, $this->id, $b64Content, $time, $media);
		$query->execute();
		$query->close();
	}

	private function getMembers() {
		$this->members = GraphManager::GetEntitiesForRelationshipType(ENTITY_PROFILE, ENTITY_MESSAGE_GROUP, $this->id, RELATIONSHIP_IN_MESSAGE_GROUP);
	}

	public function addMember($userID) {
		GraphManager::CreateRelationship(ENTITY_PROFILE, $userID, ENTITY_MESSAGE_GROUP, $this->id, RELATIONSHIP_IN_MESSAGE_GROUP);
	}

	public function removeMember($userID) {
		GraphManager::DeleteRelationship(ENTITY_PROFILE, $userID, ENTITY_MESSAGE_GROUP, $this->id, RELATIONSHIP_IN_MESSAGE_GROUP);
	}

	public static function GetAllSubscribed() {
		return GraphManager::ReverseGetEntitiesForRelationshipType(ENTITY_PROFILE, ENTITY_MESSAGE_GROUP, Profile::GetLoggedIn()->id, RELATIONSHIP_IN_MESSAGE_GROUP);
	}


}