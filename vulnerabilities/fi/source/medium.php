<?php

// The page we wish to display
$file = $_GET[ 'page' ];

// Whitelist allowed pages only
$allowed = array( 'include.php', 'file1.php', 'file2.php', 'file3.php', 'file4.php' );
if( !in_array( $file, $allowed ) ) {
	echo "ERROR: File not found!";
	exit;
}

?>
