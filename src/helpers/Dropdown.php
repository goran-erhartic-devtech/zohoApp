<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/10/2017
 * Time: 2:36 PM
 */

namespace src\helpers;

use src\models\LeaveType;

class Dropdown
{
	public static function generatePayload($results)
	{
		$leaveTypes = array();
		foreach ($results as $result) {
			$val = new LeaveType();
			$val->text = $result->Name . " (" . $result->BalanceCount . " days available)";
			$val->value = $result->Id;
			array_push($leaveTypes, $val);
		}

		$payload['text'] = "Hi there - welcome to Zoho Poeple :)";
		$payload['response_type'] = "ephemeral";

		$attachment['text'] = "Please choose type of leave from the dropdown";
		$attachment['fallback'] = "Please choose leave type to proceed";
		$attachment['color'] = "#551A8B";
		$attachment['attachment_type'] = "default";
		$attachment['callback_id'] = "leave_selection";

		$action['name'] = "leave_list";
		$action['text'] = "Select";
		$action['type'] = "select";
		$action['options'] = $leaveTypes;

		$attachment['actions'][] = $action;
		$payload['attachments'][] = $attachment;

		return json_encode($payload);
	}
}