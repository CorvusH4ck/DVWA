<?php

if( isset( $_GET[ 'Login' ] ) ) {
	// Sanitise username input
	$user = $_GET[ 'username' ];
	$user = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $user ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

	// Sanitise password input
	$pass = $_GET[ 'password' ];
	$pass = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $pass ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
	$pass = md5( $pass );

	$total_failed_login = 3;
	$lockout_time       = 15;
	$account_locked     = false;

	// Check failed login count from database
	$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], 'SELECT failed_login, last_login FROM users WHERE user = ? LIMIT 1;');
	mysqli_stmt_bind_param($stmt, 's', $user);
	mysqli_stmt_execute($stmt);
	$failed_login_count = null;
	$last_login_time    = null;
	mysqli_stmt_bind_result($stmt, $failed_login_count, $last_login_time);
	$row = mysqli_stmt_fetch($stmt) ? array( 'failed_login' => $failed_login_count, 'last_login' => $last_login_time ) : false;
	mysqli_stmt_close($stmt);

	if( $row && ( $row['failed_login'] >= $total_failed_login ) ) {
		$last_login = strtotime( $row['last_login'] );
		$timeout    = $last_login + ($lockout_time * 60);
		$timenow    = time();
		if( $timenow < $timeout ) {
			$account_locked = true;
		}
	}

	// Check the database
	$query  = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';";
	$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );

	if( $result && mysqli_num_rows( $result ) == 1 && !$account_locked ) {
		// Get users details
		$row    = mysqli_fetch_assoc( $result );
		$avatar = $row["avatar"];

		// Login successful
		$html .= "<p>Welcome to the password protected area {$user}</p>";
		$html .= "<img src=\"{$avatar}\" />";

		// Reset failed login count
		$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], 'UPDATE users SET failed_login = 0 WHERE user = ? LIMIT 1;');
		mysqli_stmt_bind_param($stmt, 's', $user);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
	else {
		// Login failed
		sleep( 2 );
		$html .= "<pre><br />Username and/or password incorrect.</pre>";

		// Increment failed login count
		$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], 'UPDATE users SET failed_login = (failed_login + 1), last_login = now() WHERE user = ? LIMIT 1;');
		mysqli_stmt_bind_param($stmt, 's', $user);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}

	((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

?>
