<html>
 <head>
  <title>Video Store</title>
 </head>
 <body>
	<p color="blue">Add a video to the database: </p>
	<form method="POST">
		<p>name: <input type="text" name="name_input" /></p>
		<p>category: <input type="text" name="category_input" /></p>
		<p>length: <input type="text" name="length_input" /></p>
		<p><input type="submit"/></p>
	</form>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$mysqli = new mysqli("oniddb.cws.oregonstate.edu",
	"rademace-db", "8xYcLE6mhsNKxGMP", "rademace-db");










/* Handle requests from button clicks */
if (isset($_POST['deleteID'])) {
    if (!($stmt = $mysqli->prepare("DELETE FROM Videos
		WHERE id = $_POST[deleteID]")))
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";
		
	if (!$stmt->execute())
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error . "<br>";
}

if (isset($_POST['deleteAll'])) {
    $mysqli->query("DROP TABLE Videos");
}

if (isset($_POST['CheckInOut'])) {
    var_dump($_POST);
}








	

if (!$mysqli || $mysqli->connect_errno)
	echo "Connection error: " . $mysqli->connect_errno . " " . $mysqli->connect_error . "<br>";
else
	echo "Connected to onid database<br>";

$result = $mysqli->query("SHOW TABLES LIKE 'Videos'");
if ($result === FALSE)
    echo "Query failed <br>";

if ($result->num_rows < 1) {
	
	if ($mysqli->query("CREATE TABLE Videos(
		id INT PRIMARY KEY AUTO_INCREMENT,
		name VARCHAR(255) UNIQUE NOT NULL,
		category VARCHAR(255),
		length INT,
		rented INT DEFAULT 0
		)") === TRUE) {
			echo "Table 'Videos' created successfully<br>";
		}
		
	else
		echo "failed to create table (" . $mysqli->errno . ") " . $mysqli->error . "<br>"; 
}








/* Add video to database */
if(isset($_POST['name_input']) AND strlen($_POST['name_input']) != 0
	AND isset($_POST['category_input']) AND strlen($_POST['category_input']) != 0
	AND isset($_POST['length_input']) AND strlen($_POST['length_input']) != 0
	) {
	
	$name = str_replace ('\'', '\\\'', $_POST['name_input']);
	$category = str_replace ('\'', '\\\'', $_POST['category_input']);
	$length = $_POST['length_input'];
	
	if (isValidInput($mysqli, $name, $length)) {
		if (!($statement = $mysqli->prepare("INSERT INTO Videos(name, category, length) VALUES
			('$name', '$category', '$length')")))
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";

		if (!$statement->execute())
			echo "Execute failed: (" . $statement->errno . ") " . $statement->error . "<br>";
	}
}






/* Display Table */
if (!($statement = $mysqli->prepare("SELECT id, name, category, length, rented FROM Videos")))
	echo "prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";
$statement->bind_param("issii", $id, $name, $category, $length, $rented); 
if (!$statement->execute())
	echo "Execute failed: (" . $statement->errno . ") " . $statement->error . "<br>";
$statement->bind_result($resultID, $resultName, $resultCategory, $resultLength, $resultRented);

echo "<td><form method=POST>";
echo "<table border='1'>";
echo "<tr>";
echo "<th><button type=submit name=deleteAll><font color=red>Delete all Videos</font></button></th>";
echo "<th>Title</th>";
echo "<th>Category</th>";
echo "<th>Length (min)</th>";
echo "<th>Status</th>";
while ($statement->fetch()) {
	echo "<tr>";
    echo "<td>";
	echo "<button type=submit name=deleteID value=$resultID>Delete</button>";
	echo "<button type=submit name=CheckInOut>Check in/out</button>";
	echo "</td>";	
	echo "<td>$resultName</td>";
	echo "<td>$resultCategory</td>";
	echo "<td>$resultLength</td>";
	if ($resultRented)
		echo "<td>checked out</td>";
	else
		echo "<td>available</td>";
	echo "</tr>";
}
echo "</table>";
echo "</form>";
$statement->close();







function isValidInput($mysqli, $name, $length) {
	
	/* Make sure video name doesn't already exist in database */
	if (!($statement = $mysqli->prepare("SELECT name FROM Videos")))
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";

	$statement->bind_param("s", $n); 
	if (!$statement->execute()) {
		echo "Execute failed: (" . $statement->errno . ") " . $statement->error . "<br>";
	}
	$statement->bind_result($resultName);
	
	while ($statement->fetch()) {
		if ($resultName == $name) {
			echo "$name already exists in database<br>";
			return FALSE;
		}
	}

	$statement->close();
	
	/* Make sure "length" is a number */
	if (!is_numeric($length)) {
		echo "length must be a number<br>";
		return FALSE;
	}
	
	return TRUE;
}

	
?>


 </body>
</html>


	
	