<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 1:12 PM
 */

namespace src\actions;

use src\services\Repository;

class RespondToLeaveType
{
	public function run(\stdClass $params, Repository $repo)
	{
		$choosenLeaveType = $params->actions[0]->selected_options[0]->value;
		$userId = $params->user->id;

		$repo->insertLeaveType($choosenLeaveType, $userId);

		return true;
	}
}