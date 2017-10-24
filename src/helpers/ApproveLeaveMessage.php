<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 24/10/2017
 * Time: 10:45 AM
 */

namespace src\helpers;

class ApproveLeaveMessage
{
	public static function generateMessage($params, $leave)
	{
		$employee = $params->user->name;
		$leaveFrom = $params->submission->leave_from;
		$leaveTo = $params->submission->leave_to;
		$leaveReason = $params->submission->leave_reason;

		$payload['text'] = "{$employee} has applied for leave request";

		$attachment['text'] = "Leave type: $leave\nFrom: $leaveFrom\nTo: $leaveTo\nReason: $leaveReason";
		$attachment['fallback'] = "New leave request";
		$attachment['color'] = "#551A8B";
		$attachment['attachment_type'] = "default";
		$attachment['callback_id'] = "leave_approval";

		$action1['name'] = "button";
		$action1['text'] = "Approve";
		$action1['type'] = "button";
		$action1['value'] = 'yes';

		$action2['name'] = "button";
		$action2['text'] = "Decline";
		$action2['type'] = "button";
		$action2['value'] = 'no';
		$action2['style'] = "danger";

		$attachment['actions'] = array($action1, $action2);
		$payload['attachments'][] = $attachment;

		return json_encode($attachment);
	}
}