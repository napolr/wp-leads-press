<?php
function wplp_system_email( $from_email, $from_name, $to_email, $subject, $message, $campaign_id, $landing_page_id, $ref_user_id, $lead_id, $member_id ) {
	
	global $wp, $wpdb, $wplp_admin, $_GET, $_POST;
	
	//$from_email = REQUIRED
	//$from_name = REQUIRED
	//$to_email = REQUIRED
	//$subject = REQUIRED
	//$message = REQUIRED
	//$campaign_id = REQUIRED
	//$landing_page_id = REQUIRED
	//$ref_user_id = the id from user table, used to pull replace data for registered user system emails, user first last name etc. OPTIONAL
	//$lead_id = OPTIONAL
	//$member_id = optional
		
	// Get all wplp options
	//$options = get_option( 'wp_leads_press_options' );
	$options = wp_load_alloptions();

	//Get Blog info.
	$blogname = get_option('blogname');
	//$blogUrl = get_bloginfo('wpurl');
	$blogUrl = site_url();
	$blogEmail = get_bloginfo('admin_email');	
	
	//Get user data for referring user
	$ref_user_data = get_userdata( $ref_user_id );
		
	if( $ref_user_id == 0 && $ref_user_data == NULL ) { //&& $options['wp_leads_press_random_members_under_noone'] == 'on' ) {
		
		$ref_user_data = new stdClass();
		$ref_user_data->user_email = $blogEmail;
		$ref_user_data->first_name = 'No';
		$ref_user_data->last_name = 'Referrer';
				
	}	
	
	//Get user data for new member
	
	if( $ref_user_id == 0 ){
		
		// Get default referrer value for campaign
		$campaign_default_referrer_ID = get_the_terms( $campaign_id, 'wplp_campaign_default_referrer_id' );
		
		if( isset( $campaign_default_referrer_ID ) && !empty( $campaign_default_referrer_ID ) ){
				
			$defaultUser = $campaign_default_referrer_ID;
		
		} elseif( isset($options['wp_leads_press_default_ancestor'] ) && !empty( $options['wp_leads_press_default_ancestor'] ) ){
			
			$defaultUser = $options['wp_leads_press_default_ancestor'];
			
		} else {
			
			$defaultUser = 1;	
			
		}		
		
		$ref_user_id = $defaultUser;	
		
	}
	
	$member_data = get_userdata( $ref_user_id );
		
	# Start Email
	$campaign_company = get_the_terms( $campaign_id, 'wplp_opportunity' );

	//$referrer = get_post_meta ( $lead_id, 'wplp_referrer_id', true );		
	$lead_first_name = get_post_meta ( $lead_id, 'wplp_lead_first_name', true );
	$lead_last_name = get_post_meta ( $lead_id, 'wplp_lead_last_name', true );
	$lead_email = get_post_meta ( $lead_id, 'wplp_lead_email', true );
	$lead_phone = get_post_meta ( $lead_id, 'wplp_lead_phone', true ); 
		
	
	$campaign_url = get_post_meta ( $campaign_id, 'wplp_campaign_url', true );
	
	$campaign_url_trailing_value = get_post_meta ( $campaign_id, 'wplp_campaign_url_trailing_value', true );
									
	$campaign_is_subdomain = get_post_meta( $campaign_id, 'wplp_campaign_is_subdomain', true );
	
	$campaign_is_https = get_post_meta( $campaign_id, 'wplp_campaign_is_https', true );					
	
	if( isset( $campaign_company ) && !empty( $campaign_company ) ) {
		
		foreach( $campaign_company as $company ) {
			// Get the ref user tracking ID for opportunity
			$slug = $company->slug;
			$key = 'wplp_tracking_id_' . $slug;
			$wplp_ref_tracking_id = get_user_meta($ref_user_id, $key, true); 								
			
			// Get Opp name
			$oppName = $company->name;	
		
		}
		
	} else {
		
		$wplp_ref_tracking_id = '';
		$oppName = __( 'Not Applicable', 'wp-leads-press' );	
		
	}// End if campaign_company isset	
	
		// Get url format
		if ( $campaign_is_https == 'yes' ) {
			
			$preUrl = 'https://';	
			
		} else {
			
			$preUrl = 'http://';
			
		}		
		
		// Create the destination URL of the referring user
		if ( $campaign_is_subdomain == 'yes' ){
		
			$wplp_redirect_url = $preUrl . $wplp_ref_tracking_id . '.' . $campaign_url;
			
		} else {
			
			$wplp_redirect_url = $preUrl . $campaign_url . $wplp_ref_tracking_id . $campaign_url_trailing_value;
			
		}	
		
		$campaign_url = $wplp_redirect_url;
	
	// Get the landing page info
	$landing_page = get_permalink($landing_page_id);
	
	
	// Get ref user's first name
	if ( isset( $ref_user_data->first_name ) && !empty( $ref_user_data->first_name ) ) {
		
		$ref_member_first_name = $ref_user_data->first_name;		
		
	} elseif( isset( $member_data->nickname ) && !empty( $member_data->nickname ) ) {
		
		$ref_member_first_name = $member_data->nickname;	
		
	} else {
		
		$ref_member_first_name = NULL;	
		
	}
	
	// Get ref user's last name
	if ( isset( $ref_user_data->last_name ) && !empty( $ref_user_data->last_name ) ) {
		
		$ref_member_last_name = $ref_user_data->last_name;
		
	} else {
		
		$ref_member_last_name = '';
		
	}
	
	// Get member first name
	if ( isset( $member_data->first_name ) && !empty( $member_data->first_name ) ) {
		
		$member_first_name = $member_data->first_name;	
		
	} elseif( isset( $member_data->nickname ) && !empty( $member_data->nickname ) ) {
		
		$member_first_name = $member_data->nickname;
		
	} else {
		
		$member_first_name = NULL;	
		
	}
	
	// Get member email
	if( isset( $member_data->user_email ) && !empty( $member_data->user_email ) ){
		
		$member_email = $member_data->user_email;
		
	} else {
		
		$member_email = NULL;	
		
	}
	
	// Get referring member user_nicename
	if( isset( $member_data->user_nicename ) && !empty( $member_data->user_nicename ) ){
		
		$ref_member_nicename = $member_data->user_nicename;
		
	} else {
		
		$ref_member_nicename = NULL;	
		
	}	
	
	
	
	//Get Unsubscribe Link
	$unsubscribeLink = '<a href=\"' . $blogUrl . '?unsub='. $lead_email . '\">Unsubscribe</a>';	
	 
	# String to search and replace
	//$subject; //This is a passed in variable
	
	# Patterns to replace
	$patterns[0] = __( '{REF-MEMBER-FIRST-NAME}', 'wp-leads-press' );
	$patterns[1] = __( '{SITE-NAME}', 'wp-leads-press' );
	$patterns[2] = __( '{SITE-URL}', 'wp-leads-press' );
	$patterns[3] = __( '{COMPANY-NAME}', 'wp-leads-press' );
	$patterns[4] = __( '{CAMPAIGN-DEST-URL}', 'wp-leads-press' );
	$patterns[5] = __( '{LANDING-PAGE}', 'wp-leads-press' );
	$patterns[6] = __( '{LEAD-FIRST-NAME}', 'wp-leads-press' );
	$patterns[7] = __( '{REF-MEMBER-EMAIL}', 'wp-leads-press' );
	$patterns[8] = __( '{REF-MEMBER-LAST-NAME}', 'wp-leads-press' );
	$patterns[9] = __( '{LEAD-LAST-NAME}', 'wp-leads-press' );
	$patterns[10] = __( '{MEMBER-FIRST-NAME}', 'wp-leads-press' );
	$patterns[11] = __( '{UNSUBSCRIBE}', 'wp-leads-press' );	
	$patterns[12] = __( '{LEAD-EMAIL}', 'wp-leads-press' );	
	$patterns[13] = __( '{MEMBER-EMAIL}', 'wp-leads-press' );
	$patterns[14] = __( '{LEAD-PHONE}', 'wp-leads-press' );		
	$patterns[15] = __( '{REF-MEMBER-CODE}', 'wp-leads-press' );
	
	# Replacement values
	$replacements[0] = $ref_member_first_name;
	$replacements[1] = $blogname;
	$replacements[2] = $blogUrl.'?ref='.$ref_member_nicename;
	$replacements[3] = $oppName;
	$replacements[4] = $campaign_url;
	$replacements[5] = $landing_page.'?ref='.$ref_member_nicename;
	$replacements[6] = $lead_first_name;
	$replacements[7] = $ref_user_data->user_email;
	$replacements[8] = $ref_member_last_name;
	$replacements[9] = $lead_last_name;
	$replacements[10] = $member_first_name;
	$replacements[11] = $unsubscribeLink;	
	$replacements[12] = $lead_email;
	$replacements[13] = $member_email;
	$replacements[14] = $lead_phone;
	$replacements[15] = '?ref='.$ref_member_nicename;
	
	# Properly order $patterns and $replacements
	ksort($patterns);
	ksort($replacements);
	# Get the string with replacements
	$content = str_replace($patterns, $replacements, $subject);
	$content = stripslashes($content);
	# Decode spec characters to get proper html	
	$content = htmlspecialchars_decode($content);
	# reset the variable string
	$subject = $content;
	
	# String to search and replace
	//$message; // This is a passed in variable
	
	# Patterns to replace
	$patterns[0] = __( '{REF-MEMBER-FIRST-NAME}', 'wp-leads-press' );
	$patterns[1] = __( '{SITE-NAME}', 'wp-leads-press' );
	$patterns[2] = __( '{SITE-URL}', 'wp-leads-press' );
	$patterns[3] = __( '{COMPANY-NAME}', 'wp-leads-press' );
	$patterns[4] = __( '{CAMPAIGN-DEST-URL}', 'wp-leads-press' );
	$patterns[5] = __( '{LANDING-PAGE}', 'wp-leads-press' );
	$patterns[6] = __( '{LEAD-FIRST-NAME}', 'wp-leads-press' );
	$patterns[7] = __( '{REF-MEMBER-EMAIL}', 'wp-leads-press' );
	$patterns[8] = __( '{REF-MEMBER-LAST-NAME}', 'wp-leads-press' );
	$patterns[9] = __( '{LEAD-LAST-NAME}', 'wp-leads-press' );
	$patterns[10] = __( '{MEMBER-FIRST-NAME}', 'wp-leads-press' );
	$patterns[11] = __( '{UNSUBSCRIBE}', 'wp-leads-press' );
	$patterns[12] = __( '{LEAD-EMAIL}', 'wp-leads-press' );	
	$patterns[13] = __( '{MEMBER-EMAIL}', 'wp-leads-press' );
	$patterns[14] = __( '{LEAD-PHONE}', 'wp-leads-press' );		
	$patterns[15] = __( '{REF-MEMBER-CODE}', 'wp-leads-press' );
	
	# Replacement values
	$replacements[0] = $ref_member_first_name;
	$replacements[1] = $blogname;
	$replacements[2] = $blogUrl.'?ref='.$ref_member_nicename;
	$replacements[3] = $oppName;
	$replacements[4] = $campaign_url;
	$replacements[5] = $landing_page.'?ref='.$ref_member_nicename;
	$replacements[6] = $lead_first_name;
	$replacements[7] = $ref_user_data->user_email;
	$replacements[8] = $ref_member_last_name;
	$replacements[9] = $lead_last_name;
	$replacements[10] = $member_first_name;
	$replacements[11] = $unsubscribeLink;	
	$replacements[12] = $lead_email;
	$replacements[13] = $member_email;
	$replacements[14] = $lead_phone;
	$replacements[15] = '?ref='.$ref_member_nicename;
		
	# Properly order $patterns and $replacements
	ksort($patterns);
	ksort($replacements);	
	# Get the string with replacements
	$content = str_replace($patterns, $replacements, $message);
	$content = stripslashes($content);
	# Decode spec characters to get proper html
	$content = htmlspecialchars_decode($content);
	# Add line breaks
	$content = nl2br($content);	
	# reset the variable string
	$message = $content;
	//require_once( ABSPATH . 'wp-admin/includes/plugin.php');
	# Send notification email
	//SMTP needs accurate times, and the PHP time zone MUST be set
	//This should be done in your php.ini, but this is how to do it if you don't have access to that
	date_default_timezone_set('Etc/UTC');
	
	//Create a new PHPMailer instance
	$mail = new PHPMailer();
	
	//Set Character encoding
	$mail->CharSet  = 'UTF-8'; // the same as 'utf-8'	
		
	if ( $options['wp_leads_press_use_smtp'] == 'on' ) {
		
		//Tell PHPMailer to use SMTP
		$mail->IsSMTP();
	
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		//$mail->SMTPDebug  = $options['wp_leads_press_smtp_debug_setting'];
		$mail->SMTPDebug  = 0;
		
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		
		//Set the hostname of the mail server
		$mail->Host = $options['wp_leads_press_smtp_hostname'];
		
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = $options['wp_leads_press_smtp_port_number'];
		
		if ( $options['wp_leads_press_smtp_auth_required'] == 'on' ) {
			
			//Whether to use SMTP authentication
			$mail->SMTPAuth = true;
			
			//Username to use for SMTP authentication
			$mail->Username = $options['wp_leads_press_smtp_username'];
			
			//Password to use for SMTP authentication
			$mail->Password = $options['wp_leads_press_smtp_password'];
			
				//Encryption type
				if ( $options['wp_leads_press_smtp_encryption_type'] != 'none' ) {
					
					$mail->SMTPSecure = $options['wp_leads_press_smtp_encryption_type'];
				
				}
		
		}
	
	}
	
	//Set who the message is to be sent from
	//$mail->SetFrom('from@example.com', 'First Last');
	$mail->SetFrom( $from_email, $from_name);
	
	//Set an alternative reply-to address
	$reply = $options['wp_leads_press_smtp_reply_email'];
	$mail->AddReplyTo($reply, $from_name);
	
	//Set who the message is to be sent to
	//$mail->AddAddress('whoto@example.com', 'John Doe');
	$mail->AddAddress( $to_email );
	
	//Set the subject line
	$mail->Subject = $subject;
	
	//Read an HTML message body, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
	//$mail->MsgHTML(file_get_contents('contents.html'), dirname(__FILE__));
	$mail->MsgHTML( $message );
	
	//Replace the plain text body with one created manually
	//$mail->AltBody = 'This is a plain-text message body';
	
	$mail->WordWrap = 70;// Set word wrap to X characters
	
	//Attach an image file
	//$mail->AddAttachment('images/phpmailer-mini.gif');
	
	// Do check here for members who are opting out of recieving mail
	// Check for lead notices, member notices, then send.
	
	$mail->Send();	  		
}
?>