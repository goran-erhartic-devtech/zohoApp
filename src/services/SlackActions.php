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
use src\actions\HandleLeaveRequest;
use src\actions\RespondToLeaveType;
use src\actions\SendLeaveRequest;
use src\actions\UnknownAction;
use src\actions\WelcomeMessage;
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

	public function welcomeMessage()
	{
		$welcomeMessage = new WelcomeMessage();

		return $welcomeMessage->run($this->client);
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

	public function generateModalDialog($params)
	{
		if (!empty($params->actions[0]->selected_options[0]->value)) {
			RespondToLeaveType::insertLeaveType($params, $this->repo);
		}
		$generateModalDialog = new GenerateModalDialog();

		return $generateModalDialog->run($this->client, $params);
	}

	public function sendLeaveRequest($params)
	{
		$sendLeaveRequest = new SendLeaveRequest();

		return $sendLeaveRequest->run($this->client, $params, $this->repo);
	}

	public function handleLeaveRequest($params)
	{
		$handleLeaveRequest = new HandleLeaveRequest();

		return $handleLeaveRequest->run($this->client, $params, $this->repo);
	}

	public function unknownAction()
	{
		$unknownAction = new UnknownAction();

		return $unknownAction->run($this->client);
	}
}