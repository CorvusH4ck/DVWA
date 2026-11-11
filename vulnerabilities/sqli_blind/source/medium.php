<?php

if( isset( $_POST[ 'Submit' ]  ) ) {
	// Get input
	$id = $_POST[ 'id' ];
	$exists = false;

	if( !is_numeric( $id ) ) {
		header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );
		$html .= '<pre>User ID is MISSING from the database.</pre>';
	} else {
	$id = intval( $id );

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// Check database
			$query  = "SELECT first_name, last_name FROM users WHERE user_id = $id;";
			try {
				$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ); // Removed 'or die' to suppress mysql errors
			} catch (Exception $e) {
				print "There was an error.";
				exit;
			}

			$exists = false;
			if ($result !== false) {
				try {
					$exists = (mysqli_num_rows( $result ) > 0); // The '@' character suppresses errors
				} catch(Exception $e) {
					$exists = false;
				}
			}
			
			break;
		case SQLITE:
			global $sqlite_db_connection;
			
			$query  = "SELECT first_name, last_name FROM users WHERE user_id = $id;";
			try {
				$results = $sqlite_db_connection->query($query);
				$row = $results->fetchArray();
				$exists = $row !== false;
			} catch(Exception $e) {
				$exists = false;
			}
			break;
	}

	if ($exists) {
		// Feedback for end user
		$html .= '<pre>User ID exists in the database.</pre>';
	} else {
		// Feedback for end user
		header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );
		$html .= '<pre>User ID is MISSING from the database.</pre>';
	}

	} // end is_numeric check

}

?>
