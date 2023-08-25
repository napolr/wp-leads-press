<?php
function wplp_itthinx_set_wplp_cookie() {	

	if( function_exists( 'affiliates_admin_init' ) ){
		
		// Get value of URL var for affiliates cookie
		$pname = get_option( 'aff_pname', AFFILIATES_PNAME );
					
		// Check if an afifiliates plugin link has been used.	
		if ( isset($_GET[$pname] ) ){

			$affiliateID = $_GET[$pname];

			
			// check if aff ref is number or text
			if( is_int( $affiliateID ) ) {
			
				
				// Get the user id of affiliate to set cookie for WPLP
				$ref_user_id = affiliates_get_affiliate_user( $affiliateID );
							
				// Get the nicename of the referring affiliate using nicename to set WPLP Cookie
				$ref_user_nicename = wplp_ref_user_nicename_by_id($ref_user_id);	

				// Set cookie for tracking in WPLP
				wplp_set_cookie_ref($ref_user_nicename);							
			
			} else { // if the url var is not a number, i.e. a username... 
				
				$ref_user_nicename = $affiliateID;
				
				// check to see if the var is a username... 
				if( wplp_ref_user_id_by_nicename($ref_user_nicename) != false ){
					
					$ref_user_nicename = $affiliateID;
					
					// lets set the cookie for itthinx affiliate plugin for affiliate
					// this is so that the affiliates and WPLP URL vars can be the same
					// i.e. www.yoursite.com/?ref=username, and the affiliate link can also be /?ref=userid OR username
					
					// get the user id of ref user from nicename
					$ref_user_id = wplp_ref_user_id_by_nicename($ref_user_nicename);
					
					// set the cookie
					//wplp_itthinx_set_affiliate_cookie($ref_user_id); // why do double duty calling again within other cookie?
					
					// Set cookie for tracking in WPLP
					wplp_set_cookie_ref($ref_user_nicename);
					
				}
								
			} // end else
					
		} // end if isset $pname
		
	} // end check if Ithinx exists
	
}

function wplp_itthinx_rsmn($ref_user_id, $user_id){
	global $wp, $wpdb, $wp_query, $_GET, $_POST;

  	$options = wp_load_alloptions();	
	// Reset the cookie for Affiliate plugins if 'run as network' is selected for integration.
	
	$wplp_affiliates_as_network = $options['wp_leads_press_affiliates_as_network'];
	$wplp_smn_on = $options['wp_leads_press_smn_on'];
	
	// referrer is not affiliate and run as network is on.
	if( ( affiliates_get_user_affiliate( $ref_user_id ) == NULL ) && ( $wplp_affiliates_as_network == 'on' ) && ( $wplp_smn_on == 'on' ) ){					

		// traverse for referrer who is affiliate below ref
		$affiliate_user_id = wplp_traverseForReferer_affiliate( array( $ref_user_id ), $user_id );
		
		// if no child is affiliate, meaning entire network is not in affiliate program, set affiliate id to user 0, i.e. 'direct'.
		if($affiliate_user_id == false){
			
			// first try giving back to original referrer or to direct if org referrer is also not an affiliate
			if( affiliates_get_user_affiliate( $ref_user_id ) ){
				
				$affiliate_user_id = $ref_user_id;
				
			} else {
			
				$affiliate_user_id = 0;	
				//error_log( 'affiliate_user_id is FALSE' );
				
				// Do check here if orphans to upline is on and pass to upline of original ref???
			
			} 			
			
		}		
		
		// Now set var for affiliate, either 0/null or to org referrer
		$affiliate = affiliates_get_user_affiliate( $affiliate_user_id );
		//error_log( '!canhaveleg affiliate id = ' . print_r( $affiliate, true ) );
							
		return $affiliate;
								
	} else { // network is not on just give to current user or to upline if null and orphans is on
		
		$affiliate = affiliates_get_user_affiliate( $ref_user_id );	 // array i.e. affiliate[0] to get result		
					
		return $affiliate;
		
	}
	
}

