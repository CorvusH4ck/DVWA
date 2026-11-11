<?php

if( isset( $_GET[ 'Login' ] ) ) {
	$user = $_GET[ 'username' ];
	$user = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $user) : "");

	$pass = $_GET[ 'password' ];
	$pass = md5( $pass );

	// Check failed login count in session for rate limiting
	if( !isset( $_SESSION['login_attempts'] ) ) {
		$_SESSION['login_attempts'] = 0;
		$_SESSION['login_lockout'] = 0;
	}

	if( $_SESSION['login_lockout'] > time() ) {
		$html .= "<pre><br />Too many failed attempts. Please try again later.</pre>";
		((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		return;
	}

	$query  = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';";
	$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );

	if( $result && mysqli_num_rows( $result ) == 1 ) {
		$row    = mysqli_fetch_assoc( $result );
		$avatar = $row["avatar"];

		$_SESSION['login_attempts'] = 0;
		$_SESSION['login_lockout'] = 0;

		$html .= "<p>Welcome to the password protected area {$user}</p>";
		$html .= "<img src=\"{$avatar}\" />";
	}
	else {
		$_SESSION['login_attempts']++;
		if( $_SESSION['login_attempts'] >= 3 ) {
			$_SESSION['login_lockout'] = time() + 900;
			$_SESSION['login_attempts'] = 0;
		}
		sleep( 2 );
		$html .= "<pre><br />Username and/or password incorrect.</pre>";
	}

	((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

?>
