<!DOCTYPE html>
<html>
	<?php
		include ("include.php");

		//perform SQL query
		if(isset($_GET["topic"])) {
			$input_topic = $_GET["topic"];
			if ($stmt = $mysqli->prepare("select cname FROM keywords natural join club_topics natural join club WHERE topic=? order by cname desc")) {
				$stmt->bind_param("s", $input_topic);
				$stmt->execute();
				$stmt->bind_result($cname);
				// Printing results in HTML
				echo "<table border = '1'>\n";
				while ($stmt->fetch()) {
					echo "<tr>";
					echo "<td>$cname</td>";
					echo "</tr>\n";
				}
				echo "</table>\n";
			}
		}else {
			echo "topic is not set\n";
		}
		echo "\n";
		echo '<br /><a href="index.php">Go back</a>';  
	?>
</html>
