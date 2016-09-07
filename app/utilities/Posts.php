<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 8:03 PM
 */

define("POST_TYPE_POST", "posttype.post");
define("POST_TYPE_PICTURE", "posttype.picture");

class Posts
{
	public static function GetPosts($orderByFf = false, $postType = POST_TYPE_POST) {
		global $conn;

		$groupIDs = implode(",", Profile::GetGroupIDs(Profile::GetLoggedIn()->id));
		if(strlen($groupIDs) > 0) {
			$query = $conn->prepare("SELECT id FROM posts WHERE (groupID IN ($groupIDs) OR groupID IS NULL) AND postType = ? ORDER BY id DESC LIMIT 50");
		} else {
			$query = $conn->prepare("SELECT id FROM posts WHERE groupID IS NULL AND postType = ? ORDER BY id DESC LIMIT 50");
		}
		$query->bind_param("s", $postType);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_postID);

		$posts = array();

		while($query->fetch()) {
			$post = new Post();
			$post->initWithID($t_postID);
			$posts[] = $post;
		}


		if($orderByFf) {
			usort($posts, array("Posts", "comparePosts"));
		}

		
		return $posts;
	}

	public static function GetPostsInAlbum($albumID) {
		global $conn;

		$query = $conn->prepare("SELECT id FROM posts WHERE albumID = ?");
		$query->bind_param("s", $albumID);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_id);

		$posts = array();

		while($query->fetch()) {
			$post = new Post();
			$post->initWithID($t_id);
			$posts[] = $post;
		}

		return $posts;
	}

	public static function comparePosts($postA, $postB) {
		return ($postB->relevance) - ($postA->relevance);
	}

	public static function GetPostsForUser($userID, $includeTaggedPosts = true) {
		global $conn;

		$groupIDs = implode(",", Profile::GetGroupIDs(Profile::GetLoggedIn()->id));

		if(strlen($groupIDs) > 0) {
			$query = $conn->prepare("SELECT id FROM posts WHERE posterID = ? AND (groupID IN ($groupIDs) OR groupID IS NULL) ORDER BY id DESC LIMIT 50");
		} else {
			$query = $conn->prepare("SELECT id FROM posts WHERE posterID = ? AND groupID IS NULL ORDER BY id DESC LIMIT 50");
		}

		$query->bind_param("s", $userID);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_postID);

		$posts = array();

		while($query->fetch()) {
			$post = new Post();
			$post->initWithID($t_postID);
			$posts[] = $post;
		}

		if($includeTaggedPosts) {
			$taggedPosts = GraphManager::ReverseGetEntitiesForRelationshipType(ENTITY_PROFILE, ENTITY_POST, $userID, RELATIONSHIP_TAGGED);


			$allPosts = array_merge($posts, $taggedPosts); //Merge the arrays
			sort($allPosts, SORT_STRING); //Sort by the post timsestamps, in asc order
			return array_reverse($allPosts); //Reverse the array, so that new posts are at the top
		} else {
			return $posts;
		}
	}

	public static function GetPostsInGroup($groupID) {
		global $conn;


		$query = $conn->prepare("SELECT id FROM posts WHERE groupID = ? ORDER BY id DESC LIMIT 50");
		$query->bind_param("s", $groupID);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_postID);

		$posts = array();

		while($query->fetch()) {
			$post = new Post();
			$post->initWithID($t_postID);
			$posts[] = $post;
		}

		return $posts;
	}
}