<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 11:22 AM
 */

namespace src\actions;

use src\services\Repository;
use GuzzleHttp\Client;
use src\models\XMLRequestModel;

class SendLeaveRequest
{
	public function run(Client $client, $params, Repository $repo)
	{
		//Prevent 3second timeout
		http_response_code(200);
		
		$fromDate = $params->submission->leave_from;
		$toDate = $params->submission->leave_to;
		$leaveReason = $params->submission->leave_reason;

		$userId = $params->user->id;
		$employee = $repo->getUserById($userId);

		$XML = new XMLRequestModel();
		$XML->setEmployeeId($employee->getZohoUserId())
			->setFrom($fromDate)
			->setTo($toDate)
			->setLeaveType($employee->getLeaveType())
			->setReasonForLeave($leaveReason);

		$xmlPayload = $XML->createXMLData();

		//Send leave request
		$response = $client->request('POST', 'https://people.zoho.com/people/api/leave/records', [
			'form_params' => [
				'authtoken' => $employee->getToken(),
				'xmlData' => $xmlPayload,
			]
		]);

		$result = json_decode($response->getBody()->getContents(), true);

		$dialogResponseChannel = $params->channel->id;
		$dialogResponseText = isset($result['message']['From']) ? $result['message']['From'] : $result['message'];
		$dialogResponseUser = $params->user->id;

		//Response message to Slack user
		$client->request('POST', 'https://slack.com/api/chat.postEphemeral', [
			'form_params' => [
				'token' => $_ENV['TOKEN'],
				'channel' => $dialogResponseChannel,
				'text' => $dialogResponseText,
				'user' => $dialogResponseUser,
				'as_user' => true
			],
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		]);
	}
}