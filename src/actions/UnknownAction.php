<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/11/2017
 * Time: 10:29 AM
 */

namespace src\actions;

use GuzzleHttp\Client;

class UnknownAction
{
	public function run(Client $client)
	{
		$client->request('POST', $_POST['response_url'], [
			'body' => '{"text": "*ERROR:* Unknown action - Please use one the following actions:\n
			*/zoho register email password* - _This action needs to be performed only once and you are good to go!_\n
			*/zoho leave* - _This action will allow you to apply for any type of leave that is currently available for you._\n"}',
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}
}