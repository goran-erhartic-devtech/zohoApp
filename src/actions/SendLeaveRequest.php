<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 11:22 AM
 */

namespace src\actions;

use src\helpers\ApproveLeaveMessage;
use src\models\User;
use src\services\Repository;
use GuzzleHttp\Client;
use src\models\XMLRequestModel;

class SendLeaveRequest
{
	public function run(Client $client, \stdClass $params, Repository $repo)
	{
		$fromDate = str_replace('/', '-', $params->submission->leave_from);
		$toDate = str_replace('/', '-', $params->submission->leave_to);
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
		$dialogResponseText = $this->getResponseText($result);
		$dialogResponseUser = $params->user->id;

		//Response message to Slack user
		$client->request('POST', 'https://slack.com/api/chat.postEphemeral', [
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

		//Request to DM
		if (isset($result['pkId'])) {
			$leaveName = $this->getLeaveName($client, $employee);

			$text = ApproveLeaveMessage::generateMessage($params, $leaveName, $result['pkId']);

			//Send PM to DM
			$client->request('POST', 'https://slack.com/api/chat.postMessage', [
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
	}

	/**
	 * @param $result
	 * @return string
	 */
	private function getResponseText($result):string
	{
		if (isset($result['pkId'])) {
			$dialogResponseText = "Leave request has been sent to your DM for approval";
		} elseif (isset($result['message']['From'])) {
			$dialogResponseText = $result['message']['From'];
		} elseif (isset($result['message']['To'])) {
			$dialogResponseText = $result['message']['To'];
		} elseif (isset($result[0]['message'][0]['From'])) {
			$dialogResponseText = $result[0]['message'][0]['From'];
		} else {
			$dialogResponseText = $result[0]['message'][0]['To'];
		}

		return $dialogResponseText;
	}

	/**
	 * @param Client $client
	 * @param $employee
	 * @return string
	 */
	private function getLeaveName(Client $client, User $employee):string
	{
		//Get all types of leave that are available
		$url = "https://people.zoho.com/people/api/leave/getLeaveTypeDetails?authtoken={$employee->getToken()}&userId={$employee->getZohoUserId()}";
		$response = $client->request('GET', $url);
		$allLeaves = json_decode($response->getBody()->getContents())->response->result;

		//Get name of applied leave
		foreach ($allLeaves as $leave) {
			if ($leave->Id === $employee->getLeaveType()) {
				return $leave->Name;
			}
		}
	}
}