<?php 	

	//make the folder for checks
	$basePath = $config['upload_dir'].'thumb/';

	//default title
	$title = htmlentities('Thumbnails', ENT_QUOTES, 'UTF-8');

	//Check there are images in the folder
	if (!dirEmpty($basePath)) {
		
		//start creating the main html
		$main = '<ul class="thumb">';

		//look for this files in this folder
		$folder = getDirAndAttach($config['upload_dir'], '/thumb/*.jpg');

		foreach(glob($folder) as $file) {

			//get the base file name
			$fileName = explode('/', $file);
			$fileName = explode('_', end($fileName));
			$fileName = $fileName[0];

			//get title of the image form the db
			$imageQuery = "SELECT i.title FROM image i WHERE i.unique_file_name = '".$fileName."'";
			$result = $connection->querying($imageQuery);

			//default thumbnail title
			$thumbTitle = htmlentities($lang['home_image_default_tittle'], ENT_QUOTES, 'UTF-8');

			while ($row = $result->fetch_assoc()) {

				if (array_key_exists('title', $row)) {
					
					//add the title form the db
					$thumbTitle = $row['title'];

				}

			}

			//make the links and images html structure
			$main .= '<li><a href="index.php?page=image&amp;size=medium&amp;image='.htmlentities($fileName, ENT_QUOTES, 'UTF-8').'"><img src="'.htmlentities($file, ENT_QUOTES,'UTF-8').'" alt="'.htmlentities($thumbTitle, ENT_QUOTES,'UTF-8').'"></a><div>'.htmlentities($thumbTitle, ENT_QUOTES,'UTF-8').'</div></li>';

			$result->free_result();

		}

		$main .= '</ul>';

	
	} else {

		//If not images found add a sorry message
		$main = '<p class="no-images">'.htmlentities($lang['home_no_images'], ENT_QUOTES, 'UTF-8').'</p>';
		$main .= '<p class="no-images"><a href="index.php?page=share">'.htmlentities($lang['share_more'], ENT_QUOTES, 'UTF-8').'</a></p>';
	}

	//assign to the template content
	$data = array('title' => $title, 'content' =>  $main);
	$content->setValues($data);
	$content->replaceInTemplate();	

?>