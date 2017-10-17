<?php
require_once(__DIR__ . '/../bootstrap/bootstrap.php');

$payload = json_decode($_POST['payload']);

$token = $_ENV['TOKEN'];
$choosenLeaveType = $payload->actions[0]->selected_options[0]->value;
$actionTriggerId = $payload->trigger_id;

$dialog = \helpers\Dialog::generateDialog();

$response = $client->request('POST', 'https://slack.com/api/dialog.open', [
	'form_params' => [
		'token' => $token,
		'trigger_id' => $actionTriggerId,
		'dialog' => $dialog,
	]
]);