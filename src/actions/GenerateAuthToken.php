<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 10:23 AM
 */

namespace src\actions;

use GuzzleHttp\Psr7\Response;
use src\exceptions\RegistrationFailedException;
use src\exceptions\SlackActionException;
use src\helpers\GetSuperiorsIM;
use src\services\contracts\iHttpRequests;
use src\services\contracts\iRepository;

class GenerateAuthToken
{
	public function run(iHttpRequests $client, iRepository $repo):Response
	{
		//Get email and password from users input
		try {
			$credentials = $this->validateRegistrationAction();
			$username = $credentials['username'];
			$password = $credentials['password'];

		} catch (RegistrationFailedException $e) {
			return SlackActionException::forInvalidRegistrationCredentials($e->getMessage());
		}

//		Check against DB if token has already been generated
		try {
			$repo->checkIfTokenGenerated();
		} catch (RegistrationFailedException $e) {
			return RegistrationFailedException::forTokenAlreadyExists($e->getMessage());
		}

		//Generate Zoho token for API requests
		$response = $client->generateZohoTokenForApiRequests($username, $password);

		//Extract token from response string
		$authToken = $this->extractTokenFromResponse($response);

		//Check if token has been successfully generated
		$this->checkToken($authToken);

		//Get Zoho info for this Slack user
		$url = "https://people.zoho.com/people/api/forms/P_EmployeeView/records?authtoken={$authToken}&searchColumn=EMPLOYEEMAILALIAS&searchValue={$username}";
		$employeeZohoInfo = $this->getEmployeeZohoInfoArray($client, $url);

		//Store the token and user info in DB
		$repo->insertToken($_POST['user_id'], $username, $authToken, $employeeZohoInfo);

		return $this->successRegistrationMessage($client);

	}

	/**
	 * @return array
	 */
	private function validateRegistrationAction():array
	{
		$usernameAndPasswordArray = explode(" ", $_POST['text']);
		$username = $usernameAndPasswordArray[1];
		$password = $usernameAndPasswordArray[2];

		if (empty($username) || empty($password)) {
			throw new RegistrationFailedException("*INFO*: Please insert your Zoho email and password like shown in example below:\n_/zoho register firstname.lastname@devtechgroup.com pass1234_");
		}

		return array("username" => $username, "password" => $password);
	}

	/**
	 * @param $response
	 * @return string
	 */
	private function extractTokenFromResponse(Response $response):string
	{
		$respToken = explode("\n", $response->getBody()->getContents())[2];
		$authToken = substr($respToken, strpos($respToken, "=") + 1);

		return $authToken;
	}

	/**
	 * @param iHttpRequests $client
	 * @return Response
	 */
	private function successRegistrationMessage(iHttpRequests $client):Response
	{
		return $client->sendSuccessRegistrationMessage();
	}

	/**
	 * @param iHttpRequests $client
	 * @param $url
	 * @return array
	 */
	private function getEmployeeZohoInfoArray(iHttpRequests $client, $url):array
	{
		$employeeZohoInfo = $client->getEmployeeZohoInfoArray($url);

		$employeeReportingToArray = explode(' ', $employeeZohoInfo['Reporting To']);
//			$superiorsMail = strtolower($employeeReportingToArray[0] . '.' . $employeeReportingToArray[1] . '@devtechhroup.com');

		//TODO delete this - ONLY for testing - use above
		$superiorsMail = strtolower($employeeReportingToArray[0] . '.ns@gmail.com');

		$employeeZohoInfo['superiorIM'] = GetSuperiorsIM::getSuperiorsIM($client, $superiorsMail);

		return $employeeZohoInfo;
	}

	/**
	 * @param $authToken
	 */
	private function checkToken($authToken)
	{
		try {
			if ($authToken === "INVALID_PASSWORD") {
				throw new RegistrationFailedException("*ERROR*: _Invalid password - please try again_");
			} elseif ($authToken === "NO_SUCH_USER") {
				throw new RegistrationFailedException("*ERROR*: _Invalid username - please try again_");
			} elseif ($authToken === "INVALID_CREDENTIALS") {
				throw new RegistrationFailedException("*ERROR*: _Invalid credentials - please check your email and password try again_");
			}
		} catch (RegistrationFailedException $e) {
			return RegistrationFailedException::forInvalidInput($e->getMessage());
		}
	}
}