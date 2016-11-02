<!DOCTYPE html>
<html>
<title>Sign Up</title>

<?php

include "include.php";

//check if they are logged in, if not prompt them to log in
if(!isset($_SESSION["pid"])) {
  echo "You are not logged in. ";
  echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php\">here</a>.\n";
  header("refresh: 3; index.php");
}
/*
//if the eid entered is private, then compare it's sponsoring club with all the clubs the user is in. 
//if it doesn't match up with with any clubs, then echo out that not allowed to sign in
if ($stmt = $mysqli->prepare ("select sponsored_by from event where eid=? and is_public_e=0")){
	$stmt->bind_param("i", $_POST["eid"]);
    $stmt->execute();
    $stmt->bind_result($sponsored_by);
	$stmt->store_result();
    if ($stmt->fetch()) {
		if ($stmt = $mysqli->prepare ("select clubid from member_of where pid=?")){
			$stmt->bind_param("i", $_SESSION["pid"]);
			$stmt->execute();
			$stmt->bind_result($clubid);
			$stmt->store_result();
			while($stmt->fetch()){
				if ($clubid == $sponsored_by) {}
				else{
					echo "<b>You are not a member of the club which is hosting the event and therefore cannot sign up for this event. Please try again.</b>";
					break;
				}
			}
		}
	}
}
*/
//insert the sign_in information to database
if(isset($_POST["pid"]) && isset($_POST["eid"])) {
	//check if pid and eid already exists in database
    if ($stmt = $mysqli->prepare("select pid,eid from sign_up where pid = ? and eid=?")) {
      $stmt->bind_param("ii", $_POST["pid"], $_POST["eid"]);
      $stmt->execute();
      $stmt->bind_result($pid, $eid);
        if ($stmt->fetch()) {
			echo "<b>You have already signed up for this event. Please try again.</b> ";
			$stmt->close();
        }
	else{ //insert into database
		if ($_POST["pid"] == $_SESSION["pid"]){
			if ($stmt = $mysqli->prepare("insert into sign_up (pid, eid) values (?,?)")) {
				$stmt->bind_param("ii", $_POST["pid"], $_POST["eid"]);
				$stmt->execute();
				$stmt->close();
				$pid = htmlspecialchars($_SESSION["pid"]);
				//insert into database, note that message_id is auto_increment and time is set to current_timestamp by default
				echo "<b>Your information has been recorded. </b>\n";
				echo "You will be returned to your homepage in 3 seconds or click <a href=\"index.php?pid=$pid\">here</a>.";
				echo "<br /><br />\n";
				header("refresh: 3; index.php?pid=$pid");
			}
		}
		else{
			echo " <b>The Pid you are requesting to sign up with is not your own pid. Please try again.</b>";
		}
	}
	}
}

//display events they have already signed up for
if ($stmt = $mysqli->prepare ("select eid, ename, edatetime, location, sponsored_by from sign_up natural join event where pid=? and date(`edatetime`) between CURDATE() and DATE_ADD(CURDATE(),INTERVAL 3 DAY)")){
	$stmt->bind_param("i", $_SESSION["pid"]);
	$stmt->execute();
	$stmt->bind_result($eid, $ename, $edatetime, $location, $sponsored_by);
	$stmt->store_result();
		echo "<br /><br />\n";
		echo "List of events in the next coming three days you have already signed up for (the list may be empty if you have not signed up for any events in the next coming three days): ";
		echo "<table border = '1'>\n";
		while ($stmt->fetch()) {
			echo "<tr>";
            echo "<td>$eid</td><td>$ename</td><td>$edatetime</td><td>$location</td><td>$sponsored_by</td>";
	        echo "</tr>\n";    
		}
		echo "</table>\n";
}


//if they are not in any clubs
if ($stmt = $mysqli->prepare ("select eid, ename, edatetime, location, sponsored_by from event where is_public_e=1 and date(`edatetime`) between CURDATE() and DATE_ADD(CURDATE(),INTERVAL 3 DAY)")) {
	$stmt->execute();
	$stmt->bind_result($eid, $ename, $edatetime, $location, $sponsored_by);	
		echo "<br /><br />\n";
		echo "List of public events in the next coming three days you are eligible to sign up for (the list may be empty if there are no public events in the next coming three days): ";
		echo "<table border = '1'>\n";
		while ($stmt->fetch()) {
			echo "<tr>";
            echo "<td>$eid</td><td>$ename</td><td>$edatetime</td><td>$location</td><td>$sponsored_by</td>";
	        echo "</tr>\n";    
		}
		echo "</table>\n";
}
	
//from their pid get their clubid from member_of table
if ($stmt = $mysqli->prepare("select clubid from member_of where pid=?")) {
	$stmt->bind_param("i", $_SESSION["pid"]);
	$stmt->execute();
	$stmt->bind_result($clubid);
	$stmt->store_result();
	while($stmt->fetch()) {
		//display all the event they are eligible for  
		if ($stmt1 = $mysqli->prepare("select eid, ename, edatetime, location, sponsored_by from event where event.sponsored_by = ? and is_public_e=0 and date(`edatetime`) between CURDATE() and DATE_ADD(CURDATE(),INTERVAL 3 DAY)")) {
			$stmt1->bind_param("i", $clubid);
			$stmt1->execute();
			$stmt1->bind_result($eid, $ename, $edatetime, $location, $sponsored_by);	
			echo "<br /><br />\n";
			echo "List of private events in the next coming three days from club: $clubid you are eligible to sign up for (the list may be empty if your club is not hosting any private events in the next coming three days): ";
			echo "<table border = '1'>\n";
			while ($stmt1->fetch()) {
				echo "<tr>";
				echo "<td>$eid</td><td>$ename</td><td>$edatetime</td><td>$location</td><td>$sponsored_by</td>";
				echo "</tr>\n";    
		}
		echo "</table>\n";						
		}
	}
}	
	
	echo "<br /><br />\n";
		//display the form for signing up
		echo "Enter your pid and the eid for the event you would like to sign up for below:<br /><br />\n";
		echo '<form action="signUp.php" method="POST">';
		echo 'Pid: <input type="text" name="pid" /><br />';
		echo "\n";
		echo 'Eid: <input type="int" name="eid" /><br />';
		echo "\n";
		echo '<input type="submit" value="Submit" />';
		echo "\n";
		echo '</form>';
		echo "\n";
		echo '<br /><a href="index.php">Go back</a>'; 

$mysqli->close();


?>
</html>