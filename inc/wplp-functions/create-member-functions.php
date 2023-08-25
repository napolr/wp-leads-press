<?php
#################
//Register Member
#################
// Get all wplp options

$options = wp_load_alloptions();
if ( $options['wp_leads_press_require_registration_code'] == 'on' ) {

	if( is_plugin_active( 'buddypress/bp-loader.php' ) ) {	
	
		add_action('bp_signup_validate', 'wplp_bp_registration_errors');
		add_action('bp_before_account_details_fields', 'wplp_register_form');	
	
	}

	if( is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php' ) ) {

		add_action('pmpro_checkout_before_submit_button', 'wplp_register_form');
		add_filter('pmpro_registration_checks', 'wplp_pmpro_registration_checks');
	
	}

	//require the fields
	function wplp_pmpro_registration_checks(){
		
		global $pmpro_msg, $pmpro_msgt, $current_user;
		
		$options = wp_load_alloptions();
		
		// Get current user info, check if wplp_registration_code previously set
		$ref_user_info = get_userdata( $current_user->ID ); //wplp_registration_code
		
		if( !empty( $ref_user_info->wplp_registration_code ) ) {
			
			return true;
			
		} else {
		
			$requestRegCode = $_REQUEST['wplp_registration_code'];
			$regCode = $options['wp_leads_press_registration_code'];
		 
			if( $requestRegCode == $regCode ) {
				
				//all good
				return true;
			
			} 
			
			//Check for valid username entered as regcode
			$checkValidUserID = wplp_ref_user_id_by_nicename($requestRegCode);
			
			if( $checkValidUserID != NULL && $requesttRegCode != $regCode && $options['wp_leads_press_username_regcode'] == 'on' ){			
				
				return true;
				
			}
			
			
			// return false	
			$pmpro_msg = __( 'Incorrect Registration Code.', 'wp-leads-press' );
			$pmpro_msgt = "pmpro_error";
			return false;
		
		}
		
		
	}
		
		
		
	//1. Add a new form element to WP registration...
	add_action('register_form','wplp_register_form');
	function wplp_register_form (){
		global $wplp_admin, $_POST, $bp, $current_user, $wpdb, $wp;	
		
		if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) {
			
			$wplp_registration_code = ( $_POST['wplp_registration_code'] ) ? $_POST['wplp_registration_code']: '';
			
			if ( !empty( $bp->signup->errors ) ) {

				if ( is_array( $bp->signup->errors ) ) {
					
					foreach ( $bp->signup->errors as $error ) {
						
						echo $error. '<br />';	
						
					}
					
				} else {
					
					echo $bp->signup->errors. '<br />';
					
				}
				
			}
			
			?>
				
					<label for="wplp_registration_code"><?php _e( 'Registration Code (Required)', 'wp-leads-press' ); ?></label>
					<input type="text" name="wplp_registration_code" id="wplp_registration_code" value="<?php echo esc_attr( stripslashes( $wplp_registration_code ) ); ?>" size="25" />
			
			<?php
			
		} elseif( is_plugin_active( 'paid-memberships-pro/paid-memberships-pro.php' ) ){ 
		
			// Get current user info, check if wplp_registration_code previously set
			$ref_user_info = get_userdata( $current_user->ID ); //wplp_registration_code
			
			if( isset( $ref_user_info->wplp_registration_code ) ) {
				
				
			} else {
			
				$wplp_registration_code = ( $_POST['wplp_registration_code'] ) ? $_POST['wplp_registration_code']: '';
				?>
				<div>
					<label for="wplp_registration_code"><?php _e( 'Registration Code', 'wp-leads-press' ); ?></label>
					<input type="text" name="wplp_registration_code" id="wplp_registration_code" class="input pmpro_error pmpro_required" value="<?php echo esc_attr( stripslashes( $wplp_registration_code ) ); ?>" size="25" />
				</div>
				<?php		
				
			}
		
		} else { // if just standard WP or anything else.
			
			if( isset( $_POST['wplp_registration_code'] ) ){
				
				$wplp_registration_code = $_POST['wplp_registration_code'];
				
			} else {
			
				$wplp_registration_code = '';
			
			}
			
			?>
			<p>
				<label for="wplp_registration_code"><?php _e( 'Registration Code', 'wp-leads-press' ); ?></label><br />
				<input type="text" name="wplp_registration_code" id="wplp_registration_code" class="input" value="<?php echo esc_attr( stripslashes( $wplp_registration_code ) ); ?>" size="25" />
			</p>
			<?php				
			
		}
	
	}
	
	
	//2. Add validation. In this case, we make sure registration code is required.
	add_filter('registration_errors', 'wplp_registration_errors', 10, 3);
	function wplp_registration_errors ($errors, $sanitized_user_login, $user_email) {
		global $wp, $_POST;
		
		// Get all wplp options
		//$options = get_option( 'wp_leads_press_options' );
		$options = wp_load_alloptions();
		
		$postRegCode = $_POST['wplp_registration_code'];
		$regCode = $options['wp_leads_press_registration_code'];
		
		if ( empty( $_POST['wplp_registration_code'] ) ) {
		
			$errors->add( 'wplp_registration_code', __('<strong>ERROR</strong>: You must include the registration code.', 'wp-leads-press' ) );
			
		return $errors;				
			
		}
		
		//Check for valid username entered as regcode
		$checkValidUserID = wplp_ref_user_id_by_nicename($postRegCode);
		if( $checkValidUserID == NULL && $postRegCode != $regCode && $options['wp_leads_press_username_regcode'] == 'on' ){
			
			$errors->add( 'wplp_registration_code', __('<strong>ERROR</strong>: Incorrect Registration Code.', 'wp-leads-press' ) );				
	
			return $errors;
			
		}
		
		
		if ( $postRegCode != $regCode && $options['wp_leads_press_username_regcode'] != 'on' ) {
		
			$errors->add( 'wplp_registration_code', __('<strong>ERROR</strong>: Incorrect Registration Code.', 'wp-leads-press' ) );				
		
			return $errors;
		
		}
		
		return $errors;	
	
	}
	
	function wplp_bp_registration_errors ($errors) {
		global $wp, $_POST, $bp;
		
		// Get all wplp options
		//$options = get_option( 'wp_leads_press_options' );
		$options = wp_load_alloptions();
		
		$postRegCode = $_POST['wplp_registration_code'];
		$regCode = $options['wp_leads_press_registration_code'];
		
		if ( empty( $postRegCode ) || !isset( $postRegCode ) ) {
		
			$bp->signup->errors = '' . __('<strong>ERROR</strong>: You must include the registration code.', 'wp-leads-press' ) . '';
	
		$errors = $bp->signup->errors;			
		return $errors;				
			
		}

		$checkValidUserID = wplp_ref_user_id_by_nicename($postRegCode);
		if( $checkValidUserID == NULL && $postRegCode != $regCode && $options['wp_leads_press_username_regcode'] == 'on' ){
			
			$bp->signup->errors = '' . __('<strong>ERROR</strong>: Incorrect Registration Code.', 'wp-leads-press' ) . '';				
			
			$errors = $bp->signup->errors;						
			return $errors;
			
		}				
		
		if ( $postRegCode != $regCode && $options['wp_leads_press_username_regcode'] != 'on' ) {
		
			$bp->signup->errors = '' . __('<strong>ERROR</strong>: Incorrect Registration Code.', 'wp-leads-press' ) . '';				
		
		$errors = $bp->signup->errors;
		return $errors;			
		
		}
		
		//$errors = $bp->signup->errors;
		//return $errors;
	
	}
	
} // End if registration code required

