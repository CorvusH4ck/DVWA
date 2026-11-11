<?php

// The page we wish to display
$file = $_GET[ 'page' ];

// Strict whitelist – fnmatch("file*") allows arbitrary file* names
$allowed = array( 'include.php', 'file1.php', 'file2.php', 'file3.php' );
if( !in_array( $file, $allowed ) ) {
	echo "ERROR: File not found!";
	exit;
}

?>
