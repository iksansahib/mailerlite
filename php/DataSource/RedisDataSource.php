<?php
namespace DataSource;
use Redis;
class RedisDataSource implements IDataSource {
	protected $redis;
	public function __construct() {
		$redis = new Redis();
		//Connecting to Redis
		$redis->connect('cache', 6379);
		$redis->auth('eYVX7EwVmmxKPCDmwMtyKVge8oLd2t81');
		$this->redis = $redis;
	}

	public function getBy($key, $value) {
		return $this->redis->hGetAll($key . ":" . $value);
	}
	public function save(array $data, string $key = "") {
		return $this->redis->hMSet($key, $data);
	}

	public function list() {
		return;
	}
}
