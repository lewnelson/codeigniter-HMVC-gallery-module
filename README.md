# codeigniter-HMVC-gallery-module
A PHP photo gallery module for wiredesignz CodeIgniter Modular Extensions


This is a PHP gallery module for Wiredesignz Modular Extensions for CodeIgniter.

##**FEATURES (planned and completed)**

- **Admin panel (gallery_manage)**
  * Scan and add galleries.
  * Update existing gallery data (change name, change main thumbnail).
  * Scan and add/delete images in existing gallery.
  * Delete galleries.
  * Delete images from galleries.
  * Create thumbnails.
  * Restore deleted galleries.
  * Purge deleted galleries.
  * Multi-user login to manage their own galleries.
  * Admin account to control all galleries.
- **Gallery view (gallery)**
  * View all galleries with thumbnail in a grid.
  * View all images inside gallery with thumbnails in a grid.
  * View image full size as a gallery with a filmstrip of thumbnails underneath.
- **Upload module (TBD)**
  * Add image uploading ability.
  * Integrate with existing methods from gallery_manage to populate database and create thumbnails.

##**INCOMPLETE**

This project is still incomplete. There are still several features which need to be added before the application is functional.


###**OVERVIEW**

A PHP gallery which is designed for displaying multiple photo albums by thumbnails then displaying a final view of the selected photo. This gallery works without any javascript, however I have included some javascript only for navigating the final view of the gallery.

###**REQUIREMENTS**

*All the included plugins/extensions/files are included.*

####**Requirements List**
* CodeIgniter (I have included v2.2. Not been tested with any other versions).
* Wiredesignz Modular Extensions plugin for CodeIgniter.
* jQuery (only if you wish to use javascript functionality).

###**SETUP**

####**Default**
If all you want is the module working with a fresh CodeIgniter project then move the contents of full_app to an empty project root directory. I have included a default .htaccess with some basic configuration including the removal of index.php from URI's.
Here are the basic steps to get started.

1. /application/config/config.php
  * Change base_url to match your sites base_url

2. /application/config/database.php
  * Modify the database settings to suite your database.

3. Setup the database tables.
  * Setup the main 'gallery' table which will point to all of your photo album tables. A full structure of the table is available at the top of /application/modules/gallery/controllers/gallery.php.
  * A default .sql database file is provided which is setup for the demo version.
  * Your database user will require SELECT INSERT, UPDATE, DELETE, CREATE and DROP grants.

4. Add your images.
  * Put all of your photo galleries into /assets/img/gallery. Each photo gallery should include a directory called images. All of your pictures should go into images. Only jpg or jpeg images are supported for now.
