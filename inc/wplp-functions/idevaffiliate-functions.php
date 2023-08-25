<?php
// Get Username by ID#
// ------------------------------------------------------------------
// Check Username Exists
// ------------------------------------------------------------------


function wplp_validate_idevUser_by_id($idev_id){

	$options = wp_load_alloptions();

	if( isset( $options['wp_leads_press_idevaffiliate_install_directory'] ) && ( !empty( $options['wp_leads_press_idevaffiliate_install_directory'] ) ) ){

		// Variables For Connection		
		$dbhost = $options['wp_leads_press_idevaffiliate_datahost'];
		$dbuser = $options['wp_leads_press_idevaffiliate_dbuser'];
		$dbpass = $options['wp_leads_press_idevaffiliate_dbpass'];
		$dbname = $options['wp_leads_press_idevaffiliate_dbname'];
			
		// Attempt to connect		
		$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

		// Error if no connection is made
		if($db->connect_errno > 0){
			die('The Force is NOT With YOU LUKE: [' . $db->connect_error . ']');
		}

		// Security Check
		if( ! is_numeric($idev_id) )
			die('invalid Affiliate ID');

		// Sql Query
		$sql = "SELECT username FROM idevaff_affiliates WHERE id = " . $idev_id;

		if(!$result = $db->query($sql)){
			
			die('The Dark Side has created error with your query: [' . $db->error . ']');
		
		}

		// Check for number of rows
		$result->num_rows;

			
				
		if($result->num_rows){
		
			// If user ID# exists
			return $idev_id;		
			
		} else {

			//error_log( 'return = NULL' );
			
			// If user ID# does not exist
			return NULL;
		
		}
		
		$result->free();
		// Close the db connection
		$db->close();
	
	}

}


function wplp_get_idev_user( $idev_id ) {
	global $wplp_admin, $wpdb, $wp, $current_user, $post_id, $_GET, $_POST;
	
  	$options = wp_load_alloptions();
	
	// Get data of company associated with idevaffiliate
	$companyIdev = $options['wp_leads_press_idevaffiliate_company'];
	$taxonomies = 'wplp_opportunity';
	$args = array(
		'orderby'       => 'name', 
		'order'         => 'ASC',
		'hide_empty'    => true, 
		'exclude'       => array(), 
		'exclude_tree'  => array(), 
		'include'       => $companyIdev,
		//'include'       => array(5,6),
		
		'number'        => '', 
		'fields'        => 'all', 
		'slug'          => '', 
		'parent'         => '',
		'hierarchical'  => true, 
		'child_of'      => 0, 
		'get'           => '', 
		'name__like'    => '',
		'pad_counts'    => false, 
		'offset'        => '', 
		'search'        => '', 
		'cache_domain'  => 'core'
	); 
	
	$companies = get_terms( $taxonomies, $args );		

	//error_log('companies value ='. print_r($companies, true));	
	
	if( !empty( $companies ) ) {
	
		//error_log('users lists'. print_r($users, true) );
		
		foreach ( $companies as $company ) {
	
		$users = get_users( array( 'fields' => array( 'ID' ) ) );
	
/* 		$trackingID = 'wplp_tracking_id_'.$company->slug;
		
		//$metaTrackID = $metaData[$trackingID][0];

		$args = array(
			'blog_id'      => $GLOBALS['blog_id'],
			'role'         => '',
			'role__in'     => array(),
			'role__not_in' => array(),
			'meta_key'     => $trackingID, // the tracking key
			'meta_value'   => $idev_id, // the value
			'meta_compare' => '=', // only return ID with value equal
			'meta_query'   => array(),
			'date_query'   => array(),        
			'include'      => array(),
			'exclude'      => array(),
			'orderby'      => 'ID',
			'order'        => 'ASC',
			'offset'       => '',
			'search'       => '',
			'number'       => '',
			'count_total'  => false,
			'fields'       => array( 'ID' ),
			'who'          => ''
		 ); 
		
		$users = get_users( $args ); */

	
			foreach ( $users as $user )	{	
				$user_id = $user->ID;	
				$metaData = get_user_meta($user_id);
				
				//error_log('user meta data'. print_r($metaData, true));
				
				$trackingID = 'wplp_tracking_id_'.$company->slug;
				
				$metaTrackID = $metaData[$trackingID][0];
				
				//error_log('metatrackID'. $metaTrackID );
				
				if( ( isset($metaTrackID) ) && ( $metaTrackID == $idev_id ) ){
					
					//error_log('meta tracking ID = '. $metaTrackID. ' and == to idev_id value = '. $idev_id );
					
					//$trackingID = $metaData[$trackingID][0];
					return $user->ID; //returns wordpress user ID
					break;
					
				}
				
			}

		}

	}
	
	return NULL;
}

