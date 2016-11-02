<!DOCTYPE html>
<html>
	<header>
		<title>Club Hub</title>
	</header>
	<body>

	<?php
		include ("include.php");

		if(!isset($_SESSION["pid"])) {
			echo "Welcome to the Club Hub, you are not logged in. <br /><br >\n";
			echo 'You may view the following things below or <a href="login.php">login</a> to Club Hub to do more. ';
			echo "\n";
		}else {
			echo "Welcome. You are logged in.<br /><br />\n";
			echo 'You may view the things listed below or <a href="comment.php">post comments</a> or <a href="signUp.php">view and sign up for events</a> or <a href="post.php">post a new event</a> or <a href="checkClubEvent.php">check your club\'s events</a> or <a href="logout.php">logout</a>';
			echo "\n";
		}
		echo "<br /><br />\n";
	?>

	<form action = "get-input-example.php" method="GET">
		<b>List of topics available for browsing: </b> <br> 
			<select name='topic'>
				<?php
					if ($stmt = $mysqli->prepare("select distinct topic from keywords order by topic asc")) {
						$stmt->execute();
						$stmt->bind_result($topic);
						while($stmt->fetch()) {
							$topic = htmlspecialchars($topic);
							echo "<option value='$topic'>$topic</option>\n";	
						}
					}
				?>
			</select>
			<input type="submit" value="Show The Clubs Associated With Each Topic">
	</form>

	<?php
		if ($stmt = $mysqli->prepare ("select ename, description, edatetime, location from event where is_public_e=1 and date(`edatetime`) between CURDATE() and DATE_ADD(CURDATE(),INTERVAL 7 DAY)"))	{
			$stmt->execute();
			$stmt->bind_result($ename, $description, $edatetime, $location);
			// Printing results in HTML		
			echo "<br /><br />\n";
			echo "<b>List of public events occurring in the coming days (list may be empty if no public events are occuring in the next 7 days)</b>:";
			echo "<table border = '1'>\n";
			while ($stmt->fetch()) {
				echo "<tr>";
				echo "<td>$ename</td><td>$description</td><td>$edatetime</td><td>$location</td>";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}else {
			echo "query is not working\n";
		}
	?>
	</body>
</html>