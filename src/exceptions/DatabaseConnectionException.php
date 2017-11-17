<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/11/2017
 * Time: 12:33 PM
 */

namespace src\exceptions;

class DatabaseConnectionException extends AbstractExceptionHandler
{
	public static function forCouldNotConnectToDB(string $message)
	{
		self::guzzleMessage($message);
	}
}