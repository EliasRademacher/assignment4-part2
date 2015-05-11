<?php
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
		echo "<font color=orange>length must be a number</font><br>";
		return FALSE;
	}
	
	if (intval($length) < 0) {
		echo "<font color=orange>length must be a positive number</font><br>";
		return FALSE;
	}
	
	return TRUE;
}


function deleteRow($mysqli){
	if (!($stmt = $mysqli->prepare("DELETE FROM Videos
		WHERE id = $_POST[deleteID]")))
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";
		
	if (!$stmt->execute())
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error . "<br>";	
}

function deleteAll($mysqli) {
    $mysqli->query("DROP TABLE Videos");
}

function checkInOut($mysqli) {
	if (!($stmt = $mysqli->prepare("SELECT rented FROM Videos
		WHERE id = $_POST[CheckInOut]")))
			echo "Prepare for 'SELECT' failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";
	
	$stmt->bind_param("i", $r); 
	if (!$stmt->execute()) {
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error . "<br>";
	}
	$stmt->bind_result($isRented);
	
	$stmt->fetch();
	
	if ($isRented) {
		$stmt->close();
		if (!($stmt = $mysqli->prepare("UPDATE Videos SET rented=0
		WHERE id = $_POST[CheckInOut]")))
			echo "Prepare for 'UPDATE(1)' failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";
	}
		
	else  {
		$stmt->close();
		if (!($stmt = $mysqli->prepare("UPDATE Videos SET rented=1
		WHERE id = $_POST[CheckInOut]")))
			echo "Prepare for 'UPDATE(2)'failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";
	}
	
	if (!$stmt->execute())
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error . "<br>";
	
	$stmt->close();	
}

function getCategories($mysqli){
	$categories = array();

	if (!($stmt = $mysqli->prepare("SELECT category FROM Videos")))
		echo "prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>";
	$stmt->bind_param("s", $category); 
	if (!$stmt->execute())
		echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error . "<br>";
	$stmt->bind_result($resultCategory);

	while ($stmt->fetch()) {	
		if (!in_array($resultCategory, $categories)) {
			array_push($categories, $resultCategory);
		}
	}
	$stmt->close();
	
	return $categories;
	
}

function displayRow($name, $category, $length, $id, $rented) {
	echo "<tr>";
	echo "<td>";
	echo "<button type=submit name=deleteID value=$id>Delete</button>";
	echo "<button type=submit name=CheckInOut value=$id>Check in/out</button>";
	echo "</td>";	
	echo "<td>$name</td>";
	echo "<td>$category</td>";
	echo "<td>$length</td>";
	if ($rented)
		echo "<td>checked out</td>";
	else
		echo "<td>available</td>";
	echo "</tr>";
}





?>