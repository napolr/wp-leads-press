<?php
function wplp_get_random_referrer(){
	global $wp, $wpdb;
	
	# Need to rewrite code to first find all users who have usermeta setting for affiliate ID of current company.
	# Then pull a random user from users table using usermeta value to only select ID of random user with Affiliate ID set.
	# for each user who doesn't have Affiliate ID set for company, set to send email using cron job telling them they missed X number of leads.

# Check code below for clue on how to setup

//$params = array(
//    'post_type' => 'portfolio',
//    'post_status' => 'publish',
//    'posts_per_page' => 10,
//    'meta_key' => 'slideorder',
//    'meta_value' => ' ',
//    'meta_compare' => '!=',
//    'ignore_sticky_posts' => 1,
//    'orderby' => 'meta_value',
//    'order' => 'ASC'
//);
//$slport_query = new WP_Query($params);	

# This is from WordPress.org	
//	$args = array(
//		'blog_id'      => $GLOBALS['blog_id'],
//		'meta_key'     => '', # set to name of company affiliate ID
//		'meta_value'   => ' ', # enter space, apparently needed as empty meta values have space? stack overflow.
//		'meta_compare' => '!=',
//		'meta_query'   => array(),
//		'include'      => array(),
//		'exclude'      => array(),
//		'orderby'      => 'ID',
//		'order'        => 'ASC',
//		'offset'       => '',
//		'search'       => '',
//		'number'       => '',
//		'count_total'  => false,
//		'fields'       => 'all',
//		'who'          => ''
// 	);
//	
//	# Get all ID's of users with value set for company
//	$ref_ids = get_users( $args );
	
	# Get one user id from results
	$ref_user_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM ".$wpdb->prefix."users WHERE RAND()<(SELECT ((1/COUNT(*))*10) FROM ".$wpdb->prefix."users) ORDER BY RAND() LIMIT %d", 1 ) );
				
	return $ref_user_id;
			
}

// Run function to check if every user has a referring user for ID, update if not
function wplp_update_usermeta_referrer_id_null(){  
	
	
	$options = wp_load_alloptions();	
		
		// Get default ancestor value
		if( isset($options['wp_leads_press_default_ancestor'] ) && !empty( $options['wp_leads_press_default_ancestor'] ) ){
			
			$defaultUser = $options['wp_leads_press_default_ancestor'];
			
		} else {
			
			$defaultUser = 1;	
			
		}	
	
	// Get all site users
	// For each user as user, if $ref_user_id == null update with default user ID
	$users = get_users( 'fields=ID' );
	
	foreach ( $users as $user ) {
		
		// get user ID of referrer
		$key = 'wplp_referrer_id';
		
		$parent_id = get_user_meta($user->ID, $key, true);	
		
		if( empty($parent_id) && $parent_id != 0 ) {
				
			update_user_meta( $user->ID, 'wplp_referrer_id', $defaultUser );
			
		}
	
	}
	
}

function wplp_multiexplode ($delimiters,$string) {
    
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}

// sanitizes our crazy array of objects... 
function wplp_object_to_array($obj) {
	
    if(is_object($obj)) $obj = (array) $obj;
    
		if(is_array($obj)) {
		
			$new = array();
			foreach($obj as $key => $val) {
				$new[$key] = wplp_object_to_array($val);
		}
    
	}
    
	else $new = $obj;
    
	return $new;       
	
}

/**
* Get all values from specific key in a multidimensional array
*
* @param $key string
* @param $arr array
* @return null|string|array
*/
function wplp_array_values_recursive($key, array $arr){
    
	$val = array();
    
	array_walk_recursive($arr, function($v, $k) use($key, &$val){ 
	
		if($k == $key) array_push($val, $v);
    
	});
    
	return count($val) > 1 ? $val : array_pop($val);
}

########################
//WPLP Licence Status Check
########################

function wplp_status_check(){
 /*		
	// Get API options
	$api_options = get_option( 'wplp_api_manager' );
				
	// Get last active time
	$wplp_status_active_time = get_option( '_transient_warp_issue_time' );
	
	// get error count
	$error_count = get_option( '_transient_warp_issue_time_error_count' );
	
	if( !isset( $error_count ) || empty( $error_count ) ) {
		
		$error_count = 0;	
		
	}

	// get last error time
	$error_time = get_option( '_transient_warp_issue_time_error' );
	
	if( !isset( $error_time ) || empty( $error_time ) ) {
			
		$error_time = time()-3600;	
		
	}
	
	$max_errors = 200;
	
	// if time has passed, reset errors and try to get status again
	if( $error_count >= $max_errors && time() >= $error_time+90  ) {
		
		$error_count = 0;	
		
	}			
	
	// if less than X time has passed, don't allow new check
	if( $error_count >= $max_errors && $error_time >= time()-90  ) {
		
		$result = array( 'status_check' => 'too_many_errors' );
			
		return $result;	
		
	}	
	
	// Days between checks
	$days = 1;

	// Set args for status check
	if( isset($api_options) && !empty( $api_options ) ){
		
		$api_email = $api_options['wplp_activation_email'];
		$api_key = $api_options['wplp_api_key'];
	
	}
	
	// Check if values are set for the email and key, if not don't call licensing server, return inactive_not_set
	if ( !isset($api_email) || empty($api_email) || $api_email == false || !isset($api_key) || empty($api_key) || $api_key == false ) {
		
		$result = array( 'status_check' => 'inactive' );
			
		return $result;	
		
	}
	
	// If set continue
	$args = array(
		'email' => $api_email,
		'licence_key' => $api_key,
		);		
	
	// Check if has already been activated and validated last X days
	if ( isset( $wplp_status_active_time ) && !empty( $wplp_status_active_time ) ) { // If activated
		
		// If X days have not passed, return 'active'
		if ( $wplp_status_active_time >= time() ) {
						
			$result = array( 'status_check' => 'active' );
			
			return $result;
			
		} else { // If past X days, check license again
		
			$var = new WPLP_Api_Manager_Key();
			$result = $var->status($args);
			//$result = WPLP_Api_Manager_Key::status($args);
			
			$result = json_decode( $result, true );	
						
			if ( $result["status_check"] == "active" ) {
				
				$active_time = time() + ( $days * 24 * 60 * 60 );
				update_option( '_transient_warp_issue_time', $active_time );
				
				$error_count = 0;
				update_option( '_transient_warp_issue_time_error_count', $error_count );

			
			} 
			
			
		}
		
	} */
		$result = array( 'status_check' => 'active' );
	return $result;
	
}
?>