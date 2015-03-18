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

class Gallery extends MX_Controller {
	
	private $gallery_base_path = "./assets/img/gallery";
	
	function __construct() {
		parent::__construct();
	}

    function index() {		
		// Specify page, modules and methods to run to populate view for the gallery main page
		$data['page'] = "gallery";
        $data['module'][] = "gallery";
        $data['method'][] = "gallery_default";
		
		$this->load->model("gallery/mdl_gallery");
		$this->mdl_gallery->set_table("gallery");
		$query = $this->mdl_gallery->get("id");
		foreach ($query->result() as $row)
		{
			$data['gallery_id'][] = $row->id;
			$data['gallery_name'][] = $row->gallery_name;
			$data['gallery_table'][] = $row->gallery_table;
			$data['gallery_thumbnail'][] = $row->gallery_thumbnail;
		}
		
		// Specify the javascript files to be used
		$data['javascript_view'][] = "gallery/gallery_javascript";
		
		//specify the template and insert module and view_file to be used
        echo Modules::run('templates/template_default', $data);
    }
	
	function gallery_default() {
		$this->load->view("gallery/gallery_default");
	}
	
	function show_gallery() {
		// $limit is the amount of thumbnails shown per page
		$limit = 12;
		$gallery_table = $this->uri->segment(2);
		
		// If the table requested doesn't exist then show 404
		if(!$this->db->table_exists($gallery_table))
		{
			show_404();
			exit;
		}
		
		// Check if a page was requested. If not formatted page_[integer] then show_404();
		if(strlen($this->uri->segment(3)) > 0)
		{
			$page_requested = $this->uri->segment(3);
			if(preg_match('/^page_[0-9]+$/', $page_requested))
			{
				// Set offset to only get results for the requested page
				$offset_page = str_replace("page_","",$page_requested);
				$offset = ($offset_page * $limit) - $limit;
			}
			else
			{
				show_404();
				exit;
			}
		}
		else
		{
			$offset_page = 1;
			$offset = 0;
		}
		
		// Specify page, modules and methods to run to populate view for the gallery
		$data['page'] = "gallery";
        $data['module'][] = "gallery";
        $data['method'][] = "gallery_display";
		
		// Load model and set table
		$this->load->model("gallery/mdl_gallery");
		$this->mdl_gallery->set_table($gallery_table);
		
		// Gets the total amount of images from the database then calculates how many pages there
		// should be.
		$total_images_images_album = $this->mdl_gallery->count_all();
		$total_number_pages = $total_images_images_album / $limit;
		if(is_float($total_number_pages))
		{
			$total_number_pages = ceil($total_number_pages);
		}
		
		// Pass page information through $data array
		$data['total_pages'] = $total_number_pages;
		$data['current_page'] = $offset_page;
		
		// Gets all images for current page
		$query = $this->mdl_gallery->get_with_limit($limit, $offset, 'id');
		$data['gallery_table'] = $gallery_table;
		foreach($query->result() as $row)
		{
			$data['image_id'][] = $row->id;
			$data['image_name'][] = $row->image;
			$data['image_caption'][] = $row->image_caption;
		}
		
		// Specify the javascript files to be used
		$data['javascript_view'][] = "gallery/gallery_javascript";
		
		// Creates dynamic and unique meta information depending on the gallery name
		$data['meta_title'] = "Photo Album - ".ucwords(str_replace("-"," ",$data['gallery_table']));
		$data['meta_description'] = "All of the photos from the album ".str_replace("-"," ",$data['gallery_table']).".";
		
		echo Modules::run('templates/template_default', $data);
	}
	
	function gallery_display() {
		$this->load->view("gallery/gallery_display");
	}
	
	function show_image() {
		// Get gallery table and image id from uri
		$gallery_table = $this->uri->segment(2);
		$image_id = explode("-", $this->uri->segment(3));
		$image_id = $image_id[0];
		$data['this_image_id'] = $image_id;
		
		// If the table doesn't exist then show 404
		if(!$this->db->table_exists($gallery_table))
		{
			show_404();
			exit;
		}
		
		$data['gallery_table'] = $gallery_table;
		
		// Load model and set table
		$this->load->model("gallery/mdl_gallery");
		$this->mdl_gallery->set_table($gallery_table);
		
		// Gets all image id's and stores them in an array.
		$query = $this->mdl_gallery->get("id");
		foreach($query->result() as $row)
		{
			$all_image_id[] = $row->id;
		}
		
		// If id exists then continue otherwise show 404
		if(in_array($image_id, $all_image_id))
		{
			// Limit the amount of thumbnails shown underneath image
			$limit = 8;
			
			// Count all images and get the index of the current image
			$image_count = count($all_image_id);
			$image_position = array_search($image_id, $all_image_id);
			
			// Determines where the current thumbnail appears on the strip
			if($image_count <= $limit)
			{
				$offset = 0;
			}
			else
			{
				if(($image_position + 1) <= ($limit / 2))
				{
					$offset = 0;
				}
				else if($image_position >= ($image_count - ($limit / 2)))
				{
					$offset = $image_count - $limit;
				}
				else
				{
					$offset = ($image_position + 1) - ($limit / 2);
				}
			}
			
			// Get image position for next previous navigation in view
			if($image_position === 0)
			{
				$data['image_position'] = 'first';
			}
			else if($image_position === ($image_count - 1))
			{
				$data['image_position'] = 'last';
			}
			else
			{
				$data['image_position'] = 'middle';
			}
			
			// Get all thumbnails to be shown
			$query = $this->mdl_gallery->get_with_limit($limit, $offset, 'id');
			foreach($query->result() as $row)
			{
				$data['image_id'][$row->id] = $row->id;
				$data['image_name'][$row->id] = $row->image;
				$data['image_caption'][$row->id] = $row->image_caption;
			}
		}
		else
		{
			show_404();
			exit;
		}
		
		// If the image has a caption then use it as part of the meta information tags otherwise
		// use a generic one based on the gallery name
		if(strlen($data['image_caption'][$image_id]) > 0)
		{
			$data['meta_title'] = "{$data['image_caption'][$image_id]} from photo album named ".str_replace("-"," ",$data['gallery_table']);
			$data['meta_description'] = "A view of the photo titled {$data['image_caption'][$image_id]} from the album called ".str_replace("-"," ",$data['gallery_table']).".";
		}
		else
		{
			$data['meta_title'] = "A photo from album ".ucwords(str_replace("-"," ",$data['gallery_table']));
			$data['meta_description'] = "A view of a photo from the album called ".str_replace("-"," ",$data['gallery_table']).".";
		}
		
		// Specify which method to run for loading the view
        $data['module'][] = "gallery";
        $data['method'][] = "gallery_image_display";
		
		$data['javascript_view'][] = "gallery/gallery_javascript_keyboard_bindings";
		
		// Load the gallery view this time
		echo Modules::run('templates/template_gallery', $data);
	}
	
	function gallery_image_display() {
		$this->load->view("gallery/gallery_image_display");
	}
}
