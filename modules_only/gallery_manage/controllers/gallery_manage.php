<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 |--------------------------------------------------------------------------
 |	CONTROLLER SUMMARY AND DATABASE TABLES
 |--------------------------------------------------------------------------
 | 
 |	Controller uses multiple tables. Hence addition of table property_exists
 |	and function to set table inside mdl_gallery. The reason behind this
 |	is that there are multiple photo albums which require their own table.
 |
 |	Database table structure
 |
 |	Table name - gallery
 |
 |	ai = auto_increment
 |	pk = primary_key
 |	null = value can be set to null if not set assume not null
 |
 |	||==========================================================================================||
 |	|| column				| type (flags)				| description							||
 |	||==========================================================================================||
 |	|| id					| mediumint	(ai, pk)		| id primary_key						||
 |	||----------------------+---------------------------+---------------------------------------||
 |	|| gallery_name			| varchar(128)				| friendly name for gallery/album		||
 |	||----------------------+---------------------------+---------------------------------------||
 |	|| gallery_table		| varchar(128)				| table name for gallery/album			||
 |	||----------------------+---------------------------+---------------------------------------||
 |	|| gallery_thumbnail	| varchar(256)				| gallery thumbnail filename			||
 |	||----------------------+---------------------------+---------------------------------------||
 |	|| image_count			| int						| number of images in the gallery		||
 |	||----------------------+---------------------------+---------------------------------------||
 |	|| deleted				| varchar(5)				| whether or not the gallery is marked	||
 |	||						|							| for deletion set to true or null		||
 |	||==========================================================================================||
 |
 |
 |	Table name - (name is dynamic dependent on gallery selected)
 |
 |	ai = auto_increment
 |	pk = primary_key
 |	null = value can be set to null if not set assume not null
 |
 |	||======================================================================================||
 |	|| column			| type (flags)				| description							||
 |	||======================================================================================||
 |	|| id				| mediumint	(ai, pk)		| id primary_key						||
 |	||------------------+---------------------------+---------------------------------------||
 |	|| image			| varchar(128)				| image filename						||
 |	||------------------+---------------------------+---------------------------------------||
 |	|| image_caption	| varchar(128) null			| user specified image name/description	||
 |	||======================================================================================||
 |
 */

class Gallery_Manage extends MX_Controller {
	
	private $gallery_base_path = "./assets/img/gallery";
	
	function __construct() {
		parent::__construct();
	}

    function index() {
		// Set meta information
		$data['meta_title'] = "Manage your galleries from the admin panel";
		$data['meta_description'] = "Take control of your galleries by adding new photo albums, editing existing albums or deleting galleries.";
		
		// Specify page, modules and methods to run to populate view for the gallery main page
        $data['module'][] = "gallery_manage";
        $data['method'][] = "gallery_manage_default";
		
		// If our scan goes OK then merge our data array with the output otherwise no directories were found
		$data['no_galleries'] = FALSE;
		$gallery_info = $this->scan_all_galleries();
			
		$data = array_merge($data, $gallery_info);
		
		//specify the template and insert module and view_file to be used
        echo Modules::run('templates/template_default', $data);
    }
	
	function gallery_manage_default() {
		$this->load->view("gallery_manage/gallery_manage_default");
	}
	
