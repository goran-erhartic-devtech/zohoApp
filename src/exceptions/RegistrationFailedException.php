<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/11/2017
 * Time: 9:12 AM
 */

namespace src\exceptions;

class RegistrationFailedException extends AbstractExceptionHandler
{
	public static function forInvalidCredentials(string $message)
	{
		self::guzzleMessage($message);
	}

	public static function forTokenAlreadyExists(string $message)
	{
		self::guzzleMessage($message);
	}

	public static function forInvalidInput(string $message)
	{
		self::guzzleMessage($message);
	}

}