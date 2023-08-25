<?php
#############
//Create Lead
#############
add_action( 'wp_ajax_wplp_create_lead', 'wplp_create_lead' );
add_action( 'wp_ajax_nopriv_wplp_create_lead', 'wplp_create_lead' );
function wplp_create_lead(){
	global $wp, $wpdb, $_POST, $_GET;
	
	// only check ajax ref if ajax is used.
	if( isset( $_POST['ajaxUsed'] ) && !empty( $_POST['ajaxUsed'] ) && $_POST['ajaxUsed'] == 'yes' ){
		
			check_ajax_referer('wplp_form_nonce_front'); // Check for our nonce from AJAX
				
	} else {
		
		if( isset( $_POST['wplp_form_nonce_front_post'] ) && !empty( $_POST['wplp_form_nonce_front_post'] ) ){
					
			wp_verify_nonce( $_POST['wplp_form_nonce_front_post'], 'wplp_create_lead' );
			
		}
			
	}	
	
 	if ( isset( $_POST['email'] ) && !empty( $_POST['email'] ) ) {
	
		//Get the referring member id
		$ref_user_id = wplp_get_referrer_id();
	
		// Get all wplp options
		//$options = get_option( 'wp_leads_press_options' );
		$options = wp_load_alloptions();
		
		//Get max random leads
		$max_random = $options['wp_leads_press_max_random_leads_allowed'];
			
		//Update referring user lead count
		$refLeads = get_user_meta($ref_user_id, 'wplp_ref_lead_count', true);
		$totalRefLeads = get_user_meta( $ref_user_id, 'wplp_total_ref_lead_count', true );		
		$refLeadsReq = $options['wp_leads_press_personally_referred_leads_required'];
		$totalRandomLeadCount = get_user_meta($ref_user_id, 'wplp_total_random_lead_count', true);
				
		//Check for bonus leads
		$bonusLeads = get_user_meta($ref_user_id, 'wplp_bonus_leads', true);
		$bonusLeadsValue = $options['wp_leads_press_ref_lead_bonus_leads_value'];
		
		
		
		
		//Check if lead is duplicate for ref_user_id
		$args = array(
			'author' 		   => $ref_user_id,
			'posts_per_page'   => -1,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => 'wplp_lead_email',
			'meta_value'       => $_POST['email'],
			'post_type'        => 'lead',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$leadExists = get_posts( $args );
		
		if( is_array( $leadExists ) && !empty( $leadExists ) ) {
			
			$duplead = 'yes';
			
		} else {
			
			$duplead = 'no';
			
		}

		
		// Do check to see if lead is direct referral
		$key = 'wplp_direct_ref';
		$status = get_user_meta($ref_user_id, $key, true);
		
		if( $options['wp_leads_press_personally_referred_leads_required'] != 0 && $duplead != 'yes' ){
		
			if ($status == 'yes' ) {
					
					if( $refLeads <= $refLeadsReq ) {
					
						$update = $refLeads+1;
						
						// Check to see if $refLeadss+1 = $refLeadsReq
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
		
		}// End of check if personally referred leads required



			
		// Create a 'Lead' with new user info and set to subscribed status
		
		// Need to get campaign info to send user to correct destination URL
		if ( isset( $_POST['wplp_campaign'] ) ){
			
			$campaign = $_POST['wplp_campaign'];			
		
		}
		
		if ( isset( $_POST['wplp_landing_page'] ) ){
			
			$wplp_landing_page = $_POST['wplp_landing_page'];
		
		}		
		
		// Get Lead Details
		if ( !isset( $_POST['name'] ) || empty( $_POST['name'] ) ) {
			
			$_POST['name'] = 'Name Not Provided';
			
		}
		
		$post_information = array(
			'post_title' => wp_strip_all_tags( $_POST['name'] ),
			'post_type' => 'lead',
			'post_author'   => $ref_user_id,
			'post_status' => 'publish'
		);



if( $duplead != 'yes' ) {
	 
		$post_id = wp_insert_post( $post_information );
		
		$fullName = $_POST['name'];
		$fullNameMult = wplp_multiexplode( array( " ", "," ), $fullName );
		//$fullName = explode( " ", $fullName );
		
		//$fullName = explode(' ', $fullName, -1);
		$firstName = $fullNameMult[0];
		
		if( isset( $fullNameMult[1] ) ) {
					
			$lastName = $fullNameMult[1];
						
		} else {
			
			$lastName = NULL;	
			
		}
		
		add_post_meta($post_id, 'wplp_lead_first_name', $firstName, true);
		add_post_meta($post_id, 'wplp_lead_last_name', $lastName, true);
		
		if ( isset( $_POST['email'] ) ) {
		
			$post_email = $_POST['email'];
			add_post_meta($post_id, 'wplp_lead_email', $_POST['email'], true);
	
		}
		
		if ( isset( $_POST['phone'] ) ){
			
			$post_phone = $_POST['phone'];
			add_post_meta($post_id, 'wplp_lead_phone', $_POST['phone'], true);
		
		}
		
		// Lets add the IP address of the user to the lead details
		// pass to lead details and to 3rd party autoresponders etc
		$leadIP = $_SERVER["REMOTE_ADDR"];
		add_post_meta($post_id, 'wplp_lead_ip_address', $leadIP, true);
		
		// Need the term ID of the custom post type
		$term = term_exists( 'subscribed', 'wplp_lead_status');
		$term = $term['term_id'];
		wp_set_post_terms( $post_id, $term, 'wplp_lead_status', false );
		
		
				// Mailster Start
	//	BugFu::log('$options["wp_leads_press_mailster"] ='+$options["wp_leads_press_mailster"]);
	//	if( $options["wp_leads_press_mailster"] == "on" ) {		
                 BugFu::log("got here");
                if( function_exists( 'mailster' ) ){
                    // do stuff with Mailster
                    $subscriber_id = mailster( 'subscribers' )->add( array(
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'email' => $_POST['email'],
                    'status' => 1, //1 = subscribed (default) , 0 = pending, 2 = unsubscribed, 3 = hardbounced
                    'phone' => $_POST['phone'],
                    //'referer' => 'Your referer' //default = $_SERVER['REQUEST_URI']
                     
                ), $overwrite );
                }
                BugFu::log("$subscriber_id="+$subscriber_id);
                if ( ! is_wp_error( $subscriber_id ) ) {
                
                		// your list ids
                		$list_ids = array( 1 );
                		mailster( 'subscribers' )->assign_lists( $subscriber_id, $list_ids );
                
                	} else {
                		// actions if adding fails. $subscriber_id is a WP_Error object
                	}
                   

 
                			  
            
 
             
   
		
		// Send to general Autoresponder for Site
		if ( $options["wp_leads_press_add_leads_general_list"] == "on" ){
			
			// Get AR option values for form integration
			$form_action_url = $options['wp_leads_press_ar_form_url'];
			$form_name = $options['wp_leads_press_ar_name_field'];
			$form_fname = $options['wp_leads_press_ar_fname_field'];
			$form_lname = $options['wp_leads_press_ar_lname_field'];									
			$form_email = $options['wp_leads_press_ar_email_field'];					
			$form_phone = $options['wp_leads_press_ar_phone_field'];
		
			$form_custom_name = $options['wp_leads_press_ar_form_custom_name'];
			$form_custom_val = $options['wp_leads_press_ar_form_custom_val'];
			
			$form_custom_name1 = $options['wp_leads_press_ar_form_custom_name1'];
			$form_custom_val1 = $options['wp_leads_press_ar_form_custom_val1'];

			$form_custom_name2 = $options['wp_leads_press_ar_form_custom_name2'];
			$form_custom_val2 = $options['wp_leads_press_ar_form_custom_val2'];
		
			$form_custom_name3 = $options['wp_leads_press_ar_form_custom_name3'];
			$form_custom_val3 = $options['wp_leads_press_ar_form_custom_val3'];

			$form_custom_name4 = $options['wp_leads_press_ar_form_custom_name4'];
			$form_custom_val4 = $options['wp_leads_press_ar_form_custom_val4'];					
									
			//error_log( $form_action_url );

			
			$params = array(
			
				// key => value 
				$form_name => $fullName,
			   	$form_fname => $firstName,
			   	$form_lname => $lastName,
			   	$form_email => $post_email,
			   	$form_phone => $post_phone,				
				$form_custom_name => $form_custom_val,
			   	
				$form_phone1 => $post_phone1,				
				$form_custom_name1 => $form_custom_val1,

			   	$form_phone2 => $post_phone2,				
				$form_custom_name2 => $form_custom_val2,

			   	$form_phone3 => $post_phone3,				
				$form_custom_name3 => $form_custom_val3,
															
			   	$form_phone4 => $post_phone4,				
				$form_custom_name4 => $form_custom_val4																
			   
			);
			
			//error_log( print_r( $params, true ) );
						
			$url = $form_action_url;
			
			//process in background, don't echo to client submit form silently
			wplp_httpPost( $url, $params );		
		
		} //end if add leads general list == on
		
		
		
		// Check if site is using AR API from general settings
		

		
		// Get Response Start
		if( $options["wp_leads_press_get_response_api"] == "on" ){		

			// Send to get response if on
			$name = $fullName;
			$email = $post_email;
			$key = $options['wp_leads_press_get_response_key'];
			$campaign_name = $options['wp_leads_press_get_response_campaign_name'];
			
			wplp_add_contact_get_response($name, $email, $key, $campaign_name);		

		}

		// Get Response End		
				
		// Aweber Start
		if( $options["wp_leads_press_aweber_api"] == "on" ){		

					
			// Send to get response if on
			$name = $fullName;
			$email = $post_email;
			$wplp_campaignID = $campaign;
			$leadIP = $leadIP;
			$consumerKey = $options['wp_leads_press_aweber_consumerKey'];
			$consumerSecret = $options['wp_leads_press_aweber_consumerSecret'];
			$accessKey = $options['wp_leads_press_aweber_accessKey'];
			$accessSecret = $options['wp_leads_press_aweber_accessSecret'];
			
			//$account_id = $options['wp_leads_press_aweber_account_id']; // depreciated
			$list_id = $options['wp_leads_press_aweber_list_id'];
			
			wplp_add_contact_aweber($name, $email, $wplp_campaignID, $leadIP, $consumerKey, $consumerSecret, $accessKey, $accessSecret, $list_id);
		}		
		// Aweber End		

		
		
		$campaign_ar_list_on = get_post_meta( $campaign, 'wplp_campaign_ar_list_on', true );
		
		// Check if campaign has segmented AR list on.
		if( $campaign_ar_list_on == 'yes' ){
			
			// Get AR option values for form integration
			$form_action_url = get_post_meta( $campaign, 'wplp_campaign_ar_url', true );
			
			$form_name = get_post_meta( $campaign, 'wplp_ar_name_field', true );
			$form_fname = get_post_meta( $campaign, 'wplp_ar_fname_field', true );
			$form_lname = get_post_meta( $campaign, 'wplp_ar_lname_field', true );									
			$form_email = get_post_meta( $campaign, 'wplp_ar_email_field', true );					
			$form_phone = get_post_meta( $campaign, 'wplp_ar_phone_field', true );

			//get custom form names and values
			// use dynamic integration...
			$form_custom_name = get_post_meta( $campaign, 'wplp_ar_custom_field_name', true );
			$form_custom_val = get_post_meta( $campaign, 'wplp_ar_custom_field_val', true );

			$form_custom_name1 = get_post_meta( $campaign, 'wplp_ar_custom_field_name1', true );
			$form_custom_val1 = get_post_meta( $campaign, 'wplp_ar_custom_field_val1', true );

			$form_custom_name2 = get_post_meta( $campaign, 'wplp_ar_custom_field_name2', true );
			$form_custom_val2 = get_post_meta( $campaign, 'wplp_ar_custom_field_val2', true );
			
			$form_custom_name3 = get_post_meta( $campaign, 'wplp_ar_custom_field_name3', true );
			$form_custom_val3 = get_post_meta( $campaign, 'wplp_ar_custom_field_val3', true );

			$form_custom_name4 = get_post_meta( $campaign, 'wplp_ar_custom_field_name4', true );
			$form_custom_val4 = get_post_meta( $campaign, 'wplp_ar_custom_field_val4', true );												
			//error_log( $form_action_url );

			
			$params = array(
			
				// key => value 
				$form_name => $fullName,
			   	$form_fname => $firstName,
			   	$form_lname => $lastName,
			   	$form_email => $post_email,
			   	$form_phone => $post_phone,
				$form_custom_name => $form_custom_val,
				
			   	$form_phone1 => $post_phone1,
				$form_custom_name1 => $form_custom_val1,
				
			   	$form_phone2 => $post_phone2,
				$form_custom_name2 => $form_custom_val2,
				
			   	$form_phone3 => $post_phone3,
				$form_custom_name3 => $form_custom_val3,
				
			   	$form_phone4 => $post_phone4,
				$form_custom_name4 => $form_custom_val4																
			   
			);
			
			//error_log( print_r( $params, true ) );
						
			$url = $form_action_url;
			
			//process in background, don't echo to client submit form silently
			wplp_httpPost( $url, $params );				
			
		} // end campaign ar list on == yes		



		// Get campaign autoresponder settings		
		//$campaign_ar_list_on = get_post_meta( $campaign, 'wplp_campaign_ar_list_on', true );

		$wplp_campaign_get_response_api = get_post_meta($campaign, 'wplp_campaign_get_response_api', true); 
		$campaign_get_response_key = get_post_meta($campaign, 'wplp_campaign_get_response_key', true);				
		$campaign_get_response_campaign_name = get_post_meta($campaign, 'wplp_campaign_get_response_campaign_name', true);				

		// Check if campaign is using AR API
		
		// Get Response Start
		if( $wplp_campaign_get_response_api == 'yes' ){		

			// Send to get response if on
			$name = $fullName;
			$email = $post_email;
			$key = $campaign_get_response_key;
			$campaign_name = $campaign_get_response_campaign_name;
			
			wplp_add_contact_get_response($name, $email, $key, $campaign_name);		
		
		}
		// Get Response End
		
		// Aweber Start
		$wplp_campaign_aweber_api = get_post_meta($campaign, 'wplp_campaign_aweber_api', true); 				
		if( $wplp_campaign_aweber_api == 'yes' ){		
					
			// Send to get response if on
			$name = $fullName;
			$email = $post_email;
			$wplp_campaignID = $campaign;
			$leadIP = $leadIP;
			$consumerKey = $options['wp_leads_press_aweber_consumerKey'];
			$consumerSecret = $options['wp_leads_press_aweber_consumerSecret'];
			$accessKey = $options['wp_leads_press_aweber_accessKey'];
			$accessSecret = $options['wp_leads_press_aweber_accessSecret'];
			
			//$account_id = $options['wp_leads_press_aweber_account_id']; // depreciated
			$list_id = get_post_meta($campaign, 'wplp_campaign_aweber_list_id', true);
			
			wplp_add_contact_aweber($name, $email, $wplp_campaignID, $leadIP, $consumerKey, $consumerSecret, $accessKey, $accessSecret, $list_id);
		}				
		// Aweber End
		
		
				
}// end if duplead != yes	
						
		// Get url of campaign to send visitor to					
		$campaign_url = get_post_meta( $campaign, 'wplp_campaign_url', true );
		$campaign_url_trailing_value = get_post_meta ( $campaign, 'wplp_campaign_url_trailing_value', true );
		
		$campaign_is_subdomain = get_post_meta( $campaign, 'wplp_campaign_is_subdomain', true );
		$campaign_is_https = get_post_meta( $campaign, 'wplp_campaign_is_https', true );
	
				
		// Get the opportunity associated with campaign
		//$taxonomy = 'wplp_opportunity';
		//$campaign_opportunity = array();
		$campaign_company = get_the_terms( $campaign, 'wplp_opportunity' );
		
		if( !empty( $campaign_company ) ) {
			
			foreach( $campaign_company as $company ){
				
				//$campaign_opportunity_name = $campaign_opportunity[0]->name;
				$slug = $company->slug;
				$name = $company->name;
				
				if( $duplead != 'yes' ) {
					
					// Add the opportunity/company name to the lead meta
					add_post_meta( $post_id, 'wplp_lead_opportunity', $name, true );
				
					// Add the company id to the lead meta
					add_post_meta( $post_id, 'wplp_lead_company_id', $campaign_company[0]->term_taxonomy_id, true );
				
				}
				
				// Get the ref user tracking ID for opportunity
				//$campaign_opportunity = $campaign_opportunity[0]->slug;		
				$key = 'wplp_tracking_id_' . $slug;
				$wplp_ref_tracking_id = get_user_meta($ref_user_id, $key, true); 
			
			


	if( $duplead != 'yes' ) {
		
		// Get ref user autoresponder settings		
		$wplp_campaign_ar_list_on = get_user_meta($ref_user_id, 'wplp_campaign_ar_list_on_'.$slug, true); 
						
		// Check if user has campaign AR list on.
		if( $wplp_campaign_ar_list_on == 'yes' ){
			
			// Get AR option values for form integration
			$form_action_url = get_user_meta($ref_user_id, 'wplp_form_action_url_'.$slug, true);
			$form_name = get_user_meta($ref_user_id, 'wplp_form_name_'.$slug, true);
			$form_fname = get_user_meta($ref_user_id, 'wplp_form_fname_'.$slug, true);
			$form_lname = get_user_meta($ref_user_id, 'wplp_form_lname_'.$slug, true);					
			$form_email = get_user_meta($ref_user_id, 'wplp_form_email_'.$slug, true);	
			$form_phone = get_user_meta($ref_user_id, 'wplp_form_phone_'.$slug, true);
			
			//get custom form names and values
			// use dynamic integration...
			$form_custom_name = get_user_meta($ref_user_id, 'wplp_form_custom_name_'.$slug, true);
			$form_custom_val = get_user_meta($ref_user_id, 'wplp_form_custom_val_'.$slug, true);

			$form_custom_name1 = get_user_meta($ref_user_id, 'wplp_form_custom_name1_'.$slug, true);
			$form_custom_val1 = get_user_meta($ref_user_id, 'wplp_form_custom_val1_'.$slug, true);

			$form_custom_name2 = get_user_meta($ref_user_id, 'wplp_form_custom_name2_'.$slug, true);
			$form_custom_val2 = get_user_meta($ref_user_id, 'wplp_form_custom_val2_'.$slug, true);

			$form_custom_name3 = get_user_meta($ref_user_id, 'wplp_form_custom_name3_'.$slug, true);
			$form_custom_val3 = get_user_meta($ref_user_id, 'wplp_form_custom_val3_'.$slug, true);

			$form_custom_name4 = get_user_meta($ref_user_id, 'wplp_form_custom_name4_'.$slug, true);
			$form_custom_val4 = get_user_meta($ref_user_id, 'wplp_form_custom_val4_'.$slug, true);
																		
			//error_log( $form_action_url );

			
			$params = array(
			
				// key => value 
				$form_name => $fullName,
			   	$form_fname => $firstName,
			   	$form_lname => $lastName,
			   	$form_email => $post_email,
			   	$form_phone => $post_phone,
				
				//process user's custom form names and values
				//change to dynamic input...
				$form_custom_name => $form_custom_val,
				$form_custom_name1 => $form_custom_val1,
				$form_custom_name2 => $form_custom_val2,
				$form_custom_name3 => $form_custom_val3,
				$form_custom_name4 => $form_custom_val4
			   
			);
			
			//error_log( print_r( $params, true ) );
						
			$url = $form_action_url;
			
			//process in background, don't echo to client submit form silently
			wplp_httpPost( $url, $params );				
			
		} // end user ar list on == yes	
		

		// Get ref user autoresponder settings		

		// Check if campaign is using AR API
		
		// Get Response Start
		$wplp_campaign_get_response_api = get_user_meta($ref_user_id, 'wplp_campaign_get_response_api_'.$slug, true); 
		$campaign_get_response_key = get_user_meta($ref_user_id, 'wplp_campaign_get_response_key_'.$slug, true);				
		$campaign_get_response_campaign_name = get_user_meta($ref_user_id, 'wplp_campaign_get_response_campaign_name_'.$slug, true);				


		if( $wplp_campaign_get_response_api == 'yes' ){		

			// Send to get response if on
			$name = $fullName;
			$email = $post_email;
			$key = $campaign_get_response_key;
			$campaign_name = $campaign_get_response_campaign_name;
			
			wplp_add_contact_get_response($name, $email, $key, $campaign_name);		
		
		}
		// Get Response End
		
		// Aweber Start
		$wplp_campaign_aweber_api = get_user_meta($ref_user_id, 'wplp_campaign_aweber_api_'.$slug, true);		
		if( $wplp_campaign_aweber_api == 'yes' ){
					
			// Send to get response if on
			$name = $fullName;
			$email = $post_email;
			$wplp_campaignID = $campaign;
			$leadIP = $leadIP;
			$consumerKey = get_user_meta($ref_user_id, 'wplp_aweber_consumerKey', true);
			$consumerSecret = get_user_meta($ref_user_id, 'wplp_aweber_consumerSecret', true);
			$accessKey = get_user_meta($ref_user_id, 'wplp_aweber_accessKey', true);
			$accessSecret = get_user_meta($ref_user_id, 'wplp_aweber_accessSecret', true);
			
			//$account_id = get_user_meta($ref_user_id, 'wplp_campaign_aweber_account_id_'.$slug, true); // depreciated
			$list_id = get_user_meta($ref_user_id, 'wplp_campaign_aweber_list_id_'.$slug, true);
			
			wplp_add_contact_aweber($name, $email, $wplp_campaignID, $leadIP, $consumerKey, $consumerSecret, $accessKey, $accessSecret, $list_id);
		}		
		// Aweber End
		
	}
			
						
			}
			
		}
		
		// Get url format
		
		if ( $campaign_is_https == 'yes' ) {
			
			$preUrl = 'https://';	
			
		} else {
			
			$preUrl = 'http://';
			
		}
		
		// Create URL to send lead to based on ref user	
		if ( $campaign_is_subdomain == 'yes' ){
		
			$wplp_redirect_url = $preUrl . $wplp_ref_tracking_id . '.' . $campaign_url;
			
		} else {
			
			//$wplp_redirect_url = 'http://' . $campaign_url . $wplp_ref_tracking_id;
			$wplp_redirect_url = $preUrl . $campaign_url . $wplp_ref_tracking_id . $campaign_url_trailing_value;
			
		}	
		
		if( $duplead != 'yes' ) { //dont add if duplead
			
			add_post_meta($post_id, 'wplp_lead_campaign', $campaign, true);
			add_post_meta($post_id, 'wplp_lead_landing_page', $wplp_landing_page, true);
		
			//Get usernicename of ref
			$ref_user_nicename = wplp_ref_user_nicename_by_id($ref_user_id);
		
		
			// Set cookie for referring user to track if lead becomes member later
			wplp_set_cookie_ref($ref_user_nicename);
				
			//Set cookie to track campaign ID 
			setcookie( 'wplp-campaign', $campaign, time()+86400*30*12, "/", COOKIE_DOMAIN, false ); //Setting new
		
			//Set cookie to track landing page ID
			setcookie( 'wplp-landing-page', $wplp_landing_page, time()+86400*30*12, "/", COOKIE_DOMAIN, false ); //Setting new	


		}
		
		// Set cookie to track user if affiliate								
		wplp_itthinx_set_affiliate_cookie($ref_user_id, $user_id = NULL );
		wplp_idevaffiliate_set_affiliate_cookie( $idev_id, $ref_user_id );

		
		// Do check to see if landing page has a redirect to internal site page
		$wplp_dest_url_override = get_post_meta( $wplp_landing_page, 'wplp_dest_url_override', true );

		if ( isset( $wplp_dest_url_override ) && !empty( $wplp_dest_url_override ) ) {
			
			if( $_POST['ajaxUsed'] == 'yes' ) {
				
				// Send visitor to override url
				echo $wplp_dest_url_override;
				
				
			} else {
								
				// Send visitor to override URL
				header( "Location: ".$wplp_dest_url_override );
			
			}			
			
		} else { // If no redirect is set, send to Destination URL

		
			if( $_POST['ajaxUsed'] == 'yes' ) {
				
				// Send visitor to destination url
				echo $wplp_redirect_url;
				
			} else {
								
				// Send visitor to destination url
				header( "Location: ".$wplp_redirect_url );
			
			}
		
		}
		
		// Send Email To New Lead
		$sendNLE = $options['wp_leads_press_send_new_lead_email'];
										
		if ( $sendNLE == "on" ) { 
		
			$from_email = $options["wp_leads_press_smtp_from_email"];
			$from_name = $options["wp_leads_press_smtp_from_name"];
		
			// Get ref member details.
			$user_info = get_userdata( $ref_user_id );
			
			// Variables for wplp_system_email() function.
			$to_email =  $_POST['email'];
					
			// Do check for custom subject
			
			$subject = $options['wp_leads_press_new_lead_subject'];
			
				if ( isset( $subject ) && !empty( $subject ) ) {
					
					$subject = $subject;
					
				} else {
					
					$subject = __( '{LEAD-FIRST-NAME}, thank you for subscribing at {SITE-NAME}!', 'wp-leads-press' );
					
				}
			
			// Do check for custom message
			
			$message = $options['wp_leads_press_new_lead_body'];
			
				if ( isset( $message ) && !empty( $message ) ) {
					
					$message = $message;
					
				} else {
				
					$message = __( '{LEAD-FIRST-NAME},
					
					Thank you for submitting your information at {SITE-NAME} to learn more about {COMPANY-NAME}, you have been assigned to one of our members, {REF-MEMBER-FIRST-NAME} {REF-MEMBER-LAST-NAME}, to help you learn more about the company, don\'t hesitate to contact them with any questions you may have at {REF-MEMBER-EMAIL}.
					
					{SITE-URL}
					
					
					
					
					{UNSUBSCRIBE}', 'wp-leads-press' );
				
				}
		
			$campaign_id = $campaign;
			$landing_page_id = $wplp_landing_page;
			$ref_user_id = $ref_user_id;	
			
			if( $duplead != 'yes' ){
				
				$lead_id = $post_id;
			
			} else {
				
				$lead_id = NULL; //duplicate lead	
				
			}
			
			$member_id = NULL; //not needed 
			
			wplp_system_email( $from_email, $from_name, $to_email, $subject, $message, $campaign_id, $landing_page_id, $ref_user_id, $lead_id, $member_id );
					
		} // End send new lead email
		
		
		// Send Member New Lead Notification
		$sendMNLE = $options['wp_leads_press_send_member_new_lead_email'];
										
		if ( $sendMNLE == "on" && $duplead != 'yes' ) { 
		
			$from_email = $options['wp_leads_press_smtp_from_email'];
			$from_name = $options['wp_leads_press_smtp_from_name'];
		
			// Get ref member details.
			$user_info = get_userdata( $ref_user_id );
			
			// Variables for wplp_system_email() function.
			$to_email =  $user_info->user_email;
					
			// Do check for custom subject
			
			$subject = $options['wp_leads_press_member_new_lead_subject'];
			
				if ( isset( $subject ) && !empty( $subject ) ) {
					
					$subject = $subject;
					
				} else {
					
					$subject = __( '{REF-MEMBER-FIRST-NAME}, you just received a new lead at {SITE-NAME}!', 'wp-leads-press' );
					
				}
			
			// Do check for custom message
			
			$message = $options['wp_leads_press_member_new_lead_body'];
			
				if ( isset( $message ) && !empty( $message ) ) {
					
					$message = $message;
					
				} else {
				
					$message = __( '{REF-MEMBER-FIRST-NAME}, 
			
						You just received a new lead at {SITE-NAME} to learn more about {COMPANY-NAME}, <a href="{SITE-URL}">Login</a> to contact your new lead and help them get started.', 'wp-leads-press' );
				
				}
		
			$campaign_id = $campaign;
			$landing_page_id = $wplp_landing_page;
			$ref_user_id = $ref_user_id;	
			$lead_id = $post_id;
			$member_id = NULL; //not needed, no member created yet to reference, only a lead.
			
			wplp_system_email( $from_email, $from_name, $to_email, $subject, $message, $campaign_id, $landing_page_id, $ref_user_id, $lead_id, $member_id );
					
		} // End send member new lead email
	
	}
	
	die();
	
}

function wplp_can_have_lead($user_id) {
	global $wp, $wpdb, $wplp_admin, $_GET, $_POST;
				
		// Check Ban Status
		if( wplp_user_banned( $user_id ) == true ) {
			
			return false;
			
		}
		
		// Get all wplp options
		$options = wp_load_alloptions();
		
		// Bonus leads
		$bonusLeads = get_user_meta($user_id, 'wplp_bonus_leads', true);
		
		// Affiliate ID Set?				
		if( wplp_is_affiliate_id_set( $user_id ) == false ) {
			
			return false;

		}

		// Did they refer lead directly? If so give them the member no matter what unless of course they are blocked or don't have tracking/affiliate ID set.
		if( isset( $_COOKIE['wplp-ref'] ) && !empty( $_COOKIE['wplp-ref'] ) ) {
			
			$ref = $_COOKIE['wplp-ref'];
			
			$field = 'slug';
			$value = $ref;
			$ref = get_user_by( $field, $value ); // Get user obj
			
			// If the user exists
			if( $ref == true ) {
			
				$ref = $ref->ID;	
				
				if( $user_id == $ref ) {
					
					return true;
					
				}			
				
			}
			
		}			
		
		// Do checks to see if users have earned leads	 			
		// Max Random Leads Allowed
		// Has user already received all initial bonus leads?
		$randomLeads = get_user_meta($user_id, 'wplp_total_random_lead_count', true);
		$randomLeadsMax = $options['wp_leads_press_max_random_leads_allowed'];		
		
		if( $randomLeads < $randomLeadsMax || $bonusLeads >0 || $randomLeadsMax = -1 ) { // OR if user has bonus leads.
		
			return true;
		
		}

		// Referred Lead Count
		// Has user referred enough leads?		
		$refleads = get_user_meta($user_id, 'wplp_ref_lead_count', true);
		$leadsReq = $options['wp_leads_press_personally_referred_leads_required'];
		
		if( $refleads >= $leadsReq || $bonusLeads >0 && $leadsReq != 0 ) {
			
			return true;
		
		}
		
		// Referred Member Count
		// Has user referred enough members?
		$members = get_user_meta($user_id, 'wplp_ref_member_count', true);
		$membersReq = $options['wp_leads_press_ref_member_count_required'];
		
		if( $members >= $membersReq || $bonusLeads >0 && $membersReq != 0 ) { // or if user has bonus leads greater than 0
		
			return true;
		
		}
		
		// Referred Traffic Count
		// Has user referred enough traffic?
		$traffic = get_user_meta($user_id, 'wplp_ref_traffic_count', true);
		$trafficReq = $options['wp_leads_press_ref_traffic_count_required'];
		
		if( $traffic >= $trafficReq || $bonusLeads >0 && $trafficReq != 0 ) { // or if user has bonus leads greater than 0
		
			return true;
		
		}					
					
		// Checking for role approval
		if ( user_can( $user_id, 'administrator' ) ) {
		  
		  // Go ahead and give lead to admin, admins always qualify for random leads
		  return true;
		  
		}
				
		return false;

}

function wplp_traverse_for_referrer($user_id_arr) {
	global $wpdb;
		
	# Get all children referred by user
	$sql = "SELECT user_id FROM ".$wpdb->prefix."usermeta WHERE meta_value IN (".implode(",", $user_id_arr).") AND meta_key IN ('wplp_referrer_id') ORDER BY user_id ASC";	
	$users = $wpdb->get_results($sql);	
	$user_id_arr = array();
	
	if( ! empty( $users ) ) {	
		
		foreach ( $users as $obj ) {
			
			if( wplp_can_have_lead( $obj->user_id ) ) {
				
				return $obj->user_id;
				break;
			
			}
			
			$user_id_arr[] = $obj->user_id;
		
		}
		
		return wplp_traverse_for_referrer($user_id_arr);
	
	}
  
}

function wplp_traverse_for_referrer_reverse(){
	global $wpdb;
	
	$sql = "SELECT ID FROM ".$wpdb->prefix."users ORDER BY ID DESC";
	$users = $wpdb->get_results($sql);	
	$user_id_arr = array();
	
	if( ! empty( $users ) ) {	
		
		foreach ( $users as $obj ) {
			
			if( wplp_can_have_lead( $obj->ID ) ) {
				
				return $obj->ID;
				break;
			
			}
			
			$user_id_arr[] = $obj->ID;
		
		}
		
		return wplp_traverse_for_referrer_reverse($user_id_arr);
	
	}
  
}

function wplp_get_referrer_id(){
	global $wp, $wpdb, $wplp_admin, $_GET, $_POST;
	
	// Get user as referrer
	if( isset( $_COOKIE['wplp-ref'] ) && !empty( $_COOKIE['wplp-ref'] ) ) {
		
		$refCookie = $_COOKIE['wplp-ref'];
		
		// Check is referred by system for unlimited random distribution to any member with access to campaign/company
		if( $refCookie == __( 'wplp_rotation', 'wp-leads-press' ) ){ //Used as generic cookie to bypass normal traffic distribution rules.
			
			$ref_user_id = wplp_get_random_referrer();	
			
			if ( wplp_is_affiliate_id_set( $ref_user_id ) ) {
				
				return $ref_user_id; // If the random user has affiliate ID set, return it
				
			} else {
				
				$findRef = wplp_traverse_for_referrer( array( $ref_user_id ) ); // Find first user below ref who has affiliate ID set.
				
				if( empty( $findRef ) ) {
					
					$findRef = wplp_traverse_for_referrer_reverse();	// Can't find a user below user who can get lead, find first user from last user back who qualifies.
					
				}
				
				$ref_user_id = $findRef;
				return $ref_user_id; // Return the ID
				
			}
			
		}


		// Check if user 
		$field = 'slug';
		$value = $refCookie;
		$ref_user_exists = get_user_by( $field, $value ); // Get user obj
		
		// If the user exists
		if( $ref_user_exists == true ) {
		
			$ref_user_id = $ref_user_exists->ID;					
	
			// Do check to see if user can have lead
			if ( !wplp_can_have_lead( $ref_user_id ) ) {
				// If referrer cannot have lead, find member of team who can
				$findRef = wplp_traverse_for_referrer(array( $ref_user_id ) );
								
					// If no member of team can have lead, find first available user who can. 
					if( empty( $findRef ) ) {
						
						// If select ID's should get random traffic
						$options = wp_load_alloptions();
						$select_ids = $options['wp_leads_press_select_user_ids'];
						
						if( $select_ids == "on" ) {
							
							$ids = $options['wp_leads_press_ids_for_random_traffic']; // single value or array of ids
										
							$ids = explode(',', $ids);


							if ( is_array( $ids ) ) {	
							
								$ref = array_rand($ids); // Get random KEY from array
							
								$findRef = $ids[$ref]; // get value of key for ref
											
							} else {
								
								$findRef = $ids;	
													
							}
											
						} else {			
						
							// Searches users from last to first to find first user who can have a lead
							$findRef = wplp_traverse_for_referrer_reverse();
							
						}						
						
					}
				
				$ref_user_id = $findRef;				
				
				update_user_meta( $ref_user_id, 'wplp_direct_ref', 'no' );
				return $ref_user_id;
				
			}	
				
			//Show lead or member as direct referral
			update_user_meta( $ref_user_id, 'wplp_direct_ref', 'yes' );						
			
			return $ref_user_id;	
			
		}
					
		// If the user id does NOT exist
		if( $ref_user_exists == false ) { 

			// If select ID's should get random traffic
			$options = wp_load_alloptions();
			
			$select_ids = $options['wp_leads_press_select_user_ids'];
						
			if( $select_ids == "on" ) {
				
				$ids = $options['wp_leads_press_ids_for_random_traffic']; // single value or array of ids
							
				$ids = explode(',', $ids);			
				
				if ( is_array( $ids ) ) {	
				
					$ref = array_rand($ids); // Get random KEY from array
				
					$findRef = $ids[$ref]; // get value of key for ref
								
				} else {
					
					$findRef = $ids;	
										
				}
								
			} else {			
				
				$findRef = wplp_get_random_referrer();
				
			}
				
				/// if referrer cannot have lead, find member of team who can
				if( ! wplp_can_have_lead( $findRef ) ) {
				
					$findRef = wplp_traverse_for_referrer(array( $findRef ) );
					
					if( empty( $findRef ) ) {
						
							// Searches users from last to first to find first user who can have a lead
							$findRef = wplp_traverse_for_referrer_reverse();

						
					} // End if empty $ref_user_id
									
				}
				
			//Show lead or member as indirect referral
			
			$ref_user_id = $findRef; 
			
			update_user_meta( $ref_user_id, 'wplp_direct_ref', 'no' );
			
			return $ref_user_id;			
		
		}
		 
		
	} else { // If user was not referred by anyone no cookie
			
			$options = wp_load_alloptions();			
			
			// If select ID's should get random traffic

			$select_ids = $options['wp_leads_press_select_user_ids'];
						
			if( $select_ids == "on" ) {
				
				$ids = $options['wp_leads_press_ids_for_random_traffic']; // single value or array of ids
							
				$ids = explode(',', $ids);				
				
				if ( is_array( $ids ) ) {	
				
					$ref = array_rand($ids); // Get random KEY from array
				
					$findRef = $ids[$ref]; // get value of key for ref
								
				} else {
					
					$findRef = $ids;	
										
				}
								
			} else {
				
				
				if ( isset( $_POST['wplp_campaign'] ) ){
			
					$campaign = $_POST['wplp_campaign'];			
				
				}
				
				// Get default referrer value for campaign
				$campaign_default_referrer_ID =  wplp_get_campaign_field_value('wplp_campaign_default_referrer_id', $campaign);
				//$campaign_default_referrer_ID = get_the_terms( $campaign->ID, 'wplp_campaign_default_referrer_id' );
				
				if( isset( $campaign_default_referrer_ID ) && !empty( $campaign_default_referrer_ID ) ){
						
					$findRef = $campaign_default_referrer_ID;
				
				} else {
					
					$findRef = wplp_get_random_referrer();
					
				}
								
																			
			}
	
			// if referrer cannot have lead, find member of team who can
			if( ! wplp_can_have_lead( $findRef ) ) {
			
				$findRef = wplp_traverse_for_referrer( array( $findRef ) );
				
				if( empty( $findRef ) ) { // If random referrer's team cannot have leads find first user who can
					
					// Searches users from last to first to find first user who can have a lead
					$findRef = wplp_traverse_for_referrer_reverse();
					
				}
							
			}
				
			//Show lead or member as indirect referral
			
			$ref_user_id = $findRef;
			update_user_meta( $ref_user_id, 'wplp_direct_ref', 'no' );					
	
			return $ref_user_id;
	
	}
	



}
?>