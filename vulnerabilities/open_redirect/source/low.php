<?php

$allowed_targets = array(
	"info.php?id=1",
	"info.php?id=2",
	"https://digi.ninja",
);

if (array_key_exists ("redirect", $_GET) && $_GET['redirect'] != "") {
	$target = $_GET['redirect'];
	if( in_array( $target, $allowed_targets ) ) {
		header ("location: " . $target);
		exit;
	}
}

http_response_code (500);
?>
<p>Missing or invalid redirect target.</p>
<?php
exit;
?>
