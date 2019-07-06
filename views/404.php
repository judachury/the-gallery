<?php

	//Get all the i18n messages
	$title = $lang['404_title'];

	$url = 'index.php?page=home';
 	$subContent = str_replace('{{url}}', $url, $lang['404_content']);
	
	//add content in a html structure
 	$main = '<p class="message-404">'.htmlentities($lang['404_subtitle'], ENT_QUOTES, 'UTF-8').'</p>';
 	$main .= '<p class="message-404">'.$subContent.'</p>';

	//assign to the template content
	$data = array('title' => $title, 'content' =>  $main);
	$content->setValues($data);
	$content->replaceInTemplate();

?>