<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/11/2017
 * Time: 10:29 AM
 */

namespace src\actions;

use src\services\contracts\iHttpRequests;

class UnknownAction
{
	public function run(iHttpRequests $client)
	{
		$client->unknownAction();
	}
}