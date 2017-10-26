<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 26/10/2017
 * Time: 2:38 PM
 */

namespace src\helpers;

use GuzzleHttp\Client;

class TimeoutWorkaround
{
	private static $message;

	public static function execute(Client $client, string $url, string $text)
	{
		self::$message = ["text" => $text];

		ob_start();
		$client->requestAsync('POST', $url, [
			'body' => json_encode(self::$message),
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
	}
}