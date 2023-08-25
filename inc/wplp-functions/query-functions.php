<?php
add_filter('query_vars', 'wplp_add_custom_page_variables');
function wplp_add_custom_page_variables( $public_query_vars ){
	$public_query_vars[] = "action";
	$public_query_vars[] = "ref";
	$public_query_vars[] = "unsub";
	$public_query_vars[] = "sublead";
	$public_query_vars[] = "subtraffic";	
	$public_query_vars[] = "rotation";
	//$public_query_vars[] = "id";
	return $public_query_vars;
}

function wplp_template_redirect_intercept() {
	global $wp, $wpdb, $wp_query, $_GET, $_POST;
	
	$options = wp_load_alloptions();
	
	//Executes the create lead function when opt-in code is used
	if ( isset($_GET['sublead'] ) ){
		
		wplp_create_lead();
		
	}

	if ( isset($_GET['unsub'] ) ){
		
		wplp_unsubscribe_lead($_GET['unsub']);	
		
	}
		
	if ( isset($_GET['subtraffic'] ) ){
		
		wplp_traffic_redirect();
		
	}

	if ( isset($_GET['rotation'] ) ){
		
		// Set cookie using nicename
		wplp_set_cookie_ref( 'wplp_rotation' );
		
	}	

	
	// Set cookie for WPLP if Affiliates link is used
	wplp_itthinx_set_wplp_cookie();
	
	
	// Set cookie for WPLP if iDevAffiliates link is used			
	if( isset( $_COOKIE['idev'] ) ){
		
		//error_log('refcookie is set = '. $_COOKIE['idev']);
		
		$iDevCookie = $_COOKIE['idev'];
		
	} else {
		
		$iDevCookie = NULL;
		
	}
		
	if( isset($iDevCookie) ){
		
		// Sanitize the cookie value to only have idev cookie value for user ID#
		list($idev_id, $junk) = explode( "-", $iDevCookie, 2 );
		//$idev_id holds the idevaffiliate ID#
		//$junk holds the rest of the cookie value
	
		// Set to integer
		settype($idev_id, 'int');
	
	} else {
		
		$idev_id = NULL;
		
	}
	
	if ( isset($idev_id) != NULL ){
	
		// Set cookie for WPLP if idevaffiliate link is used
		//$getID = $refCookie;
		
		//error_log( 'issset idev id -- ' . $idev_id );
		wplp_idevaffiliate_set_wplp_cookie($idev_id);

	}
	
/* 	if ( isset( $_GET['id'] ) ){
			
		$idev_id = $_GET['id'];
		wplp_idevaffiliate_set_wplp_cookie($idev_id);
		
	} */
	
	//Sets WPLP cookie using URL parameters
	if ( isset( $_GET['ref'] ) ) {

	
		$ref_raw = $_GET['ref'];		

		// Get Referrer's user ID by nicename/cookie value
		$check_for_user_id = wplp_ref_user_id_by_nicename($ref_raw);
		
		if( $check_for_user_id == NULL ){ // if user not found by nicename, for cases when other plugin uses same ?ref= format with user id

			// if null lets see if user is using an affiliate link from iithinx
			if( function_exists( 'affiliates_admin_init' ) ){
				
				include_once( AFFILIATES_CORE_LIB . '/wp-init.php' );		
				
				// checl of ref_raw matches any affiliates in system
				$ref_user_id = affiliates_get_affiliate_user( $ref_raw );				
							
				// Get the nicename of the referring affiliate using nicename to set WPLP Cookie
				$ref_user_nicename = wplp_ref_user_nicename_by_id($ref_user_id);	
			
			}
			

			// if null lets see if user is using an affiliate link from idevaffiliate
			if( isset( $options['wp_leads_press_idevaffiliate_install_directory'] ) ){
				
				
				// check of ref_raw matches any idevaffiliates in system
				if( is_int( $ref_raw ) ){
					
					$idev_id = wplp_validate_idevUser_by_id( $ref_raw );
					
					// Now find Wordpress member with affiliate ID set for company and return wp user id				
					$ref_user_id = wplp_get_idev_user( $idev_id );
								
					// Get the nicename of the referring affiliate using nicename to set WPLP Cookie
					$ref_user_nicename = wplp_ref_user_nicename_by_id($ref_user_id);	
				
				}
			}

			
			// Add other checks for other plugins here.
			
			if( $ref_user_id == NULL ){
				
				// Do check to see if ID # is being used for ref value
				$check_for_user_nicename = wplp_ref_user_nicename_by_id($ref_raw);
				
				if($check_for_user_nicename != NULL ){
					
					$ref_user_nicename = $check_for_user_nicename;
					$ref_user_id = $ref_raw;
					
				}
					
			}
				
		} else { // if the user is found
		
			$ref_user_id = $check_for_user_id; // wp user ID# of referrer			
			$ref_user_nicename = $_GET['ref'];
		
		}
		
		if( $ref_user_id == NULL ){ // if no user found, do nothing.
			
		} else { // if $ref_user_id returns a actual user...
			
			// Set cookie using nicename
			wplp_set_cookie_ref($ref_user_nicename);							
	
			// Check if itthinx Affiliates plugin is being used and set cookie for affiliates plugin
			wplp_itthinx_set_affiliate_cookie($ref_user_id, $user_id = NULL );		

			// Check if idevaffiliate is being used and set cookie for idevaffiliate
			// $ref_user_id === wordpress user ID#
			//$idev_id = wplp_get_idev_user( $ref_user_id );
			//settype($idev_id, 'int');
			
			wplp_idevaffiliate_set_affiliate_cookie($idev_id, $ref_user_id );
			
			// Get User ref traffic count		
			$refTraffic = get_user_meta( $ref_user_id, 'wplp_ref_traffic_count', true );
			
			//error_log('### ref traffic ### - ' . $refTraffic );
			
			// Get all wplp options
			$options = wp_load_alloptions();
			
			$requiredTraffic = $options['wp_leads_press_ref_traffic_count_required'];
			
			//Check for bonus leads
			$bonusLeads = get_user_meta($ref_user_id, 'wplp_bonus_leads', true);
			$bonusLeadsValue = $options['wp_leads_press_ref_traffic_bonus_leads_value'];
			
				if( $options['wp_leads_press_ref_traffic_count_required'] != 0 ){
									
					if( $refTraffic <= $requiredTraffic ) {
					
						$update = $refTraffic+1;
						// Check to see if $refTraffic+1 = $requireTraffic
						if( $update == $requiredTraffic ){
							
							// Add bonus leads here
							$bonusLeads = $bonusLeads+$bonusLeadsValue;
							update_user_meta( $ref_user_id, 'wplp_bonus_leads', $bonusLeads );
							
						}										
					
					}
					
					if( $refTraffic >= $requiredTraffic ) {
					
						$update = 1;
					
					} 				
					
				
				// Update user traffic count of referring user
				update_user_meta( $ref_user_id, 'wplp_ref_traffic_count', $update );
				
				} // End if traffic count required
		
				// Update User total ref traffic count
				$totalRefTraffic = get_user_meta( $ref_user_id, 'wplp_total_ref_traffic_count', true );
				$totalRefTraffic = $totalRefTraffic+1;
				update_user_meta( $ref_user_id, 'wplp_total_ref_traffic_count', $totalRefTraffic );

		}// end is null ref_user_id, i.e. bad value sent in url for ref=
		
		
	
	}// End isset( $_GET['ref'] )
	
}

