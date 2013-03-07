<?php

session_start();

require 'user.php';

if(isset($_SESSION['user-id'])) {
	header('location: logboek.php');
	exit;
} else if (isset($_POST['email'])) {
	if(authenticate($_POST['email'], $_POST['password']) != null) {
		header('location: logboek.php');
		exit;
	} else {
		echo "You entered the wrong account details, please try again! <br/><br/>";
		showLoginForm();
	}
} else {
	showLoginForm();
}

function showLoginForm()
{
	?>
	<form method='POST'>
		<label for='email'> E-mail: </label>
		<input type='text' name='email' id='email'><br/>
		<label for='password'> password: </label>
		<input type='password' name='password' id='password'><br/>
		<button type='submit'>log in!</button>
	</form>
	<br/><br/>
	<a href='register.php'>or register here!</a>
	<?php
}
?>