<?php

require_once(__DIR__ . '/../bootstrap/bootstrap.php');

if (isset($_POST['text']) && $_POST['text'] != '') {
	$usernameAndPasswordArray = explode(" ", $_POST['text']);
	$username = $usernameAndPasswordArray[0];
	$password = $usernameAndPasswordArray[1];

	$repository->checkIfTokenGenerated();

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

		echo "*Your token has been successfully generated! Thanks for setting up the ZohoApp*";

		$url = "https://people.zoho.com/people/api/forms/P_EmployeeView/records?authtoken={$authToken}&searchColumn=EMPLOYEEMAILALIAS&searchValue={$username}";
		$response = $client->request('GET', $url);
		$employeeZohoInfo = json_decode($response->getBody()->getContents())[0];
		$employeeZohoId = $employeeZohoInfo->ownerID;

		$repository->insertToken($_POST['user_id'], $username, $authToken, $employeeZohoId);
	} catch (PDOException $e) {
		echo $e->getMessage();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
} else {
	try {
		$getUser = $repository->getUserById($_POST['user_id']);
	} catch (PDOException $e) {
		die($e->getMessage());
	}

	$authToken = $getUser->getToken();
	$userId = $getUser->getEmail();

	$url = "https://people.zoho.com/people/api/leave/getLeaveTypeDetails?authtoken={$authToken}&userId={$userId}";
	$response = $client->request('GET', $url);
	$results = json_decode($response->getBody()->getContents())->response->result;

	$payload = \helpers\Payload::generatePayload($results);

	$response = $client->request('POST', $_POST['response_url'], [
		'body' => $payload,
		'headers' => [
			'Content-Type' => 'application/json',
		]
	]);
}