<?php

if( isset( $_GET[ 'Login' ] ) ) {
	$user = $_GET[ 'username' ];
	$user = stripslashes( $user );

	$pass = $_GET[ 'password' ];
	$pass = stripslashes( $pass );
	$pass = md5( $pass );

	$total_failed_login = 3;
	$lockout_time       = 15;
	$account_locked     = false;

	$user_safe = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $user) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

	// Check failed login count
	$lock_res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT failed_login, last_login FROM users WHERE user = '$user_safe' LIMIT 1;");
	if( $lock_res && ($lock_row = mysqli_fetch_assoc($lock_res)) ) {
		if( (int)$lock_row['failed_login'] >= $total_failed_login ) {
			$last_login = strtotime( $lock_row['last_login'] );
			$timeout    = $last_login + ($lockout_time * 60);
			if( time() < $timeout ) {
				$account_locked = true;
			}
		}
	}

	// Check the database
	$auth_res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM `users` WHERE user = '$user_safe' AND password = '$pass' LIMIT 1;");

	if( $auth_res && mysqli_num_rows( $auth_res ) == 1 && !$account_locked ) {
		$row    = mysqli_fetch_assoc( $auth_res );
		$avatar = $row["avatar"];

		$html .= "<p>Welcome to the password protected area {$user}</p>";
		$html .= "<img src=\"{$avatar}\" />";

		// Reset failed login count
		mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET failed_login = 0 WHERE user = '$user_safe' LIMIT 1;");
	} else {
		$html .= "<pre><br />Username and/or password incorrect.</pre>";

		// Increment failed login count
		mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET failed_login = (failed_login + 1) WHERE user = '$user_safe' LIMIT 1;");
	}

	// Always update last login time
	mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE users SET last_login = now() WHERE user = '$user_safe' LIMIT 1;");

	((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

// Generate Anti-CSRF token
generateSessionToken();

?>
