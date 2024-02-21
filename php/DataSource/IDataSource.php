<?php
namespace DataSource;

interface IDataSource {
	public function getBy($key, $value);
	public function list();
	public function save(array $data, string $key = "");
}
