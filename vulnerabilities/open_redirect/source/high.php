<?php

if (array_key_exists ("redirect", $_GET) && $_GET['redirect'] != "") {
	$target = $_GET['redirect'];
	// preg_match prevents bypasses like "info.php@evil.com" that strpos() allows
	if (preg_match( '/^info\.php\?id=\d+$/', $target )) {
		header ("location: " . $target);
		exit;
	} else {
		http_response_code (500);
		?>
		<p>You can only redirect to the info page.</p>
		<?php
		exit;
	}
}

http_response_code (500);
?>
<p>Missing redirect target.</p>
<?php
exit;
?>
