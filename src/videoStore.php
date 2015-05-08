<html>
 <head>
  <title>Video Store</title>
 </head>
 <body>
	<p>Add a video to the database: </p>
	<form method="GET">
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

if (!$mysqli || $mysqli->connect_errno) {
	echo "Connection error: " . $mysqli->connect_errno . " " . $mysqli->connect_error . "<br>";
}

else
	echo "Connection worked!<br>";


$result = $mysqli->query("SHOW TABLES LIKE 'Videos'");
if ($result === FALSE) {
    echo "Query failed <br>";
}


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

else
	echo "Table already exists <br>";


if(isset($_GET['name_input']) AND strlen($_GET['name_input']) != 0
	AND isset($_GET['category_input']) AND strlen($_GET['category_input']) != 0
	AND isset($_GET['length_input']) AND strlen($_GET['length_input']) != 0
	) {


	$name = str_replace ('\'', '\\\'', $_GET['name_input']);
	$category = str_replace ('\'', '\\\'', $_GET['category_input']);
	$length = $_GET['length_input'];
	
	else if (!is_numeric($length))
		echo "length must be a number<br>";
	
	else {
		
		
		if (!($statement = $mysqli->prepare("INSERT INTO Videos(name, category, length) VALUES
			('$name', '$category', '$length')"))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";
		}

		if (!$statement->execute()) {
			echo "Execute failed: (" . $statement->errno . ") " . $statement->error . "<br>";
		}
	}
}
	
if (!($statement = $mysqli->prepare("SELECT name, category, length FROM Videos"))) {
	echo "prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";
}

$statement->bind_param("ssi", $name, $category, $length); 
if (!$statement->execute()) {
	echo "Execute failed: (" . $statement->errno . ") " . $statement->error . "<br>";
}
$statement->bind_result($resultName, $resultCategory, $resultLength);

echo "<table border='1'>";
echo "<tr>";
echo "<th>Title</th>";
echo "<th>Category</th>";
echo "<th>Length (min)</th>";
while ($statement->fetch()) {
	echo "<tr>";
	echo "<td>$resultName</td>";
	echo "<td>$resultCategory</td>";
	echo "<td>$resultLength</td>";
}
$statement->close();
	
	
?>

 </body>
</html>


	
	