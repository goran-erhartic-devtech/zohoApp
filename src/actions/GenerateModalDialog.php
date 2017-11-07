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
use src\helpers\TimeoutWorkaround;

class GenerateModalDialog
{
	private $generatedDialog;

	public function run(Client $client, \stdClass $params)
	{
		$token = $_ENV['TOKEN'];

		//Timeout workaround
		$this->timeoutWorkaround($client, $params);

		$actionTriggerId = $params->trigger_id;

		$dialog = $this->chooseDialogType($params);

		$client->request('POST', 'https://slack.com/api/dialog.open', [
			'form_params' => [
				'token' => $token,
				'trigger_id' => $actionTriggerId,
				'dialog' => $dialog,
			]
		]);
	}

	private function chooseDialogType($params)
	{
		if ($params->callback_id === "leave_selection"){
			$this->generatedDialog = Dialog::generateDialog();
		} elseif ($params->callback_id === "leave_approval"){
			$requestId = $params->original_message->attachments[0]->footer;
			$yesNo = $params->actions[0]->value;
			$isApproved = $yesNo ? 1 : 0;
			$this->generatedDialog = Dialog::generateReasonDialog($requestId, $isApproved);
		}
		return $this->generatedDialog;
	}

	/**
	 * @param Client $client
	 * @param \stdClass $params
	 */
	private function timeoutWorkaround(Client $client, \stdClass $params)
	{
		ob_start();
		$client->requestAsync('POST', 'https://slack.com/api/chat.postEphemeral', [
			'form_params' => [
				'token' => $_ENV['TOKEN'],
				'channel' => $params->channel->id,
				'text' => "Please fill out the fields",
				'user' => $params->user->id,
				'as_user' => false
			],
			'headers' => [
				'Content-Type' => 'application/x-www-form-urlencoded',
			]
		])->wait();
		$size = ob_get_length();
		header("Content-Length: $size");
		header('Connection: close');

		// flush all output
		ob_end_flush();
		ob_flush();
		flush();
		session_write_close();
		//End timeout workaround
	}
}