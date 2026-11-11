<?php

// The page we wish to display
$file = $_GET[ 'page' ] ?? null;

// Only allow include.php
if( $file !== null && $file != "include.php" ) {
	echo "ERROR: File not found!";
	exit;
}

?>
