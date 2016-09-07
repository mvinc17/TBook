<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 7:57 PM
 */

//Define constants

function GraphManager_Init() {
	define("ENTITY_PROFILE", "entity.profile");
	define("ENTITY_POST", "entity.post");
	define("ENTITY_COMMENT", "entity.comment");
	define("ENTITY_PICTURE", "entity.picture");
	define("ENTITY_GROUP", "entity.group");
	define("ENTITY_MESSAGE_GROUP", "entity.messagegroup");
	
	define("RELATIONSHIP_COMMENT", "relationship.comment");
	define("RELATIONSHIP_LIKE", "relationship.like");
	define("RELATIONSHIP_IN_GROUP", "relationship.in-group");
	define("RELATIONSHIP_TAGGED", "relationship.tagged");
	define("RELATIONSHIP_IN_MESSAGE_GROUP", "relationship.in-message-group");
}

GraphManager_Init();



class GraphManager
{
	private static $entityClassNames = array(
		ENTITY_PROFILE  => "Profile",
		ENTITY_POST     => "Post",
		ENTITY_COMMENT  => "Comment",
		ENTITY_GROUP    => "Group",
		ENTITY_MESSAGE_GROUP => "MessageGroup"
	);

	public static function GetEntities($relationshipID) {

	}

	public static function GetLikesForPost($postID) {

	}

	public static function GetEntitiesForRelationshipType($fromType, $toType, $toID, $relationshipType) {
		global $conn;

		$query = $conn->prepare("SELECT from_id FROM relationships WHERE from_type = ? AND to_type = ? AND to_id LIKE ? AND `type` = ?");
		$query->bind_param("ssss", $fromType, $toType, $toID, $relationshipType);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_fromID);



		$entities = array();

		while($query->fetch()) {
			$fromClass = GraphManager::$entityClassNames[$fromType];

			$entity = new $fromClass();
			$entity->initWithID($t_fromID);
			$entities[] = $entity;
		}

		return $entities;
	}

	public static function ReverseGetEntitiesForRelationshipType($fromType, $toType, $fromID, $relationshipType) {
		global $conn;

		$query = $conn->prepare("SELECT to_id FROM relationships WHERE from_type = ? AND to_type = ? AND from_id LIKE ? AND `type` = ?");
		$query->bind_param("ssss", $fromType, $toType, $fromID, $relationshipType);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_fromID);



		$entities = array();

		while($query->fetch()) {
			$fromClass = GraphManager::$entityClassNames[$toType];
			$entity = new $fromClass();
			$entity->initWithID($t_fromID);
			$entities[] = $entity;
		}

		return $entities;
	}

	public static function CreateRelationship($fromType, $fromID, $toType, $toID, $relationshipType) {
		global $conn;

		$query = $conn->prepare("INSERT INTO relationships (from_id,from_type,to_id,to_type,`type`) VALUES (?,?,?,?,?)");
		$query->bind_param("sssss", $fromID, $fromType, $toID, $toType, $relationshipType);
		$query->execute();
		$query->close();
	}

	public static function CheckRelationshipExists($fromType, $fromID, $toType, $toID, $relationshipType) {
		global $conn;

		$query = $conn->prepare("SELECT id FROM relationships WHERE from_type = ? AND from_id = ? AND to_type = ? AND to_id = ? AND `type` = ?");
		$query->bind_param("sssss", $fromType, $fromID, $toType, $toID, $relationshipType);
		$query->execute();
		$query->store_result();
		if($query->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public static function DeleteRelationship($fromType, $fromID, $toType, $toID, $relationshipType) {
		global $conn;

		$query = $conn->prepare("DELETE FROM relationships WHERE from_type = ? AND from_id = ? AND to_type = ? AND to_id = ? AND `type` = ?");
		$query->bind_param("sssss", $fromType, $fromID, $toType, $toID, $relationshipType);
		$query->execute();
	}

	public static function DeleteAllRelationshipsForEntity($toType, $toID) {
		global $conn;
		$query = $conn->prepare("DELETE FROM relationships WHERE to_type = ? AND to_id = ?");
		$query->bind_param("ss", $toType, $toID);
		$query->execute();
		$query->close();
	}
}