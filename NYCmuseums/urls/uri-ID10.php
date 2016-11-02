<!DOCTYPE html>
<html>
	<head>
		<title>MORE INFORMATION</title>
		<link rel="stylesheet" href="css/info-style.css" type="text/css"/> 
	</head>
	<body>
	<?php
	$mysqli = new mysqli("localhost", "", "", "cl18-museums");

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	?>
	<p class="headings">WEBSITE:</p>
	<?php
	$query = "SELECT website FROM moreInfo where infoID = 10";
	/* prepare statement */
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->execute();
		/* bind variables to prepared statement */
		$stmt->bind_result($website);
		/* fetch values */
		if ($stmt->fetch()) {
			echo '<a href="' . $website . '">' . $website . '</a>';
		}
		/* close statement */
		$stmt->close();
	}
	?>
	
	<p class="headings">PHONE:</p>
	<?php
	$query = "SELECT phone FROM moreInfo where infoID = 10";
	/* prepare statement */
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->execute();
		/* bind variables to prepared statement */
		$stmt->bind_result($phone);
		/* fetch values */
		if ($stmt->fetch()) {
			echo"$phone";
		}
		/* close statement */
		$stmt->close();
	}
	?>
		
	<p class="headings">HOURS:</p>
	<?php
	$query = "SELECT hours FROM moreInfo where infoID = 10";
	/* prepare statement */
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->execute();
		/* bind variables to prepared statement */
		$stmt->bind_result($hours);
		/* fetch values */
		if ($stmt->fetch()) {
			echo"$hours";
		}
		/* close statement */
		$stmt->close();
	}
	?>
	
	<p class="headings">ADMISSIONS:</p>
	<?php
	$query = "SELECT admissions FROM moreInfo where infoID = 10";
	/* prepare statement */
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->execute();
		/* bind variables to prepared statement */
		$stmt->bind_result($admissions);
		/* fetch values */
		if ($stmt->fetch()) {
			echo"$admissions";
		}
		/* close statement */
		$stmt->close();
	}
	/* close connection */
	$mysqli->close();
	?>
	
	<p class="line"> --------------------- </p>
	<p class="headings">Disclaimer: Information may not be up-to-date. Visit museum's website for most recent information.</p>
	</body>
</html>
