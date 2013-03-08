<html>
<head>
	<title>Logboek</title>
	<style>
	table, th, td {
		border : 1px solid black;
		text-align: left;
	}
	</style>
</head>
<body>

<h1>Logboek</h1>
<a href='logout.php'>Click here to log out</a><br/>

<?php

session_start();

require 'user.php';


if(!isset($_SESSION['user-id'])) {
	header('location: index.php');
	exit;
}
if(isset($_GET['e']))
{
	deleteEntry();
}

$user_info = current_user();

if(isset($_POST['desc'])) {
	//insert new entry into database
	insertEntry();
}

echo '<br/>Add logbook entry:<br/>';
showEntryForm();
echo '<br/>Logbook entries:<br/>';
showLogboek();
echo '<br/>Total time per user: <br/>';
showUserTotals();

function showEntryForm()
{
	?>
	<form method='POST' action="logboek.php">
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
	global $db, $user_info;
	echo "<table><tr><th width='150px'>Wie:</th><th width='750px'>Wat:</th><th>tijd(in min)</th><th>Delete:</th>";
	$entries = $db->query("SELECT * FROM entries LIMIT 100", array());
	foreach($entries as $e) {
		echo "<tr><td>".$user_info['name']."</td><td>".$e['entry']."</td><td>".$e['time']."</td><td><a href='logboek.php?e=".$e['id']."'>delete</a></td></tr>";
	}
	echo "</table>";
}

function showUserTotals()
{
	global $db;
	$result = $db->query("SELECT * FROM users", array());
	if(count($result) == 0 || $result == null) 
		{

			return;
		}
	echo "<table> <tr> <th> Users </th><th>Total time:</th></tr>";
	foreach( $result as $u)
	{
		$entries = $db->query("SELECT * FROM entries WHERE uid=:id", array(":id" => $u['id']));
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
	global $db;
	global $user_info;
	$time = intval($_POST['time']);
	$db->query("INSERT INTO entries (`date`, entry, time, uid) VALUES (NOW(), :post, :time, :uid)", array(':post' => $_POST['desc'], 
																										':time' => $time, 
																										':uid' => $user_info['id']));
	echo "<b>Entry succesfully added<br/>";
}
function deleteEntry()
{
	//we only want people to be capable of removing them if it is one of theirs or the user id is one (thats me)
	$eid = $_GET['e'];
	global $db, $user_info;
	$entry = $db->query_one("SELECT * FROM entries WHERE id=:eid", array(':eid' => $eid));

	if($entry['uid'] == 1 || $entry['uid'] == $user_info['id'])
	{
		$db->query("DELETE FROM entries WHERE id=:eid", array(':eid' => $eid));
		echo "Entry succesfully deleted! <br/>";
	} else {
		echo "You can only delete your own entries!";
	}
}
?>

</body>
</html>