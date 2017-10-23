<?php
//Include composer autoload.
require_once __DIR__ . '/vendor/autoload.php';

use src\DI\Container;

if (isset($_POST['text']) && $_POST['text'] != '') {
	//Prevent 3second timeout
	http_response_code(200);

	Container::getInstance()->generateAuthToken();
} else {
	Container::getInstance()->generateLeaveTypeDropdown();
}