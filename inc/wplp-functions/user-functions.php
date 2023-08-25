<?php
function wplp_ref_user_id_by_nicename($ref_user_nicename) {
	
	// get ID# of user referring traffic
	$field = 'slug';
	$value = $ref_user_nicename;
	$ref = get_user_by( $field, $value ); // Get user obj
	
	if ( is_object($ref) ){
		
		$ref_user_id = $ref->ID;	
	
	} else {
		
		$ref_user_id = NULL;	
		
	}
	
	return $ref_user_id;
	
}


function wplp_ref_user_nicename_by_id($ref_user_id) {

	$field = 'id';
	$value = $ref_user_id;
	$refuser = get_user_by( $field, $value ); // Get user obj
	
	if( is_object($refuser) ){
			
		$ref_user_nicename = $refuser->user_nicename;
		
	} else {
		
		$ref_user_nicename = NULL;	
		
	}
	
	return $ref_user_nicename;
	
}

function wplp_get_parent_user_id($user_id) {
	global $wp, $wpdb;
	
	// get user ID of referrer
	$key = 'wplp_referrer_id';
	$parent_id = get_user_meta($user_id, $key, true);
	
	// sanitize $parent_id 
	if( $parent_id != NULL ){
		
		return $parent_id;
		
	} else {
		
		$parent_id = 0;
		return $parent_id;	
		
	}
	
}

function wplp_get_ancestors( $user_id, $ancestors=array() ){
	global $wp, $wpdb;
	
	if( $user_id != 1 ) { // If the current user isn't first user in system
		
		// get user ID of referrer
		$key = 'wplp_referrer_id';
		
		if( $parent_id = get_user_meta($user_id, $key, true) ) {
			
			if ( $parent_id != $user_id ) {
				
				$ancestors[] = $parent_id;
				return wplp_get_ancestors($parent_id, $ancestors);
				
			}
	
		}
	
		return $ancestors;
	
	} else { // If user is first user/admin return empty array
		
		return $ancestors;	
		
	}
}

function wplp_get_children($user_id, $children=array() ){
	global $wp, $wpdb;
	
	$sql = "SELECT user_id FROM ".$wpdb->prefix."usermeta WHERE meta_key IN ( 'wplp_referrer_id' ) AND meta_value IN ( '".$user_id."' ) ORDER BY user_id ASC";		
	$child_ids = $wpdb->get_results( $sql );	
	
	if( $child_ids ) {
			
		$children[] = $child_ids;

			foreach ( $child_ids as $child ) {
			
					$children[] = $child;
					return wplp_get_children($child->user_id, $children);
								
			}
	
	}
	
	// Convert objects to array
	$children = wplp_object_to_array($children);
	
	return $children;
	
}

function wplp_get_network_personals($user_id){
	global $wp, $wpdb;
	
	// Get all users in multidimensional array, array[0], array[1], to infinity possible	
	$user_network_arrays = wplp_get_children($user_id);	

	if( !empty($user_network_arrays) ){
						
		return $user_network_arrays[0];
		
	} else {
		
		$user_network_arrays = NULL;
		return $user_network_arrays;
	
	}
	
}

function wplp_get_network_total( $user_id ) {
	global $wp, $wpdb;
	
	// Get all users in multidimensional array, array[0], array[1], to infinity possible	
	$user_network_arrays = wplp_get_children($user_id);
	
	if( !empty( $user_network_arrays ) ){
		
	// Get all user_ids in user's network
	$network_ids = wplp_array_values_recursive( 'user_id', $user_network_arrays );
	
	// Remove duplicates to return total number of members in network
	$network_ids = array_unique($network_ids);
	return $network_ids;	
	
	} else {
		
		$network_ids = NULL;
		return $network_ids;	
		
	}
}

function wplp_user_banned($user_id){
	// Check Ban Status
	$key = 'wplp_ban_status';
	$status = get_user_meta($user_id, $key, true);
				
	if ( ($status == "b" ) ) {
	
		return true;
	
	} else {
		
		return false;
		
	}
	
}

