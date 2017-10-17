<?php

//Include composer autoload.
require_once __DIR__ . '/../vendor/autoload.php';
//Load database configuration
require_once(__DIR__ . '/../database/conf.php');

//Instantiate DB PDO
$db = \Database\Database::getInstance()->getConnection();

//Instantiate Guzzle
$client = new \GuzzleHttp\Client([]);

//Read .env
$dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();