function wplp_get_idev_user_wpID( $ref_user_id = array() ) {
	global $wplp_admin, $wpdb, $wp, $current_user, $post_id, $_GET, $_POST;
	
  	$options = wp_load_alloptions();
	
	// Get data of company associated with idevaffiliate
	$companyIdev = $options['wp_leads_press_idevaffiliate_company'];
	$taxonomies = 'wplp_opportunity';
	$args = array(
		'orderby'       => 'name', 
		'order'         => 'ASC',
		'hide_empty'    => true, 
		'exclude'       => array(), 
		'exclude_tree'  => array(), 
		'include'       => $companyIdev,
		//'include'       => array(5,6),
		
		'number'        => '', 
		'fields'        => 'all', 
		'slug'          => '', 
		'parent'         => '',
		'hierarchical'  => true, 
		'child_of'      => 0, 
		'get'           => '', 
		'name__like'    => '',
		'pad_counts'    => false, 
		'offset'        => '', 
		'search'        => '', 
		'cache_domain'  => 'core'
	); 
	
	$companies = get_terms( $taxonomies, $args );		

	//error_log('companies value ='. print_r($companies, true));	
	
	if( !empty( $companies ) ) {
	
		foreach ( $companies as $company ) {
					
				$user_id = $ref_user_id;
				$metaData = "";
				$metaData = get_user_meta($user_id);
				
				//error_log('user meta data'. print_r($metaData, true));
				
				$trackingID = "";
				$trackingID = 'wplp_tracking_id_'.$company->slug;
				
				if( isset($metaData) && ( !empty($metaData) ) ){
					
					$metaTrackID = "";
					if( !empty( $metaData[$trackingID][0] ) ){
						
						$metaTrackID = $metaData[$trackingID][0];
					
					}
					
				}
				
					//error_log('metatrackID'. $metaTrackID );
				
				if( ( isset($metaTrackID) ) && ( !empty($metaTrackID) ) ){
					
					//error_log('meta tracking ID = '. $metaTrackID. ' and == to idev_id value = '. $ref_user_id );
					
					//$trackingID = $metaData[$trackingID][0];
					return $metaTrackID; //returns wordpress user ID
					
				} else {
					
					return NULL;
					
				}
				
			

		}

	}
	
	return NULL;
}
	
