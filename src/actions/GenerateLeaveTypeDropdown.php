<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 11:02 AM
 */

namespace src\actions;

use GuzzleHttp\Client;
use src\helpers\Dropdown;
use src\helpers\ExceptionHandler;
use src\helpers\TimeoutWorkaround;
use src\models\User;
use src\services\Repository;

class GenerateLeaveTypeDropdown
{
	public function run(Client $client, Repository $repo)
	{
		try {
			$getUser = $repo->getUserById($_POST['user_id']);
		} catch (ExceptionHandler $e) {
			die($e->getMessage());
		}

		//Timeout workaround
		TimeoutWorkaround::execute();

		//Get all types of leave that are available
		$results = $this->getAllLeaveTypes($client, $getUser);

		//Generate dropdown list of available leave types
		$payload = Dropdown::generatePayload($results);

		//Send the payload privetly to user
		$client->request('POST', $_POST['response_url'], [
			'body' => $payload,
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}

	private function getAllLeaveTypes(Client $client, User $getUser)
	{
		$authToken = $getUser->getToken();
		$userId = $getUser->getEmail();

		$url = "https://people.zoho.com/people/api/leave/getLeaveTypeDetails?authtoken={$authToken}&userId={$userId}";
		$response = $client->request('GET', $url);
		$results = json_decode($response->getBody()->getContents())->response->result;

		return $results;
	}
}