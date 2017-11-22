<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 6/11/2017
 * Time: 9:34 AM
 */

namespace src\actions;

use src\services\contracts\iHttpRequests;
use src\services\contracts\iRepository;

class HandleLeaveRequest
{
	public function run(iHttpRequests $client, $params, iRepository $repo)
	{
		//DM's action to Approve/Decline leave request
		$respText = $this->dmResponseForLeaveRequest($client, $params, $repo);

		//Temporary message to DM telling him if the action was successful
		$client->messageDMifActionWasSuccessful($params, $respText);
	}

	/**
	 * @param iHttpRequests $client
	 * @param $params
	 * @param iRepository $repo
	 * @return string
	 */
	private function dmResponseForLeaveRequest(iHttpRequests $client, $params, iRepository $repo):string
	{
		//Get DM's ID for private message channel
		$dmId = $params->user->id;
		$departmentManager = $repo->getUserById($dmId);

		//Prepare ID of request, along with comment and DM's decision
		$dmToken = $departmentManager->getToken();
		$leaveRequestId = $params->submission->reqest_id;
		$isApproved = $params->submission->is_approved;
		$remark = $params->submission->leave_reply;

		//Request to Zoho API to approve/reject leave request
		$resp = $client->requestZohoApiToApproveOrRejectLeaveRequest($dmToken, $leaveRequestId, $isApproved, $remark);

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