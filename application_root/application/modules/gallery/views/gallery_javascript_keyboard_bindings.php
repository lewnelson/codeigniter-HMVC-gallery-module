<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

?>

<script type="text/javascript">
	// Binds keys to simulate link clicks
	$(function() {
		window.addEventListener("keydown", function(e) {			
			// If escape key is pressed
			if (e.keyCode === 27) {
				e.preventDefault();
				$(".gallery-back-to-link-view").trigger("click");
			}
			
			// If right arrow key is pressed
			if (e.keyCode === 39) {
				e.preventDefault();
				$(".gallery-next-image-allow").trigger("click");
			}
			
			// If left arrow key is pressed
			if (e.keyCode === 37) {
				e.preventDefault();
				$(".gallery-previous-image-allow").trigger("click");
			}
		});
	});
	
	// When the back button is pressed whilst viewing the gallery it will take you back
	// to the album rather than the last picture viewed.
	$(function() {
		if (window.history && window.history.pushState) {
			var base_url = window.location.host + "/";
			var uri_segments = window.location.pathname.split( "/" );
			var gallery_uri = uri_segments[1] + "/" + uri_segments[2];
			window.history.pushState('forward', null, '#image');
			$(window).on('popstate', function() {
				window.location.replace("http://" + base_url + gallery_uri);
			});
		}
	});
</script>