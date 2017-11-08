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

if (isset($_POST['text']) && $_POST['text'] != '') {
	Container::getInstance()->generateAuthToken();
} else {
	Container::getInstance()->generateLeaveTypeDropdown();
}