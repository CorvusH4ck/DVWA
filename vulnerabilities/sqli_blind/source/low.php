<?php

if( isset( $_GET[ 'Submit' ] ) ) {
	// Get input
	$id = $_GET[ 'id' ];
	$exists = false;

	// Cast to int — prevents any SQL injection for a numeric ID field
	$id = intval( $id );

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			$query  = "SELECT first_name, last_name FROM users WHERE user_id = $id;";
			try {
				$result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
			} catch (Exception $e) {
				print "There was an error.";
				exit;
			}
			$exists = false;
			if ($result !== false) {
				try {
					$exists = (mysqli_num_rows( $result ) > 0);
				} catch(Exception $e) {
					$exists = false;
				}
			}
			((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			break;
		case SQLITE:
			global $sqlite_db_connection;

			$stmt = $sqlite_db_connection->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id LIMIT 1;');
			$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
			$results = $stmt->execute();
			$row = $results ? $results->fetchArray() : false;
			$exists = $row !== false;
			break;
	}

	if ($exists) {
		// Feedback for end user
		$html .= '<pre>User ID exists in the database.</pre>';
	} else {
		// User wasn't found, so the page wasn't!
		header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );

		// Feedback for end user
		$html .= '<pre>User ID is MISSING from the database.</pre>';
	}

}

?>
