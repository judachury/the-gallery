<?php
	require_once '../classes/Connection.php';

	//make sure the image parameter is present
	if (isset($_GET['image'])) {	

		//configure the db detail to get the queries
		$config['db_host'] = '';
		$config['db_name'] = '';
		$config['db_user'] = '';
		$config['db_pass'] = '';
		$config['language'] = 'en';
		$config['company'] = 'The Gallery';
		$config['domain'] = $_SERVER['HTTP_HOST'];
		//error messages for i18n or an empty array for default language - English
		$config['error_messages'] = array();
		
		//connect to the db or catch an error
		try {

			$connection = new Connection($config['db_host'],  $config['db_user'],  $config['db_pass'], $config['db_name'], $config['error_messages']);	

		} catch (Exception $e) {

			echo $e.getMessage();

		}

		//make the query to gather all the images
		$query = "SELECT i.title, i.description, i.image_width as width, i.image_height as height FROM image i;";

		try {

			$result = $connection->querying($query);

		} catch (Exception $e) {

			echo $e.getMessage();

		}

		//loop and get output
		$images = array();
		while ($rows = $result->fetch_assoc()) {
			$images[] = array('image' => $rows);
		}

		// Send appropriate header 
		header('Content-type: application/json'); 
		//format the json
		echo json_encode(array('images' => $images));

		//free results an disconnect
		$connection->free($result);
		$connection->disconnect();
	}

	

?>