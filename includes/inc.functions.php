<?php
	
	function attachInPatter($pattern, $arrayOfValues, $arrayOfPlaceholders) {		
		$result = '';

		if (count($arrayOfPlaceholders) > 1) {
			
			foreach ($arrayOfValues as $value) {
				$result .= replaceWithTwoArrays($arrayOfPlaceholders, $value, $pattern);
			}

		} else {

			foreach ($arrayOfValues as $key => $value) {
				$temp_result = str_replace($arrayOfPlaceholders[0], $value, $pattern);
				$result .= $temp_result;
			}

		}
		
		return $result;
	}

	function replaceWithTwoArrays($search, $replace, $subject) {
		$result = $subject;

		foreach ($search as $key => $value) {
			$result = str_replace($value, $replace[$key], $result);
		}

		return $result;
	}

	function getLinks($base, $arrayOfValues) {
		$result = array();

		foreach ($arrayOfValues as $value) {
			
			$temp_link = $base.strtolower($value);
			$temp_array = array($temp_link, $value);

			array_push($result, $temp_array);
		}

		return $result;
	}

	function getDirAndAttach($path, $attach) {		
		$handler = explode('/', $path);
		$handler = array_filter($handler);
		
		$result = end($handler).$attach;

		return $result;
	}

	function dirEmpty($path) {

		if (is_readable($path) && is_dir($path)) {
			
			$content = scandir($path);
			
			if (count($content) > 2) {

				return false;
			}

		}

		return true;
	}

?>