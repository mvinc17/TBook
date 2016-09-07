<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 21/7/16
 * Time: 9:43 AM
 */
class user
{
	public function index($username) {
		global $profile;
		$profile = new Profile();
		$profile->initWithUsername($username);
		view("user/index");
	}

	public function profile($username) {
		global $profile;
		$profile = new Profile();
		$profile->initWithUsername($username);
		view("user/index");
	}

	public function groups($username) {
		global $groups;
		$profileID = Profile::UsernameToID($username);
		$groups = Profile::GetGroups($profileID);
		view();
	}

	public function albums($username) {
		global $posts;

		$posts = Posts::GetPosts(false, POST_TYPE_PICTURE);
	}
}