// Ref = userid#
function wplp_itthinx_set_affiliate_cookie($ref_user_id, $user_id){
	global $wp, $wpdb, $wp_query, $_GET, $_POST;

  	$options = wp_load_alloptions();		
	//error_log( 'ref user id = ' . $ref_user_id );
		
	//  Check if affiliates, affiliates pro or enterprise is installed
	if( function_exists( 'affiliates_admin_init' ) ){

		include_once( AFFILIATES_CORE_LIB . '/wp-init.php' );
		
		
		if ( $ref = $wp_query->get('ref') ) {
			
			// try giving back to original referrer or to direct if referrer is not an affiliate
			if( affiliates_get_user_affiliate( $ref_user_id ) ){
				
				$affiliate_user_id = $ref_user_id;
				
			} else {
			
				
				$affiliate_user_id = 0;	
				//error_log( 'affiliate_user_id is FALSE' );
			
			}
			
			// Now set var for affiliate, either 0/null or to org referrer
			$affiliate = affiliates_get_user_affiliate( $affiliate_user_id );
			
			$wplp_smn_on = $options['wp_leads_press_smn_on'];
			
			if( $wplp_active_distribution_on = $options['wp_leads_press_active_distribution_on'] == 'on' && $wplp_smn_on == 'on' ){
 

				if( $affiliate == NULL ) {
	
					// Check for RSMN and reset value if null
					$affiliate = wplp_itthinx_rsmn($ref_user_id, $user_id);
						
				}
				
				// check if orphans are passed up
				if( $affiliate == NULL ){
				
					$affiliate = wplp_itthinx_orphans_to_upline( $ref_user_id, $affiliate = NULL );
					
				}
			
			
			}
						
			// Track the original visit in affiliates plugin
			affiliates_record_hit( $affiliate[0] );
			
			
			
			
		} else { // No ref, i.e. registration action 
		
			$affiliate = affiliates_get_user_affiliate( $ref_user_id );

//			if( $affiliate == NULL ) {
//
//				// if the user has a cookie already, give to the user who referred to affiliate program
//				if( isset( $_COOKIE['wp_affiliates'] ) ) { // Let's extend this to give options for cookie duration in later version.
//	
//					$affiliate = affiliates_get_user_affiliate( $_COOKIE['wp_affiliates'] );
//	
//				}	
//					
//			}

			if( $affiliate == NULL ) {

				// Check for RSMN and reset value if null
				$affiliate = wplp_itthinx_rsmn($ref_user_id, $user_id);
					
			}
			
			// check if orphans are passed up
			if( $affiliate == NULL ){
			
				$affiliate = wplp_itthinx_orphans_to_upline( $ref_user_id, $affiliate = NULL );
				
			}			
				
		} // end if ref Not set
		


				
		// Set cookie for affiliate 
		if ( isset( $affiliate ) ) {
				
			if( isset( $_COOKIE['wp_affiliates'] ) ) { // Let's extend this to give options for cookie duration in later version.
				
				// Unset previous cookie, if it exists
				setcookie( 'wp_affiliates', NULL, time()-1, SITECOOKIEPATH, COOKIE_DOMAIN, false );//Unsetting
				
			}						
			
			// Now reset cookie under this affiliate
			
			setcookie(
				AFFILIATES_COOKIE_NAME,
				$affiliate[0],
				time()+86400*30*12,
				SITECOOKIEPATH,
				COOKIE_DOMAIN
			);	
			
		}
	
	}	
	
}

function wplp_itthinx_orphans_to_upline( $ref_user_id, $affiliate ){
	global $wp, $wpdb, $wp_query, $_GET, $_POST;

  	$options = wp_load_alloptions();
		
	// if after looking at users team and not finding user who is affiliate with open leg
	// if option selected pass orphan referrals to upline user who is active in affiliate program.			
	$wplp_orphans_to_upline = $options['wp_leads_press_orphans_to_upline'];

	if ( $wplp_orphans_to_upline == 'on' ) {	

		if ( $affiliate == NULL ) {
		
			$ancestors = wplp_get_ancestors( $ref_user_id, $ancestors=array() );
			//error_log( 'null ancestors = ' . print_r( $ancestors, true ) );
			
			if( is_array( $ancestors ) ) {
		
				foreach ( $ancestors as $ancestor ) {
					
					//error_log( 'ancestors as ancestor = ' . print_r( $ancestor, true ) );
					
					if( affiliates_get_user_affiliate( $ancestor ) ){
					
						$affiliate = affiliates_get_user_affiliate( $ancestor );
						//error_log( 'affiliate null new member = ' . print_r( $affiliate, true ) );
						
						return $affiliate;
						break;
					
					} 
					
				} // end foreach
			
			} // end if array
		
		} // end if $affiliate null
	
	} // end if $wplp_orphans_to_upline == yes
	
	
}