	function scan_all_galleries() {
		// Scans directory and compares against database to find out if gallery is
		// new, already exists or has been marked for deletion
		
		// Scan the gallery base directory
		$results = scandir($this->gallery_base_path);
		// Filter current and parent directories
		$results = array_diff($results, array('.', '..'));
		
		// Used for counting directories
		$data['directory_count'] = 0;
		
		// If the directory isn't empty
		if(!empty($results))
		{			
			// Get all directories and store them to an array
			foreach($results as $result)
			{
				if(is_dir($this->gallery_base_path . '/' . $result))
				{
					$data['directory_count'] += 1;
					$galleries_directories[] = $result;
				}
			}
		}
		
		// Load model to find all current added galleries
		$this->load->model("gallery_manage/mdl_gallery_manage");
		$this->mdl_gallery_manage->set_table("gallery");
		$query = $this->mdl_gallery_manage->get("id");
		foreach($query->result() as $row)
		{
			// Create/append array for existing galleries and add some additional information on the gallery
			$existing_galleries[] = $row->gallery_table;
			$existing_galleries_name[$row->gallery_table] = $row->gallery_name;
			$existing_gallery_delete_flag[$row->gallery_table] = $row->deleted;
			$existing_galleries_image_count[$row->gallery_table] = $row->image_count;
		}
		
		// If any directories were found
		if(isset($galleries_directories))
		{
			foreach($galleries_directories as $directory)
			{
				// Check if we have any existing galleries
				if(isset($existing_galleries))
				{
					// If the directory is already in our database
					if(in_array($directory, $existing_galleries))
					{
						// If gallery is marked for deletion
						if($existing_gallery_delete_flag[$directory] === "true")
						{
							$data['deleted_galleries'][] = $directory;
							$data['deleted_galleries_name'][$directory] = $existing_galleries_name[$directory];
						}
						else
						{
							$data['existing_galleries'][] = $directory;
							$data['existing_galleries_name'][$directory] = $existing_galleries_name[$directory];
							$data['existing_galleries_image_count'][$directory] = $existing_galleries_image_count[$directory];
						}
					}
					else
					{
						// Must be a new gallery
						$data['new_galleries'][] = $directory;
					}
				}
				else
				{
					// If we have no existing galleries then any directories must be new galleries
					$data['new_galleries'][] = $directory;
				}
			}
		}
		
		// Check for any directories that were manually removed after being used in our database
		if(isset($existing_galleries))
		{
			// Remove galleries from database that no longer have their directory
			foreach($existing_galleries as $existing_gallery)
			{
				// Only remove galleries that are not in the directory. If the directory
				// is empty then remove all the galleries in the database.
				if(isset($galleries_directories))
				{
					// If it isn't in our directory array then it must have been manually removed
					// Manual removal leads to automatic database removal.
					if(!in_array($existing_gallery, $galleries_directories))
					{
						// Delete the gallery from the database
						// Just make sure we are using the correct table
						$this->mdl_gallery_manage->set_table("gallery");
						$this->mdl_gallery_manage->delete_where("gallery_table", $existing_gallery);
						
						// Drop the galleries own table
						$this->load->dbforge();
						$this->dbforge->drop_table($existing_gallery);
						
						// Pass the deleted gallery name for viewing
						$data['manually_deleted_galleries'][] = $existing_gallery;
						$data['manually_deleted_galleries_name'][$existing_gallery] = $existing_galleries_name[$existing_gallery];
					}
				}
				else
				{
					// Delete the gallery from the database
					// Just make sure we are using the correct table
					$this->mdl_gallery_manage->set_table("gallery");
					$this->mdl_gallery_manage->delete_where("gallery_table", $existing_gallery);
					
					// Drop the galleries own table
					$this->load->dbforge();
					$this->dbforge->drop_table($existing_gallery);
					
					// Pass the deleted gallery name for viewing
					$data['manually_deleted_galleries'][] = $existing_gallery;
					$data['manually_deleted_galleries_name'][$existing_gallery] = $existing_galleries_name[$existing_gallery];
				}
			}
		}

		// Return all the directory/gallery data
		return $data;
	}
	
	function add_gallery($gallery) {
		// Set meta information
		$data['meta_title'] = "Add a new gallery";
		$data['meta_description'] = "Add a new gallery to the database.";
		
		// Specify page, modules and methods to run to populate view for the gallery main page
        $data['module'][] = "gallery_manage";
        $data['method'][] = "gallery_manage_add_gallery";
		
		$directory_data = $this->update_gallery_add($gallery);
		
		$data = array_merge($data, $directory_data);
		
        echo Modules::run('templates/template_default', $data);
    }
	
	function gallery_manage_add_gallery() {
		$this->load->view("gallery_manage/gallery_manage_add_gallery");
	}
	
	function update_gallery_add($gallery) {
		// Set the gallery path
		$gallery_path = $this->gallery_base_path . "/" . $gallery;
		$gallery_image_path = $gallery_path . "/images/";
		$gallery_thumbnail_path = $gallery_path . "/thumbnails/";
		
		if(is_dir($gallery_path))
		{
			$data['no_gallery'] = FALSE;
			
			if(is_dir($gallery_image_path))
			{
				$data['no_image_directory'] = FALSE;
				
				if($this->get_image_files($gallery_image_path))
				{
					$database_insert = $this->get_image_files($gallery_image_path);
				}
			}
			else
			{
				$data['no_image_directory'] = TRUE;
			}
		}
		else
		{
			$data['no_gallery'] = TRUE;
		}
		
		return $data;
	}
	
