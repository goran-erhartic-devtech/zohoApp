<?php

namespace Database;

class Repository
{
    public static function checkIfTokenGenerated(\PDO $db){
        try {
            $checkUser = $db->prepare("SELECT * from credentials WHERE userid = :userid");
            $checkUser->bindParam(':userid', $_POST['user_id']);
            $checkUser->execute();
            if ($checkUser->rowCount() > 0) {
                throw new \PDOException("Token generated already");
            }
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function insertToken(\PDO $db, $userid, $authToken){
        $stmt = $db->prepare("INSERT INTO credentials (userid, token) VALUES (:userid, :token)");
        $stmt->execute(array(
            "userid" => $userid,
            "token" => $authToken,
        ));
    }
}