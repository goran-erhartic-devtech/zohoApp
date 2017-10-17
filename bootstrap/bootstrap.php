<?php

//Include composer autoload.
require_once __DIR__ . '/../vendor/autoload.php';
//Load database configuration
require_once(__DIR__ . '/../database/conf.php');

$db = \Database\Database::getInstance()->getConnection();

$client = new \GuzzleHttp\Client([]);

