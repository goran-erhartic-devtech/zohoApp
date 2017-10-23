<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 10:26 AM
 */

namespace src\DI;

use Dotenv\Dotenv;
use src\services\SlackActions;

class Container
{
	private static $instance = null;

	public static function getInstance()
	{
		if (self::$instance === null) {
			//Get .env variables
			$dotenv = new Dotenv(__DIR__ . '/../../');
			$dotenv->load();

			self::$instance = new SlackActions();
		}

		return self::$instance;
	}

	private function __construct()
	{
	}

	/**
	 *Prevent duplication of connection
	 */
	private function __clone()
	{
	}
}