// Used to keep url variables in string so when home page set as static is used blog index is not shown in error
// also keeps url variable from interfering with other pages and/or plugins.
add_action( 'pre_get_posts', 'wplp_unset_query_arg' );
//add_action( 'wp', 'wplp_unset_query_arg' );
function wplp_unset_query_arg( $query ) {
	global $wp, $wpdb, $wp_query, $_GET, $_POST;

    if ( is_admin() || ! $query->is_main_query() ) {
	
      return;
    
	}
	
	wplp_template_redirect_intercept();	
    
    $keys = array( 'ref', 'unsub', 'subtraffic', 'sublead', 'rotation' ); // this is the url variable
	
	foreach( $keys as $key ){
  
		$value = $query->get( $key );
	  
		if ( ! empty( $value ) ) {
	  
		  // unset ref var from $wp_query
		  $query->set( $key, null );
	  
		  // unset ref var from $wp
		  unset( $wp->query_vars[ $key ] );
	  
		  // if in home (because $wp->query_vars is empty) and 'show_on_front' is page
		  if ( empty( $wp->query_vars ) && get_option( 'show_on_front' ) === 'page' ) {
			
			// reset and re-parse query vars
			$wp->query_vars['page_id'] = get_option( 'page_on_front' );
			$query->parse_query( $wp->query_vars );
		  
		  }
	  
		}	
	
	}
	
}
?>