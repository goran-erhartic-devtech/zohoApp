<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 18/10/2017
 * Time: 12:33 PM
 */

namespace src\models;

class User
{
	private $userId;
	private $email;
	private $token;
	private $zohoUserId;
	private $leaveType;
	private $reportingTo;

	/**
	 * @return mixed
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @param mixed $userId
	 * @return User
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $email
	 * @return User
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @param mixed $token
	 * @return User
	 */
	public function setToken($token)
	{
		$this->token = $token;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getZohoUserId()
	{
		return $this->zohoUserId;
	}

	/**
	 * @param mixed $zohoUserId
	 * @return User
	 */
	public function setZohoUserId($zohoUserId)
	{
		$this->zohoUserId = $zohoUserId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLeaveType()
	{
		return $this->leaveType;
	}

	/**
	 * @param mixed $leaveType
	 * @return User
	 */
	public function setLeaveType($leaveType)
	{
		$this->leaveType = $leaveType;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getReportingTo()
	{
		return $this->reportingTo;
	}

	/**
	 * @param mixed $reportingTo
	 * @return User
	 */
	public function setReportingTo($reportingTo)
	{
		$this->reportingTo = $reportingTo;

		return $this;
	}


}