<?php

if (array_key_exists ("redirect", $_GET) && $_GET['redirect'] != "") {
	$target = $_GET['redirect'];
	// Only allow relative redirects to the info page (no external URLs)
	if (preg_match( '/^info\.php\?id=\d+$/', $target )) {
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