	function get_image_files($gallery_image_path) {		
		// Get all files inside images
		$image_files = get_filenames($gallery_image_path);
		
		if(!empty($image_files))
		{
			// Find all .jpg or .jpeg files
			foreach($image_files as $index=>$file)
			{
				$file_type = get_mime_by_extension($file);
				
				if($file_type === "image/jpeg")
				{
					$image_data = exif_read_data($gallery_image_path . $file);
					if(isset($image_data['FileDateTime']))
					{
						$image_timestamp = $image_data['FileDateTime'];
						
						$database_insert[$index]['timestamp'] = $image_timestamp;
					}
					
					$database_insert[$index]['image'] = $file;
				}
			}
		}
		else
		{
			return FALSE;
			exit;
		}
		
		if(!isset($database_insert))
		{
			return FALSE;
			exit;
		}
		
		return $database_insert;
	}
	
	function insert_gallery_into_database($gallery_path, $gallery, $data) {
		$this->load->dbforge();
		
		$this->load->model("gallery_manage/mdl_gallery_manage");
		$this->mdl_gallery_manage->set_table($gallery);
		
		// Strip file extension and add _thumb
		foreach($data as $key=>$value)
		{
			$file_name = $data[$key]['image'];
			$extension = strrchr($file_name, ".");
			$thumbnail_file_name = str_replace($extension, "", $file_name);
			$thumbnail_file_name = $thumbnail_file_name . "_thumb" . $extension;
			$thumbnail_images[] = $thumbnail_file_name;
		}
		
		// If it is a new gallery then create a table
		if(!($this->db->table_exists($gallery)))
		{
			$table_fields = array(
				'id' => array(
					'type' => 'MEDIUMINT',
					'auto_increment' => TRUE
				),
				'image' => array(
					'type' => 'varchar',
					'constraint' => 256
				),
				'timestamp' => array(
					'type' => 'varchar',
					'constraint' => 15,
					'null' => TRUE
				)
			);
			
			$this->dbforge->add_field($table_fields);
			$this->dbforge->add_key('id', TRUE);
			$this->dbforge->create_table($gallery);
			$this->mdl_gallery_manage->insert_batch($data);
			
			$this->mdl_gallery_manage->set_table("gallery");
			$gallery_name = str_replace("_", " ", $gallery);
			$gallery_thumbnail = $thumbnail_images[0];
			$new_table = array("gallery_name" => $gallery_name,
				"gallery_table" => $gallery,
				"gallery_thumbnail" => $gallery_thumbnail,
				"image_count" => count($data)
			);
			$this->mdl_gallery_manage->_insert($new_table);
		}	
		else
		{
			$query = $this->mdl_gallery_manage->get("id");
			foreach($query->result() as $row)
			{
				$existing_images[] = $row->image;
			}
			
			if(isset($existing_images))
			{
				foreach($data as $index=>$key)
				{
					if(in_array($data[$index]['image'], $existing_images))
					{
						unset($data[$index]);
					}
				}
			}
			
			if(!empty($data))
			{
				$this->mdl_gallery_manage->insert_batch($data);
				"new images were added";
			}
			else
			{
				return "no new images were added";
			}
		}
		
		$this->create_thumbnails($gallery, $gallery_path, $data);
	}
	
	function create_thumbnails($gallery, $gallery_path, $images) {
		if(empty($images))
		{
			exit;
		}
		
		$thumbnail_gallery_path = str_replace("/images/", "/thumbnails/", $gallery_path);
		
		if(!is_dir($thumbnail_gallery_path))
		{
			mkdir($thumbnail_gallery_path, 0755);
		}
		
		$thumbnails = get_filenames($thumbnail_gallery_path);
		
		foreach($images as $key=>$index)
		{
			$this->create_the_thumbnail($images[$key]['image'], $thumbnail_gallery_path, $gallery_path."/".$images[$key]['image']);
		}
	}
	
	function create_the_thumbnail($image, $target, $source) {		
		if(!is_dir($target))
		{
			mkdir($target, 0755);
		}
	
		$thumb_config = array(
			'image_library' => 'gd2',
			'source_image' => $source,
			'new_image' => $target.$image,
			"maintain_ratio" => TRUE,
			'create_thumb' => TRUE,
			'width' => 200,
			'height' => 200
		);
		$this->load->library('image_lib');
		$this->image_lib->initialize($thumb_config);
		if(!$this->image_lib->resize())
		{
			echo $this->image_lib->display_errors();
		}
		
		$this->image_lib->clear();
	}
}
