<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 10:29 AM
 */

namespace src\DI;

class Database
{
	private $db;
	private static $_instance = null;

	/**
	 * Get an instance of the database
	 * @return Database
	 */
	public static function getInstance()
	{
		if (!self::$_instance) { // If no instance then make one
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Get database connection
	 */
	public function getConnection()
	{
		return $this->db;
	}

	/**
	 * database constructor.
	 */
	private function __construct()
	{
		try {
			$this->db = new \PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
			$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			echo $e->getMessage();
		}
	}

	/**
	 *Prevent duplication of connection
	 */
	private function __clone()
	{
	}
}