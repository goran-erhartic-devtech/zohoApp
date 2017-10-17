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

	public function insertToken(string $userid, string $email, string $authToken)
	{
		$stmt = $this->db->prepare("INSERT INTO credentials (userid, email, token) VALUES (:userid, :email, :token)");
		$stmt->execute(array(
			"userid" => $userid,
			"email" => $email,
			"token" => $authToken,
		));
	}

	public function getUser()
	{
		$stmt = $this->db->prepare("SELECT * from credentials WHERE userid = :userid LIMIT 1");
		$stmt->bindParam(':userid', $_POST['user_id']);
		$stmt->execute();
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);
		if ($result) {
			$newUser = new User();
			$newUser->setUserId($result['userid'])->setEmail($result['email'])->setToken($result['token']);
			return $newUser;
		} else {
			throw new \PDOException("Hi there, looks like this is your first time running the Zoho APP. Please run this command */zoho _username password_* so I can generate a token for you.");
		}
	}
}