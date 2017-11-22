<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 17/11/2017
 * Time: 8:52 AM
 */

namespace src\actions;

use src\services\contracts\iHttpRequests;

class WelcomeMessage
{
	public function run(iHttpRequests $client)
	{
		return $client->welcomeMessage();
	}
}