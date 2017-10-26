<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 10:23 AM
 */

namespace src\actions;

use src\DI\Container;
use src\services\Repository;
use GuzzleHttp\Client;

class GenerateAuthToken
{
	public function run(Client $client, Repository $repo)
	{
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

		//Get email and password from users input
		$usernameAndPasswordArray = explode(" ", $_POST['text']);
		$username = $usernameAndPasswordArray[0];
		$password = $usernameAndPasswordArray[1];

		//Check against DB if token has already been generated
		$repo->checkIfTokenGenerated();

		//Generate Zoho token for API requests
		$response = $client->request('POST', 'https://accounts.zoho.com/apiauthtoken/nb/create', [
			'form_params' => [
				'SCOPE' => 'zohopeople/peopleapi',
				'EMAIL_ID' => $username,
				'PASSWORD' => $password,
			]
		]);

		//Extract token from response string
		$respToken = explode("\n", $response->getBody()->getContents())[2];
		$authToken = substr($respToken, strpos($respToken, "=") + 1);

		try {
			if ($authToken === "INVALID_PASSWORD") {
				throw new \Exception("*ERROR*: _Invalid password - please try again_");
			} elseif ($authToken === "NO_SUCH_USER") {
				throw new \Exception("*ERROR*: _Invalid username - please try again_");
			} elseif ($authToken === "INVALID_CREDENTIALS") {
				throw new \Exception("*ERROR*: _Invalid credentials - please check your email and password try again_");
			}

			$client->request('POST', $_POST['response_url'], [
				'body' => '{"text": "*Your token has been successfully generated! Thanks for setting up the ZohoApp*"}',
				'headers' => [
					'Content-Type' => 'application/json',
				]
			]);

			//Get Zoho info for this Slack user
			$url = "https://people.zoho.com/people/api/forms/P_EmployeeView/records?authtoken={$authToken}&searchColumn=EMPLOYEEMAILALIAS&searchValue={$username}";
			$response = $client->request('GET', $url);
			$employeeZohoInfo = json_decode($response->getBody()->getContents(), true)[0];

			$employeeReportingToArray = explode(' ', $employeeZohoInfo['Reporting To']);
//			$superiorsMail = strtolower($employeeReportingToArray[0] . '.' . $employeeReportingToArray[1] . '@devtechhroup.com');

			//TODO delete this - ONLY for testing - use above
			$superiorsMail = strtolower($employeeReportingToArray[0] . '.ns@gmail.com');

			$employeeZohoInfo['superiorIM'] = Container::getInstance()->getSuperiorsIM($superiorsMail);


			//Store the token and user info in DB
			$repo->insertToken($_POST['user_id'], $username, $authToken, $employeeZohoInfo);
		} catch (\PDOException $e) {
			$text = ['text' => $e->getMessage()];
			$client->request('POST', $_POST['response_url'], [
				'body' => json_encode($text),
				'headers' => [
					'Content-Type' => 'application/json',
				]
			]);
		} catch (\Exception $e) {
			$text = ['text' => $e->getMessage()];
			$client->request('POST', $_POST['response_url'], [
				'body' => json_encode($text),
				'headers' => [
					'Content-Type' => 'application/json',
				]
			]);
		}
	}
}