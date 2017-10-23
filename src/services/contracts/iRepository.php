<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 2:05 PM
 */

namespace src\services\contracts;

interface iRepository
{
	/**
	 * @return mixed
	 */
	public function checkIfTokenGenerated();

	/**
	 * @param string $userid
	 * @param string $email
	 * @param string $authToken
	 * @param array $zohoUserInfo
	 * @return mixed
	 */
	public function insertToken(string $userid, string $email, string $authToken, array $zohoUserInfo);

	/**
	 * @param string $userId
	 * @return mixed
	 */
	public function getUserById(string $userId);

	/**
	 * @param string $leaveType
	 * @param string $userId
	 * @return mixed
	 */
	public function insertLeaveType(string $leaveType, string $userId);
}