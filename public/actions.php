<?php
require_once(__DIR__ . '/../bootstrap/bootstrap.php');

$payload = json_decode($_POST['payload']);

$token = '';
$choosenLeaveType = $payload->actions[0]->selected_options[0]->value;
$actionTriggerId = $payload->trigger_id;

$dialog = '{
    "callback_id": "rydegegre",
    "title": "Please enter the dates:",
    "submit_label": "Request",
    "elements": [
        {
            "type": "text",
            "label": "From",
            "name": "loc_origin"
        },
        {
            "type": "text",
            "label": "To",
            "name": "loc_destination"
        }
    ]
}';

$response = $client->request('POST', 'https://slack.com/api/dialog.open', [
	'form_params' => [
		'token' => $token,
		'trigger_id' => $actionTriggerId,
		'dialog' => $dialog,
	]
]);