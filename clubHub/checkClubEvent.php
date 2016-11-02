<!DOCTYPE html>
<html>
	<header>
		<title>Check Club Events</title>
	</header>

	<?php
		include "include.php";

		if(!isset($_SESSION["pid"])) {
			echo "You are not logged in. ";
			echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php\">here</a>.\n";
			header("refresh: 3; index.php");
		}
		//check if the user has an admin role
		if ($stmt = $mysqli->prepare("select clubid from role_in where pid = ? and role='Admin'")) {
			$stmt->bind_param("i", $_SESSION["pid"]);
			$stmt->execute();
			$stmt->bind_result($clubid);
			$stmt->store_result();
			while ($stmt->fetch()) { 
				//if user is an admin then display upcoming events and number of people who have signed up for each
				if ($stmt1 = $mysqli->prepare("SELECT eid, ename, sponsored_by, count(pid) as num_of_people_signed_up FROM event natural left outer join sign_up where sponsored_by=$clubid group by eid, ename, sponsored_by")) {
					$stmt1->execute();
					$stmt1->bind_result($eid, $ename, $sponsored_by, $num_of_people_signed_up);
					// Printing results in HTML
					while ($stmt1->fetch()){
						echo "<table border = '1'>\n";
						echo "<tr>";
						echo "<td>$eid</td><td>$ename</td><td>$sponsored_by</td><td>$num_of_people_signed_up</td>";
						echo "</tr>\n";
					}
					echo "</table>\n";		
				}
			}
		}
			
		//checks if you are part of a club but do not have an admin role
		if ($stmt = $mysqli->prepare("select distinct pid from member_of where pid not in (select pid from role_in where role='Admin')")) {
			$stmt->execute();
			$stmt->bind_result($pid);
			$stmt->store_result();
			while ($stmt->fetch()){
				if ($_SESSION["pid"] == $pid) {
				echo "<br /><br />\n";
				echo "You are not an admin of any clubs and therefore cannot access this page.";
				//echo '<br /><a href="index.php">Go back</a>'; 
				}
			}
		}
		//check for people in person table 
		if ($stmt = $mysqli->prepare("select pid from person where pid not in (select pid from role_in) ")) {
			$stmt->execute();
			$stmt->bind_result($pid);
			$stmt->store_result();
			while ($stmt->fetch()){
				if ($_SESSION["pid"] == $pid){
				echo "<br /><br />\n";
				echo "You are not an admin of any clubs and therefore cannot access this page.";
				//echo '<br /><a href="index.php">Go back</a>'; 
				}
			}
		}
	echo '<br /><a href="index.php">Go back</a>';	
	?>
</html>