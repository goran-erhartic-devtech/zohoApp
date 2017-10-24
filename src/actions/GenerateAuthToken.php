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
			}

			echo "*Your token has been successfully generated! Thanks for setting up the ZohoApp*";

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
			echo $e->getMessage();
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}
}