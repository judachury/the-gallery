<?php
	class Image {

		//properties used
		protected $thumbSize;
		protected $newThumbSize;
		protected $mediumSize;
		protected $newMediumSize;
		protected $quality;
		
		//default values
		protected $defaultThumb = array('width' => 150, 'height' => 150);
		protected $defaultMedium = array('width' => 600, 'height' => 600);

		//error messages
		public $errorMessages = array();

		//This is made .jpg because it seems to be the most accepted extension compare to .jpeg
		const JPG_EXTENSION = '.jpg';
		const WIDTH = 'width';
		const HEIGHT = 'height';		

		//default constants for file name and folder to drop the images
		const THUMB = 'thumb';
		const MEDIUM = 'medium';
		const QUAL = 90;

		/**
		This makes an instance of the class with values for the two resizing images type of values and custom error messages.
		The quality of the image will be set as default, but changeable from setQuality()
		The user must have a medium and thumb forlder with full permission to save the created images there
		The custom messages should have the keys from the following example:
		$m = array(
						'image_details' => 'This image file is corrupted or incorrect',
						'image' => 'Not an image file',
						'permissions' => 'There is an error with your directory or file, please check with your suplier that you have the perssions to do this action',
						'upload' => 'there was a problem uploading this file'
					);
		@param array - $thumbSize image sizes
		@param array - $mediumSize image sizes
		*/
		public function __construct ($thumbSize, $mediumSize, $errorMessages) {

				//set the error messages
				$this->setErrorMessages($errorMessages);

				//set values for each size
				$this->setThumbSize($thumbSize);
				$this->setMediumSize($mediumSize);

				//The Quality of the picture will be setup with a default value unless redefined by the user with the public setter
				$this->setQuality(self::QUAL);

		}

		/**
		Sets the error messages or exception provided or default ones
		@param array - $errorMessages
		*/
		public function setErrorMessages($errorMessages) {

			//only set custom messages if not empty
			if (!empty($errorMessages)) {
					
				$this->errorMessages = $errorMessages;
			
			} else {

				//set default messages if they are not given
				$m = array(
						'image_details' => 'This image file is corrupted or incorrect',
						'image' => 'Not an image file',
						'permissions' => 'There is an error with your directory or file, please check with your suplier that you have the perssions to do this action',
						'upload' => 'there was a problem uploading this file'
					);

				$this->errorMessages = $m;

			}

		}

		/**
		This thumbnail size setter allows to change the default size
		@param array $thumbSize
		*/
		public function setThumbSize($thumbSize) {
			
			$tempArray = $this->makeArray($thumbSize, $this->defaultThumb);
			
			$this->thumbSize = $tempArray;
			
		}

		/**
		Returns the $thumbSize array
		@return array $thumbSize
		*/
		public function getThumbSize() {
			return $this->thumbSize;
		}

		/**
		This Medium size setter allows to change the default size
		@param array $mediumSize
		*/
		public function setMediumSize($mediumSize) {
			
			$tempArray = $this->makeArray($mediumSize, $this->defaultMedium);
		
			$this->mediumSize = $tempArray;
			
		}

		/**
		Returns the $mediumSize array
		@return array $mediumSize
		*/
		public function getMediumSize() {
			return $this->mediumSize;
		}

		/**
		Returns the $mediumSize array
		@return array $mediumSize
		*/
		public function getNewMediumSize() {
			return $this->newMediumSize;
		}
		
		/**
		This quality setter allows to change the default image quality for jpg files.
		It allows values from 1 to 100
		@param int $quality
		*/
		public function setQuality($quality) {
			
			//set QUAK constance as default quality
			$this->quality = ($quality > 0 || $quality < 101 ? $quality : self::QUAL);
			
		}

		/**
		Returns the $mediumSize array
		@return array $mediumSize
		*/
		public function getQuality() {
			return $this->quality;
		}

		/**
		Method that validates an image file
		Returns an array with a validate image and extension	
		@param $_FILES - $image php constant
		@param string - $param with the parameter to look in the image
		@return array - $validatedImg a sub array of $_FILES already validated
		@throw Exception - $this->errorMessages['image'] if this is not an image file
		*/
		public function imageValidation($image, $param) {

			$ext = $this->validateExtension($image[$param]);

			if (!$ext) {
				
				//error handling
				throw new Exception($this->errorMessages['image']);
				
			} else {

				//set the img values
				$validatedImg = array('valImg' => $image, 'valExt' => $ext);
				
				return $validatedImg;

			}

		}

		/**
		Method to check the extesion of a $_FILES array returning an application extension or
		false if not an image file
		@param $_FILES - $image php constant
		@return string - $ext JPG_EXTENSION constant with a valid extension
		@return boolean - false if not a jpg or jpeg file
		*/
		public function validateExtension($image) {
			
			$ext = '';

			//Get the real current extension
			$currentExt = pathinfo($image['name'], PATHINFO_EXTENSION);
			
			//With this switch statement, we try to identify the type and the extension of the image
			//to mantain a high security the extension is added by the application
			switch  (true) {

				case ($image['type'] === 'image/jpeg' && ($currentExt === 'jpeg' || $currentExt === 'jpg')):
					$ext = self::JPG_EXTENSION;
					break;

				case ($image['type'] === 'image/jpg' && ($currentExt === 'jpeg' || $currentExt === 'jpg')):
					$ext = self::JPG_EXTENSION;
					break;

				default:
					return false;
			}			
			
			return $ext;
		}

		/**
		Gets all needed to do Thumbnails images. The folder thumb is required with full permission and in the original image folder
		@param string - $image full name path
		@throw Exception - from imageResize() method
		*/
		public function createThumbnail($image) {

			$thumb = $this->getThumbSize();

			$maxWidth = $thumb['width'];
			$maxHeight = $thumb['height'];

			$path = $this->getThePathOrFile($image, 'path').'/'.self::THUMB;
			$file = $this->getThePathOrFile($image, 'file');
			$file = $this->createName($file, '_'.self::THUMB);

			$pathName = $path.'/'.$file;
			

			try {

				$imageZise = $this->imageResize($image, $maxWidth, $maxHeight, $pathName, $path);
				$this->newThumbSize = $imageZise;


			} catch (Exception $e) {

				throw new Exception($e->getMessage());

			}
			
		}

		/**
		Gets all needed to do Medium images. The folder medium is required with full permission and in the original image folder
		@param string - $image full name path
		@throw Exception - from imageResize() method
		*/
		public function createMedium($image) {
			$medium = $this->getMediumSize();

			$maxWidth = $medium['width'];
			$maxHeight = $medium['height'];

			
			$path = $this->getThePathOrFile($image, 'path').'/'.self::MEDIUM;
			$file = $this->getThePathOrFile($image, 'file');
			$file = $this->createName($file, '_'.self::MEDIUM);

			$pathName = $path.'/'.$file;
			
			try {

				$imageZise = $this->imageResize($image, $maxWidth, $maxHeight, $pathName, $path);
				$this->newMediumSize = $imageZise;

			} catch (Exception $e) {

				throw new Exception($e->getMessage());

			}
			
		}

		/**
		This method creates a new image based on the sizes given and keeping the proportions of the original images
		@param string - $image full path name
		@param int - $maxWidth
		@param int - $maxHeight		 
		@param string - $pathName
		@param string - $path checks if the image can be drop in it
		@throw Exception - $this->errorMessages['image_details'] in two different places
		@throw Exception - $this->errorMessages['permissions']
		*/
		private function imageResize($image, $maxWidth, $maxHeight, $pathName, $path) {

			//find if the $image and the path are valid
			if (file_exists($image) && $this->validateDirectory($path)) {

					//get image details
					$details = getimagesize($image);

					if ($details) { 
						
						//get type and select the correct imagecreatefrom function
						$type = $details[2];
						$src = $this->createImageFrom($type, $image);

						//get the image w & h
						$width = $details[0]; 
        				$height = $details[1];

        				//create new image keeping proportion
        				$newImgSize = $this->getNewImageSize($width, $height, $maxWidth, $maxHeight);

        				//set the new image size
        				$nWidth = $newImgSize['width'];
						$nHeight = $newImgSize['height'];       				

						//if the image path is incorrect throw this error
        				if (!$src) {

							//very specific error, very unlikely to happen at this stage
							//but just in case - throw the same exception than for $details
							throw new Exception($this->errorMessages['image_details']);

        				} else { 

        					//create the new image based on the new messuments
        					$new = imagecreatetruecolor($nWidth, $nHeight);
							imagecopyresampled($new, $src, 0, 0, 0, 0, $nWidth, $nHeight, $width, $height);

							//select the appropiate imagecreate function and save in in the desired folder
							$this->createImage($type, $new, $pathName);

							//remove the temporary images from memory
							imagedestroy($src);
	        				imagedestroy($new);

	        				//return the new image size
	        				return $newImgSize;

						}

					} else { 

						//In case anything is corrupted with the image details
						throw new Exception($this->errorMessages['image_details']);
					}

			} else {

				throw new Exception($this->errorMessages['permissions']);
				
			}

		}

		/**
		This method get the appropiate image function to create new images
		@param getimagesize function type - $type
		@param string - $image 
		@return imagecreatefrom fucntion -$src
		@return boolean - false when type not found
		*/
		private function createImageFrom($type, $image) {

			//if the type doesn't match, return false
			switch ($type) {

	            case IMAGETYPE_GIF:
	                $src = imagecreatefromgif($image);
	                break;
	            case IMAGETYPE_JPEG:
	           		$src = imagecreatefromjpeg($image);
	                break;
	            case IMAGETYPE_PNG:
	            	$src = imagecreatefrompng($image);
	                break;
	            default:
	            	return false;
	            	break;
        	}

        	return $src;
		}

		/**
		@param getimagesize function type - $type
		@param canvas imagecreatetruecolor function - $image
		@param string - $name full path
		@return boolean - false if the type is not git, png or jpeg/jpg
		*/
		private function createImage($type, $image, $name) {

			switch ($type) {

	            case IMAGETYPE_GIF:
	                $newImage = imagegif($image, $name, $this->quality);
	                break;
	            case IMAGETYPE_JPEG:
	           		$newImage = imagejpeg($image, $name, $this->quality);
	                break;
	            case IMAGETYPE_PNG:
	            	$newImage = imagepng($image, $name, $this->quality);
	                break;
	            default:
	            	return false;
	            	break;
        	}

		}

		/**
		Use this method to attach part of the name to the end of the name and before of the
		extension
		@param string - $fileName
		@param string - $append
		@return string - $newName
		*/
		public function createName($fileName, $append) {

			$string = explode('.', $fileName);
			
			$newFile = $string[0].$append.'.'.$string[1];

			return $newFile;
		}

		/**
		This method returns an image size to keep the correct proporcion of the original image size
		@param int - $width
		@param int - $height
		@param int - $maxWidth
		@param int - $maxHeight
		@return array - $newSize
		*/
		public function getNewImageSize($width, $height, $maxWidth, $maxHeight) {


			if ($width >= $height && $width > $maxWidth) {
				
				$nWidth = $maxWidth;
				$nHeight = ($nWidth * $height) / $width;

			} elseif ($height > $width && $height > $maxHeight) {

				$nHeight = $maxHeight;
				$nWidth  = ($nHeight * $width) / $height;
				
			} else {

				$nWidth = $width;
				$nHeight = $height;

			}

			//add new size to an array
			$newSize = array('width' => $nWidth, 'height' => $nHeight);

			return $newSize;

		}

		/**
		A method to aid getting a string from a full path or the file of a full path fiven
		@param string - $fullPath
		@param string - $selection
		@return string - $result
		*/
		public function getThePathOrFile($fullPath, $selection) {

			$temp = explode('/', $fullPath);
			$result = '';
			
			foreach ($temp as $value) {

				if (!empty($value) && ($value !== end($temp)) && $selection === 'path') {
					
					$result .= '/'.$value;
				
				} elseif ($value === end($temp) && $selection === 'file') {
					
					$result = $value;

				}

			}

			return $result;
		}
		
		/**
		Check for permission in the directory
		@param string $directory path
		@return boolean - false or true
		*/
		public function validateDirectory($directory) {
			
			if (is_readable($directory) && is_writable($directory) && is_dir($directory)) {
				return true;
			} 
			
			return false;
		}
		
		/**
		This validates that a give array has the same keys as the default array
		@param array $array
		@param array $defaultArray
		@return array $result
		*/
		public function makeArray($array, $defaultArray) {
		
			$result = array();
			$count = 0;
			
			//make sure this is an array and that it has the keys specified
			if (is_array($array)) {
			
				foreach ($array as $key => $value) {
					
					//count if the keys exist
					$count = ($key === self::WIDTH || $key === self::HEIGHT ? $count + 1 : $count);
					
				}
				
			}
			
			//only set $array when the keys are accurate
			if ($count === 2) {
				
				$result = $array;
				
			} else {

				$result = $defaultArray;
				
			}
			
			return $result;
			
		}

	}
?>