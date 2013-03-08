<html>
<head>
	<title>Logboek D.M.C</title>
	<link href='css/main.css' rel='stylesheet'>
</head>
<body>

<h1>Logboek D.M.C</h1>
<div id='logoutdiv'><a href='logout.php'>Click here to log out</a></div>

<?php

session_start();

require_once(dirname(__file__) . '/lib/user.php');


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

echo '<h2>Add logbook entry:</h2>';
showEntryForm();
echo '<h2>Logbook entries:</h2>';
showLogboek();
echo '<h2>Total time per user: </h2>';
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
	if($_POST['desc'] == '')
	{
		echo "<div id='errdiv'>You didn't enter a description!</div>";
		return;
	}
	if(strlen($_POST['desc']) < 8) {
		echo "<div id='errdiv'>The description of the entry was too short, please make it longer than 8 characters!</div>";
		return;
	}
	$time = intval($_POST['time']);
	if($time == 0) {
		echo "<div id='errdiv'>The time you entered wasn't correct! please enter a valid number, like '18'!</div>";
		return;
	}
	$db->query("INSERT INTO entries (`date`, entry, time, uid) VALUES (NOW(), :post, :time, :uid)", array(':post' => $_POST['desc'], 
																										':time' => $time, 
																										':uid' => $user_info['id']));
	echo "<div id='notdiv'>Entry succesfully added</div>";
}
function deleteEntry()
{
	//we only want people to be capable of removing them if it is one of theirs or the user id is one (thats me)
	$eid = $_GET['e'];
	global $db, $user_info;
	$entry = $db->query_one("SELECT * FROM entries WHERE id=:eid", array(':eid' => $eid));
	if($entry == null) {
		echo "<div id='errdiv'>The entry you specified was not found!</div>";
		return;
	}
	if($entry['uid'] == 1 || $entry['uid'] == $user_info['id'])
	{
		$db->query("DELETE FROM entries WHERE id=:eid", array(':eid' => $eid));
		echo "<div id='notdiv'>Entry succesfully deleted! </div>";
	} else {
		echo "<div id='errdiv'>You can only delete your own entries!</div>";
	}
}
?>

</body>
</html>