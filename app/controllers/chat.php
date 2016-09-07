<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 27/8/16
 * Time: 5:38 PM
 */
class chat
{
	public function __construct()
	{
	}

	public function index($groupID) {
		global $messageGroups;
		$messageGroups = MessageGroup::GetAllSubscribed();

		view();
	}

	public function messages($groupID) {
		global $messageGroup;
		$messageGroup = new MessageGroup();
		$messageGroup->initWithID($groupID);
		view();
	}
}