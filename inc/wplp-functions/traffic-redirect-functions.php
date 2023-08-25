<?php
#################
//Traffic Redirect
#################
add_action( 'wp_ajax_wplp_traffic_redirect', 'wplp_traffic_redirect' );
add_action( 'wp_ajax_nopriv_wplp_traffic_redirect', 'wplp_traffic_redirect' );
function wplp_traffic_redirect(){
	global $wp, $wpdb, $_POST, $_GET;
	
	// only check ajax ref if ajax is used.
	if( isset( $_POST['ajaxUsed'] ) && !empty( $_POST['ajaxUsed'] ) && $_POST['ajaxUsed'] == 'yes' ){
		
			check_ajax_referer('wplp_form_nonce_front'); // Check for our nonce from AJAX
				
	} else {
		
		if( isset( $_POST['wplp_form_nonce_front_post'] ) && !empty( $_POST['wplp_form_nonce_front_post'] ) ){
					
			wp_verify_nonce( $_POST['wplp_form_nonce_front_post'], 'wplp_create_lead' );
			
		}
			
	}
	
	
	// Get all wplp options
	//$options = get_option( 'wp_leads_press_options' );
	$options = wp_load_alloptions();

	//Get the referring member id
	$ref_user_id = wplp_get_referrer_id();
	
	//Get max random leads/traffic
	$max_random = $options['wp_leads_press_max_random_leads_allowed'];
		
	//Update referring user lead count
	$refLeads = get_user_meta($ref_user_id, 'wplp_ref_lead_count', true);
	$totalRefLeads = get_user_meta( $ref_user_id, 'wplp_total_ref_lead_count', true );
	$refLeadsReq = $options['wp_leads_press_personally_referred_leads_required'];
	$totalRandomLeadCount = get_user_meta($ref_user_id, 'wplp_total_random_lead_count', true);
			
	//Check for bonus leads
	$bonusLeads = get_user_meta($ref_user_id, 'wplp_bonus_leads', true);
	$bonusLeadsValue = $options['wp_leads_press_ref_lead_bonus_leads_value'];
	
	// Do check to see if lead is direct referral
	$key = 'wplp_direct_ref';
	$status = get_user_meta($ref_user_id, $key, true);
	
	if( $options['wp_leads_press_personally_referred_leads_required'] != 0 ){
		
		if ($status == 'yes' ) {
				
			if( $refLeads < $refLeadsReq ) {
			
				$update = $refLeads+1;
			
				// Check to see if $refLeads+1 = $refLeadsReq
				// if so: update bonus leads
				if( $update >= $refLeadsReq ){
					
					// Add bonus leads here
					$bonusLeads = $bonusLeads+$bonusLeadsValue;
					update_user_meta( $ref_user_id, 'wplp_bonus_leads', $bonusLeads );
				
				}					
			
			}
				
			if( $refLeads >= $refLeadsReq ) {
			
				$update = 1;
			
			} 				
			
			// Update user lead count of referring user
			update_user_meta( $ref_user_id, 'wplp_ref_lead_count', $update );
			
			// Update user total referred lead count
			$totalUpdate = $totalRefLeads+1;				
			update_user_meta( $ref_user_id, 'wplp_total_ref_lead_count', $totalUpdate );
	
		}
		
		if ($status == 'no' ) {
			
			if ( ( $totalRandomLeadCount <= $max_random ) && ( $totalRandomLeadCount-$max_random != 0 ) ){
				
				$update = $totalRandomLeadCount+1;
				update_user_meta( $ref_user_id, 'wplp_total_random_lead_count', $update );
			
			}
			
			if ( $bonusLeads >0 && $totalRandomLeadCount >= $max_random ) {
				
				$update = $bonusLeads-1;
				update_user_meta( $ref_user_id, 'wplp_bonus_leads', $update );
				
			}		
			
		}	
		
	} // end check for ref leads
		
		//Need to get campaign info to send user to correct destination URL		
		//$campaign = $_GET['wplp_campaign'];
		
		if ( isset( $_POST['wplp_campaign'] ) ){
			
			$campaign = $_POST['wplp_campaign'];			
		}
		
		if ( isset( $_POST['wplp_landing_page'] ) ){
			
			$wplp_landing_page = $_POST['wplp_landing_page'];
		}
		
		
		// Check for GET vars
		if ( isset( $_GET['wplp_campaign'] ) ){
			
			$campaign = $_GET['wplp_campaign'];			
		}
		
		if ( isset( $_GET['wplp_landing_page'] ) ){
			
			$wplp_landing_page = $_GET['wplp_landing_page'];
		}		
		
		
				
		
		$campaign_url = get_post_meta( $campaign, 'wplp_campaign_url', true );
		$campaign_url_trailing_value = get_post_meta ( $campaign, 'wplp_campaign_url_trailing_value', true );
		$campaign_is_https = get_post_meta( $campaign, 'wplp_campaign_is_https', true );		
		$campaign_is_subdomain = get_post_meta( $campaign, 'wplp_campaign_is_subdomain', true );
				
		// Get the opportunity associated with campaign
		$campaign_company = get_the_terms( $campaign, 'wplp_opportunity' );
		
			if( isset( $campaign_company ) && !empty( $campaign_company ) ){
				
				foreach( $campaign_company as $company ){
					
					$slug = $company->slug;	
					// Get the ref user tracking ID for opportunity
					$key = 'wplp_tracking_id_' . $slug;
					$wplp_ref_tracking_id = get_user_meta($ref_user_id, $key, true);
					
				}
		
			}
		
		// Get URL format
		if ( $campaign_is_https == 'yes' ) {
			
			$preUrl = 'https://';	
			
		} else {
			
			$preUrl = 'http://';
			
		}				
		
		// Create URL to send lead to based on ref user	
		if ( $campaign_is_subdomain == 'yes' ){
		
			$wplp_redirect_url = $preUrl . $wplp_ref_tracking_id . '.' . $campaign_url;
			
		} else {
			
			$wplp_redirect_url = $preUrl . $campaign_url . $wplp_ref_tracking_id . $campaign_url_trailing_value;
			
		}
		
		//Get usernicename of ref
		$user_nicename = wplp_ref_user_nicename_by_id($ref_user_id);
		
		// Set cookie for referring user to track if lead becomes member later
		wplp_set_cookie_ref($user_nicename);	
		
		//Set cookie to track campaign ID 
		setcookie( 'wplp-campaign', $campaign, time()+86400*30*12, "/", COOKIE_DOMAIN, false ); //Setting new
		
		//Set cookie to track landing page ID
		setcookie( 'wplp-landing-page', $wplp_landing_page, time()+86400*30*12, "/", COOKIE_DOMAIN, false ); //Setting new
		
		// Set cookie to track user if affiliate								
		wplp_itthinx_set_affiliate_cookie($ref_user_id, $user_id = NULL);
		
		wplp_idevaffiliate_set_affiliate_cookie($idev_id, $ref_user_id);
									
		//add_post_meta($post_id, 'wplp_lead_campaign', $campaign, true);
		//add_post_meta($post_id, 'wplp_lead_landing_page', $wplp_landing_page, true);	

		// Do check to see if landing page has a redirect to internal site page
		$wplp_dest_url_override = get_post_meta( $wplp_landing_page, 'wplp_dest_url_override', true );

		if ( isset( $wplp_dest_url_override ) && !empty( $wplp_dest_url_override ) ) {
			
			if( isset( $_POST['ajaxUsed'] ) && $_POST['ajaxUsed'] == 'yes' ) {
				
				// Send visitor to override url
		 
				$wplp_dest_url_override = urldecode($wplp_dest_url_override);
				echo $wplp_dest_url_override;
				$tmp=$wplp_dest_url_override . $wplp_dest_url_override;	
				//BugFu::log($tmp);
				
				
			} else {
								
				// Send visitor to override URL
			//	$wplp_dest_url_override= $wplp_dest_url_override . "/?ref=" . $user_nicename;
				header( "Location: ".$wplp_dest_url_override );
			    $tmp=$wplp_dest_url_override . $wplp_dest_url_override;	
			//	BugFu::log($tmp);
			}			
			
		} else { // If no redirect is set, send to Destination URL

		
			if( isset( $_POST['ajaxUsed'] ) && $_POST['ajaxUsed'] == 'yes' ) {
				
				// Send visitor to destination url
			//	$wplp_redirect_url= $wplp_redirect_url . "/?ref=" . $user_nicename;
				$wplp_redirect_url = urldecode($wplp_redirect_url);
				
				echo $wplp_redirect_url;
			//	$tmp=$wplp_redirect_url . $wplp_redirect_url;	
			//	BugFu::log($tmp); 
				
			} else {
				$wplp_redirect_url= $wplp_redirect_url . "/?ref=" . $user_nicename;				
				// Send visitor to destination url
				$wplp_redirect_url = urldecode($wplp_redirect_url);
				$tmp=$wplp_redirect_url . $wplp_redirect_url;	
			//	BugFu::log($tmp);
			}
		
		}		
	//	BugFu::log("$wplp_redirect_url=".$wplp_redirect_url); 
		//kill the process
		die();
				
		//header( "Location: ".$wplp_redirect_url );
}
?>