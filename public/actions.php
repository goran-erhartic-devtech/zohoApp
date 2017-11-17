<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 18/10/2017
 * Time: 12:33 PM
 */

//Include composer autoload.
require_once __DIR__ . '/../vendor/autoload.php';

use src\DI\Container;
use src\helpers\TimeoutWorkaround;

TimeoutWorkaround::execute();

$params = json_decode($_POST['payload']);

switch ($params->callback_id) {
	//After chosen leave type generate modal dialog
	case "leave_selection" :
		Container::getInstance()->generateModalDialog($params);
		break;
	//After modal dialog submitted send request leave to Zoho API
	case "leave_dates" :
		Container::getInstance()->sendLeaveRequest($params);
		break;
	//Generate modal after DM chooses Approve/Reject
	case "leave_approval" :
		Container::getInstance()->generateModalDialog($params);
		break;
	//DM Approve/Reject
	case "dm_reason" :
		Container::getInstance()->handleLeaveRequest($params);
		break;
}