function wplp_is_affiliate_id_set($user_id){
	global $wp, $wpdb, $wplp_admin, $_GET, $_POST;
	
	// Get all wplp options
	$options = wp_load_alloptions();
				
	if ( isset( $_POST['wplp_campaign'] ) || ( isset( $_GET['wplp_campaign'] ) ) ) {
		
		// Get the campaign ID
		if( isset( $_POST['wplp_campaign'] ) ){
			
			$campaign_id = $_POST['wplp_campaign'];
		
		}
		
		if( isset( $_GET['wplp_campaign'] ) ){
			
			$campaign_id = $_GET['wplp_campaign'];	
			
		}
	
		// Get the landing page ID
		if( isset( $_POST['wplp_landing_page'] ) ) {
			
			$landing_page_id = $_POST['wplp_landing_page'];
		
		}
		
		if( isset( $_GET['wplp_landing_page'] ) ){
			
			$landing_page_id = $_GET['wplp_landing_page'];	
			
		}
				
		// Get the opportunity associated with campaign
		$campaign_company = get_the_terms( $campaign_id, 'wplp_opportunity' );
				
		if( isset( $campaign_company ) && !empty( $campaign_company ) ) {
		
			foreach($campaign_company as $company ){
			
				$slug = $company->slug;
				// Get the ref user tracking ID for opportunity
				$key = 'wplp_tracking_id_' . $slug;			
				$wplp_ref_tracking_id = get_user_meta($user_id, $key, true);
						
			}
		
		}
		
		$checkID = 'ID For Opportunity';
		$checkID2 = 'empty value';
		
		// Do check to see if user has set their tracking ID for opportunity
		// We don't want to send prospects to dead links, do we?
		
		if( ( $wplp_ref_tracking_id == $checkID2 ) || ( $wplp_ref_tracking_id == $checkID ) || ( $wplp_ref_tracking_id == '' ) || ( $wplp_ref_tracking_id == ' ' ) || ( $wplp_ref_tracking_id == false ) || ( $wplp_ref_tracking_id === NULL ) ){
			
			// Send email to member who would have received a lead
			// WHOOPS YOU missed a lead because your Tracking ID is not set! Go to Lead Dashboard>Tracking Settings to set.
			
			$sendSTIE = $options['wp_leads_press_send_set_tracking_id_email'];
			
			if ( $sendSTIE == "on" ) { 
			
				$from_email = $options['wp_leads_press_smtp_from_email'];
				$from_name = $options['wp_leads_press_smtp_from_name'];

				// Get member details.
				$user_info = get_userdata( $user_id );
				
				// Variables for wplp_system_email() function.
				$to_email =  $user_info->user_email;
						
				
				// Do check for custom subject
				
				$subject = $options['wp_leads_press_set_tracking_id_subject'];
				
				if ( isset( $subject ) && !empty( $subject ) ) {
					
					$subject = $subject;
					
				} else {
					
					$subject = __( '{REF-MEMBER-FIRST-NAME}, you just missed a lead from {SITE-NAME}!', 'wp-leads-press' );
					
				}
				
				// Do check for custom message
				
				$message = $options['wp_leads_press_set_tracking_id_body'];
				
				if ( isset( $message ) && !empty( $message ) ) {
					
					$message = $message;
					
				} else {
				
					$message = __( '{REF-MEMBER-FIRST-NAME}, 
						You would have just received a lead from {SITE-NAME}, you just need to update your Affiliate ID in your Lead Dashboard at <a href="{SITE-URL}">Login</a>.
						
						Then visit the Lead Dashboard to update your Affiliate Settings with your ID for {COMPANY-NAME} and you will then be eligible to receive leads.', 'wp-leads-press' );
				
				}
				
				$lead_id = NULL; //NA for this section of code.
				
				$ref_user_id = $user_id; //pass the ref user to system email function.
				
				$member_id = $user_id;
				
				wplp_system_email( $from_email, $from_name, $to_email, $subject, $message, $campaign_id, $landing_page_id, $ref_user_id, $lead_id, $member_id );
			
			} 
	
			// Checking for role
			if ( user_can( $user_id, 'administrator' ) ) {
			  
			  // Go ahead and give lead to admin, even though Affiliate ID isn't set, this is likely only to happen during testing of plugin by admin and they need to know affiliate ID's are not set.
			  return true;
			  
			}
			
			return false; //if tracking ID isn't set
		
		} // End send email to member who would have received a lead
				
	} //End campaign set check
	
	return true; // If only setting cookie and not submitting lead.
		
}

add_action( 'wp_ajax_wplp_edit_member_info', 'wplp_edit_member_info' );
function wplp_edit_member_info(){
	global $wpdb, $_POST;
	
	check_admin_referer('wplp_edit_member_info','wplp_form_nonce');
	
	check_ajax_referer('wplp_form_nonce'); // Check for our nonce from AJAX
		
	$user_id = $_POST['ID'];
	
	$referrerId = $_POST['wplp_referrer_id'];
	
	// Get details of all opportunities
	$taxonomies = 'wplp_opportunity';
	$args = NULL;
	$opps = get_terms( $taxonomies, $args );
	
	if( isset( $opps ) && !empty( $opps ) ) {
		
		foreach ( $opps as $opp ){
			
			// Get the tracking ID for each opportunity and update user meta
			$opp = $opp;
			$trackingID = 'wplp_tracking_id_' . $opp->slug;
			$value = $_POST[$trackingID];
			
			if ( empty($value) ) {
				
				$value = '';
				
			}
					
			update_user_meta( $user_id, 'wplp_tracking_id_'.$opp->slug.'', $value );
			
		}
	
	}
						
	$status = $_POST['block_unblock'];
		
	# Update member data         	
	update_user_meta($user_id, 'wplp_referrer_id', $referrerId);
	update_user_meta($user_id, 'wplp_ban_status', $status);
	die();	
}


// Update affiliate IDs
add_action( 'wp_ajax_nopriv_wplp_update_affiliate', 'wplp_update_affiliate' );
add_action( 'wp_ajax_wplp_update_affiliate', 'wplp_update_affiliate' );
function wplp_update_affiliate(){
	global $wp, $wpdb, $_GET, $_POST, $current_user;
			
	$user_id = $current_user->ID;
	
	// Get data of companies
	$taxonomies = 'wplp_opportunity';
		
	// get all companies
	$companies = get_terms( $taxonomies );
	
	if( isset( $companies ) && !empty( $companies ) ) {
				
		foreach ( $companies as $company ){
			
			// Get the tracking ID for each opportunity and update user meta
			$company = $company;
			$trackingID = 'wplp_tracking_id_' . $company->slug;
			
			if( isset( $_POST[$trackingID] ) ){
				
				$value = $_POST[$trackingID];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_tracking_id_' . $company->slug . '', $value );
				
			}
			
		}
	
	}
		
		$url_location = '#';
		
		echo $url_location;
		die();
	
}

add_action( 'wp_ajax_nopriv_wplp_autoresponder_save', 'wplp_autoresponder_save' );
add_action( 'wp_ajax_wplp_autoresponder_save', 'wplp_autoresponder_save' );
function wplp_autoresponder_save(){
	global $wp, $wpdb, $_GET, $_POST, $current_user;
			
	$user_id = $current_user->ID;
	
	// Get data of companies
	$taxonomies = 'wplp_opportunity';
		
	// If no companies passed get all companies
	$companies = get_terms( $taxonomies );
	
	if( isset( $companies ) && !empty( $companies ) ) {
				
		foreach ( $companies as $company ){
			
			// wplp_campaign_ar_list_on
			$wplp_campaign_ar_list_on = 'wplp_campaign_ar_list_on_' . $company->slug;
			
			if( isset( $_POST[$wplp_campaign_ar_list_on] ) ){
				
				$value = $_POST[$wplp_campaign_ar_list_on];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_campaign_ar_list_on_' . $company->slug . '', $value );
				
			}
			
			
			
			// wplp_form_action_url
			$wplp_form_action_url = 'wplp_form_action_url_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_action_url] ) ){
				
				$value = $_POST[$wplp_form_action_url];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_action_url_' . $company->slug . '', $value );
				
			}
			
			
			
			// wplp_form_name
			$wplp_form_name = 'wplp_form_name_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_name] ) ){
				
				$value = $_POST[$wplp_form_name];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_name_' . $company->slug . '', $value );
				
			}
			
			
			
			// wplp_form_fname
			$wplp_form_fname = 'wplp_form_fname_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_fname] ) ){
				
				$value = $_POST[$wplp_form_fname];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_fname_' . $company->slug . '', $value );
				
			}
			
			
			
			// wplp_form_lname
			$wplp_form_lname = 'wplp_form_lname_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_lname] ) ){
				
				$value = $_POST[$wplp_form_lname];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_lname_' . $company->slug . '', $value );
				
			}
			
			
			
			// wplp_form_email
			$wplp_form_email = 'wplp_form_email_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_email] ) ){
				
				$value = $_POST[$wplp_form_email];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_email_' . $company->slug . '', $value );
				
			}	
			


			// wplp_form_phone
			$wplp_form_phone = 'wplp_form_phone_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_phone] ) ){
				
				$value = $_POST[$wplp_form_phone];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_phone_' . $company->slug . '', $value );
				
			}	
			

			// wplp_form_custom_name_
			$wplp_form_custom_name = 'wplp_form_custom_name_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_name] ) ){
				
				$value = $_POST[$wplp_form_custom_name];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_name_' . $company->slug . '', $value );
				
			}																								
			

			// wplp_form_custom_val_
			$wplp_form_custom_val = 'wplp_form_custom_val_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_val] ) ){
				
				$value = $_POST[$wplp_form_custom_val];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_val_' . $company->slug . '', $value );
				
			}	

			// wplp_form_custom_name_
			$wplp_form_custom_name1 = 'wplp_form_custom_name1_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_name1] ) ){
				
				$value = $_POST[$wplp_form_custom_name1];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_name1_' . $company->slug . '', $value );
				
			}																								
			

			// wplp_form_custom_val_
			$wplp_form_custom_val1 = 'wplp_form_custom_val1_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_val1] ) ){
				
				$value = $_POST[$wplp_form_custom_val1];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_val1_' . $company->slug . '', $value );
				
			}
			
			// wplp_form_custom_name_
			$wplp_form_custom_name2 = 'wplp_form_custom_name2_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_name2] ) ){
				
				$value = $_POST[$wplp_form_custom_name2];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_name2_' . $company->slug . '', $value );
				
			}																								
			

			// wplp_form_custom_val_
			$wplp_form_custom_val2 = 'wplp_form_custom_val2_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_val2] ) ){
				
				$value = $_POST[$wplp_form_custom_val2];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_val2_' . $company->slug . '', $value );
				
			}
			// wplp_form_custom_name_
			$wplp_form_custom_name3 = 'wplp_form_custom_name3_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_name3] ) ){
				
				$value = $_POST[$wplp_form_custom_name3];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_name3_' . $company->slug . '', $value );
				
			}																								
			

			// wplp_form_custom_val_3
			$wplp_form_custom_val3 = 'wplp_form_custom_val3_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_val3] ) ){
				
				$value = $_POST[$wplp_form_custom_val3];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_val3_' . $company->slug . '', $value );
				
			}
			// wplp_form_custom_name_
			$wplp_form_custom_name4 = 'wplp_form_custom_name4_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_name4] ) ){
				
				$value = $_POST[$wplp_form_custom_name4];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_name4_' . $company->slug . '', $value );
				
			}																								
			

			// wplp_form_custom_val_
			$wplp_form_custom_val4 = 'wplp_form_custom_val4_' . $company->slug;
			
			if( isset( $_POST[$wplp_form_custom_val4] ) ){
				
				$value = $_POST[$wplp_form_custom_val4];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_form_custom_val4_' . $company->slug . '', $value );
				
			}												
			// Form Integration End


			// API Integration Start 


			// Get Response API yes/no
			$wplp_campaign_get_response_api = 'wplp_campaign_get_response_api_' . $company->slug;
			
			if( isset( $_POST[$wplp_campaign_get_response_api] ) ){
				
				$value = $_POST[$wplp_campaign_get_response_api];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_campaign_get_response_api_' . $company->slug . '', $value );
				
			}
			
						
			// Get Response Key
			$wplp_campaign_get_response_key = 'wplp_campaign_get_response_key_'.$company->slug;

			if( isset( $_POST[$wplp_campaign_get_response_key] ) ){
				
				$value = $_POST[$wplp_campaign_get_response_key];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_campaign_get_response_key_' . $company->slug . '', $value );
				
			}
			
			// Get Response Campaign Name					
			$wplp_campaign_get_response_campaign_name = 'wplp_campaign_get_response_campaign_name_'.$company->slug;
			
			if( isset( $_POST[$wplp_campaign_get_response_campaign_name] ) ){
				
				$value = $_POST[$wplp_campaign_get_response_campaign_name];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_campaign_get_response_campaign_name_' . $company->slug . '', $value );
				
			}

			// Awever API yes/no
			$wplp_campaign_aweber_api = 'wplp_campaign_aweber_api_' . $company->slug;
			
			if( isset( $_POST[$wplp_campaign_aweber_api] ) ){
				
				$value = $_POST[$wplp_campaign_aweber_api];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_campaign_aweber_api_' . $company->slug . '', $value );
				
			}
			
			// Aweber List ID					
			$wplp_campaign_aweber_list_id = 'wplp_campaign_aweber_list_id_'.$company->slug;
			
			if( isset( $_POST[$wplp_campaign_aweber_list_id] ) ){
				
				$value = $_POST[$wplp_campaign_aweber_list_id];
				
			} else {
				
				$value = NULL;	
				
			}
			
			if ( isset($value) ) {
					
				update_user_meta( $user_id, 'wplp_campaign_aweber_list_id_' . $company->slug . '', $value );
				
			}
								
			// API Integration - END					
			
		}// end foreach companies as company
	
	}
		
		$url_location = '#';
		
		echo $url_location;
		die();
	
}
?>