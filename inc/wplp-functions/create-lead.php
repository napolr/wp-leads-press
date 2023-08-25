<?php
global $wp, $wpdb, $_POST;
	
require_once( '../../../../../wp-load.php');		
require_once( WPLP_ROOT . 'inc/create-lead-functions.php' );

wplp_create_lead();

?>