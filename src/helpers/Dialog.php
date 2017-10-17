<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/10/2017
 * Time: 4:00 PM
 */

namespace helpers;

class Dialog
{
	public static function generateDialog(){
		$dialog = new \stdClass();
		$dateFrom = new \stdClass();
		$dateTo = new \stdClass();

		$dialog->callback_id = "leave_dates";
		$dialog->title = "Please enter the dates:";
		$dialog->submit_label = "Request";
		$dialog->elements = array($dateFrom, $dateTo);

		$dateFrom->type = "text";
		$dateFrom->placeholder = "DD/MM/YYYY";
		$dateFrom->hint = "Please enter the start date of your leave according to shown format";
		$dateFrom->label = "From:";
		$dateFrom->name = "leave_from";

		$dateTo->type = "text";
		$dateTo->placeholder = "DD/MM/YYYY";
		$dateTo->hint = "Please enter the end date of your leave according to shown format";
		$dateTo->label = "To:";
		$dateTo->name = "leave_to";

		return json_encode($dialog);
	}
}