<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 10:19 AM
 */

namespace src\services;

use GuzzleHttp\Client;
use src\actions\GenerateAuthToken;
use src\actions\GenerateLeaveTypeDropdown;
use src\actions\GenerateModalDialog;
use src\actions\RespondToLeaveType;
use src\actions\SendLeaveRequest;
use src\services\contracts\iSlackActions;

class SlackActions implements iSlackActions
{
	private $client;
	private $repo;

	public function __construct()
	{
		$this->client = new Client();
		$this->repo = new Repository();
	}

	public function generateAuthToken()
	{
		$generateAuthToken = new GenerateAuthToken();

		return $generateAuthToken->run($this->client, $this->repo);
	}

	public function generateLeaveTypeDropdown()
	{
		$generateLeaveTypeDropdown = new GenerateLeaveTypeDropdown();

		return $generateLeaveTypeDropdown->run($this->client, $this->repo);
	}

	public function respondToLeaveType($params)
	{
		$respondToLeaveType = new RespondToLeaveType();

		return $respondToLeaveType->run($params, $this->repo);
	}

	public function generateModalDialog($params)
	{
		$generateModalDialog = new GenerateModalDialog();

		return $generateModalDialog->run($this->client, $params);
	}

	public function sendLeaveRequest($params)
	{
		$sendLeaveRequest = new SendLeaveRequest();

		return $sendLeaveRequest->run($this->client, $params, $this->repo);
	}
}