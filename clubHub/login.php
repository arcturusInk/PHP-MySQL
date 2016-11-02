<!DOCTYPE html>
<html>
<title>Login</title>

<?php

include "include.php";

//if the user is already logged in, redirect them back to homepage
if(isset($_SESSION["pid"])) {
  echo "You are already logged in. \n";
  echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.\n";
  header("refresh: 3; index.php");
}
else {
  //if the user have entered both entries in the form, check if they exist in the database
  if(isset($_POST["pid"]) && isset($_POST["passwd"])) {
    //check if entry exists in database
    if ($stmt = $mysqli->prepare("select pid, passwd from person where pid = ? and passwd = md5(?)")) {
		$stmt->bind_param("ss", $_POST["pid"], $_POST["passwd"]);
		$stmt->execute();
		$stmt->bind_result($pid, $passwd);
	    //if there is a match set session variables and send user to homepage
        if ($stmt->fetch()) {
			$_SESSION["pid"] = $pid;
			$_SESSION["passwd"] = $passwd;
			$_SESSION["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"]; //store clients IP address to help prevent session hijack
			echo "Login successful. \n";
			echo "You will be redirected in 3 seconds or click <a href=\"index.php\">here</a>.";
			header("refresh: 3; index.php");
        }
		//if no match then tell them to try again
		else {
		  sleep(1); //pause a bit to help prevent brute force attacks
		  echo "Your username or password is incorrect, click <a href=\"login.php\">here</a> to try again.";
		}
      $stmt->close();
	  $mysqli->close();
    }  
  }
  //if not then display login form
  else {
    echo "Enter your pid and password below: <br /><br />\n";
    echo '<form action="login.php" method="POST">';
	echo "\n";
    echo 'Pid: <input type="text" name="pid" /><br />';
	echo "\n";
    echo 'Password: <input type="passwd" name="passwd" /><br />';
	echo "\n";
    echo '<input type="submit" value="Submit" />';
	echo "\n";
    echo '</form>';
	echo "\n";
	echo '<br /><a href="index.php">Go back</a>';
  }
}
?>

</html>