<?php

session_start();

require 'user.php';


if(!isset($_SESSION['user-id'])) {
	header('location: index.php');
	exit;
}

$user_info = current_user();

if(isset($_POST['entry'])) {
	//insert new entry into database
	insertEntry();
}

showEntryForm();
showLogboek();
showUserTotals();

function showEntryForm()
{
	?>
	<form method='POST'>
		<table>
			<tr>
				<th style='width:400px'>Description:</th>
				<th style='width:30px'>Time in minutes:</th>
				<th></th>
			</tr>
			<tr>
				<td><input type='text' name='desc' id='desc' style='width:390px'></input></td>
				<td><input type='text' name='time' id='time' style='width:40px'></td>
				<td><input type='submit'></input></td>
			</tr>
		</table>
	</form>
	<?php
}

function showLogboek() 
{

}

function showUserTotals()
{
	$result = $db->query("SELECT * FROM users", array());

	echo "<table> <tr> <th> Users </th><th>Total time:</th></tr>";
	foreach( $result as $u)
	{
		$entries = $db->query("SELECT * entries WHERE uid=:uid", array(":uid" => $u['id']));
		$usertotal = 0;
		foreach($entries as $e) {
			$usertotal += $e['time'];
		}
		echo "<tr><td>".$u['name']."</td><td>".$usertotal."</td></tr>";
	}
	echo "</table>";
}

function insertEntry()
{

}
?>