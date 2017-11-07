<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 26/10/2017
 * Time: 2:38 PM
 */

namespace src\helpers;

class TimeoutWorkaround
{
	public static function execute()
	{
		ob_start();
		http_response_code(200);
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