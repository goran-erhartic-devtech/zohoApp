<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 23/10/2017
 * Time: 1:12 PM
 */

namespace src\helpers;

use src\services\contracts\iRepository;
use src\services\Repository;

class RespondToLeaveType
{
	public static function insertLeaveType($params, iRepository $repo)
	{
		$choosenLeaveType = $params->actions[0]->selected_options[0]->value;
		$userId = $params->user->id;

		return $repo->insertLeaveType($choosenLeaveType, $userId);
	}
}