<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 24/10/2017
 * Time: 9:51 AM
 */

namespace src\helpers;

use GuzzleHttp\Client;

class GetSuperiorsIM
{
	public function getSuperiorsIM(Client $client, string $superiorsMail)
	{
		$getUserList = $client->request('POST', 'https://slack.com/api/users.list', [
			'form_params' => [
				'token' => $_ENV['TOKEN'],
			],
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		]);

		$allUsers = json_decode($getUserList->getBody()->getContents(), true)['members'];

		$superiorsSlackId = '';
		foreach ($allUsers as $user) {
			if($user['profile']['email'] === $superiorsMail){
				$superiorsSlackId = $user['id'];
				break;
			}
		}

		$getIMList = $client->request('POST', 'https://slack.com/api/im.list', [
			'form_params' => [
				'token' => $_ENV['TOKEN'],
			],
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		]);

		$allIMs = json_decode($getIMList->getBody()->getContents(), true)['ims'];
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