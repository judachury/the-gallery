<?php

	/**
	A Template class to replace content with ease. Refactored from the TMA

	@author Juan David Achury Herera - jachur01
	@link 


	*/
	class Template {
		
		//properties used in the clase
		private $template;
		private $values;
		private $content;

		/**
		This helps to replace the content in the respective placeholder
		@param string - $template, the template location
		*/
		public function __construct ($template) {		
			$this->setTemplate($template);
		}

		/**
		This helps to set content values , which can be replaced in the template
		@param array - $values
		*/
		public function setValues($values) {
			$this->values = $values;
		}

		/**
		This helps to replace placeholder for actual values
		@param string - $content
		*/
		private function setContent($content) {
			$this->content = $content;
		}

		/**
		This returns the content ready, but the function replaceInTemplate must be run to populate it
		@method string
		@return string $this->content
		*/
		public function getContent() {
			return $this->content;
		}

		/**
		This helps to keep private the initialised template
		@param string - $template
		*/
		private function setTemplate($template) {
			$this->template = $template;
		}

		/**
		It replaces the value for the respective placeholder found in the template, if not found it a warning message instead ??$placeholder??
		@param string - $value
		@return string - $val
		*/
		public function replacer($value){
			$val = $value[1];

			return isset($this->values[$val]) ? $this->values[$val] : "??$val??";
		}

		/**
		This helps to replace the content in the respective placeholder
		@param string $content
		*/
		public function replaceInTemplate() {

			//get the real template location and make it string
			$template = file_get_contents($this->template);

			//find placeholders and attach the real value to the template variable
			$template = preg_replace_callback("/\{\{(\w+)\}\}/", array($this, "replacer"), $template);
			
			//save template ready for display
			$this->setContent($template);
		}
	}

?>