// Ref = userid#
function wplp_idevaffiliate_set_affiliate_cookie($idev_id, $ref_user_id){
// Sets cookie for idevaffliate when WPLP link is used

	global $wp, $wpdb, $wp_query, $_GET, $_POST;

  	$options = wp_load_alloptions();		
	//error_log( 'ref user id# set cookie = ' . $idev_id );
		
	//  Check if idevaffiliate is installed and active
	if( isset( $options['wp_leads_press_idevaffiliate_install_directory'] ) ){
		
		if ( $ref = $wp_query->get('ref') ) {
			
			// verify the idevaffiliate user ID#
			if( wplp_get_idev_user_wpID( $ref_user_id ) ){ #checks to see if an idevaffiliate member
				
				error_log('wplp get idev user wpID value = '. $ref_user_id);
				
				// Check ref_user_id for metadata related to company affiliated with idevaffiliate for tracking
				$idevaffID = wplp_get_idev_user_wpID( $ref_user_id );
				
//				$affiliate_user_id = $idevaffID;
				$affiliate = $idevaffID;
				
			} else {
				
//				$affiliate_user_id = NULL;	
				$affiliate = NULL;	

				//error_log( 'affiliate_user_id is FALSE' );
			
			}
			
			// Now set var for affiliate, either 0/null or to org referrer
			//$validID = wplp_validate_idevUser_by_id( $idev_id );
			
			//settype($validID, 'int');
			
			// Validate if user has ID set for company associated with idevaffiliate
			//$affiliate = wplp_get_idev_user($validID);
			
			if( $options['wp_leads_press_idevaffiliate_active_distribution_on'] == 'on' && $options['wp_leads_press_smn_on'] == 'on' ){
 
				if( $affiliate == NULL ) {
	
					// Check for RSMN and reset value if null
					$affiliate = wplp_idevaffiliate_rsmn($idev_id, $ref_user_id = NULL );
						
				}
				
				// check if orphans are passed up
				if( $options['wp_leads_press_idevaffiliate_orphans_to_upline'] == 'on' ){
					
					if( $affiliate == NULL ){
					
						$affiliate = wplp_idevaffiliate_orphans_to_upline( $idev_id, $affiliate = NULL );
						
					}
					
				}
			
			
			}
						
			// Track the original visit in idevaffiliate plugin
			###affiliates_record_hit( $affiliate[0] );			
			
			
		} else { // No ref, i.e. direct registration action 
		
			//$validID = wplp_validate_idevUser_wpID( $idev_id );
			
			//settype($validID, 'int');
			// Now check if affiliate has ID set for program associated with ideveaffiliate.
			
			$affiliate = wplp_get_idev_user_wpID($ref_user_id);			
			
			if( $options['wp_leads_press_idevaffiliate_active_distribution_on'] == 'on' && $options['wp_leads_press_smn_on'] == 'on' ){
			
				if( $affiliate == NULL ) {

					// Check for RSMN and reset value if null
					$affiliate = wplp_idevaffiliate_rsmn($idev_id, $ref_user_id);
						
				}
				
				// check if orphans are passed up
				if( $options['wp_leads_press_idevaffiliate_orphans_to_upline'] == 'on' ){

					if( $affiliate == NULL ){
				
						$affiliate = wplp_idevaffiliate_orphans_to_upline( $idev_id, $affiliate = NULL );
					
					}
				
				}

			}			
				
		} // end if ref Not set
		

		//error_log('affiliate set and ===='. $affiliate);
				
		// Set cookie for affiliate 
		if ( isset( $affiliate ) ) {
	
			//error_log('cookie info idev before reset: ' . $_COOKIE['idev']);
			
			//$cookieSet = $_COOKIE['idev'];
			
			if( isset( $_COOKIE['idev'] ) ) { // Let's extend this to give options for cookie duration in later version.
				
				// Unset previous cookie, if it exists
				setcookie( 'idev', NULL, time()-1, SITECOOKIEPATH, COOKIE_DOMAIN, false );//Unsetting
				
			}						
			
			// Now reset cookie under this affiliate
			
			setcookie(
				'idev',
				$affiliate.'-',
				time()+86400*30*60,
				SITECOOKIEPATH,
				COOKIE_DOMAIN
			);	
			
		}
	
	}	
	
}


