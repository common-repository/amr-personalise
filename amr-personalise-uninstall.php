<?php
/* This is the  uninstall file */
	function amr_personalise_uninstall(){	
	
	if (function_exists ('delete_option')) {  			
		if ( $del1 = delete_option('amr-personalise-ifnoname')) {
			echo '<p>'.__('AmR personalise Option deleted from Database', 'apers').'</p>';
		};
		return ($del1);	 
	}
	else {
		die ('<p>Wordpress Function delete_option does not exist.</p>');
		}
				
	}

	register_uninstall_hook(__FILE__,'amr_personalise_uninstall');
	
?>