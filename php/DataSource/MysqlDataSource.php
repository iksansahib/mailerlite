<?php
namespace DataSource;

use mysqli;

class MysqlDataSource implements IDataSource {
	protected $mysqli = null;
	protected $table = "";
	public function __construct($table) {
		$mysqli = new mysqli("mysql-db","db_user","password","test_database", 3306);
		// Check connection
		if ($mysqli -> connect_errno) {
			throw new Exception($mysqli -> connect_errno);
		}
		$this->mysqli = $mysqli;
		$this->table = $table;
	}

	public function getBy($key, $value) {
		$result = $this->mysqli->query("SELECT * FROM " . $this->table . " WHERE " . $key . " = '" . $value . "'");
		return $result->fetch_assoc();
	}

	public function list() {
		$page = $_GET["page"] ?? 1;
		$size = $_GET["size"] ?? 10;
		$offset = $page === 1 ? 0 : ($page - 1) * $size;
		$result = $this->mysqli->query("SELECT * FROM " . $this->table . " limit " . $offset . ", " . $size);
		$total = $this->mysqli->query("SELECT COUNT(*) FROM " . $this->table)->fetch_row()[0];
		return [
			"data" => $result->fetch_all(MYSQLI_ASSOC),
			"total" => (int)$total,
			"size" => (int) $size,
			"page" => (int) $page
		];
	}
	public function save(array $data, $key = "") {
		$id = $data["id"];
		$name = $data["name"];
		$last_name = $data["last_name"];
		$email = $data["email"];
		$status = $data["status"];
		$statement = $this->mysqli->prepare("INSERT INTO " . $this->table . "
			(id, name, last_name, status, email)
			VALUES (?, ?, ?, ?, ?)
		");
		$statement->bind_param('sssss', $id, $name, $last_name, $status, $email);
		$statement->execute();
	}

	public function escape_string($string) {
		return $this->mysqli->real_escape_string($string);
	}
}
