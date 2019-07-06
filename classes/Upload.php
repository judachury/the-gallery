<?php
	
	//include the Image class in case there we need to create an instance
	include_once 'Image.php';

	/**

	A Upload class that deal with the uploads. It takes the path of where the file should be deposited and custom exceptions and errors

	@author Juan David Achury Herera - jachur01
	@link 

	*/
	class Upload {

		//properties
		protected $depot;
		protected $imageObj;
		protected $fileBaseName;
		protected $imageMaxSize;
		
		//error messages
		public $errorMessages = array();

		/**
		Enter the file location and messages.
		Note that the error messages need to have the keys of the following example:
		$m = array(
						'directory' => 'Wrong or non existen path. You must have permission to use this depot',
						'image_instance' => 'Not an image instance',
						'extension' => 'The file extension is invalid',
						'upload' => 'there was a problem uploading this file'
					);
		@param string - $depot where the files are going to be droped
		@param array - $errorMessages custome error messages or exceptions
		@throw Exception - $this->errorMessages['directory']
		*/
		public function __construct($depot, $errorMessages) {

			$this->setErrorMessages($errorMessages);

			//ensure the path provided works
			if (!is_readable($depot) && !is_writable($depot) && !is_dir($depot)) {			
				
				throw new Exception($this->errorMessages['directory']);
			
			} else {

				//save the path as instance variable of the class
				$this->depot = $depot;				

			}
		}

		/**
		Sets the error messages or exception provided or default ones
		@param array - $errorMessages
		*/
		public function setErrorMessages($errorMessages) {
			
			//only set error messages if they exist
			if (!empty($errorMessages)) {
					
				$this->errorMessages = $errorMessages;
			
			} else {

				//set default messages if they are hot given
				$m = array(
						'directory' => 'Wrong or non existen path. You must have permission to use this depot',
						'image_instance' => 'Not an image instance',
						'extension' => 'The file extension is invalid',
						'upload' => 'there was a problem uploading this file'
					);

				$this->errorMessages = $m;

			}
	        
		}
		
		/**
		Checks that the $imageObj is an instance of Image class
		@param object - $imageObj
		@throw Exception - $this->errorMessages['image_instance']
		*/
		public function setImageObj($imageObj) {
			
			//verify the object is Image instance
			if (is_object($imageObj) && $imageObj instanceof Image) {

				$this->imageObj = $imageObj;
			
			} else {
				
				throw new Exception($this->errorMessages['image_instance']);
				
			}
			
		}

		/**
		gets the imageObj
		@returns object - $this->imageObj
		*/		
		public function getImageObj() {
			return $this->imageObj;
		}

		/**
		Set the unique file name to the property
		@param string - $fileBaseName
		*/
		public function setFileBaseName($fileBaseName) {
			$this->fileBaseName = $fileBaseName;
		}

		/**
		Get the unique file name created
		@return string - $this->fileBaseName
		*/
		public function getFileBaseName() {
			return $this->fileBaseName;
		}

		/**
		get the maximum size of an image
		@return int - $this->imageMaxSize
		*/
		public function getImageMaxSize() {
			return $this->imageMaxSize;
		}

		/**
		It takes the $_FILES as an argument	
		@param $_FILES - $file php constant
		@param string - $param form parameter to check get the file array
		@throw Exception - from uploadImage()
		@return $this->errorMessages['extension']
		*/
		public function uploadFile($file, $param) {

			//get the file type
			$type = $file[$param]['type'];

			//this sends the file to a class that handles the respective file
			//currently only images are handle. Further development is needed for other file types
			if ($type === 'image/jpeg' || $type === 'image/jpg') {			
					
				try {
					
					$this->uploadImage($file, $param);

				} catch (Exception $e) {
					
					throw new Exception($e->getMessage());						

				}
		
			} else {

				return $this->errorMessages['extension'];

			}

		}

		/**
		This method creates all the require size of images and saves them in the folder
		Please note that the original image is also saved
		@param $_FILES - $file php constant
		@param string - $param form parameter to check get the file array
		@throw Exception from the Image class
		@throw Exception from the Image class
		*/
		private function uploadImage($file, $param) {
			
			$fileHandler;
			
			//create an instance of Image if it has not been given
			if (!empty($this->imageObj)) {
			
				$fileHandler = $this->getImageObj();
				
			} else {
			
				$fileHandler = new Image(array(), array(), array());
				
			}

			//validate the image file
			try {

				$tempValImg = $fileHandler->imageValidation($file, $param);

			} catch (Exception $e) {

				//Exception from the Image class
				throw new Exception($e->getMessage());				
			
			}			

			//get a valid image and extension
			$validImage = $tempValImg['valImg'];
			$validExtension = $tempValImg['valExt'];

			//drop the file in the folder
			$imageFile = $this->dropFile($validImage[$param], $validExtension);

			try {

				//create a Medium and a Thumbnail images
				$fileHandler->createMedium($imageFile);
				$fileHandler->createThumbnail($imageFile);				

				//get the size of the biggest image
				$this->imageMaxSize = $fileHandler->getNewMediumSize();

			} catch (Exception $e) {

				//throw an exception from the Image class
				throw new Exception($e->getMessage());

			}					

		}

		/**
		Upload the file to a location $depot. The file should be validated before hand
		@param $_FILES - $file php constant
		@param string - $ext the file extension
		@return string - $newname 13 uniques char
		@return string - $this->errorMessages['upload']
		*/
		private function dropFile($file, $ext) {
			
			$error = $file['error'];

			//check if there were errors uploading
			if ($error === UPLOAD_ERR_OK) {
				
				$tmpFile = $file['tmp_name'];

				//check if the file has been genunely uploaded
				if (is_uploaded_file($tmpFile)) {
					
					//get a new and unique file name
					$uniquename = uniqid();
					$newname = $this->depot.$uniquename.$ext;

					//make sure $newname is unique
					$newname = $this->checkFileUniqueName($newname);					

					//upload the file
					if (move_uploaded_file($tmpFile, $newname)) {

						$this->setFileBaseName($uniquename);	
						
						//return the file new name
						return $newname;

					}					
				
				}
				
			} else {
				
				//return a string with the error message
				return $this->errorMessages['upload'];

			}
			
		}	

		/**
		Recursive function to ensure the file new file name is unique
		@param $newname
		@return $newname
		*/
		function checkFileUniqueName($newname) {

			//is new name unique?
			if (!file_exists($newname)) {

				return $newname;

			} 

			//get the existing extension
			$ext = '.'.pathinfo($newname, PATHINFO_EXTENSION);

			//rename newname and recall the function
			$newname = $this->depot.uniqid().$ext;
			$this->checkFileUniqueName($newname);

		}	
		
	}
?>