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