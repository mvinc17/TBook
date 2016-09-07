<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 19/7/16
 * Time: 6:32 PM
 */
class ajax
{
	public function index() {

	}

	public function __construct()
	{
		register_include(mPHP_PATH.'/_mphp/helpers/AJAXResponse.php');
	}

	public function auth_login() {
		if(Authentication::isLoggedIn()) {
			Authentication::Logout(true); //Silently log the user out
		}
		Authentication::Login($_POST['username'], $_POST['password']);
	}

	public function get_tagged() {
		global $conn;

		$query = $conn->prepare("SELECT `name` FROM profiles");
		$query->execute();
		$query->store_result();
		$query->bind_result($t_name);

		$names = array();

	}

	public function me() {  
		AJAXResponse::Success(Profile::GetLoggedIn());
	}

	public function post_comment() {
		$postID = HTTP::POST("postID");
		$content = HTTP::POST("content");

		$comment = new Comment();
		$comment->content = $content;
		$comment->posterID = Profile::GetLoggedIn()->id;
		$comment->save($postID);
	}
	
	public function new_post() {
		$content = HTTP::POST("content");
		$postType = HTTP::POST("post-type");
		$albumID = HTTP::POST("album-id");
		$taggedList = HTTP::POST("tagged", ",");

		$image = FileUploader::Save("post-image");

		$post = new Post();
		$post->content = $content;
		$post->posterProfile = Profile::GetLoggedIn();
		$post->time = time();
		$post->groupID = HTTP::POST("group-id");

		if($image) {
			$post->image = $image;
		}

		if(strlen($albumID) > 0) {
			//Post is in an album
			$post->albumID = $albumID;
		}

		switch($postType) {
			case(POST_TYPE_PICTURE):
				$post->postType = POST_TYPE_PICTURE;
				break;
			default:
				$post->postType = POST_TYPE_POST;
				break;
		}

		$post->save();

		foreach($taggedList as $tagged) {
			GraphManager::CreateRelationship(ENTITY_PROFILE, Profile::UsernameToID($tagged), ENTITY_POST, $post->id, RELATIONSHIP_TAGGED);
		}

		AJAXResponse::Success(
			array(
				"image" => $image
			)
		);
	}

	public function like_post($postID) {
		$profileID = Profile::GetLoggedIn()->id;
		if(!GraphManager::CheckRelationshipExists(ENTITY_PROFILE, $profileID, ENTITY_POST, $postID, RELATIONSHIP_LIKE)) {
			GraphManager::CreateRelationship(ENTITY_PROFILE, $profileID, ENTITY_POST, $postID, RELATIONSHIP_LIKE);
			AJAXResponse::Success(
				array(
					"liked" => true
				)
			);
		} else {
			GraphManager::DeleteRelationship(ENTITY_PROFILE, $profileID, ENTITY_POST, $postID, RELATIONSHIP_LIKE);
			AJAXResponse::Success(
				array(
					"liked" => false
				)
			);
		}
	}

	public function delete_post($postID) {
		$post = new Post();
		$post->initWithID($postID);
		$post->delete();
		
		GraphManager::DeleteAllRelationshipsForEntity(ENTITY_POST, $postID);
		
		AJAXResponse::Success();
	}

	public function join_group($groupID) {
		GraphManager::CreateRelationship(ENTITY_PROFILE, Profile::GetLoggedIn()->id, ENTITY_GROUP, $groupID, RELATIONSHIP_IN_GROUP);
		AJAXResponse::Success();
	}

	public function Ff($people) {
		$ids = explode(",", $people);

		AJAXResponse::Success(array(
			"Ff" => Friendliness::CalculateFriendlinessFactor($ids[0], $ids[1])
		));
	}

	public function get_messages($messageGroupID) {
		$mgroup = new MessageGroup();
		$mgroup->initWithID($messageGroupID);

		AJAXResponse::Success(array(
			"messages" => $mgroup->messages
		));
	}

	public function get_members($messageGroupID) {
		$mgroup = new MessageGroup();
		$mgroup->initWithID($messageGroupID);

		AJAXResponse::Success(array(
			"messages" => $mgroup->members
		));
	}

	public function post_message($messageGroupID) {
		$message = $_POST['message'];

		$mgroup = new MessageGroup();
		$mgroup->initWithID($messageGroupID);

		$image = FileUploader::Save("image");

		if($image) {

		}

		$mgroup->postMessage(Profile::GetLoggedIn()->id, $message, $image);
	}

	public function add_member_to_message_group($messageGroupID) {
		$memberID = Profile::UsernameToID($_POST['username']);

		$mgroup = new MessageGroup();
		$mgroup->initWithID($messageGroupID);

		$mgroup->addMember($memberID);
	}

	public function remove_member_from_message_group($messageGroupID) {
		$memberID = Profile::UsernameToID($_POST['username']);

		$mgroup = new MessageGroup();
		$mgroup->initWithID($messageGroupID);

		$mgroup->removeMember($memberID);
	}

	public function new_message_group() {
		$name = $_POST['name'];
		$colour = $_POST['colour'];
		$emoji = $_POST['emoji'];

		$mgroup = new MessageGroup();
		$mgroup->initWithNew($name, $colour, $emoji);

		AJAXResponse::Success($mgroup);
	}

	public function all_messages() {
		AJAXResponse::Success(
			MessageGroup::GetAllSubscribed()
		);
	}

	public function sign_up() {
		$name = HTTP::POST("name");
		$username = HTTP::POST("username");
		$password = HTTP::POST("password");

		$profilePic = FileUploader::Save("profile-pic");
		$coverImg = FileUploader::Save("cover-pic");

		$profile = new Profile();

		$profile->name = $name;
		$profile->username = $username;
		$profile->coverPic = $coverImg;
		$profile->profilePic = $profilePic;
		$profile->bio = HTTP::POST("bio");

		AJAXResponse::Success(
			$profile->CreateNew($password)
		);
	}

	public function check_group_exists($slug) {
		$group_exists = Group::CheckIfExists($slug);

		AJAXResponse::Success(
			array(
				"exists" => $group_exists
			)
		);
	}

	public function create_group() {
		$group = new Group();
		$group->name = HTTP::POST("name");
		$group->slug = str_replace(" ", "-", strtolower($group->name));

		if(Group::CheckIfExists($group->slug)) {
			AJAXResponse::Failure();
		} else {
			$group->coverImg = FileUploader::Save("cover-photo");
			$group->profileImg = FileUploader::Save("logo-image");

			$memberUsernames = HTTP::POST("members", ",");

			$group->save();

			foreach($memberUsernames as $memberUsername) {
				$userID = Profile::UsernameToID($memberUsername);

				GraphManager::CreateRelationship(ENTITY_PROFILE, $userID, ENTITY_GROUP, $group->id, RELATIONSHIP_IN_GROUP);
			}

			AJAXResponse::Success(array(
				"slug" => $group->slug
			));

		}
	}
	
	public function create_album() {
		$album = new Album();
		
		$album->description = HTTP::POST("description");
		$album->name = HTTP::POST("title");
		$album->ownerProfile = Profile::GetLoggedIn();

		$album->save();

		AJAXResponse::Success(
			$album
		);
	}
}