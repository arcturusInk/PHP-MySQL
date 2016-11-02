<!DOCTYPE html>
<html>
	<header>
		<title>Post New Events</title>
	</header>
	<?php
		include "include.php";

		//if the user is not logged in, redirect them back to homepage
		if(!isset($_SESSION["pid"])) {
		  echo "You are not logged in. ";
		  echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php\">here</a>.\n";
		  header("refresh: 3; index.php");
		}
		if(isset($_POST["ename"]) && isset($_POST["description"]) && isset($_POST["edatetime"]) && isset($_POST["location"]) && isset($_POST["is_public_e"]) && isset($_POST["sponsored_by"])) {
			//if the sponsoring clubid is not the same as the clubid, the person is in or not an existing clubid
			if ($stmt = $mysqli->prepare("select clubid from member_of where pid=?")) {
				$stmt->bind_param("i", $_SESSION["pid"]);
				$stmt->execute();
				$stmt->bind_result($clubid);
				$stmt->store_result();
				if ($stmt->fetch()){
					if($_POST["sponsored_by"] != $clubid) {
						//display a message and do not insert into the database
						echo "<b>Your information has not been posted. You are either not a member/admin of the club or the clubid you have entered is not an existing clubid. Please try again.</b>\n";
						echo "<br /><br />\n";
					}
				}
			else{
				//insert into the database
				if ($stmt = $mysqli->prepare("insert into event (ename, description, edatetime, location, is_public_e, sponsored_by) values (?,?,?,?,?,?)")) {
					$stmt->bind_param("ssssii", $_POST["ename"], $_POST["description"], $_POST["edatetime"], $_POST["location"], $_POST["is_public_e"], $_POST["sponsored_by"]);
					$stmt->execute();
					$stmt->close();
					$pid = htmlspecialchars($_SESSION["pid"]);
					echo "<b>Your information has been posted.</b> \n";
					echo "You will be returned to your homepage in 3 seconds or click <a href=\"index.php?pid=$pid\">here</a>.";
					echo "<br /><br />\n";
					header("refresh: 3; index.php?pid=$pid");
				}
			}
			}
		}			
		// check if the user is admin
		if ($stmt = $mysqli->prepare("select pid from role_in natural join member_of where pid = ? and role='Admin'")) {
			$stmt->bind_param("s", $_SESSION["pid"]);
			$stmt->execute();
			$stmt->bind_result($pid);		  
			if($stmt->fetch()) {				
				//then display the form for posting information  
				echo "Enter the event's name, date, time, description, location, sponsoring club and whether the event is public or not below: <br /><br />\n";
				echo '<form action="post.php" method="POST">';
				echo "\n";
				echo 'Event\'s name: <input type="text" name="ename" /><br />';
				echo "\n";
				echo 'Event\'s description: <input type="text" name="description" /><br />';
				echo "\n";
				echo 'Event\'s date (YYYY-MM-DD): <input type="datetime" name="edatetime" /><br />';
				echo "\n";
				echo 'Event\'s location: <input type="text" name="location" /><br />';
				echo "\n";
				echo 'Is event public or private (Enter 0 for private or any other number for public):  <input type="tinyint" name="is_public_e" /><br />';	
				echo "\n";
				echo 'Event is sponsored by (Enter existing clubid): <input type="int" name="sponsored_by" /><br />';
				echo "\n";
				echo '<input type="submit" value="Submit" />';
				echo "\n";
				echo '</form>';
				echo "\n";
				echo '<br /><a href="index.php">Go back</a>';  
			}	
		}else{
			echo "You are not an admin of a club so cannot insert information here.";
			echo "\n";
			echo '<br /><a href="index.php">Go back</a>';
		}
	?>
</html>