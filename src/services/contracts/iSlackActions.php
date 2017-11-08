<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 2:08 PM
 */

namespace src\services\contracts;

interface iSlackActions
{
	/**
	 * @return mixed
	 */
	public function generateAuthToken();

	/**
	 * @return mixed
	 */
	public function generateLeaveTypeDropdown();

	/**
	 * @param $params
	 * @return mixed
	 */
	public function respondToLeaveType(\stdClass $params);

	/**
	 * @param $params
	 * @return mixed
	 */
	public function generateModalDialog(\stdClass $params);

	/**
	 * @param $params
	 * @return mixed
	 */
	public function sendLeaveRequest(\stdClass $params);

	/**
	 * @param $mail
	 * @return mixed
	 */
	public function getSuperiorsIM(string $mail);

	/**
	 * @param \stdClass $params
	 * @return mixed
	 */
	public function handleLeaveRequest(\stdClass $params);

}