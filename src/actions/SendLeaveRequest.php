<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 11:22 AM
 */

namespace src\actions;

use src\helpers\ApproveLeaveMessage;
use src\helpers\TimeoutWorkaround;
use src\services\Repository;
use GuzzleHttp\Client;
use src\models\XMLRequestModel;

class SendLeaveRequest
{
	public function run(Client $client, \stdClass $params, Repository $repo)
	{
		//Timeout workaround
//		$this->timeoutWorkaround($client, $params);
		TimeoutWorkaround::execute();

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
		$dialogResponseText = isset($result['pkId']) ? "Leave request has been sent to your DM for approval" : (isset($result['message']['From']) ? $result['message']['From'] : $result[0]['message'][0]['From']);
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
			//Get all types of leave that are available
			$url = "https://people.zoho.com/people/api/leave/getLeaveTypeDetails?authtoken={$employee->getToken()}&userId={$employee->getZohoUserId()}";
			$response = $client->request('GET', $url);
			$allLeaves = json_decode($response->getBody()->getContents())->response->result;

			//Get name of applied leave
			$leaveName = '';
			foreach ($allLeaves as $leave) {
				if ($leave->Id === $employee->getLeaveType()) {
					$leaveName = $leave->Name;
					break;
				}
			}

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

//	/**
//	 * @param Client $client
//	 * @param \stdClass $params
//	 */
//	private function timeoutWorkaround(Client $client, \stdClass $params)
//	{
//		ob_start();
//		$client->requestAsync('POST', 'https://slack.com/api/chat.postEphemeral', [
//			'form_params' => [
//				'token' => $_ENV['TOKEN'],
//				'channel' => $params->channel->id,
//				'text' => " ",
//				'user' => $params->user->id,
//				'as_user' => false
//			],
//			'headers' => [
//				'Content-Type' => 'application/x-www-form-urlencoded',
//			]
//		])->wait();
//		$size = ob_get_length();
//		header("Content-Length: $size");
//		header('Connection: close');
//
//		// flush all output
//		ob_end_flush();
//		ob_flush();
//		flush();
//		session_write_close();
//		//End timeout workaround
//	}
}