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

$command = explode(' ', $_POST['text']);

switch ($command[0]) {
	case "":
		Container::getInstance()->welcomeMessage();
		break;
	case "register":
		Container::getInstance()->generateAuthToken();
		break;
	case "leave":
		Container::getInstance()->generateLeaveTypeDropdown();
		break;
	default:
		Container::getInstance()->unknownAction();
		break;
}
