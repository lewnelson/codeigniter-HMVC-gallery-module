<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


echo "<div class='gallery-full-container'>";

// Turns gallery table into friendly name
$gallery_table = $this->uri->segment(2);
$gallery_name = str_replace("-", " ", $gallery_table);

// Lazy image loader placeholder image
$lazy_load_image = "/assets/img/generic/image-loading.gif";

// gets the current image with no extension to be used for naming
$current_image_name_no_extension = pathinfo(str_replace("tn-","",$image_name[$this_image_id]), PATHINFO_FILENAME);

//Convert to integer for === comparison otherwise if this_image_id is 1 or 0 could get dodgy results
//with 1 or 0 resolving to TRUE of FALSE
$this_image_id = intval($this_image_id);

$i = 0;
$current_image_reindex = '';
$reindexed_image_array = array();

foreach($image_id as $index=>$value)
{
	if($index === $this_image_id)
	{
		$current_image_reindex = $i;
	}
	$reindexed_image_array[] = $index;
	$i = $i + 1;
}

if($image_position === 'first')
{
	$next_image_id = $reindexed_image_array[$current_image_reindex + 1];
	$next_image_name = pathinfo(str_replace("tn-","",$image_name[$next_image_id]), PATHINFO_FILENAME);
	$previous_image = FALSE;
	$next_image = TRUE;
}
else if($image_position === 'last')
{
	$previous_image_id = $reindexed_image_array[$current_image_reindex - 1];
	$previous_image_name = pathinfo(str_replace("tn-","",$image_name[$previous_image_id]), PATHINFO_FILENAME);
	$previous_image = TRUE;
	$next_image = FALSE;
}
else
{
	$next_image_id = $reindexed_image_array[$current_image_reindex + 1];
	$next_image_name = pathinfo(str_replace("tn-","",$image_name[$next_image_id]), PATHINFO_FILENAME);
	$previous_image_id = $reindexed_image_array[$current_image_reindex - 1];
	$previous_image_name = pathinfo(str_replace("tn-","",$image_name[$previous_image_id]), PATHINFO_FILENAME);
	$previous_image = TRUE;
	$next_image = TRUE;
}

echo "<a href='/gallery/{$gallery_table}'><div class='gallery-back-to-link-view'>(esc)</div></a>";

echo "<div class='gallery-viewport-container' style=\"background-image: url('/assets/img/gallery/".$gallery_table."/images/".$image_name[$this_image_id]."');\">";

if($previous_image === TRUE)
{
	echo "<a href='/gallery/{$gallery_table}/{$image_id[$previous_image_id]}-{$previous_image_name}' class='gallery-previous-image-allow-container'>";
	echo "<div class='gallery-previous-image-allow'>";
	echo "<p>&lt;</p>";
	echo "</div>";
	echo "</a>";
}
else
{
	echo "<div class='gallery-previous-image-allow-container'>";
	echo "<p>&lt;</p>";
	echo "</div>";
}

if($next_image === TRUE)
{
	echo "<a href='/gallery/{$gallery_table}/{$image_id[$next_image_id]}-{$next_image_name}' class='gallery-next-image-allow-container'>";
	echo "<div class='gallery-next-image-allow'>";
	echo "<p>&gt;</p>";
	echo "</div>";
	echo "</a>";
}
else
{
	echo "<div class='gallery-next-image-allow-container'>";
	echo "<p>&gt;</p>";
	echo "</div>";
}

echo "</div>";


echo "<div class='gallery-thumbnail-strip'>";
foreach($image_id as $index=>$value)
{
	$image_name_no_extension = pathinfo(str_replace("tn-","",$image_name[$index]), PATHINFO_FILENAME);
	
	if(strlen($image_caption[$index]) > 0)
	{
		$alt = $image_caption[$index];
	}
	else
	{
		$alt = "{$gallery_name} - {$image_name_no_extension}";
	}
	
	if($index === $this_image_id)
	{
		echo "<div class='gallery-image-gallery-thumbnail-container-current'>";
		echo "<img src='{$lazy_load_image}' data-original='/assets/img/gallery/{$gallery_table}/thumbnails/tn-{$image_name[$index]}' class='gallery-image-gallery-thumbnail-current lazy-load-image' alt=\"{$alt}\" />";
		echo "<noscript><img src='/assets/img/gallery/{$gallery_table}/thumbnails/tn-{$image_name[$index]}' class='gallery-image-gallery-thumbnail-current' alt=\"{$alt}\" /></noscript>";
		echo "</div>";
	}
	else
	{
		echo "<a href='/gallery/{$gallery_table}/{$image_id[$index]}-{$image_name_no_extension}'>";
		echo "<div class='gallery-image-gallery-thumbnail-container'>";
		echo "<img src='{$lazy_load_image}' data-original='/assets/img/gallery/{$gallery_table}/thumbnails/tn-{$image_name[$index]}' class='gallery-image-gallery-thumbnail lazy-load-image' alt=\"{$alt}\" />";
		echo "<noscript><img src='/assets/img/gallery/{$gallery_table}/thumbnails/tn-{$image_name[$index]}' class='gallery-image-gallery-thumbnail' alt=\"{$alt}\" /></noscript>";
		echo "</div>";
		echo "</a>";
	}
}
echo "</div>";

?>
<div id="gallery-footer-container">
	<div id="gallery-footer">
		<p>lewisandlauraswedding.com &#169; 2015 <?php echo date("Y"); ?> | Site designed by <a href="http://lewnelson.com" target="_blank">Lewis Nelson</a></p>
	</div>
</div>
<?php

echo "</div>";

?>