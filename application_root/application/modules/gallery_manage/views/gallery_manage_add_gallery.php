<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$base_url = base_url();

if($no_gallery === TRUE)
{
	echo "<p>this gallery doesn't exist</p>";
}
else
{
	echo "<p>gallery exists</p>";
	
	if($no_image_directory === TRUE)
	{
		echo "<p>all images must be placed inside a directory called images to be scanned into the database automatically</p>";
	}
	else
	{
		echo "<p>gallery contains a directory called images</p>";
	}
}

?>