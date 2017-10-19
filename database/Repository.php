<?php

namespace Database;

use models\User;

class Repository
{
	private $db;

	public function __construct(\PDO $db)
	{
		$this->db = $db;
	}

	public function checkIfTokenGenerated()
	{
		try {
			$checkUser = $this->db->prepare("SELECT * from credentials WHERE userid = :userid");
			$checkUser->bindParam(':userid', $_POST['user_id']);
			$checkUser->execute();
			if ($checkUser->rowCount() > 0) {
				throw new \PDOException("Token generated already");
			}
		} catch (\PDOException $e) {
			die($e->getMessage());
		}
	}

	public function insertToken(string $userid, string $email, string $authToken, string $zoho_user_id)
	{
		$stmt = $this->db->prepare("INSERT INTO credentials (userid, email, token, zoho_user_id) VALUES (:userid, :email, :token, :zoho_user_id)");
		$stmt->execute(array(
			"userid" => $userid,
			"email" => $email,
			"token" => $authToken,
			"zoho_user_id" => $zoho_user_id,
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
				->setLeaveType($result['leave_type']);

			return $newUser;
		} else {
			throw new \PDOException("Hi there, looks like this is your first time running the Zoho APP. Please run this command */zoho _username password_* so I can generate a token for you.");
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