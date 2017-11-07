<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 6/11/2017
 * Time: 9:34 AM
 */

namespace src\actions;

use GuzzleHttp\Client;
use src\helpers\TimeoutWorkaround;
use src\services\Repository;

class HandleLeaveRequest
{
	public function run(Client $client, \stdClass $params, Repository $repo)
	{
		//Timeout workaround
		TimeoutWorkaround::execute();

		$dmId = $params->user->id;
		$departmentManager = $repo->getUserById($dmId);

		$leaveRequestId = $params->submission->reqest_id;
		$isApproved = $params->submission->is_approved;
		$remark = $params->submission->leave_reply;

		$request = $client->request('POST', 'https://people.zoho.com/people/api/approveRecord', [
			'form_params' => [
				'authtoken' => $departmentManager->getToken(),
				'pkid' => $leaveRequestId,
				'status' => $isApproved,
				'remarks' => $remark,
			]
		]);

		$resp = json_decode($request->getBody()->getContents(), true)['response'];

//		if ($resp['message'] === "Success" && $isApproved === "1") {
//			$respText = "Request approved :+1:";
//		} elseif ($resp['message'] === "Success" && $isApproved === "0") {
//			$respText = "Request declined :rage:";
//		} else {
//			$respText = $resp['errors']['message'];
//		}

		if(isset($resp['errors']['message'])){
			$respText = $resp['errors']['message'];
			$client->requestAsync('POST', 'https://slack.com/api/chat.postMessage', [
				'form_params' => [
					'token' => $_ENV['TOKEN'],
					'channel' => $params->channel->id,
					'text' => $respText,
					'replace_original' => true,
					'user' => $params->user->id,
					'as_user' => false
				],
				'headers' => [
					'Content-Type' => 'application/x-www-form-urlencoded',
				]
			])->wait();
		}
	}
}