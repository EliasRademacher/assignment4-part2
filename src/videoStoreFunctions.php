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
		echo "length must be a number<br>";
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







?>