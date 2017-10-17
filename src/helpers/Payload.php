<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/10/2017
 * Time: 2:36 PM
 */

namespace helpers;

class Payload
{
	public static function generatePayload($results){
		$leaveTypes = array();
		foreach ($results as $result) {
			$val = new \stdClass();
			$val->text = $result->Name . " (" . $result->BalanceCount . " days available)";
			$val->value = $result->Id;
			array_push($leaveTypes, $val);
		}

		$payload['text'] = "Hi there - welcome to Zoho Poeple :)";
		$payload['response_type'] = "in_channel";

		$attachment['text'] = "Please choose type of leave from the dropdown";
		$attachment['fallback'] = "Please choose leave type to proceed";
		$attachment['color'] = "#3AA3E3";
		$attachment['attachment_type'] = "default";
		$attachment['callback_id'] = "leave_selection";

		$action['name'] = "leave_list";
		$action['text'] = "Select";
		$action['type'] = "select";
		$action['options'] = $leaveTypes;

        $attachment['actions'] = $action;
        $payload['attachments'] = $attachment;

		return json_encode($payload);
	}
}