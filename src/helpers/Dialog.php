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
		$dateFrom['placeholder'] = "DD/MM/YYYY";
		$dateFrom['hint'] = "Please enter the start date of your leave according to shown format";
		$dateFrom['label'] = "From:";
		$dateFrom['name'] = "leave_from";

		$dateTo['type'] = "text";
		$dateTo['placeholder'] = "DD/MM/YYYY";
		$dateTo['hint'] = "Please enter the end date of your leave according to shown format";
		$dateTo['label'] = "To:";
		$dateTo['name'] = "leave_to";

		$reasonForLeave['type'] = "textarea";
		$reasonForLeave['placeholder'] = "Summer holiday";
		$reasonForLeave['hint'] = "Please giva a short summary of reason for leave request";
		$reasonForLeave['label'] = "Reason for leave:";
		$reasonForLeave['name'] = "leave_reason";

		$dialog['elements'] = array($dateFrom, $dateTo, $reasonForLeave);

		return json_encode($dialog);
	}
}