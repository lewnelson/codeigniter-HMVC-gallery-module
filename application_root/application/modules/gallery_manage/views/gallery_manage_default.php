<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$base_url = base_url();
echo "<h2>gallery information</h2>";

if(isset($manually_deleted_galleries))
{
	echo "<p>the following galleries have been removed because their respective directories no longer exist.</p>";
	echo "<ul>";
	foreach($manually_deleted_galleries as $deleted_gallery)
	{
	echo "<li>{$deleted_gallery} - {$manually_deleted_galleries_name[$deleted_gallery]}</li>";
	}
	echo "</ul>";
}

echo "<h3>new galleries</h3>";

// If any new galleries were found
if(isset($new_galleries))
{
	echo "<table>";
	echo "<tr>";
	echo "<th>gallery</th>";
	echo "<th>options</th>";
	echo "</tr>";
	foreach ($new_galleries as $gallery)
	{
		echo "<tr>";
		echo "<td>{$gallery}</td>";
		echo "<td><a href='{$base_url}gallery_manage/add_gallery/{$gallery}'>add gallery</a></td>";
		echo "</tr>";
	}
	echo "</table>";
}
else
{
	echo "<p>no new galleries were found</p>";
}


echo "<h3>existing galleries</h3>";

// If any existing galleries were found
if(isset($existing_galleries))
{
	echo "<table>";
	echo "<tr>";
	echo "<th>gallery</th>";
	echo "<th>gallery name</th>";
	echo "<th>total images</th>";
	echo "<th>options</th>";
	echo "</tr>";
	foreach ($existing_galleries as $gallery)
	{
		echo "<tr>";
		echo "<td>{$gallery}</td>";
		echo "<td>{$existing_galleries_name[$gallery]}</td>";
		echo "<td>{$existing_galleries_image_count[$gallery]}</td>";
		echo "<td><a href='{$base_url}gallery_manage/edit_gallery/{$gallery}'>edit gallery</a></td>";
		echo "</tr>";
	}
	echo "</table>";
}
else
{
	echo "<p>you have no existing galleries</p>";
}


echo "<h3>deleted galleries</h3>";

// If any galleries are marked for deletion
if(isset($deleted_galleries))
{
	echo "<table>";
	echo "<tr>";
	echo "<th>gallery</th>";
	echo "<th>gallery name</th>";
	echo "<th>options</th>";
	echo "</tr>";
	foreach ($deleted_galleries as $gallery)
	{
		echo "<tr>";
		echo "<td>{$gallery}</td>";
		echo "<td>{$deleted_galleries_name[$gallery]}</td>";
		echo "<td><a href='{$base_url}gallery_manage/purge_gallery/{$gallery}'>remove gallery</a> <a href='{$base_url}gallery_manage/restore_gallery/{$gallery}'>restore gallery</a></td>";
		echo "</tr>";
	}
	echo "</table>";
}
else
{
	echo "<p>you haven't got any deleted galleries available for recovery</p>";
}

?>