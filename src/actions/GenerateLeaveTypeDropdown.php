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
use src\services\Repository;

class GenerateLeaveTypeDropdown
{
	public function run(Client $client, Repository $repo)
	{
		try {
			$getUser = $repo->getUserById($_POST['user_id']);
		} catch (\PDOException $e) {
			die($e->getMessage());
		}

		//Timeout workaround
		ob_start();
		$client->requestAsync('POST', $_POST['response_url'], [
			'body' => '{"text": " "}',
			'headers' => [
				'Content-Type' => 'application/json',
			]
		])->wait();
		$size = ob_get_length();
		header("Content-Length: $size");
		header('Connection: close');

		// flush all output
		ob_end_flush();
		ob_flush();
		flush();
		session_write_close();
		//End timeout workaround

		$authToken = $getUser->getToken();
		$userId = $getUser->getEmail();

		//Get all types of leave that are available
		$url = "https://people.zoho.com/people/api/leave/getLeaveTypeDetails?authtoken={$authToken}&userId={$userId}";
		$response = $client->request('GET', $url);
		$results = json_decode($response->getBody()->getContents())->response->result;

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
}