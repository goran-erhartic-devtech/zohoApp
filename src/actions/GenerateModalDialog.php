<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 11:14 AM
 */

namespace src\actions;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use src\helpers\Dialog;
use src\services\contracts\iHttpRequests;

class GenerateModalDialog
{
	private $generatedDialog;

	public function run(iHttpRequests $client, $params)
	{
		$token = $_ENV['TOKEN'];
		$actionTriggerId = $params->trigger_id;
		$dialog = $this->chooseDialogType($params, $client);

		$client->generateModalDialog($token, $actionTriggerId, $dialog);
	}

	/**
	 * Dynamically choose which modal should be created
	 * @param $params
	 * @param $client
	 * @return string
	 */
	private function chooseDialogType($params, $client):string
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

	/**
	 * Edit DM's approve/decline message after clicking on button
	 * @param iHttpRequests $client
	 * @param $params
	 * @param $isApproved
	 * @return Response
	 */
	private function editButtonMessage(iHttpRequests $client, $params, $isApproved):Response
	{
		$message = $isApproved ? ":white_check_mark: request approved " : ":x: request declined";
		$payload = $params->original_message;
		$payload->attachments[0]->actions = null;
		$payload->attachments[0]->text .= "\n{$message}";
		$body = json_encode($payload);

		return $client->editButtonMessage($params, $body);
	}
}