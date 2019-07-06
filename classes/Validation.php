<?php
	/**

	A Validation class that validate all form inputs that are provided in the constructor or addInputs() method

	@author Juan David Achury Herera - jachur01
	@link 

	*/
	class Validation {

		protected $post = array();
		protected $inputs = array();
		protected $errors = array();
		protected $upload;

		/*A simple default error message when an upload Class is provided. Not need for i18n, it should be managed from the exterior of the class*/
		const UPLOAD_INSTANCE = 'Not an Upload instance';

		/**
		A constructor that takes several values to do the validation, check the following @param
		@param $_POST - $post php form constant
		@param array - $inputs the inputs to validate. They should be provided as in the following example
		array('title' => array(
							'type' => 'text',
							'min' => 5,
							'max' => 20,
							'error' => 'The title is 5 to 20 characters long'
						)
			)
		@param object - $upload instance of Upload
		@throw UPLOAD_INSTANCE Exception - An exception from the Upload class
		*/
		public function __construct($post, $inputs, $upload) {
			
			//assign these values to the class properties
			$this->post = $post;
			$this->inputs = $inputs;
			try {
				
				$this->setUpload($upload);

			} catch (Exception $e) {

				throw new Exception($e->getMessage());
				
			}			
			
		}

		/**
		Adds more inputs to validate
		@param array - $input
		*/
		public function addInput($input) {
			array_push($this->input, $input);
		}

		/**
		Get all the current errors
		@return array - $this->errors
		*/
		public function getErrors() {
			return $this->errors;
		}

		/**
		Sets errors found in the $post values
		@param string - $error
		*/
		private function setErrors($error) {
			array_push($this->errors, $error);
		}

		/**
		Checks the $upload is an instance of Upload
		@param object - $upload instance of Upload
		@throw UPLOAD_INSTANCE Exception
		*/
		public function setUpload($upload) {
			
			//The value can be empty, but if is not empty it should be Upload instance
			if (!empty($upload)) {

				//assign the upload obj to the property 
				if (is_object($upload) && $upload instanceof Upload) {

					$this->upload = $upload;
			
				} else {
					
					throw new Exception(self::UPLOAD_INSTANCE);
					
				}
				
			}
			
		}

		/**
		A method that validates all the inputs added in the $inputs property with the specifications provided
		@throw Exception - An exception from the Upload class
		@return array - $result all the valid values
		*/
		public function validate() {
			
			$result = array();

			//make sure that post has been setup
			if (!empty($this->post)) {

				//initialise variables to use later
				$imageError;
				$image;

				//loop through the inputs that need validation
				foreach ($this->inputs as $key => $value) {				

					//remove whitespaces
					$postValue = trim($this->post[$key]);

					//if the value have been submitted 
					if (isset($postValue)) {
						
						//deal separately with text or image files
						switch (true) {

							case ($value['type'] === 'text'):

								//validate the input fields
								$valid = $this->validationHandler($postValue, $value);

								//merge the valid values to the result
								$result = array_merge($result, array($key => $valid));	

								break;

							case ($value['type'] === 'image'):
								//save the image to deal with after the other fields are valid						
								$imageError = $value['error'];

								//if this is set to something, it activates the image validation
								$image = $key;

								break;
						}										
					
					}
			
				}

				//get any errors
				$errors = $this->getErrors();

				//do this only when there is an image type
				if (!empty($image)) {

					//only validate and upload the image if no errors with other fields
					if (!empty($imageError) && count($errors) === 0) {

						try {

							//the upload file will deal with the validation of the image further
							$error = $this->upload->uploadFile($_FILES, 'image');
						
						} catch (Exception $e) {

							throw new Exception($e->getMessage());
							
						}					

						//if any image errors produce set it in the $errors property
						if (!empty($error)) {

							$this->setErrors($error);

						} else {

							//return the generated image file
							$valid = $this->upload->getFileBaseName();

							//return the generated maximun image size
							$fileSize = $this->upload->getImageMaxSize();
							
							//set values the valid image values
							$result = array_merge($result, 
														array(
																'image' => array(
																				'name' => $valid,
																				'width' => $fileSize['width'],
																				'height' => $fileSize['height']
																			)
															)
													);
						
						}						

					} else {

						//give the generic image error form the input
						$this->setErrors($imageError);
						
					}

				}

				return $result;

			}
		}

		/**
		A method that validates a string with the values in the array returning the valid value
		@param string - $postValue
		@param array - $validation
		@return string - $postValue
		*/
		private function validationHandler($postValue, $validation) {
			
			$length = strlen($postValue);

			//validate the value otherwise set an error
			if ($length >= $validation['min'] && $length <= $validation['max']) {

				//sanitize the postvalue to prevent errors when queriyng
				return filter_var($postValue, FILTER_SANITIZE_STRING);

			} else {

				//set the error
				$this->setErrors($validation['error']);

			}
			
		}

	} 

?>