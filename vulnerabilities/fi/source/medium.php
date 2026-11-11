<?php

// The page we wish to display
$file = $_GET[ 'page' ] ?? null;

// Whitelist allowed pages only
$allowed = array( 'include.php', 'file1.php', 'file2.php', 'file3.php' );
if( $file !== null && !in_array( $file, $allowed ) ) {
	echo "ERROR: File not found!";
	exit;
}

?>
