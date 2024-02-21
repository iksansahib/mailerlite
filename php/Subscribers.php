<?php
use DataSource\MysqlDataSource;
use DataSource\IDataSource;

class Subscribers {
	private $subscriber;
	public function __construct(private IDataSource $datasource) {
	}
	public function get() {
		try {
			$email = $_GET["email"];
			$query = $this->datasource->getBy("email",$email);
			return $query;
		} catch (Exception $e) {
			http_response_code(400);
			return json_encode(["message" => $e->getMessage()]);
		}
	}

	public function list() {
		try {
			$query = $this->datasource->list();
			return $query;
		} catch (Exception $e) {
			http_response_code(400);
			return json_encode(["message" => $e->getMessage()]);
		}
	}

	public function save($data, $key = "") {
		$this->datasource->save($data, $key);
	}
}
