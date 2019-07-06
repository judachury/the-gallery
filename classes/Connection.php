<?php
	
	/**
	
	A Connection class, which inherits PHP mysqli class to personalise the connection. Refactored from the TMA for inprovements

	@author Juan David Achury Herera - jachur01
	@link 

	*/
	class Connection extends mysqli {

		//error messages
		public $errorMessages = array();

		/**
		This constructor takes all the data necessary to make connection and error messages for exception and errors
		@param string - $host
		@param string - $user
		@param string - $pass password
		@param string - $db database name
		@param array - $errorMessages
		@throw Exception - $this->errrorMessages['connection']
		*/
		public function __construct($host, $user, $pass, $db, $errorMessages) {
			
			$this->setErrorMessages($errorMessages);

			parent::__construct($host, $user, $pass, $db);
			
			if ($this->connect_errno) {

				exit($this->connect_error);
				throw new Exception($this->errrorMessages['connection']);
				
			}
						
		}

		/**
		Sets the error messages or the default messages
		@param array $errorMessages
		*/
		public function setErrorMessages($errorMessages) {
			
			if (!empty($errorMessages)) {
					
				$this->errorMessages = $errorMessages;
			
			} else {

				//set default messages if they are hot given
				$m = array(
						'connection' => 'Something has failed the connection, contact your service provide',
						'query' => 'Something has failed to get the information, contact your service provide'						
					);

				$this->errorMessages = $m;

			}
	        
		}
		
		/**
		Make the query and return it, otherwise, return an exception
		@param string - $sql with the desired query	
		@throw Exception - $this->errrorMessages['connection']
		@return array_map - $result of the query
		*/
		public function querying($sql) {
			
			$result = parent::query($sql);
			
			if ($result === false) {
					
				throw new Exception($this->errrorMessages['query']);
				
				exit();	
			}
			
			return $result;
		}

		
		/**
		free results
		@param array $results should be an isntance of the db query results
		*/
		public function free($result) {
			$result->free_result();
		}
		
		/**
		close the database
		*/
		public function disconnect() {
			$this->close();	
		}

	}
	
?>