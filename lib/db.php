<?php

class Database {
	private $conn;

	public function __construct($connection_str, $username, $password) {
		$this->conn = new PDO($connection_str, $username, $password);
		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function query($query, $bindings) {
		$stmt = $this->conn->prepare($query);
		foreach ($bindings as $k => $v) {
			$stmt->bindValue($k, $v);
		}
		$stmt->execute();
		try {
			return $stmt->fetchAll();
		} catch (PDOException $e) {
			return null;
		}
	}

	public function query_one($query, $bindings) {
		$results = $this->query($query, $bindings);
		if ($results === null || count($results) === 0) return null;
		return $results[0];
	}
};

$db = new Database('mysql:dbname=logboek;host=127.0.0.1', 'root', '');
?>