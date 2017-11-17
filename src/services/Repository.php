<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 18/10/2017
 * Time: 12:33 PM
 */

namespace src\services;

use src\exceptions\RegistrationFailedException;
use src\helpers\ExceptionHandler;
use src\models\User;
use src\DI\Database;
use src\services\contracts\iRepository;

class Repository implements iRepository
{
	private $db;

	public function __construct()
	{
		$this->db = Database::getInstance()->getConnection();
	}

	public function checkIfTokenGenerated()
	{
		$checkUser = $this->db->prepare("SELECT * from credentials WHERE userid = :userid");
		$checkUser->bindParam(':userid', $_POST['user_id']);
		$checkUser->execute();

		if ($checkUser->rowCount() > 0) {
			throw new RegistrationFailedException("*INFO:* Token already exists for this user");
		}
	}

	public function insertToken(string $userid, string $email, string $authToken, array $zohoUserInfo)
	{
		$employeeZohoId = $zohoUserInfo['ownerID'];
		$superiorsMail = $zohoUserInfo['superiorIM'];

		$stmt = $this->db->prepare("INSERT INTO credentials (userid, email, token, zoho_user_id, reporting_to) VALUES (:userid, :email, :token, :zoho_user_id, :reporting_to)");
		$stmt->execute(array(
			"userid" => $userid,
			"email" => $email,
			"token" => $authToken,
			"zoho_user_id" => $employeeZohoId,
			"reporting_to" => $superiorsMail,
		));
	}

	public function getUserById(string $userId)
	{
		$stmt = $this->db->prepare("SELECT * from credentials WHERE userid = :userid LIMIT 1");
		$stmt->bindParam(':userid', $userId);
		$stmt->execute();
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);
		if ($result) {
			$newUser = new User();
			$newUser
				->setUserId($result['userid'])
				->setEmail($result['email'])
				->setToken($result['token'])
				->setZohoUserId($result['zoho_user_id'])
				->setLeaveType($result['leave_type'])
				->setReportingTo($result['reporting_to']);

			return $newUser;
		} else {
			throw new RegistrationFailedException("Hi there, looks like this is your first time running the Zoho APP. Please run this command */zoho register _username password_* so I can generate a token for you.");
		}
	}

	public function insertLeaveType(string $leaveType, string $userId)
	{
		$stmt = $this->db->prepare("UPDATE credentials SET leave_type = :leave_type WHERE userid = :id");
		$stmt->execute(array(
			"leave_type" => $leaveType,
			"id" => $userId,
		));
	}
}