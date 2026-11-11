<?php

if( isset( $_GET[ 'Login' ] ) ) {
	// Check Anti-CSRF token
	checkToken( $_REQUEST[ 'user_token' ], $_SESSION[ 'session_token' ], 'index.php' );

	$user = $_GET[ 'username' ];
	$user = stripslashes( $user );
	$user = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $user ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

	$pass = $_GET[ 'password' ];
	$pass = stripslashes( $pass );
	$pass = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $pass ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	$pass = md5( $pass );

	// Fetch failed_login and last_login for lockout check
	$stmt = mysqli_prepare( $GLOBALS["___mysqli_ston"], 'SELECT failed_login, last_login FROM users WHERE user = ? LIMIT 1;' );
	mysqli_stmt_bind_param( $stmt, 's', $user );
	mysqli_stmt_execute( $stmt );
	$failed_login_count = null;
	$last_login_time    = null;
	mysqli_stmt_bind_result( $stmt, $failed_login_count, $last_login_time );
	$row = mysqli_stmt_fetch( $stmt ) ? array( 'failed_login' => $failed_login_count, 'last_login' => $last_login_time ) : false;
	mysqli_stmt_close( $stmt );

	$total_failed_login = 3;
	$lockout_time       = 15;

	if( $row ) {
		if( (int) $row['failed_login'] >= $total_failed_login ) {
			$last = strtotime( $row['last_login'] );
			$next = $last + ( $lockout_time * 60 );
			if( time() < $next ) {
				$html .= "<pre><br />This account has been locked due to too many incorrect logins.</pre>";
				((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
				generateSessionToken();
				return;
			}
		}
	}

	$query  = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';";
	$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );

	if( $result && mysqli_num_rows( $result ) == 1 ) {
		$row    = mysqli_fetch_assoc( $result );
		$avatar = $row["avatar"];

		// Reset failed login counter on success
		$upd = mysqli_prepare( $GLOBALS["___mysqli_ston"], 'UPDATE users SET failed_login = 0 WHERE user = ? LIMIT 1;' );
		mysqli_stmt_bind_param( $upd, 's', $user );
		mysqli_stmt_execute( $upd );
		mysqli_stmt_close( $upd );

		$html .= "<p>Welcome to the password protected area {$user}</p>";
		$html .= "<img src=\"{$avatar}\" />";
	}
	else {
		// Increment failed login counter
		$upd = mysqli_prepare( $GLOBALS["___mysqli_ston"], 'UPDATE users SET failed_login = (failed_login + 1), last_login = now() WHERE user = ? LIMIT 1;' );
		mysqli_stmt_bind_param( $upd, 's', $user );
		mysqli_stmt_execute( $upd );
		mysqli_stmt_close( $upd );

		$html .= "<pre><br />Username and/or password incorrect.</pre>";
	}

	((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

// Generate Anti-CSRF token
generateSessionToken();

?>
