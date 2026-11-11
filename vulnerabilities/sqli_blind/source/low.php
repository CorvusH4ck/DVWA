<?php

if( isset( $_GET[ 'Submit' ] ) ) {
	// Get input
	$id = $_GET[ 'id' ];
	$exists = false;

	// Reject non-numeric input, then cast to int
	if( !is_numeric( $id ) ) {
		header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );
		$html .= '<pre>User ID is MISSING from the database.</pre>';
	} else {
		$id = intval( $id );

		switch ($_DVWA['SQLI_DB']) {
			case MYSQL:
				$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], 'SELECT first_name FROM users WHERE user_id = ? LIMIT 1;');
				mysqli_stmt_bind_param($stmt, 'i', $id);
				mysqli_stmt_execute($stmt);
				$first_name = null;
				mysqli_stmt_bind_result($stmt, $first_name);
				$exists = mysqli_stmt_fetch($stmt) ? true : false;
				mysqli_stmt_close($stmt);
				((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
				break;
			case SQLITE:
				global $sqlite_db_connection;

				$stmt = $sqlite_db_connection->prepare('SELECT first_name FROM users WHERE user_id = :id LIMIT 1;');
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
}

?>
