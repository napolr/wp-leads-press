<?php 
function wplp_set_cookie_ref($ref_user_nicename) {
	
//error_log('cookie function start ref user nicename = '. $ref_user_nicename);
	
	if( isset( $_COOKIE['wplp-ref'] ) ) { // Let's extend this to give options for cookie duration in later version.
		
			// Unset previous cookie, if it exists
			setcookie( 'wplp-ref', NULL, time()-1, SITECOOKIEPATH, COOKIE_DOMAIN, false );//Unsetting
			
	}
		
	// Set new cookie with new referrer
	setcookie( 'wplp-ref', $ref_user_nicename, time()+86400*30*12, SITECOOKIEPATH, COOKIE_DOMAIN, false );//Setting new	
	
	//error_log('cookie function complete');
}
?>