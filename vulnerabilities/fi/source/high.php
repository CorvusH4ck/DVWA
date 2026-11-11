<?php

// The page we wish to display
$file = $_GET[ 'page' ];

// Only allow include.php
if( $file != "include.php" ) {
	echo "ERROR: File not found!";
	exit;
}

?>
