<?php

require_once(__DIR__ . '/../bootstrap/bootstrap.php');

if (isset($_POST['text']) && $_POST['text'] != '') {
	$usernameAndPasswordArray = explode(" ", $_POST['text']);
	$username = $usernameAndPasswordArray[0];
	$password = $usernameAndPasswordArray[1];

	$aa = $_POST;

	\Database\Repository::checkIfTokenGenerated($db);

	$client = new \GuzzleHttp\Client([]);
	$response = $client->request('POST', 'https://accounts.zoho.com/apiauthtoken/nb/create', [
		'form_params' => [
			'SCOPE' => 'zohopeople/peopleapi',
			'EMAIL_ID' => $username,
			'PASSWORD' => $password,
		]
	]);

	$respToken = explode("\n", $response->getBody()->getContents())[2];
	$authToken = substr($respToken, strpos($respToken, "=") + 1);

	try {
		if ($authToken === "INVALID_PASSWORD") {
			throw new Exception("*ERROR*: _Invalid password - please try again_");
		} elseif ($authToken === "NO_SUCH_USER") {
			throw new Exception("*ERROR*: _Invalid username - please try again_");
		}

		\Database\Repository::insertToken($db, $_POST['user_id'], $username, $authToken);

		echo "*Your token has been successfully generated! Thanks for setting up the ZohoApp*";
	} catch (PDOException $e) {
		echo $e->getMessage();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
} else {
	try {
		$getUser = \Database\Repository::getUser($db);
	} catch (PDOException $e) {
		die($e->getMessage());
	}

	$authToken = $getUser->getToken();
	$userId = $getUser->getEmail();
	$client = new \GuzzleHttp\Client([]);
	$url = "https://people.zoho.com/people/api/leave/getLeaveTypeDetails?authtoken={$authToken}&userId={$userId}";
	$response = $client->request('GET', $url);

	$results = json_decode($response->getBody()->getContents())->response->result;

	$leaveTypes = array();
	foreach ($results as $result) {
		$val = new stdClass();
		$val->text = $result->Name . " (" . $result->BalanceCount . " days available)";
		$val->value = $result->Name;
		array_push($leaveTypes, $val);
	}

	$payload =
		'{
			"text": "Hi there - welcome to Zoho Poeple :)",
			"response_type": "in_channel",
			"attachments": [
				{
   					"text": "Please choose type of leave from the dropdown",
   					"fallback": "Please choose leave type to proceed",
					"color": "#3AA3E3",
					"attachment_type": "default",
					"callback_id": "leave_selection",
					"actions": [
						{
							"name": "leave_list",
							"text": "Select",
							"type": "select"
						}
					]
				}
			]
		}';

	$dec = json_decode($payload);
	$dec->attachments[0]->actions[0]->options = $leaveTypes;

	$fin = json_encode($dec);

	$response = $client->request('POST', "https://hooks.slack.com/services/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", [
		'body' => $fin,
		'headers' => [
			'Content-Type' => 'application/json',
		]
	]);



}