function wplp_traverseForReferer_affiliate($ref_user_id, $user_id) {
	global $wp, $wpdb, $wp_query, $_GET, $_POST;
	
  	$options = wp_load_alloptions();
	
	$wplp_affiliates_as_network = $options['wp_leads_press_affiliates_as_network'];

	# Get all children below this level
	$values = implode(",", $ref_user_id);	
	$sql = "SELECT user_id FROM ".$wpdb->prefix."usermeta WHERE meta_value IN (".$values.") AND meta_key IN ('wplp_referrer_id') ORDER BY user_id ASC";	

	$users = $wpdb->get_results($sql);
	$ref_user_id = array();

	if(!empty($users)){
	
		foreach ( $users as $obj ) {
		
			if( $wplp_affiliates_as_network == 'on' ) {
					
				if( ( wplp_can_have_member( $obj->user_id, $user_id ) === true ) && ( affiliates_get_user_affiliate( $obj->user_id ) != NULL ) ) {
				
				  return $obj->user_id;
				  break;
				
				}
				
			} else {

				if( affiliates_get_user_affiliate( $obj->user_id ) != NULL ) {
				
				  return $obj->user_id;
				  break;
				
				}				
				
			}
			
			$ref_user_id[] = $obj->user_id;
		
		}
	
		return wplp_traverseForReferer_affiliate($ref_user_id, $user_id);
	
	} else {
		
		return false;
		
	} 
  
}

// Lets remove the shortcode 'affiliates_url' and replace with our modified version, if affiliate plguin by itthinx is used
if( function_exists( 'affiliates_admin_init' ) ){
	
	// remove affiliates standard shortcode
	remove_shortcode( 'affiliates_url' );
	
	// add our modified version
	add_shortcode( 'affiliates_url', 'wplp_affiliates_url' );
	
}

// Modified version of affiliates by itthinx function
function wplp_affiliates_url( $atts, $content = null ) {
	global $wpdb;

	$pname = get_option( 'aff_pname', AFFILIATES_PNAME );

	remove_shortcode( 'affiliates_url' );
	$content = do_shortcode( $content );
	add_shortcode( 'affiliates_url', 'wplp_affiliates_url' );

	$output = "";
	$user_id = get_current_user_id();
	if ( $user_id && affiliates_user_is_affiliate( $user_id ) ) {
		$affiliates_table = _affiliates_get_tablename( 'affiliates' );
		$affiliates_users_table = _affiliates_get_tablename( 'affiliates_users' );
		if ( $affiliate_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT $affiliates_users_table.affiliate_id FROM $affiliates_users_table LEFT JOIN $affiliates_table ON $affiliates_users_table.affiliate_id = $affiliates_table.affiliate_id WHERE $affiliates_users_table.user_id = %d AND $affiliates_table.status = 'active'",
			intval( $user_id )
		))) {
			
			// WPLP Hack Start
			$affiliate_id = wplp_ref_user_nicename_by_id($user_id);
			// WPLP Hack End
			
			$encoded_affiliate_id = affiliates_encode_affiliate_id( $affiliate_id );
			if ( strlen( $content ) == 0 ) {
				$base_url = get_bloginfo( 'url' );
			} else {
				$base_url = $content;
			}
			$separator = '?';
			$url_query = parse_url( $base_url, PHP_URL_QUERY );
			if ( !empty( $url_query ) ) {
				$separator = '&';
			}
			$output .= $base_url . $separator . $pname . '=' . $encoded_affiliate_id;
		}
	}
	return $output;
}
// end Affilliates by itthinx integration
?>