function wplp_idevaffiliate_set_wplp_cookie($idev_id) {	
	global $wp, $wpdb, $wp_query, $_GET, $_POST;

	//sets cookie for wplp when idevaffiliate link is used

	$options = wp_load_alloptions();
	//$installDir = $options['wp_leads_press_idevaffiliate_install_directory'];
	
	if( ( isset($options['wp_leads_press_idevaffiliate_install_directory']) ) && ( !empty($options['wp_leads_press_idevaffiliate_install_directory']) ) ){
			
		// Check if an idevaffiliate plugin link has been used.	
		//if( isset($idev_id) ){


			//error_log('idevaffiliate ID =' . $affiliateID);
			
			// check if aff ref is number or text
			if( is_numeric( $idev_id ) ) {
			
				//error_log('in integer yes and idev_id ID =' . $idev_id);
				
				// NOTES
				// Get the user id of affiliate to set cookie for WPLP
				// match idevaffiliate ID to 'internal' affiliate program campaign company affiliate ID
				
				
				// match to username of WPLP Wordpress install
				$validID = wplp_validate_idevUser_by_id( $idev_id );
				//error_log('valid id value = '. $validID );
				
				if ( isset($validID) ){
					
					//error_log('valid ID isset =' . $validID);
					
					// Get Wordpress user ID with idev_id
					if( wplp_get_idev_user( $idev_id ) != NULL ){
						
						$wpuser = wplp_get_idev_user( $idev_id );
				
					} else {
						
						$wpuser = 0;
						
					}
					//error_log('wp get idev user function = '. $wpuser );
					//error_log('wpuser =='. $wpuser . 'idev_id== ' . $idev_id );					
					$ref_user_id = $wpuser;

				} else {
					
					$ref_user_id = 0;					
					
				}
				
				// Get the nicename of the referring affiliate using nicename to set WPLP Cookie
				$ref_user_nicename = wplp_ref_user_nicename_by_id( $ref_user_id );	
				//error_log('ref username set value = '. $ref_user_nicename . 'ref user id value = ' . $ref_user_id );
				
				if( !isset($ref_user_nicename) ){
					
					//error_log('ref user nicename not set.');
					$ref_user_nicename = 'NULL';
					
				}
				
				//error_log('ref user nicename =='. $ref_user_nicename);
				//error_log('just before setting cookie function called.');
				// Set cookie for tracking in WPLP
				wplp_set_cookie_ref($ref_user_nicename);							
			
			} else { // if the url var is not a number, i.e. a username... 
			
//error_log('after else statement');
			
				$ref_user_nicename = $idev_id;
				
				// check to see if the var is a username... 
				if( wplp_ref_user_id_by_nicename($ref_user_nicename) != false ){
					
					$ref_user_nicename = $idev_id;
					
					// lets set the cookie for idevaffiliate plugin for affiliate
					// this is so that the idevaffiliate and WPLP URL vars can be the same
					// i.e. www.yoursite.com/?ref=username, and the affiliate link can also be /?ref=userid OR username
					
					// get the user id of ref user from nicename
					$ref_user_id = wplp_ref_user_id_by_nicename($ref_user_nicename);
					
					// Set cookie for tracking in WPLP
					wplp_set_cookie_ref($ref_user_nicename);
					
				}
								
			} // end else
					
		//} // end if isset $pname
		
	} // end check if idevaffiliate exists
	
}

function wplp_idevaffiliate_rsmn($ref_user_id, $user_id){
	global $wp, $wpdb, $wp_query, $_GET, $_POST;

  	$options = wp_load_alloptions();	
	// Reset the cookie for idevaffiliate plugin if 'run as network' is selected for integration.
	
	$wplp_idevaffiliate_as_network = $options['wp_leads_press_idevaffiliate_as_network'];
	$wplp_smn_on = $options['wp_leads_press_smn_on'];
	
	// referrer is not affiliate and run as network is on.
	if( ( wplp_validate_idevUser_by_id( $ref_user_id ) == NULL ) && ( $wplp_affiliates_as_network == 'on' ) && ( $wplp_smn_on == 'on' ) ){					

		// traverse for referrer who is affiliate below ref
		$affiliate_user_id = wplp_traverseForReferer_idevaffiliate( array( $ref_user_id ), $user_id );
		
		// if no child is affiliate, meaning entire network is not in affiliate program, set affiliate id to user 0, i.e. 'direct'.
		if($affiliate_user_id == false){
			
			// first try giving back to original referrer or to direct if org referrer is also not an affiliate
			if( wplp_validate_idevUser_by_id( $idev_id ) ){
				
				$affiliate_user_id = $ref_user_id;
				
			} else {
			
				$affiliate_user_id = 0;	
				//error_log( 'affiliate_user_id is FALSE' );
				
				// Do check here if orphans to upline is on and pass to upline of original ref???
			
			} 			
			
		}		
		
		// Now set var for affiliate, either 0/null or to org referrer
		$affiliate = wplp_validate_idevUser_by_id( $affiliate_user_id );
		//error_log( '!canhaveleg affiliate id = ' . print_r( $affiliate, true ) );
							
		return $affiliate;
								
	} else { // network is not on just give to current user or to upline if null and orphans is on
		
		$affiliate = wplp_validate_idevUser_by_id( $ref_user_id );	 // array i.e. affiliate[0] to get result		
		
		$ref_user_id = wplp_get_idev_user( $ref_user_id );
			
		return $affiliate;
		
	}
	
}


