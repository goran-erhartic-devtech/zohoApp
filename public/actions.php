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

$params = json_decode($_POST['payload']);

//Check/set if leave type has been selected
if (!empty($params->actions[0]->selected_options[0]->value)) {
	Container::getInstance()->respondToLeaveType($params);
}

//After chosen leave type generate modal dialog
if ($params->callback_id === "leave_selection") {
	Container::getInstance()->generateModalDialog($params);
}

//After modal dialog submitted send request leave to Zoho API
if ($params->callback_id === "leave_dates") {
	Container::getInstance()->sendLeaveRequest($params);
}