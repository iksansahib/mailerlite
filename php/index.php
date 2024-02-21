<?php
require_once "autoload.php";
require_once "vendor/autoload.php";
use DataSource\MysqlDataSource;
use DataSource\RedisDataSource;
use Ramsey\Uuid\Uuid;

$router = new Router();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, origin");
header('Content-Type: application/json; charset=utf-8');
$router->addRoute('GET', '/index.php/subscribers/list', function () {
	$subscribers = new Subscribers(new MysqlDataSource("subscribers"));
	$data = $subscribers->list();
	echo json_encode($data);
	exit;
});

$router->addRoute('GET', '/index.php/subscribers', function () {
	try {
		$redis = new RedisDataSource();
	} catch (Exception $e) {
		$redis = null;
	}

	// get from redis first
	if ($redis) {
		$subscribers = new Subscribers($redis);
		$data = $subscribers->get();
	}

	// if empty, load the data from mysql to redis
	if (!$data) {
		$subscribers = new Subscribers(new MysqlDataSource("subscribers"));
		$data = $subscribers->get();
		$redisSubscribers = new Subscribers($redis);
		if($data) $redisSubscribers->save($data, "email:" . $data["email"]);
	}

	echo json_encode($data);
	exit;
});
$router->addRoute('POST', '/index.php/subscribers', function () {
	$postString = file_get_contents('php://input');
	$post = json_decode($postString);
	$name = addslashes($post->name);
	$last_name = addslashes($post->last_name);
	$email = addslashes($post->email);
	$status = addslashes($post->status);
	$uuid = Uuid::uuid4();
	$data = [
		"name" => $name,
		"last_name" => $last_name,
		"email" => $email,
		"status" => $status,
		"id" => $uuid
	];

	// store to mysql
	try {
		$subscribers = new Subscribers(new MysqlDataSource("subscribers"));
		$subscribers->save($data);
	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode(["message" => $e->getMessage()]);
	}

	// store to redis, log if failed, no need to throw error response
	try {
		$redis = new RedisDataSource();
		$subscribersToRedis = new Subscribers($redis);
		$subscribersToRedis->save($data, "email:" . $data["email"]);
	} catch (Exception $e) {
		error_log($e->getMessage());
	}
	http_response_code(204);
	exit;
});

$router->matchRoute();
