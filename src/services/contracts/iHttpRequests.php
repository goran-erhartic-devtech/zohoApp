<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 22/11/2017
 * Time: 10:07 AM
 */

namespace src\services\contracts;

use src\models\User;

interface iHttpRequests
{
	public function generateZohoTokenForApiRequests(string $username, string $password);

	public function getEmployeeZohoInfoArray(string $url);

	public function sendSuccessRegistrationMessage();

	public function sendPayloadPrivetlyToUser(string $payload);

	public function getAllLeaveTypes(string $url); //!!

	public function generateModalDialog(string $token, string $actionTriggerId, string $dialog);

	public function editButtonMessage($params, string $body);

	public function messageDMifActionWasSuccessful($params, string $respText);

	public function requestZohoApiToApproveOrRejectLeaveRequest(string $dmToken, string $leaveRequestId, string $isApproved, string $remark);

	public function sendLeaveRequest(User $employee, string $xmlPayload);

	public function responseMessageToSlackUser(string $dialogResponseChannel, string $dialogResponseText, string $dialogResponseUser);

	public function sendPMToDM(User $employee, $params, string $text);

	public function unknownAction();

	public function welcomeMessage();

	public function getAllUsersArray();

	public function getAllIMchannels();
}