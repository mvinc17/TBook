<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 21/7/16
 * Time: 7:51 PM
 */
class group_url
{
	public function index() {
		global $groupSlug, $group;
		$groupSlug = explode("/", request_path())[1];
		$group = new Group();
		$group->initWithSlug($groupSlug);
		view('group/index');
	}

	public function find() {
		global $groups;


		$groups = Group::GetAll(HTTP::GET("q"));

		view("group/find");
	}

	public function create() {
		view("group/create");
	}
}