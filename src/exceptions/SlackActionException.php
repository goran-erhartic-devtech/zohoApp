<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/11/2017
 * Time: 1:06 PM
 */

namespace src\exceptions;

class SlackActionException extends AbstractExceptionHandler
{
	public static function forMissingUser(string $message)
	{
		self::guzzleMessage($message);
	}

	public static function forInvalidRegistrationCredentials(string $message)
	{
		self::guzzleMessage($message);
	}
}