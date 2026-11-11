<?php

if( isset( $_COOKIE[ 'id' ] ) ) {
	$id = $_COOKIE[ 'id' ];
	$exists = false;

	// Reject non-numeric input to prevent SQL injection via cookie
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

				$query  = "SELECT first_name FROM users WHERE user_id = $id LIMIT 1;";
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
		}
		else {
			header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );
			$html .= '<pre>User ID is MISSING from the database.</pre>';
		}
	}
}

?>
