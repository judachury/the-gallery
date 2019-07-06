<?php
	
	//Initialise basic template variables
	$title = htmlentities($lang['share_title'], ENT_QUOTES, 'UTF-8');

	//Initialise the data from input fields for validation and sanitization
	$inputs = array(
				'title' => array(
							'type' => 'text',
							'min' => 5,
							'max' => 20,
							'error' => $lang['share_error_title_input']
						),

				'description' => array(
							'type' => 'text',
							'min' => 5,
							'max' => 100,
							'error' => $lang['share_error_description_input']
						),

				'myimage' => array(
							'type' => 'image',
							'error' => $lang['share_error_image_input']
						)
			);

	//get errors for the Upload class. This is for i18n purposes
	$uploadErrors =	array(
						'directory' => $lang['share_error_directory'],
						'image_instance' => $lang['share_error_image_instance'],
						'extension' => $lang['share_error_extension'],
						'upload' => $lang['share_errror_upload']
					);

	//get errors for the Image class. This is for i18n purposes
	$imageErrors = array(
						'image_details' => $lang['share_error_image_details'],
						'image' => $lang['share_error_image'],
						'permissions' => $lang['share_error_permissions'],
						'upload' => $lang['share_error_upload']
					);

	
	try {

		//create an Image instance for uploads to deal with my own images settings
		$image = new Image(array(), array(), $imageErrors);

		//Initialise upload action
		$upload = new Upload($config['upload_dir'], $uploadErrors);

		$upload->setImageObj($image);

		//Start validation - If all inputs valid then upload the file
		$formVal = new Validation($_POST, $inputs, $upload);

	} catch (Exception $e) {

		echo $lang['caught'].$e->getMessage()."\n";

	}	
	
	//Return the valid values
	try {

		$validValues = $formVal->validate();

	} catch (Exception $e) {
	
		echo $lang['caught'].$e->getMessage()."\n";

	}
	
	//Return any errors in the validation
	$errors = $formVal->getErrors();

	$formData = array(	
						'legend' => $lang['share_form_legend'],
						'title_label' => $lang['share_title_lable'],
						'image_label' => $lang['share_image_label'],
						'description_label' => $lang['share_description_label'],
						'button' => $lang['share_upload_button'],
						'action' => htmlentities('index.php?page=share', ENT_QUOTES, 'UTF-8'),
						'title' => '',
						'description' => ''
					);

	/****************************************
	*  			PAGE BUILD HERE 	    	*
	*	Note: this logic is for UI purposes	*
	*			and not validation			*
	*		the db image table is also		*
	*		   populated from here			*
	*		                  				*
	****************************************/

	//If the form is submitted and there are not errors, show a thank you message
	if (isset($_POST['myimage']) && empty($errors)) {
		
		//add a thank you message to the the main
		$main = '<p class="image-upload">'.htmlentities($lang['share_file_uploaded'], ENT_QUOTES, 'UTF-8').'</p>';
		$main .= '<p class="image-upload"><a href="index.php?page=home">'.$lang['share_check'].'</a></p>';
		//make a query to insert the image data		
		$insertData = "INSERT INTO image (title, description, unique_file_name, image_width, image_height) VALUES ('".$validValues['title']."', '".$validValues['description']."', '".$validValues['image']['name']."', '".$validValues['image']['width']."', '".$validValues['image']['height']."')";
		
		//thows an Error with query Exception if the query fails
		try {

			//make the query
			$connection->querying($insertData);

		} catch(Exception $e) {

			//Exception from the Connection class
			echo $lang['caught'].$e->getMessage()."\n";

		}

	//If the form is submitted and there are errors
	} elseif (isset($_POST['myimage']) && !empty($errors)) {

		//add all the errors with an html format
		$temp = '<ul class="errors"><li>'.htmlentities($lang['share_error_message'], ENT_QUOTES, 'UTF-8').'</li>';
		foreach ($errors as $value) {

			$temp .= '<li>'.htmlentities($value, ENT_QUOTES, 'UTF-8').'</li>';

		}
		$temp .= '</ul>';
		
		//initilise valid values, empty by default
		$fieldTitle = '';
		$fieldDescription = '';

		//If there is a valid value get it and add it to the form template
		if (array_key_exists('title', $validValues)) {
			$fieldTitle .= htmlentities($validValues['title'], ENT_QUOTES, 'UTF-8');
		}

		//If there is a valid value get it and add it to the form template
		if (array_key_exists('description', $validValues)) {
			$fieldDescription .= htmlentities($validValues['description'], ENT_QUOTES, 'UTF-8');
		}		

		//add the valid values to the form data array
		$formData['title'] = $fieldTitle;
		$formData['description'] =	$fieldDescription;
		
		//Add the the validated content to the form and attach to the main content for this page
		$form->setValues($formData);
		$form->replaceInTemplate();
		$main .= $temp.$form->getContent();

	//Display the default layout
	} else {

		//Get the form template with default values an attch it to the main content for this page
		$form->setValues($formData);
		$form->replaceInTemplate();
		$main .= $form->getContent();

	}

	//Get the data to the content template
	$data = array('title' => $title, 'content' =>  $main);
	$content->setValues($data);
	$content->replaceInTemplate();

?>