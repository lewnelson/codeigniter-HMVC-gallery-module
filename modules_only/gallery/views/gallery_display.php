<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


echo "<div class='gallery-container'>";

// Create friendly name from the gallery table
$gallery_name = str_replace("-", " ", $this->uri->segment(2));

echo "<h3>{$gallery_name}</h3>";

// Displays each image thumbnail with the overlay
foreach($image_id as $index=>$value)
{
	echo "<div class='gallery-album'>";
	echo "<a href='/gallery/{$gallery_table}/{$image_id[$index]}-".pathinfo(str_replace("tn-","",$image_name[$index]), PATHINFO_FILENAME)."'>";
	if(strlen($image_caption[$index]) > 0)
	{
		echo "<div class='gallery-album-overlay'>";
		echo "<h3><span class='gallery-album-overlay-text'>{$image_caption[$index]}</span></h3>";
		echo "</div>";
		echo "<img src='/assets/img/generic/image-loading.gif' data-original='/assets/img/gallery/{$gallery_table}/thumbnails/tn-{$image_name[$index]}' class='gallery-album-thumbnail lazy-load-image' alt=\"thumbnail for image {$image_caption[$index]}\" />";
		echo "<noscript>img src='/assets/img/gallery/{$gallery_table}/thumbnails/tn-{$image_name[$index]}' class='gallery-album-thumbnail' alt=\"thumbnail for image {$image_caption[$index]}\" /></noscript>";
	}
	else
	{
		echo "<img src='/assets/img/generic/image-loading.gif' data-original='/assets/img/gallery/{$gallery_table}/thumbnails/tn-{$image_name[$index]}' alt=\"thumbnail for image {$image_id[$index]}\" class='gallery-album-thumbnail lazy-load-image' />";
		echo "<noscript>img src='/assets/img/gallery/{$gallery_table}/thumbnails/tn-{$image_name[$index]}' class='gallery-album-thumbnail' alt=\"thumbnail for image {$image_id[$index]}\" /></noscript>";
	}
	echo "</a>";
	echo "</div>";
}

// Page information
$current_page = intval($current_page);
$total_pages = intval($total_pages);

// Renders page navigation and back to albums link
if($total_pages > 1)
{
	// If we are on a page after one then show the previous page link
	echo "<div class='gallery-page-links'>";
	if($current_page > 1)
	{
		echo "<span class='gallery-page-link-previous-page'><a href='/gallery/{$gallery_table}/page_".($current_page - 1)."'>previous</a></span>";
	}
	else
	{
		echo "<span class='gallery-page-link-previous-page'></span>";
	}
	
	// This displays what page we are on and shows the available pages in a range
	echo "<div class='gallery-page-link-number-container'>";
	$page_range = 7;
	if($total_pages > $page_range)
	{
		$half_up = ceil($page_range / 2);
		$half_down = floor($page_range / 2);
		if($current_page <= $half_up)
		{
			for($i = 1; $i <= $page_range; $i++)
			{
				if($i === $current_page)
				{
					echo "<span class='gallery-page-link-number'>{$i}</span>";
				}
				else
				{
					echo "<span class='gallery-page-link-number'><a href='/gallery/{$gallery_table}/page_{$i}'>{$i}</a></span>";
				}
			}
		}
		else if($current_page > ($total_pages - $half_up))
		{
			for($i = ($total_pages - ($page_range - 1)); $i <= $total_pages; $i++)
			{
				if($i === $current_page)
				{
					echo "<span class='gallery-page-link-number'>{$i}</span>";
				}
				else
				{
					echo "<span class='gallery-page-link-number'><a href='/gallery/{$gallery_table}/page_{$i}'>{$i}</a></span>";
				}
			}
		}
		else
		{
			for($i = ($current_page - $half_down); $i <= ($current_page + $half_down); $i++)
			{
				if($i == $current_page)
				{
					echo "<span class='gallery-page-link-number'>{$i}</span>";
				}
				else
				{
					echo "<span class='gallery-page-link-number'><a href='/gallery/{$gallery_table}/page_{$i}'>{$i}</a></span>";
				}
			}
		}
	}
	else
	{
		for($i = 1; $i <= $total_pages; $i++)
		{
			if($i === $current_page)
			{
				echo "<span class='gallery-page-link-number'>{$i}</span>";
			}
			else
			{
				echo "<span class='gallery-page-link-number'><a href='/gallery/{$gallery_table}/page_{$i}'>{$i}</a></span>";
			}
		}
	}
	echo "</div>";
	if($current_page < $total_pages)
	{
		echo "<span class='gallery-page-link-next-page'><a href='/gallery/{$gallery_table}/page_".($current_page + 1)."'>next</a></span>";
	}
	else
	{
		echo "<span class='gallery-page-link-next-page'></span>";
	}
	echo "</div>";
}

echo "<div class='gallery-back-to-link'><a href='/gallery'>Back To Albums</a></div>";

echo "</div>";

?>