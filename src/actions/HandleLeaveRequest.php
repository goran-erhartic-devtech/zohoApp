<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 6/11/2017
 * Time: 9:34 AM
 */

namespace src\actions;

use GuzzleHttp\Client;
use src\services\Repository;

class HandleLeaveRequest
{
	public function run(Client $client, \stdClass $params, Repository $repo)
	{
		//DM's action to Approve/Decline leave request
		$respText = $this->dmResponseForLeaveRequest($client, $params, $repo);

		//Temporary message to DM telling him if the action was successful
		$client->request('POST', 'https://slack.com/api/chat.postEphemeral', [
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
	 * @param Client $client
	 * @param \stdClass $params
	 * @param Repository $repo
	 * @return string
	 */
	private function dmResponseForLeaveRequest(Client $client, \stdClass $params, Repository $repo):string
	{
		//Get DM's ID for private message channel
		$dmId = $params->user->id;
		$departmentManager = $repo->getUserById($dmId);

		//Prepare ID of request, along with comment and DM's decision
		$leaveRequestId = $params->submission->reqest_id;
		$isApproved = $params->submission->is_approved;
		$remark = $params->submission->leave_reply;

		//Request to Zoho API to approve/reject leave request
		$request = $client->request('POST', 'https://people.zoho.com/people/api/approveRecord', [
			'form_params' => [
				'authtoken' => $departmentManager->getToken(),
				'pkid' => $leaveRequestId,
				'status' => $isApproved,
				'remarks' => $remark,
			]
		]);

		$resp = json_decode($request->getBody()->getContents(), true)['response'];

		//Response message that DM will recieve after accepting/rejecting leave request
		if ($resp['message'] === "Success" && $isApproved === "1") {
			$respText = "Request successfully approved";
		} elseif ($resp['message'] === "Success" && $isApproved === "0") {
			$respText = "Request successfully declined";
		} else {
			$respText = $resp['errors']['message'];
		}
		return $respText;
	}
}