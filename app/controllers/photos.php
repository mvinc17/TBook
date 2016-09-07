<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 1/9/16
 * Time: 9:18 AM
 */
class photos
{
	public function index() {

	}

	public function view($postID) {
		global $post;

		$post = new Post();
		$post->initWithID($postID);

		view();
	}

	public function album($albumID) {
		global $album;

		$album = new Album();
		$album->initWithID($albumID);

		view();
	}

	public function newalbum() {
		view();
	}
}