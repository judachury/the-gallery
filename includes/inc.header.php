<?php

	//Styles generator based on the parameters enter in the inc.config.php file
	$stylesPattern = '<link rel="stylesheet" type="text/css" href="{{css}}">';
	$stylesPlh = array('{{css}}');
	$styles = attachInPatter($stylesPattern, $config['css'], $stylesPlh);

	//Main Navigation generator based on the parameters enter in the inc.config.php file
	$navPattern = '<li><a href="{{link}}">{{name}}</a></li>';
	$navPlh = array('{{link}}', '{{name}}');
	
	$index = $config['index_page'].'?page=';
	$links = getLinks($index, $config['nav']);	

	$nav = attachInPatter($navPattern, $links, $navPlh);
	$nav = '<ul>'.$nav.'</ul>';
	
	$title = '<a href="index.php?page=home">'.htmlentities($lang['default_title'], ENT_QUOTES, 'UTF-8').'</a>';

	$data = array(
				'lang' => $config['language'],
				'scripts' =>  $styles,
				'title' => htmlentities($lang['company'], ENT_QUOTES, 'UTF-8'),
				'heading' => $title,
				'nav' =>  $nav
			);
	
	$header->setValues($data);
	$header->replaceInTemplate();
?>