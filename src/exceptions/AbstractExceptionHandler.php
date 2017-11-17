<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/11/2017
 * Time: 1:08 PM
 */

namespace src\exceptions;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

abstract class AbstractExceptionHandler extends \RuntimeException
{
	protected function guzzleMessage(string $message):Response
	{
		$client = new Client();
		$text = json_encode(['text' => $message]);

		return $client->request('POST', $_POST['response_url'], [
			'body' => $text,
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
	}
}