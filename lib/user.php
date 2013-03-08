<?php

if (session_id() == '') session_start();

require_once(dirname(__file__) . '/db.php');
require_once(dirname(__file__) . '/bcrypt.php');
$bcrypt = new Bcrypt(12);
function create_user($name, $email, $password) {
	global $db;
	global $bcrypt;

	if (!$name) return false;
	if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) return false;
	if (strlen($password) < 4) return false;

	$password_hash = $bcrypt->hash($password);

	$db->query(
		'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)',
		array(
			':name' => $name,
			':email' => $email,
			':password' => $password_hash
		)
	);

	return true;
}

function authenticate($email, $password) {
	global $db;
	global $bcrypt;

	if (!$email || !$password) return null;

	$user = $db->query_one(
		'SELECT * FROM Users WHERE email=:email LIMIT 1',
		array(':email' => $email)
	);
	if ($user === null) return null;

	if ($bcrypt->verify($password, $user['password'])) 
	{
		$_SESSION['user-id'] = $user['id'];
		return $user;
	}
	return null;
}

function current_user() {
	global $db;

	if (isset($_SESSION['user-id'])) {
		$id = $_SESSION['user-id'];
		return $db->query_one('SELECT * FROM Users WHERE id=:id', array(':id' => $id));
	}
	return null;
}
?>