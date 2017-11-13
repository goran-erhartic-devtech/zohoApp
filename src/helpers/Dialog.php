<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/10/2017
 * Time: 4:00 PM
 */

namespace src\helpers;

class Dialog
{
	public static function generateDialog()
	{
		$dialog['callback_id'] = "leave_dates";
		$dialog['title'] = "Please enter the dates:";
		$dialog['submit_label'] = "Request";

		$dateFrom['type'] = "text";
		$dateFrom['placeholder'] = "DD-MM-YYYY";
		$dateFrom['hint'] = "Please enter the start date of your leave according to shown format";
		$dateFrom['label'] = "From:";
		$dateFrom['name'] = "leave_from";

		$dateTo['type'] = "text";
		$dateTo['placeholder'] = "DD-MM-YYYY";
		$dateTo['hint'] = "Please enter the end date of your leave according to shown format";
		$dateTo['label'] = "To:";
		$dateTo['name'] = "leave_to";

		$durationSelect['type'] = "select";
		$durationSelect['label'] = "Apply for half a day?";
		$durationSelect['placeholder'] = "Please choose";
		$durationSelect['value'] = "1";
		$durationSelect['name'] = "is_half_day";

		$durationSelect1['label'] = "Yes";
		$durationSelect1['value'] = "0.5";
		$durationSelect2['label'] = "No";
		$durationSelect2['value'] = "1";

		$durationSelect['options'] = array($durationSelect1, $durationSelect2);

		$reasonForLeave['type'] = "textarea";
		$reasonForLeave['placeholder'] = "Summer holiday";
		$reasonForLeave['hint'] = "Please giva a short summary of reason for leave request";
		$reasonForLeave['label'] = "Reason for leave:";
		$reasonForLeave['name'] = "leave_reason";

		$dialog['elements'] = array($dateFrom, $dateTo, $durationSelect, $reasonForLeave);

		$a = json_encode($dialog);

		return $a;
	}

	public static function generateReasonDialog($requestId, $isApproved)
	{
		$dialog['callback_id'] = "dm_reason";
		$dialog['title'] = "Please leave a reply:";
		$dialog['submit_label'] = "Reply";

		$reply['type'] = "textarea";
		$reply['placeholder'] = "Have a great time on summer holiday";
		$reply['hint'] = "Please giva a short response for this leave request";
		$reply['label'] = "Reply:";
		$reply['name'] = "leave_reply";

		$reqId['type'] = "text";
		$reqId['hint'] = "*DO NOT EDIT THIS*";
		$reqId['label'] = "Request ID:";
		$reqId['name'] = "reqest_id";
		$reqId['value'] = $requestId;

		$decision['type'] = "text";
		$decision['hint'] = "*DO NOT EDIT THIS*";
		$decision['label'] = "Decision:";
		$decision['name'] = "is_approved";
		$decision['value'] = $isApproved;

		$dialog['elements'] = array($reply, $reqId, $decision);

		return json_encode($dialog);
	}
}