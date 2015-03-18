<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

?>

<script type="text/javascript">
	// If device is touch screen then remove hover effect to display gallery/image name
	// instead display name underneath
	$(function() {
		function isTouchDevice() {
			return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
		}
		
		if (isTouchDevice() === true) {
			$(".gallery-name-touchscreen-index").css("display", "block");
			$(".gallery-album-overlay-index").css("display", "none");
			$(".gallery-album-overlay").css("display", "none");
		}
	});
</script>