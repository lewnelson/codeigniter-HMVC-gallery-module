<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// Lazy load image placeholder
$lazy_load_image = "/assets/img/generic/image-loading.gif";

echo "<div class='gallery-container'>";

echo "<h2>photo albums</h2>";

foreach($gallery_id as $index=>$value)
{
	echo "<div class='gallery-album-index'>";
	echo "<a href='/gallery/{$gallery_table[$index]}'>";
	echo "<div class='gallery-album-overlay-index'>";
	echo "<h3><span class='gallery-album-overlay-text-index'>{$gallery_name[$index]}</span></h3>";
	echo "</div>";
	echo "<img src='{$lazy_load_image}' data-original='/assets/img/gallery/{$gallery_table[$index]}/thumbnails/tn-{$gallery_thumbnail[$index]}' alt='thumbnail image for photo album {$gallery_name[$index]}' class='gallery-album-thumbnail-index lazy-load-image' />";
	echo "<noscript><img src='/assets/img/gallery/{$gallery_table[$index]}/thumbnails/tn-{$gallery_thumbnail[$index]}' class='gallery-album-thumbnail-index' alt='thumbnail image for photo album {$gallery_name[$index]}' /></noscript>";
	echo "<div class='gallery-name-touchscreen-index'>{$gallery_name[$index]}</div>";
	echo "</a>";
	echo "</div>";
}

echo "</div>";

?>