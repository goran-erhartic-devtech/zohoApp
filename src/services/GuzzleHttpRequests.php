<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 22/11/2017
 * Time: 10:07 AM
 */

namespace src\services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use src\models\User;
use src\services\contracts\iHttpRequests;

class GuzzleHttpRequests implements iHttpRequests
{
	private $client;

	public function __construct()
	{
		$this->client = new Client();
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @return Response
	 */
	public function generateZohoTokenForApiRequests(string $username, string $password)
	{
		return $this->client->request('POST', 'https://accounts.zoho.com/apiauthtoken/nb/create', [
			'form_params' => [
				'SCOPE' => 'zohopeople/peopleapi',
				'EMAIL_ID' => $username,
				'PASSWORD' => $password,
			]
		]);
	}

	/**
	 * @param string $url
	 * @return array
	 */
	public function getEmployeeZohoInfoArray(string $url)
	{
		$response = $this->client->request('GET', $url);
		$employeeZohoInfo = json_decode($response->getBody()->getContents(), true)[0];
		return $employeeZohoInfo;
	}

	public function sendSuccessRegistrationMessage()
	{
		$this->client->request('POST', $_POST['response_url'], [
			'body' => '{"text": "*Your token has been successfully generated! Thanks for setting up the ZohoApp :)*\nPlease continue using this app by simply entering \"_/zoho leave_\" anywhere in the chat window - it will be only visible to you"}',
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}

	/**
	 * @param string $payload
	 * @return Response
	 */
	public function sendPayloadPrivetlyToUser(string $payload)
	{
		return $this->client->request('POST', $_POST['response_url'], [
			'body' => $payload,
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}

	/**
	 * @param string $url
	 * @return array
	 */
	public function getAllLeaveTypes(string $url)
	{
		$response = $this->client->request('GET', $url);
		$results = json_decode($response->getBody()->getContents())->response->result;

		return $results;
	}

	/**
	 * @param string $token
	 * @param string $actionTriggerId
	 * @param string $dialog
	 * @return Response
	 */
	public function generateModalDialog(string $token, string $actionTriggerId, string $dialog)
	{
		return $this->client->request('POST', 'https://slack.com/api/dialog.open', [
			'form_params' => [
				'token' => $token,
				'trigger_id' => $actionTriggerId,
				'dialog' => $dialog,
			]
		]);
	}

	/**
	 * @param $params
	 * @param string $body
	 * @return Response
	 */
	public function editButtonMessage($params, string $body)
	{
		return $this->client->request('POST', $params->response_url, [
			'body' => $body,
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}

	/**
	 * @param $params
	 * @param string $respText
	 * @return Response
	 */
	public function messageDMifActionWasSuccessful($params, string $respText)
	{
		return $this->client->request('POST', 'https://slack.com/api/chat.postEphemeral', [
			'form_params' => [
				'token' => $_ENV['TOKEN'],
				'channel' => $params->channel->id,
				'text' => $respText,
				'user' => $params->user->id,
				'as_user' => false
			],
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		]);
	}

	/**
	 * @param string $dmToken
	 * @param string $leaveRequestId
	 * @param string $isApproved
	 * @param string $remark
	 * @return array
	 */
	public function requestZohoApiToApproveOrRejectLeaveRequest(string $dmToken, string $leaveRequestId, string $isApproved, string $remark)
	{
		$request = $this->client->request('POST', 'https://people.zoho.com/people/api/approveRecord', [
			'form_params' => [
				'authtoken' => $dmToken,
				'pkid' => $leaveRequestId,
				'status' => $isApproved,
				'remarks' => $remark,
			]
		]);

		$resp = json_decode($request->getBody()->getContents(), true)['response'];

		return $resp;
	}

	/**
	 * @param User $employee
	 * @param string $xmlPayload
	 * @return array
	 */
	public function sendLeaveRequest(User $employee, string $xmlPayload)
	{
		$response = $this->client->request('POST', 'https://people.zoho.com/people/api/leave/records', [
			'form_params' => [
				'authtoken' => $employee->getToken(),
				'xmlData' => $xmlPayload,
			]
		]);
		$result = json_decode($response->getBody()->getContents(), true);

		return $result;
	}

	/**
	 * @param string $dialogResponseChannel
	 * @param string $dialogResponseText
	 * @param string $dialogResponseUser
	 * @return Response
	 */
	public function responseMessageToSlackUser(string $dialogResponseChannel, string $dialogResponseText, string $dialogResponseUser)
	{
		return $this->client->request('POST', 'https://slack.com/api/chat.postEphemeral', [
			'form_params' => [
				'token' => $_ENV['TOKEN'],
				'channel' => $dialogResponseChannel,
				'text' => $dialogResponseText,
				'user' => $dialogResponseUser,
				'as_user' => false
			],
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		]);
	}

	/**
	 * @param User $employee
	 * @param $params
	 * @param string $text
	 * @return Response
	 */
	public function sendPMToDM(User $employee, $params, string $text)
	{
		return $this->client->request('POST', 'https://slack.com/api/chat.postMessage', [
			'form_params' => [
				'token' => $_ENV['TOKEN'],
				'channel' => $employee->getReportingTo(),
				'text' => "New leave request from: {$params->user->name}",
				'attachments' => "[{$text}]",
				'username' => "ZohoApp",
				'as_user' => false
			],
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		]);
	}

	/**
	 * @return Response
	 */
	public function welcomeMessage()
	{
		return $this->client->request('POST', $_POST['response_url'], [
			'body' => '{"text": "*INFO:* Hi, welcome to *Zoho People App*! Please use one the following actions:\n
			*/zoho register email password* - _This action needs to be performed only once and you are good to go!_\n
			*/zoho leave* - _This action will allow you to apply for any type of leave that is currently available for you._\n
			"}',
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}

	/**
	 * @return Response
	 */
	public function unknownAction()
	{
		return $this->client->request('POST', $_POST['response_url'], [
			'body' => '{"text": "*ERROR:* Unknown action - Please use one the following actions:\n
			*/zoho register email password* - _This action needs to be performed only once and you are good to go!_\n
			*/zoho leave* - _This action will allow you to apply for any type of leave that is currently available for you._\n"}',
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}

	/**
	 * @return array
	 */
	public function getAllUsersArray()
	{
		$getUserList = $this->client->request('POST', 'https://slack.com/api/users.list', [
			'form_params' => [
				'token' => $_ENV['TOKEN'],
			],
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		]);

		$allUsers = json_decode($getUserList->getBody()->getContents(), true)['members'];

		return $allUsers;
	}

	/**
	 * @return array
	 */
	public function getAllIMchannels()
	{
		$getIMList = $this->client->request('POST', 'https://slack.com/api/im.list', [
			'form_params' => [
				'token' => $_ENV['TOKEN'],
			],
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		]);

		$allIMs = json_decode($getIMList->getBody()->getContents(), true)['ims'];

		return $allIMs;
	}

}