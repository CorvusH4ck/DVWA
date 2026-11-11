<?php

if( isset( $_REQUEST[ 'Submit' ] ) ) {
	// Get input
	$id = $_REQUEST[ 'id' ];
	$exists = false;

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
				$result = mysqli_stmt_get_result($stmt);
				$exists = ($result && mysqli_fetch_assoc($result) !== null);
				mysqli_stmt_close($stmt);
				// Note: do NOT close the mysqli connection here;
				// sqli_blind/index.php queries the DB for the dropdown after this file runs.
				break;
			case SQLITE:
				global $sqlite_db_connection;

				$query  = "SELECT first_name FROM users WHERE user_id = $id;";
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
			$html .= '<pre>User ID exists in the database.</pre>';
		} else {
			header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );
			$html .= '<pre>User ID is MISSING from the database.</pre>';
		}
	}
}

?>
