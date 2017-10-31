<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 31/10/2017
 * Time: 9:15 AM
 */

namespace src\helpers;

use GuzzleHttp\Client;

class ExceptionHandler extends \Exception
{
	private $client;

	public function __construct($message, $code = 0, \Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->client = new Client();
	}

	public function execute()
	{
		$text = ['text' => $this->getMessage()];
		$this->client->request('POST', $_POST['response_url'], [
			'body' => json_encode($text),
			'headers' => [
				'Content-Type' => 'application/json',
			]
		]);
		die();
	}
}