function wplp_idevaffiliate_orphans_to_upline( $ref_user_id, $affiliate ){
	global $wp, $wpdb, $wp_query, $_GET, $_POST;

  	$options = wp_load_alloptions();
		
	// if after looking at users team and not finding user who is affiliate with open leg
	// if option selected pass orphan referrals to upline user who is active in affiliate program.			
	$wplp_orphans_to_upline = $options['wp_leads_press_idevaffiliates_orphans_to_upline'];

	if ( $wplp_orphans_to_upline == 'on' ) {	

		if ( $affiliate == NULL ) {
		
			$ancestors = wplp_get_ancestors( $ref_user_id, $ancestors=array() );
			//error_log( 'null ancestors = ' . print_r( $ancestors, true ) );
			
			if( is_array( $ancestors ) ) {
		
				foreach ( $ancestors as $ancestor ) {
					
					//error_log( 'ancestors as ancestor = ' . print_r( $ancestor, true ) );
					
					if( ( wplp_can_have_member( $ancestor, $ref_user_id ) === true ) && ( wplp_valdidate_idevUser_by_id( $ancestor ) !== NULL ) && ( wplp_get_idev_user( $ancestor ) !== NULL ) ){
					
						$affiliate = wplp_validate_idevUser_by_id( $ancestor );
						//error_log( 'affiliate null new member = ' . print_r( $affiliate, true ) );
						
						return $affiliate;
						break;
					
					} 
					
				} // end foreach
			
			} // end if array
		
		} // end if $affiliate null
	
	} // end if $wplp_orphans_to_upline == yes
	
	
}

function wplp_traverseForReferer_idevaffiliate($ref_user_id, $user_id) {
	global $wp, $wpdb, $wp_query, $_GET, $_POST;
	
  	$options = wp_load_alloptions();
	
	$wplp_affiliates_as_network = $options['wp_leads_press_idevaffiliate_as_network'];

	# Get all children below this level
	$values = implode(",", $ref_user_id);	
	$sql = "SELECT user_id FROM ".$wpdb->prefix."usermeta WHERE meta_value IN (".$values.") AND meta_key IN ('wplp_referrer_id') ORDER BY user_id ASC";	

	$users = $wpdb->get_results($sql);
	$ref_user_id = array();

	if(!empty($users)){
	
		foreach ( $users as $obj ) {
		
			if( $wplp_idevaffiliate_as_network == 'on' ) {
					
				if( ( wplp_can_have_member( $obj->user_id, $user_id ) === true ) && ( wplp_validate_idevUser_by_id( $obj->user_id ) !== NULL ) && (wplp_get_idev_user( $obj->user_id ) !== NULL ) ) {
				
				  return $obj->user_id;
				  break;
				
				}
				
			} else {

				if( ( wplp_validate_idevUser_by_id( $obj->user_id ) != NULL ) && (wplp_get_idev_user( $obj->user_id ) != NULL ) ) {
				
				  return $obj->user_id;
				  break;
				
				}				
				
			}
			
			$ref_user_id[] = $obj->user_id;
		
		}
	
		return wplp_traverseForReferer_idevaffiliate($ref_user_id, $user_id);
	
	} else {
		
		return false;
		
	}

}

?>