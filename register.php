<?php
session_start();
require 'user.php';

if(isset($_POST['email'])) {
	if(create_user($_POST['name'], $_POST['email'], $_POST['password']))
	{
		authenticate($_POST['email'], $_POST['password']);
		header('location: logboek.php');
	}
	else {
		echo "You didn't fill in all the fields correctly. Please try again:<br/><br/>";
		showRegisterForm();
	}
} else showRegisterForm();

function showRegisterForm()
{
	?>
	<form method='POST'>
		<label for='name'> Naam: </label>
		<input type='text' name='name' id='name'><br/>
		<label for='email'> Email: </label>
		<input type='text' name='email' id='email'><br/>
		<label for='password'> Wachtwoord: </label>
		<input type='password' name='password' id='password'><br/>
		<input type='submit'>Register!</submit>
	</form>
	<?php
}

?>