------------------------------------------------------------------------
THE GALLERY WEB APP
------------------------------------------------------------------------
This is an image upload which creates a thumbnail and a medium size of the original image


Configuration:
-------------------
To change any configuration go to 'includes/inc.config.php'. The database connection and the site language details can be modified in here. The following array shows where you should add the data:

$config['db_host'] = 'the host name or url here';
$config['db_name'] = 'the database name here';
$config['db_user'] = 'the user name here';
$config['db_pass'] = 'the password here';
$config['language'] = 'the language here'; // Please follow the convention explain in Lang

Add any style in $config['css'].

Add any new template in this file if you would like to use the Template class. The following explains how to do this:
		
$tmp = 'add the template location';

------------------------------------------------------------------------
########################################################################
------------------------------------------------------------------------

FILE TREE:
----------
This app is located at the url '' and it contains the following structure of directories and files:

- classes: 										
	- Connection.php
	- Image.php
	- Template.php
	- Upload.php
	- Validation.php

- i18n: 										
	-en.php	

- images: 										(make sure permissions are set to 777)
	- medium: 									(make sure permissions are set to 777)
		- *_medium.jpg
	- thum: 									(make sure permissions are set to 777)
		- *_thumb.jpg
	- *.jpg

- includes:
	- inc.config.php
	- inc.footer.php
	- inc.function.php
	- inc.header.php

- scripts: 										
	- create-table.sql

- styles
	- global.css
	- reset.css

- templates
	- tl.content.html
	- tl.footer.html
	- tl.header.html
	- tl.upload.html

- view
	- 404.php
	- home.php
	- image.php
	- share.php

- web-service									
	- thegallery.php
	
- index.php


classes:
--------
All classes are well commented and explain every method. Please refer to the desired Class file find how to use them.
These classes are well encapsulated and you should know how to use classes in php for facility.

i18n:
-----
Internationalisation is currently with one language only, but, it is ready to work throughout the site with any other language.
If a new language is require just name it according to 'ISO 639-1' standardised nomenclature and set it in the config file under $config['language'] array key.

images:				NOTE - this and any subfolder must have full permission to be overwritten
-------				-------------------------------------------------------------------------
Some images are already uploaded to provide a live example. The original images are saved in this folder and the images created by the application are added in the subfolders.
The images folder must contain a medium and a thumb folder with full permission (777), otherwise the Image class will not be able to create new images and will throw errors for debugging

includes:
--------
Find here, the include files that make the header and the footer. The config file and some functions that are used to make the code reusable

scripts:
--------
The script included here must be run before you are able to use the web-service or all the main functionalities of this app. Also, it should be run if the app is deploy to a different environment
This query creates the image table with basic info of the image.

styles:
-------
There are 2 css files here, one that should reset some of the browser defaults styles and a global style for general styling.

Templates:
---------
All the template are located in this folder. The templates contain placeholder such as {{content}}, which can be replaced with content. They can be used with the Template class, which will replace placeholder. Please look at the classes information or are the classes comment for more details.

Views:
------
These views are located in the views directory and are name according to its page name, plus a 404 view to display any error pages. All queries to the database are done here.

web-services:
-------------
For simplicity the web service displays all images in the database. It only requires the 'image' parameter and it can be access from:









