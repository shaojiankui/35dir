<?php
function smarty_block_nocache($param, $content, $smarty) {
    if (is_null($content)) {
        return;
    }
	
	return $content;
}

$smarty->register_block('nocache', 'smarty_block_nocache', false);
?>