<?php

// If access is requested from anywhere other than index.php then exit
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 |--------------------------------------------------------------------------
 |	CONTROLLER SUMMARY AND DATABASE TABLES
 |--------------------------------------------------------------------------
 | 
 |	Templates is used to put together the main structure of the HTML view. It
 |	calls head, header, content and footer in most cases. Other items can been
 |	called and used. Each part can be dynamic but content is loaded through
 |	modules and methods.
 |
 |	Database table structure
 |
 |	No table
 |
 */

class Templates extends MX_Controller
{
	private $meta_module;

	function __construct() {
		parent::__construct();
	}

	function template_default($data) {
		
		// Assign meta information to array meta
		$meta['meta_title'] = $data['meta_title'];
		$meta['meta_description'] = $data['meta_description'];
		
		// Loads in the head template
		$this->load->view('templates/template_default_head', $meta);

		// Loads in the content header, content and footer
		$this->load->view('templates/template_default_header', $data);
		$this->load->view('templates/template_default_content', $data);
		$this->load->view('templates/template_default_footer', $data);
		
	}

	function template_gallery($data) {
		// Run method to get meta info from database if meta info hasn't been defined
		if(!isset($data['meta_title']))
		{
			// Run meta_module to retrieve meta data. Pass parameter $data['page'] as page
			$meta = Modules::run($this->meta_module, $data['page']);
		}
		else
		{
			// Assign meta information to array meta
			$meta['meta_title'] = $data['meta_title'];
			$meta['meta_description'] = $data['meta_description'];
		}

		// Load 
		$this->load->view('templates/template_default_head', $meta);
		
		//load the views requested
		$this->load->view('template_gallery_content', $data);
		$this->load->view('template_default_footer', $data);
	}
}
