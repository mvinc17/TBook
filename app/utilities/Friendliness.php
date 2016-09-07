<?php

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 9/8/16
 * Time: 2:20 PM
 */
class Friendliness
{
	public static function GetFriendlinessFactor($fromID, $toID) {
		global $conn;


		$query = $conn->prepare("SELECT Ff FROM friendliness WHERE fromID = ? OR toID = ?");
		$query->bind_param("ss", $fromID, $toID);
		$query->execute();
		$query->store_result();
		$query->bind_result($t_Ff);
		$query->fetch();

		if($t_Ff == null) {
			$t_Ff = Friendliness::CalculateFriendlinessFactor($fromID, $toID);
		}

		return $t_Ff;
	}

	public static function CalculateFriendlinessFactor($fromID, $toID) {
		global $conn;

		$Ff = 1; //Start at 1

		//Get mentions
		//If < 10, make 10, else multiply by 2

		$lp_query = $conn->prepare("SELECT count(relationships.id) FROM relationships RIGHT JOIN posts ON relationships.to_id = posts.id WHERE relationships.`type`='relationship.tagged' AND posts.posterID = ? AND relationships.from_id = ?");
		$lp_query->bind_param("ss", $fromID, $toID);
		$lp_query->execute();
		$lp_query->store_result();
		$lp_query->bind_result($t_tag_count);
		$lp_query->fetch();

		for($i = 0; $i < $t_tag_count; $i++) {
			if($Ff < 10) {
				$Ff = 10;
			} else {
				$Ff = $Ff * 2;
			}
		}

		//Get liked posts
		//Multiply by 1.2
		$lp_query = $conn->prepare("SELECT count(relationships.id) FROM relationships RIGHT JOIN posts ON relationships.to_id = posts.id WHERE relationships.`type`='relationship.like' AND posts.posterID = ? AND relationships.from_id = ?");
		$lp_query->bind_param("ss", $fromID, $toID);
		$lp_query->execute();
		$lp_query->store_result();
		$lp_query->bind_result($t_lp_count);
		$lp_query->fetch();

		$Ff = $Ff * pow(1.2, $t_lp_count);

		//Get comments
		//Multiply by 1.5

		$lp_query = $conn->prepare("SELECT count(relationships.id) FROM relationships LEFT JOIN comments ON relationships.from_id = comments.id RIGHT JOIN posts ON relationships.to_id = posts.id WHERE relationships.`type`='relationship.comment' AND posts.posterID = ? AND comments.posterID = ? ");
		$lp_query->bind_param("ss", $fromID, $toID);
		$lp_query->execute();
		$lp_query->store_result();
		$lp_query->bind_result($t_comment_count);
		$lp_query->fetch();

		$Ff = $Ff * pow(1.5, $t_comment_count);

		return $Ff;
	}
}