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
	public function run(Client $client, \stdClass $params)
	{
		$token = $_ENV['TOKEN'];

		//Timeout workaround
		ob_start();
		$client->requestAsync('POST', $params->response_url, [
			'body' => '{"text": "Please fill out the required fields"}',
			'headers' => [
				'Content-Type' => 'application/json',
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

		$actionTriggerId = $params->trigger_id;

		$dialog = Dialog::generateDialog();

		$client->request('POST', 'https://slack.com/api/dialog.open', [
			'form_params' => [
				'token' => $token,
				'trigger_id' => $actionTriggerId,
				'dialog' => $dialog,
			]
		]);
	}
}