<?php
	$Copyrights = htmlentities($lang['copyrights'], ENT_QUOTES, 'UTF-8').htmlspecialchars_decode('&copy;');

	$data = array('footer' => $Copyrights);
	
	$footer->setValues($data);
	$footer->replaceInTemplate();

?>