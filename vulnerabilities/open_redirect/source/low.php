<?php

$allowed_targets = array(
	1 => "info.php?id=1",
	2 => "info.php?id=2",
	99 => "https://digi.ninja",
);

if (array_key_exists ("redirect", $_GET) && is_numeric( $_GET['redirect'] ) ) {
	$target_id = intval( $_GET['redirect'] );
	if( isset( $allowed_targets[$target_id] ) ) {
		header ("location: " . $allowed_targets[$target_id]);
		exit;
	}
}

http_response_code (500);
?>
<p>Missing or invalid redirect target.</p>
<?php
exit;
?>
