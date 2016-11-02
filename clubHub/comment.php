<!DOCTYPE html>
<html>
<title>Post Comments</title>

<?php

include ("include.php");

//if the user is not logged in, redirect them back to homepage
if(!isset($_SESSION["pid"])) {
  echo "You are not logged in. ";
  echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php\">here</a>.\n";
  header("refresh: 3; index.php");
}
//if user entered all fields
if(isset($_POST["ctext"]) && !empty($_POST["ctext"]) && isset($_POST["is_public_c"]) && !empty($_POST["is_public_c"]) && isset($_POST["clubid"]) && !empty($_POST["clubid"]) && isset($_POST["eid"]) && !empty($_POST["eid"])) {
	echo "<b>You have entered values for both clubid and eid. Therefore your comment is not posted. Please try again. </b>";
	echo "<br /><br />";
}
//if the user have entered a comment and existing club_id, insert it into database
if(isset($_POST["ctext"]) && !empty($_POST["ctext"]) && isset($_POST["is_public_c"]) && !empty($_POST["is_public_c"]) && isset($_POST["clubid"]) && !empty($_POST["clubid"]) && empty($_POST["eid"]) ) {
//insert into database, note that comment_id is auto_increment 
if ($stmt = $mysqli->prepare("insert into comment (commenter, ctext, is_public_c) values (?,?,?)")) {
	$stmt->bind_param("isi", $_SESSION["pid"], $_POST["ctext"], $_POST["is_public_c"]);
	$stmt->execute();
}
if ($stmt1 = $mysqli->prepare("select comment_id from comment where commenter=? and ctext=? and is_public_c=?")) {
	$stmt1->bind_param("isi", $_SESSION["pid"], $_POST["ctext"], $_POST["is_public_c"]);
	$stmt1->execute();
	$stmt1->bind_result($comment_id);	
	$stmt1->store_result();
	if ($stmt1->fetch()) {						
		if ($stmt2 = $mysqli->prepare("insert into club_comment (comment_id, clubid) values (?,?)")) {
			$stmt2->bind_param("ii", $comment_id, $_POST["clubid"]);
			$stmt2->execute();
		}
	}
}			
$pid = htmlspecialchars($_SESSION["pid"]);
echo "Your comment is posted. \n";
echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php?pid=$pid\">here</a>.";
echo "<br /><br />\n";
header("refresh: 3; index.php?pid=$pid");
}

//if the user have entered a comment and existing club_id, insert it into database
if(isset($_POST["ctext"]) && !empty($_POST["ctext"]) && isset($_POST["is_public_c"]) && !empty($_POST["is_public_c"]) && isset($_POST["eid"]) && !empty($_POST["eid"]) && empty($_POST["clubid"])) {
//insert into database, note that comment_id is auto_increment 
if ($stmt = $mysqli->prepare("insert into comment (commenter, ctext, is_public_c) values (?,?,?)")) {
	$stmt->bind_param("isi", $_SESSION["pid"], $_POST["ctext"], $_POST["is_public_c"]);
	$stmt->execute();
	$stmt->close();
}
if ($stmt = $mysqli->prepare("select comment_id from comment where commenter=? and ctext=? and is_public_c=?")) {
	$stmt->bind_param("isi", $_SESSION["pid"], $_POST["ctext"], $_POST["is_public_c"]);
	$stmt->execute();
	$stmt->bind_result($comment_id);
	$stmt->store_result();
	if($stmt->fetch()) {				
		if ($stmt = $mysqli->prepare("insert into event_comment (comment_id, eid) values (?,?)")) {
		$stmt->bind_param("ii", $comment_id, $_POST["eid"]);
		$stmt->execute();
		$stmt->close();
		}
	}
}
$pid = htmlspecialchars($_SESSION["pid"]);
echo "Your comment is posted. \n";
echo "You will be returned to the homepage in 3 seconds or click <a href=\"index.php?pid=$pid\">here</a>.";
echo "<br /><br />\n";
header("refresh: 3; index.php?pid=$pid");
}
  //display the form for posting comment
    echo '<form action="comment.php" method="POST">';
    echo "\n";
	echo 'Do you wish to make your comment public or private (Enter 0 for private or any other number for public):  <input type="tinyint" name="is_public_c" /><br />';	
	echo "<br /><br />";
	echo 'Is this comment about a club? If so, enter existing clubid (if not leave it blank)*: <input type="int" name="clubid" /><br />';
	echo "<br /><br />";
	echo 'Is this comment about an event? If so, enter existing eid (if not leave it blank)*: <input type="int" name="eid" /><br />';
	echo "<br /><br />";
    echo "Enter your comments: <br /><br />\n";
    echo '<textarea cols="40" rows="10" name="ctext" />enter your comments here</textarea><br />';
    echo "\n";
	echo '<b>*Caution: You may enter either a clubid or eid, but not both. Else your comment will not be posted. Also, if you do not enter an existing clubid or eid, your comment will not be properly displayed. </b>';
	echo "<br /><br />";
	echo '<input type="submit" value="Submit" />';
    echo "\n";
	echo '</form>';
	echo "\n";
	echo '<br /><a href="index.php">Go back</a>';

$mysqli->close();
?>

</html>