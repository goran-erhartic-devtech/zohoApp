<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/11/2017
 * Time: 8:52 AM
 */

namespace src\actions;

use GuzzleHttp\Client;

class WelcomeMessage
{
	public function run(Client $client)
	{
		$client->request('POST', $_POST['response_url'], [
			'body' => '{"text": "*INFO:* Hi, welcome to *Zoho People App*! Please use one the following actions:\n
			*/zoho register email password* - _This action needs to be performed only once and you are good to go!_\n
			*/zoho leave* - _This action will allow you to apply for any type of leave that is currently available for you._\n
			"}',
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}
}