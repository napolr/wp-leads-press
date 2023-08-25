<?php
// Refer to our getting started guide for a complete API walkthrough
// https://labs.aweber.com/getting_started/main

add_action( 'wp_ajax_wplp_connect_aweber', 'wplp_connect_aweber' );
add_action( 'wp_ajax_nopriv_wplp_connect_aweber', 'wplp_connect_aweber' );
function wplp_connect_aweber(){
	global $_POST;
	
	require_once('aweber_api.php');
	
    if( isset( $_POST['wplp_aweber_auth'] ) ){
 
		$auth = $_POST['wplp_aweber_auth'];
	 
		// Do your validation, sanitization, etc. 
	 
		try {
	 
			// We'll use the method from AweberAPI to extract the credentials
			$aweber_auth = AWeberAPI::getDataFromAweberID($auth);
	 
			// Let's get the credentials from the $aweber_auth array	 
			$consumerKey = $aweber_auth[0];
			$consumerSecret = $aweber_auth[1];
			$accessKey = $aweber_auth[2];
			$accessSecret = $aweber_auth[3];
			
			if( isset( $_POST['wplp_user_selector'] ) ) {
				
				$user_selector = $_POST['wplp_user_selector'];
				
			} else {
				
				$user_selector = NULL;	
				
			}
	 
			//And now we will store the credentials
			if ( $user_selector == 'admin' ){
				
				update_option('wp_leads_press_aweber_auth', $auth); 
				update_option('wp_leads_press_aweber_consumerKey', $consumerKey); 
				update_option('wp_leads_press_aweber_consumerSecret', $consumerSecret);
				update_option('wp_leads_press_aweber_accessKey', $accessKey);
				update_option('wp_leads_press_aweber_accessSecret', $accessSecret);
				
			}
			
			if ( $user_selector == 'user' ) {
	
				if( isset( $_POST['wplp_ref_user_id'] ) ) {
					
					$ref_user_id = $_POST['wplp_ref_user_id'];	
					
				} else {
					
					$ref_user_id = NULL;	
					
				}
				
				//error_log( 'ref user id = '.$ref_user_id );
				if ( get_user_meta($ref_user_id,  'wplp_aweber_consumerKey', true ) != $consumerKey ){

					update_user_meta( $ref_user_id, 'wplp_aweber_auth', $auth); 
					update_user_meta( $ref_user_id, 'wplp_aweber_consumerKey', $consumerKey); 
					update_user_meta( $ref_user_id, 'wplp_aweber_consumerSecret', $consumerSecret);
					update_user_meta( $ref_user_id, 'wplp_aweber_accessKey', $accessKey);
					update_user_meta( $ref_user_id, 'wplp_aweber_accessSecret', $accessSecret);
					
				}
				
			}
			
			return __( 'Success!', 'wp-leads-press' );
	 
		} catch( AWeberAPIException $exc ){
	 
			print "AWeberAPIException:";
			print "Type: $exc->type";
			print "Msg : $exc->message";
			print "Docs: $exc->documentation_url";
	 
		}
 
    } else {
		
		return __( 'no post', 'wp-leads-press' );
		
	}
 
	// Kill process
	die();
}

function wplp_add_contact_aweber($name, $email, $wplp_campaignID, $leadIP, $consumerKey, $consumerSecret, $accessKey, $accessSecret, $list_id){
	
	require_once('aweber_api.php');
	
	$aweber = new AWeberAPI($consumerKey, $consumerSecret);
	
	try {

		$account = $aweber->getAccount($accessKey, $accessSecret);
		$listURL = "/accounts/".$account->data['id']."/lists/".$list_id;
	
	try {
    
		$list = $account->loadFromUrl($listURL);
	
	} catch (AWeberAPIException $exc) {
    
		echo '<pre>';
		print_r( $exc );
		echo '</pre>';
	
	}

		# create a subscriber
		$params = array(
			'email' => $email,
			'ip_address' => $leadIP, // Get IP associated with lead
			'ad_tracking' => $wplp_campaignID,
			//'misc_notes' => '',
			'name' => $name,
			//'custom_fields' => array(
				//'Car' => 'Ferrari 599 GTB Fiorano',
				//'Color' => 'Red',
			//),
		);

		$subscribers = $list->subscribers;
		$new_subscriber = $subscribers->create($params);
	
		# success!
		//print "A new subscriber was added to the $list->name list!";
	
	} catch(AWeberAPIException $exc) {
		print "AWeberAPIException:";
		print "Type: $exc->type";
		print "Msg : $exc->message";
		print "Docs: $exc->documentation_url";
		exit(1);
	}
	
}