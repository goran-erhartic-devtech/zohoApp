<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 11:14 AM
 */

namespace src\actions;

use GuzzleHttp\Client;
use src\helpers\Dialog;

class GenerateModalDialog
{
	private $generatedDialog;

	public function run(Client $client, \stdClass $params)
	{
		$token = $_ENV['TOKEN'];

		$actionTriggerId = $params->trigger_id;

		$dialog = $this->chooseDialogType($params, $client);

		$client->request('POST', 'https://slack.com/api/dialog.open', [
			'form_params' => [
				'token' => $token,
				'trigger_id' => $actionTriggerId,
				'dialog' => $dialog,
			]
		]);
	}

	private function chooseDialogType($params, $client)
	{
		//Generate modal for user with From, To dates and a Message
		if ($params->callback_id === "leave_selection") {
			$this->generatedDialog = Dialog::generateDialog();
			//Generate modal for DM's reply message
		} elseif ($params->callback_id === "leave_approval") {
			$requestId = $params->original_message->attachments[0]->footer;
			$yesNo = $params->actions[0]->value;
			$isApproved = $yesNo ? 1 : 0;
			$this->editButtonMessage($client, $params, $isApproved);
			$this->generatedDialog = Dialog::generateReasonDialog($requestId, $isApproved);
		}

		return $this->generatedDialog;
	}

	private function editButtonMessage(Client $client, $params, $isApproved)
	{
		$message = $isApproved ? ":white_check_mark: request approved " : ":x: request declined";
		$payload = $params->original_message;
		$payload->attachments[0]->actions = null;
		$payload->attachments[0]->text .= "\n{$message}";
		$body = json_encode($payload);
		$client->request('POST', $params->response_url, [
			'body' => $body,
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}
}