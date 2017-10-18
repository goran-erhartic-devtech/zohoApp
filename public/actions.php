<?php
require_once(__DIR__ . '/../bootstrap/bootstrap.php');

$payload = json_decode($_POST['payload']);
$userId = $payload->user->id;
$token = $_ENV['TOKEN'];

if (!empty($payload->actions[0]->selected_options[0]->value)) {
	$choosenLeaveType = $payload->actions[0]->selected_options[0]->value;
	$repository->insertLeaveType($choosenLeaveType, $userId);
}

if ($payload->callback_id === "leave_selection") {
	$actionTriggerId = $payload->trigger_id;

	$dialog = \helpers\Dialog::generateDialog();

	$response = $client->request('POST', 'https://slack.com/api/dialog.open', [
		'form_params' => [
			'token' => $token,
			'trigger_id' => $actionTriggerId,
			'dialog' => $dialog,
		]
	]);
}

if ($payload->callback_id === "leave_dates") {
	$fromDate = $payload->submission->leave_from;
	$toDate = $payload->submission->leave_to;

	$employee = $repository->getUserById($userId);

	$XML = new \models\XMLRequestModel();
	$XML->setEmployeeId($employee->getZohoUserId())
		->setFrom($fromDate)
		->setTo($toDate)
		->setLeaveType($employee->getLeaveType());

	$xmlPayload = $XML->xmlSerialize($service);

	$opa = "ASDFASDFASDF";
}