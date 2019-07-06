<?php
	
	//configure the database connection, language and client's name
	$config['db_host'] = '';
	$config['db_name'] = '';
	$config['db_user'] = '';
	$config['db_pass'] = '';
	$config['error_messages'] = array(
									'connection' => $lang['connection'],
									'query' => $lang['query']
								);

	$config['language'] = 'en';
	$config['domain'] = $_SERVER['HTTP_HOST'];

	//upload folder
	$config['app_dir'] = dirname(dirname(__FILE__));
	$config['upload_dir'] = $config['app_dir'] . '/images/';
	
	//navigation - This should be according the existing pages
	$config['index_page'] = 'index.php';
	$config['nav'] = array(
							$lang['nav_home'],
							$lang['nav_share']
						);

	//styles and scripts
	$config['css'] = array('styles/reset.css', 'styles/global.css');

	//configure templates
	$headerTmp = 'templates/tl.header.html';	
	$footerTmp = 'templates/tl.footer.html';
	$contentTmp = 'templates/tl.content.html';	
	$formTmp = 'templates/tl.upload-form.html';

	//global variables
	$title = ''	;
	$main = '';

	//make sure time zone is same as in the server
	date_default_timezone_set('Europe/London');

?>