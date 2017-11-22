<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 24/10/2017
 * Time: 9:51 AM
 */

namespace src\helpers;

use src\services\contracts\iHttpRequests;

class GetSuperiorsIM
{
	public static function getSuperiorsIM(iHttpRequests $client, string $superiorsMail)
	{
		$allUsers = $client->getAllUsersArray();
		$superiorsSlackId = '';
		foreach ($allUsers as $user) {
			if($user['profile']['email'] === $superiorsMail){
				$superiorsSlackId = $user['id'];
				break;
			}
		}

		$allIMs = $client->getAllIMchannels();
		$superiorsIMid = '';
		foreach ($allIMs as $im) {
			if($im['user'] === $superiorsSlackId){
				$superiorsIMid = $im['id'];
				break;
			}
		}

		return $superiorsIMid;
	}
}