add_action( 'user_register', 'wplp_registration_actions', 10, 1 );
function wplp_registration_actions($user_id) {
	global $wp, $wpdb, $_POST, $_GET;

	
	error_log('blam function fires...');
	//die('shit hit the fan');
	
	
	// Get all wplp options
	//$options = get_option( 'wp_leads_press_options' );
	$options = wp_load_alloptions();	
	
	//Get the referring member id		
	$ref_user_id = wplp_get_referrer_id_register($user_id);
	
error_log( 'ref user id from wplp_get_referrer_id_register'. $ref_user_id );

			
	// If random users are all set to go under user 0 and registering user is not using valid username as reg code
	// Check if valid username is used for registration
	$wplp_registration_code = ( $_POST['wplp_registration_code'] ) ? $_POST['wplp_registration_code']: '';
	
	// Validate username, get member who can have new member
	$checkUserNameReg = wplp_ref_user_id_by_nicename($wplp_registration_code);
			
	if( !isset( $_COOKIE['wplp-ref'] ) && $options['wp_leads_press_random_members_under_noone'] == 'on' && $checkUserNameReg == NULL  ) { // check if random members go under user 0
	
		$ref_user_id = 0;
	
	}
			
	// Reset the cookie for Affiliate plugins if 'run as network' is selected for integration and smn is on.
	$wplp_affiliates_as_network = $options['wp_leads_press_affiliates_as_network'];
	$wplp_smn_on = $options['wp_leads_press_smn_on'];	
	
	if ( $wplp_affiliates_as_network == 'on' && $wplp_smn_on == 'on' ) {
		
		// call function to set cookie for each 3rd party affiliate plugin here
		// Lets add do_actions here in later release, 2.0
		// Set cookie to track user if affiliate								
		wplp_itthinx_set_affiliate_cookie($ref_user_id, $user_id);
	
	}

	// Reset the cookie for idevaffiliate if 'run as network' is selected for integration and smn is on.
	$wplp_idevaffiliate_as_network = $options['wp_leads_press_idevaffiliate_as_network'];
	$wplp_smn_on = $options['wp_leads_press_smn_on'];	
	
	if ( $wplp_idevaffiliate_as_network == 'on' && $wplp_smn_on == 'on' ) {
		
		// call function to set cookie for each 3rd party affiliate plugin here
		// Lets add do_actions here in later release, 2.0
		// Set cookie to track user if affiliate								
		wplp_idevaffiliate_set_affiliate_cookie($ref_user_id, $user_id);
	
	}

	//set referrer id to track who referred member to site			
  	update_user_meta( $user_id, 'wplp_referrer_id', $ref_user_id);
	
	//used for counting personal leads generated to determine if user can have random leads
	update_user_meta( $user_id, 'wplp_ref_lead_count', 0 );
	
	//Used to track total number of leads generated
	update_user_meta( $user_id, 'wplp_total_ref_lead_count', 0 );
		
	//used to keep total of random leads recieved
	update_user_meta( $user_id, 'wplp_total_random_lead_count', 0 );
	
	//used to track referred members generated to determine if user can have random leads and members
	update_user_meta( $user_id, 'wplp_ref_member_count', 0 );
	
	//used to track total visits referred to site by user
	update_user_meta( $user_id, 'wplp_total_ref_traffic_count', 0 );
	
	//used to track code user used to register
	if ( isset( $_POST['wplp_registration_code'] ) ) {
    	
		update_user_meta($user_id, 'wplp_registration_code', $_POST['wplp_registration_code']);
    
	}
	
	
	// Send Email To New Member
	$sendNMWE = $options['wp_leads_press_send_new_member_welcome_email'];
									
	if ( $sendNMWE == "on" ) { 
	
		$from_email = get_option( 'admin_email' );
		$from_name = get_option('blogname');
	
		// Get ref member details.
		$ref_user_info = get_userdata( $ref_user_id );
		
		//Get new member details
		$member_info = get_userdata( $user_id );
		
		// Variables for wplp_system_email() function.
		$to_email =  $member_info->user_email;
				
		// Do check for custom subject
		$subject = $options['wp_leads_press_welcome_new_member_subject'];
		
			if ( isset( $subject ) && !empty( $subject ) ) {
				
				$subject = $subject;
				
			} else {
				
				$subject = __( '{MEMBER-FIRST-NAME}, thank you for registering at {SITE-NAME}!', 'wp-leads-press' );
				
			}
		
		// Do check for custom message
		
		$message = $options['wp_leads_press_welcome_new_member_body'];
		
			if ( isset( $message ) && !empty( $message ) ) {
				
				$message = $message;
				
			} else {
			
				$message = __( '{MEMBER-FIRST-NAME}, 
		
					Thank you for registering at {SITE-NAME} to learn more about {COMPANY-NAME}, your referrer is, {REF-MEMBER-FIRST-NAME} {REF-MEMBER-LAST-NAME}, don\'t hesitate to contact them with any questions you may have at {REF-MEMBER-EMAIL}.', 'wp-leads-press' );
			
			}
		
		if( isset($_COOKIE['wplp-campaign']) ) {
			
			$campaign_id = $_COOKIE['wplp-campaign'];
		
		} else {
			
			$campaign_id = NULL;	
			
		}
		
		if( isset($_COOKIE['wplp-landing-page'] ) ){
			
			$landing_page_id = $_COOKIE['wplp-landing-page'];
			
		} else {
			
			$landing_page_id = NULL;	
			
		}
		
		$ref_user_id = $ref_user_id;	
		$lead_id = NULL;
		$member_id = NULL;
		
		wplp_system_email( $from_email, $from_name, $to_email, $subject, $message, $campaign_id, $landing_page_id, $ref_user_id, $lead_id, $member_id);
				
	} // End send new member email
	
	
	if( $ref_user_id != 0 ) {	
	
		// Send Member lead upgrade to member email
		$sendLUME = $options['wp_leads_press_send_lead_upgrade_member_email'];
										
		if ( $sendLUME == "on" ) { 
		
			$from_email = get_option( 'admin_email' );
			$from_name = get_option('blogname');
		
			// Get ref member details.
			$user_info = get_userdata( $ref_user_id );
			
			// Variables for wplp_system_email() function.
			$to_email =  $user_info->user_email;
					
			// Do check for custom subject
			if( isset( $options['wp_leads_press_new_lead_to_member_subject'] ) && !empty($options['wp_leads_press_new_lead_to_member_subject'] ) ){
			
				$subject = $options['wp_leads_press_new_lead_to_member_subject'];
			
			}
			
			if ( isset( $subject ) && !empty( $subject ) ) {
				
				$subject = $subject;
				
			} else {
				
				$subject = __( '{REF-MEMBER-FIRST-NAME}, you just had a lead upgrade to member at {SITE-NAME}!', 'wp-leads-press' );
				
			}
			
			// Do check for custom message
			if( isset( $options['wp_leads_press_new_lead_to_member_body'] ) && !empty($options['wp_leads_press_new_lead_to_member_body'] ) ){
				
				$message = $options['wp_leads_press_new_lead_to_member_body'];
			
			}
			
			if ( isset( $message ) && !empty( $message ) ) {
				
				$message = $message;
				
			} else {
			
				$message = __( '{REF-MEMBER-FIRST-NAME}, 
		
					You just had a lead upgrade to member at {SITE-NAME} as a member of {COMPANY-NAME}, <a href="{SITE-URL}">Login</a> to contact your new member and help them get started.', 'wp-leads-press' );
			
			}
		
			if( isset( $_COOKIE['wplp-campaign'] ) ) {
				
				$campaign_id = $_COOKIE['wplp-campaign'];
			
			} else {
				
				$campaign_id = NULL;	
				
			}
			
			if( isset( $_COOKIE['wplp-landing-page'] ) ){
				
				$landing_page_id = $_COOKIE['wplp-landing-page'];
				
			} else {
				
				$landing_page_id = NULL;	
				
			}
			
			
			$ref_user_id = $ref_user_id;	
			//$lead_id = $post_id;
			$lead_id = NULL;
			$member_id = NULL;
			wplp_system_email( $from_email, $from_name, $to_email, $subject, $message, $campaign_id, $landing_page_id, $ref_user_id, $lead_id, $member_id);
					
		} // End send lead upgrade to member notice
	
	}
	
}

function wplp_get_referrer_id_register($user_id){
	global $wp, $wpdb, $_GET, $_POST;

	$options = wp_load_alloptions();	
	
	// get user as referrer using username as referral code
	if( $options['wp_leads_press_require_registration_code'] == 'on' && $options['wp_leads_press_username_regcode'] == 'on' ){						
	
		// Check if registration code is valid username OR if is default registration code 
		$regCode = $options['wp_leads_press_registration_code'];
		
		// Get reg code and check if valide username
		$wplp_registration_code = ( $_POST['wplp_registration_code'] ) ? $_POST['wplp_registration_code']: '';
		
		if( $regCode == $wplp_registration_code && isset( $_COOKIE['wplp-ref'] ) ){
			
			$userName = $_COOKIE['wplp-ref'];
			
		} else {
			
			$userName = $wplp_registration_code;
			
		}
				
		// validate username see if can have member
		$ref_user_id = wplp_ref_username_valid_return( $field = 'slug', $userName, $user_id );

		// If ref_user_id is valid and = to default registration code if default, do nothing
		if( $wplp_registration_code = $regCode && $ref_user_id != NULL ) {
			
			//update_user_meta( $ref_user_id, 'wplp_direct_ref', 'no' );
			
			return $ref_user_id;
			
		} 
		
	} // end if( $options['wp_leads_press_require_registration_code'] == 'on' && $options['wp_leads_press_username_regcode'] == 'on' )

	
	// Get user as referrer using cookie
	if( isset( $_COOKIE['wplp-ref'] ) && !empty( $_COOKIE['wplp-ref'] ) ) {
		
	//error_log('blam... cookie is set');
	//die('cookie is set');
	
		$refCookie = $_COOKIE['wplp-ref'];
		
		// Check is referred by system for unlimited random distribution to any member with access to campaign/company
		if( $refCookie == __( 'wplp_rotation', 'wp-leads-press' ) ){ //Used as generic cookie to bypass normal traffic distribution rules.
			
			$ref_user_id = wplp_get_random_referrer();	
			
			if ( !wplp_user_banned( $ref_user_id ) ) {
				
				return $ref_user_id; // If the random user is not banned
				
			} else {
				
				$findRef = wplp_traverse_for_referrer_register( array( $ref_user_id ), $user_id ); // Find first user below ref who can have a member
				
				if( empty( $findRef ) ) {
					
					$findRef = wplp_traverse_for_referrer_reverse_register($user_id);	// Can't find a user below user who can get member, find first user from last user back who qualifies.
					
				}
				
				$ref_user_id = $findRef;	
							
				return $ref_user_id; // Return the ID
				
			}
			
		} // end if cookie = rotation


		// Check if cookie is for valid user 
		$userName = $refCookie;	

//die('ref cookie' . $refCookie );	
		
		$ref_user_id = wplp_ref_username_valid_return( $field = 'slug', $userName, $user_id );
		
		if( $ref_user_id != NULL ){
			
//die('ref user id is not null'. $ref_user_id);
			
			return $ref_user_id;
		 
		} else { // just find a user to give NULL user too
			
//die('ref user id is null'. $ref_user_id);

			// Search and users top to bottom and give to first user who can have new member	
			$ref_user_id = wplp_traverse_for_referrer_register( $user_id_arr = 0, $user_id);	
			return $ref_user_id;
						
		}
		
	} else { // If user was not referred by anyone no cookie
						
		// If convert to forced network is on and forced randoms is selected, start search from top of network down
		if( $options['wp_leads_press_smn_on'] == 'on' && $options['wp_leads_press_convert_forced_on'] == 'on' && $options['wp_leads_press_force_randoms'] == 'on' ) {
		
			$findRef = 0;
			
			// find a user under user 0 to give new member to
			$findRef = wplp_traverse_for_referrer_register( $findRef, $user_id );
										
		} else {
			
			$findRef = wplp_get_random_referrer();		
			
		}
		
		
							
		// if referrer cannot have member, find member of team who can
		if( ! wplp_can_have_member( $findRef, $user_id ) ) {
				
			$findRef = wplp_traverse_for_referrer_register( $findRef, $user_id );
					
			if( empty( $findRef ) ) { // If team cannot have members find first user who can
						
				// Searches users from last to first to find first user who can have a member
				$findRef = wplp_traverse_for_referrer_reverse_register($user_id);
								
			}
										
		}
				
				
		$ref_user_id = $findRef;
		
		//Show lead or member as indirect referral			
		update_user_meta( $ref_user_id, 'wplp_direct_ref', 'no' );
		
		//Get total count of members random members	
		$totalRandomMembersCount = get_user_meta($ref_user_id, 'wplp_total_random_members_count', true);

		//update number of non personally referred users for ref_user
		$update = $totalRandomMembersCount + 1;  
		update_user_meta( $ref_user_id, 'wplp_total_random_members_count', $update);
														
		//return value of ref 
		return $ref_user_id;
	
	} // end no cookie/indirect
	
}

// validates username and returns $ref_user_id or NULL if false
function wplp_ref_username_valid_return( $field = 'slug', $userName, $user_id ){
	global $wp, $_POST;
	
	$options = wp_load_alloptions();	

		$ref_user_exists = get_user_by( $field, $userName ); // Get user obj	
				
				
		// If the user exists
		if( is_object( $ref_user_exists ) ) {
		
		
			$rawID = $ref_user_exists->ID;				
			
			// Do check to see if user can have member
			if ( !wplp_can_have_member( $rawID, $user_id ) ) {			
			
			// If referrer cannot have member, find member of team who can
				$findRef = wplp_traverse_for_referrer_register(array( $rawID ), $user_id );
								
					// If no member of team can have member, find first available user who can. 
					if( empty( $findRef ) ) {			
						
						// Searches users from last to first to find first user who can have a lead
						$findRef = wplp_traverse_for_referrer_reverse_register($user_id);
							
					}						
						
				
				$ref_user_id = $findRef;							
				update_user_meta( $ref_user_id, 'wplp_direct_ref', 'no' );
		
				return $ref_user_id;
				
			} // end if can't have member				
			
			$ref_user_id = $rawID;
			update_user_meta( $ref_user_id, 'wplp_direct_ref', 'yes' );
			
			return $ref_user_id;	
			
		} // end if user exists
					
		// If the user id does NOT exist
		if( $ref_user_exists == false ) {

			return NULL;			
		
		}	
	
}

function wplp_traverse_for_referrer_register($user_id_arr, $user_id) {
	global $wpdb;
		
	# Get all children referred by user
	if( is_array( $user_id_arr ) ){
	
		$values = implode(",", $user_id_arr);
	
	} else {
		
		$values = $user_id_arr;	
		
	}
	
	$sql = "SELECT user_id FROM ".$wpdb->prefix."usermeta WHERE meta_value IN (".$values.") AND meta_key IN ('wplp_referrer_id') ORDER BY user_id ASC";	
	$users = $wpdb->get_results($sql);	
	$user_id_arr = array();
	
	if( ! empty( $users ) ) {	
		
		foreach ( $users as $obj ) {
			
			if( wplp_can_have_member( $obj->user_id, $user_id ) ) {
				
				return $obj->user_id;
				break;
			
			}
			
			$user_id_arr[] = $obj->user_id;
		
		}
		
		return wplp_traverse_for_referrer_register($user_id_arr, $user_id);
	
	}
  
}

function wplp_traverse_for_referrer_reverse_register($user_id){
	global $wpdb;
	
	$sql = "SELECT ID FROM ".$wpdb->prefix."users ORDER BY ID DESC";
	$users = $wpdb->get_results($sql);	
	
	if( ! empty( $users ) ) {	
		
		foreach ( $users as $obj ) {
			
			if( wplp_can_have_member( $obj->ID, $user_id ) ) {
				
				return $obj->ID;
				break;
			
			}
			
			//$user_id_arr[] = $obj->ID;
		
		}
		
		//return wplp_traverse_for_referrer_reverse_register($user_id);
	
	}
  
}


function wplp_can_have_member( $rawID, $user_id) {
	global $wp, $wpdb, $_GET, $_POST;
		
	// $current_user == $user_id No referring yourself... 
	if( $user_id == $rawID ){
		
		return false;
		
	}
	
	// Check Ban Status
	if( wplp_user_banned( $rawID ) == true ) {
		
		return false;
		
	}


	
	// Get all wplp options
	$options = wp_load_alloptions();

	// Check if user is active idev affiliate by checking if value is set for company and user is active.
	if( isset( $options['wp_leads_press_idevaffiliate_install_directory'] ) && ( !empty( $options['wp_leads_press_idevaffiliate_install_directory'] ) ) ){
	
		$idev_id = wplp_get_idev_user_wpID($rawID);
		
		// if null
		if( !isset( $idev_id ) ){
			
			return false;
			
		}
		
		if( wplp_validate_idevUser_by_id($idev_id) == NULL ){
			
			return false;
			
		}
		
	}


	
	// check SMN and act accordingly
	if ( $options['wp_leads_press_smn_on'] == 'on' ){
		
		// get value for building structured member network	
		$smnValue = $options['wp_leads_press_smn_value'];							
		// check if random user has been given random members by system
		$totalRandomMembersCount = get_user_meta($rawID, 'wplp_total_random_members_count', true);
		//Check for bonus members
		$bonusMembers = get_user_meta($rawID, 'wplp_bonus_members', true);

		// If convert to forced network is off and SMN is on
		if( $options['wp_leads_press_convert_forced_on'] != 'on' ) {
			
			// Check if registration code is valid username OR if is default registration code 
			$regCode = $options['wp_leads_press_registration_code'];
	
			// Get registration code if used
			$wplp_registration_code = ( $_POST['wplp_registration_code'] ) ? $_POST['wplp_registration_code']: '';
	
			if( $regCode == $wplp_registration_code ) {

				if( isset( $_COOKIE['wplp-ref'] ) && !empty( $_COOKIE['wplp-ref'] ) ){

					// If cookie is username, get user obj
					$ref_user_exists = get_user_by( 'slug', $_COOKIE['wplp-ref'] ); // Get user obj			
										
					if( is_object( $ref_user_exists ) ) {
										
						$ref_user_exists_nicename = $ref_user_exists->user_nicename;
							
					} 				
				
				} else {
						
					$ref_user_exists_nicename = 'not set';
						
				}// end if cookie set
								
			} elseif ( $regCode != $wplp_registration_code && get_user_by( 'slug', $wplp_registration_code ) == true ){ // end if regCode = wplp_registration_code

				// If reg code is username, get user obj
				$ref_user_exists = get_user_by( 'slug', $wplp_registration_code ); // Get user obj			
				
				if( is_object( $ref_user_exists ) ) {
										
					$ref_user_exists_nicename = $ref_user_exists->user_nicename;
						
				}				
				
			} else {
				
				$ref_user_exists_nicename = 'not set';
								
			}
			
			// if cookie is set and is = ref user or if reg code matches ref user, give as direct ref
			if( isset( $_COOKIE['wplp-ref'] ) && !empty( $_COOKIE['wplp-ref'] ) && $_COOKIE['wplp-ref'] == $ref_user_exists_nicename || $wplp_registration_code == $ref_user_exists_nicename ) {
							
				//Update referring user member count
				$refMembers = get_user_meta($rawID, 'wplp_ref_member_count', true);		
				$refMembersReq = $options['wp_leads_press_ref_member_count_required_smn'];
				
				//Check for bonus members
				//$bonusMembers = get_user_meta($user_id, 'wplp_bonus_members', true);
				$bonusMembersValue = $options['wp_leads_press_ref_member_bonus_members_value_smn'];
				
				//Random Structured Member Network
				if( $options['wp_leads_press_ref_member_count_required_smn'] != 0 ){
					
					//Update referring user member count
					$refMembers_smn = get_user_meta($rawID, 'wplp_ref_member_count_smn', true);		
					$refMembersReq_smn = $options['wp_leads_press_ref_member_count_required_smn'];	
					
					//Check for bonus members
					$bonusMembers = get_user_meta($rawID, 'wplp_bonus_members', true);
					$bonusMembersValue = $options['wp_leads_press_ref_member_bonus_members_value_smn'];
														
					if( $refMembers_smn <= $refMembersReq_smn ) {
					
						$update = $refMembers_smn+1;
						
						// Check to see if $refMembers+1 = $refMembersReq
						// if so: update bonus members						
						if( $update >= $refMembersReq_smn ){
							
							// Add bonus members here
							$bonusMembers = $bonusMembers+$bonusMembersValue;

							update_user_meta( $rawID, 'wplp_bonus_members', $bonusMembers );
							
							$update = 0;

							update_user_meta( $rawID, 'wplp_ref_member_count_smn', $update );
					
						} elseif( $update < $refMembersReq_smn ) {
						
							update_user_meta( $rawID, 'wplp_ref_member_count_smn', $update );
						
						}						
					
					}				
					
					// Update user member count of referring user
					//update_user_meta( $rawID, 'wplp_ref_member_count', $update );
					update_user_meta( $rawID, 'wplp_ref_member_count_smn', $update );
					
				}// end check for ref member count RSMN
	
				update_user_meta( $rawID, 'wplp_direct_ref', 'yes' );

				return true;
			
			} // end if is set ref cookie 			
			
		} // end convert != on


		// if convert to forced network is on		
		if( $options['wp_leads_press_convert_forced_on'] == 'on' ) {
			
			//check if referred by current user_id, i.e. cookie is set, if so update ref member count, yet give to another member of team if they do not have bonus members
		
			// Get registration code if used
			$wplp_registration_code = ( $_POST['wplp_registration_code'] ) ? $_POST['wplp_registration_code']: '';
			$regCode = $options['wp_leads_press_registration_code'];

			if( $regCode == $wplp_registration_code ) {


				if( isset( $_COOKIE['wplp-ref'] ) && !empty( $_COOKIE['wplp-ref'] ) ){

					// If code is username, get user obj
					$ref_user_exists = get_user_by( 'slug', $_COOKIE['wplp-ref'] ); // Get user obj			
										
					if( is_object( $ref_user_exists ) ) {
												
						$ref_user_exists_nicename = $ref_user_exists->user_nicename;
							
					} 				
				
				} else {
						
					$ref_user_exists_nicename = 'not set';
						
				}// end if cookie set
								
			} elseif ( $regCode != $wplp_registration_code && get_user_by( 'slug', $wplp_registration_code ) == true ){ // end if regCode = wplp_registration_code

				// If code is username, get user obj
				$ref_user_exists = get_user_by( 'slug', $wplp_registration_code ); // Get user obj			
								
				if( is_object( $ref_user_exists ) ) {
										
					$ref_user_exists_nicename = $ref_user_exists->user_nicename;
						
				}				
				
			} else {
				
				$ref_user_exists_nicename = 'not set';
					
			}
											
			// if cookie is set and is = ref user or if reg code matches ref user, give as direct ref
			if( isset( $_COOKIE['wplp-ref'] ) && !empty( $_COOKIE['wplp-ref'] ) && $_COOKIE['wplp-ref'] == $ref_user_exists_nicename || $wplp_registration_code == $ref_user_exists_nicename ) {
							
				//Update referring user member count
				$refMembers = get_user_meta($rawID, 'wplp_ref_member_count_smn', true);	
					
				$refMembersReq = $options['wp_leads_press_ref_member_count_required_smn'];
				
				//Check for bonus members
				//$bonusMembers = get_user_meta($user_id, 'wplp_bonus_members', true);
				$bonusMembersValue = $options['wp_leads_press_ref_member_bonus_leads_value_smn'];
				
				//Get total count of members random members
				$totalRandomMembersCount = get_user_meta($user_id, 'wplp_total_random_members_count', true);		
								
				//Random Structured Member Network
				if( $options['wp_leads_press_ref_member_count_required_smn'] != 0 ){
					
					//Update referring user member count
					$refMembers_smn = get_user_meta($rawID, 'wplp_ref_member_count_smn', true);							
					$refMembersReq_smn = $options['wp_leads_press_ref_member_count_required_smn'];	
					
					//Check for bonus members
					$bonusMembers = get_user_meta($rawID, 'wplp_bonus_members', true);
					$bonusMembersValue = $options['wp_leads_press_ref_member_bonus_members_value_smn'];

					if( $refMembers_smn <= $refMembersReq_smn && $rawID == $ref_user_exists->ID ) {
					
						$update = $refMembers_smn+1;
						
						// Check to see if $refMembers+1 = $refMembersReq
						// if so: update bonus members
						if( $update >= $refMembersReq_smn ){
							
							// Add bonus members here
							$bonusMembers = $bonusMembers+$bonusMembersValue;

							update_user_meta( $rawID, 'wplp_bonus_members', $bonusMembers );
							
							$update = 0;

							update_user_meta( $rawID, 'wplp_ref_member_count_smn', $update );
					
						} elseif( $update < $refMembersReq_smn ) {
						
							update_user_meta( $rawID, 'wplp_ref_member_count_smn', $update );
						
						}
						
					}			
				
				}// end check for ref member count RSMN
			
			} // end if is set ref cookie 
			
			$user_personals = wplp_get_network_personals($rawID);
			$bonusMembers = get_user_meta($rawID, 'wplp_bonus_members', true);

			$count = count( $user_personals );
			if( $count >= $smnValue && $bonusMembers <= 0 ) {				

				//find another member to give user as direct.
				return false; 
				
			}
			
		} //end convert to forced on			


		//Get total count of member front level children			
		$user_personals = wplp_get_network_personals($rawID);

		if( ( $totalRandomMembersCount || count( $user_personals ) >= $smnValue ) && ( $bonusMembers > 0 ) ){
			
			// Update bonus members value if run as forced is selected to properly subtract bonus members from ref user total		
			if ( $options['wp_leads_press_smn_on'] == 'on' ){
				
				// get value for building structured member network	
				$smnValue = $options['wp_leads_press_smn_value'];							
				//Check for bonus members
				$bonusMembers = get_user_meta($rawID, 'wplp_bonus_members', true);
		
				// if convert to forced network is on do check		
				if( $options['wp_leads_press_convert_forced_on'] == 'on' ) {
					
					$count = count( $user_personals );	
					
					if( $count >= $smnValue && $bonusMembers > 0 ) {	
	
						//update bonus member count of user
						$update = $bonusMembers - 1;
									
						// Update
						if( $update >= 0 ){
						
							update_user_meta( $rawID, 'wplp_bonus_members', $update );					
							
						}				
					
					}			
					
				} // end if convert on
				
			} // end if smn on			
			
			return true;
			
		}
		
		if( $totalRandomMembersCount < $smnValue ){ // if less than total
			
			return true;
			
		}

		// Checking for role approval
		if ( user_can( $user_id, 'administrator' ) ) {
		  
			// Go ahead and give member to admin, admins always qualify for random members
			return true;
		  
		}		

		if( $totalRandomMembersCount >= $smnValue && !isset( $_COOKIE['wplp-ref'] ) ){ // greator than or equal to total and no ref cookie set for visitor
			
			return false;
			
		} 
							
	} // end if smn on	
	
	// Fail safe return	
	return true;

}
?>