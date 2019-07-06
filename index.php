<?php
	
	//Includes require globally
	require_once 'i18n/en.php';
	require_once 'classes/Connection.php';
	require_once 'classes/Template.php';
	require_once 'classes/Upload.php';
	require_once 'classes/Validation.php';
	require_once 'includes/inc.config.php';
	require_once 'includes/inc.functions.php';
	
	
	//Connect to the database
	try {

		$connection = new Connection(
							$config['db_host'],
							$config['db_user'],
							$config['db_pass'],
							$config['db_name'],
							$config['error_messages']
						);

	} catch (Exception $e) {

		echo 'Caught Error: '.$e->getMessage()."\n";

	}	
	
	//Initialise templates
	$header = new Template($headerTmp);
	$footer = new Template($footerTmp);
	$content = new Template($contentTmp);
	$form = new Template($formTmp);
	
		
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	} else {
		$page = 'home';
	}
	
	//Include common areas such as header
	include_once 'includes/inc.header.php';
	include_once 'includes/inc.footer.php';


	switch ($page) {
		case 'home':
			//The home page displays the thumbnails
			include_once 'views/home.php';
			break;
		case 'share':
			include_once 'views/share.php';
			break;
		case 'image':
			include_once 'views/image.php';
			break;
		default:
			include_once 'views/404.php';
			break;
	}
	
	/****************************************
	*  			PAGES ARE BUILD HERE 		*
	*										*
	*		1- Header						*
	*		2- Content						*
	*		3- Footer						*
	*										*
	****************************************/
		
	
	echo $header->getContent();
	echo $content->getContent();
	echo $footer->getContent();
	
	$connection->disconnect();

?>