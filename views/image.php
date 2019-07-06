<?php
	
	//sanitize the get value and make the image path name
	$imageName = (isset($_GET['image'])) ? filter_var($_GET['image'], FILTER_SANITIZE_STRING) : false;
	$imagePathName = $config['upload_dir'].'/medium/'.$imageName.'_medium.jpg';

	//Set a default title
	$title = $lang['image_default_title'];

	//check all needed details are correct
	if (isset($_GET['image']) && ($_GET['size'] === 'medium') && file_exists($imagePathName)) {
			
			//get the sanitized parameter for db query
			$baseName = $imageName;
			$imageQuery = "SELECT i.title, i.description FROM image i WHERE i.unique_file_name = '".$baseName."'";
			$result = $connection->querying($imageQuery);

			while ($row = $result->fetch_assoc()) {

				//if the values don't exit, provide default message
				$title = (array_key_exists('title', $row)) ? $row['title'] : $lang['image_unavailable_title'];
				$description = (array_key_exists('description', $row)) ? $row['description'] : $lang['image_unavailable_description'];

			}

			//get the upload folder
			$uploadsFolder = getDirAndAttach($config['upload_dir'], '');		

			//make the html format to display the image and image description
			$main = '<div class="medium"><a href="index.php?page=home"><img src="'.htmlentities($uploadsFolder.'/medium/'.$baseName.'_medium.jpg', ENT_QUOTES,'UTF-8').'" alt="'.htmlentities($title, ENT_QUOTES,'UTF-8').'"></a><p>'.htmlentities($description, ENT_QUOTES,'UTF-8').'</p></div>';

			$result->free_result();

	//if data missing in the url, provide a sorry message
	} else {		
		
		$main = '<p class="no-images">'.htmlentities($lang['image_no_images'], ENT_QUOTES, 'UTF-8').'</p>';
		$main .= '<p class="no-images"><a href="index.php?page=share">'.htmlentities($lang['share_more'], ENT_QUOTES, 'UTF-8').'</a></p>';

	}
	
	$data = array('title' => $title, 'content' =>  $main);
	$content->setValues($data);
	$content->replaceInTemplate();

?>