<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 22/7/16
 * Time: 9:04 AM
 */
class EntityManager
{
	public static function GetAllConnectedProfilesForUser($userID) {
		global $conn;

		$query = $conn->prepare("SELECT id,userID,`type`,url FROM connectedProfiles WHERE userID = ?");
		$query->bind_param("s", $userID);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_id, $t_userID, $t_type, $t_url);

		$profiles = array();

		while($query->fetch()) {
			$profile = new ConnectedProfile();
			$profile->id = $t_id;
			$profile->profileID = $t_userID;
			$profile->type = $t_type;
			$profile->url = $t_url;
			$profile->build();

			$profiles[] = $profile;
		}

		return $profiles;
	}
}