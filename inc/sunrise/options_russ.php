<?php

	$options_dashboard = array(

		###
		# 	Tab START
		###
		
		array(
			'name' => __( 'WP Leads Press Dashboard', 'wp-leads-press' ),
			'type' => 'opentab',
			
		),
				
		
		array(
			'name' => __( 'Coming Soon!', 'wp-leads-press' ),
			'type' => 'title'
		),
		
		array(
			'content' => '<p>Shortly we will be adding lead data for tracking your most successful landing pages and campaign combinations, showing traffic, click throughs and lead to member conversions.',
			'type' => 'html'
		),				
		
		array(
			'type' => 'closetab',
			'actions' => false
		),
		
		###
		#	Tab END
		###
		
	);				


	// Get response from API				
	$result = wplp_status_check();
	
	//print_r($result);
	//echo '<br />';
	//echo $result["status_check"].'status check one';
	//echo $result->status_check;
			
//	if( $result["status_check"] == "active") {

	$aweber_auth = get_option( 'wplp_aweber_auth' );

	//update_option('wplp_aweber_auth', $auth); 
	
	$awConnected = 'Aweber is connected!';
	$awEnterAuthCode = 'Enter Authorization Code';
		
	if( ( isset( $aweber_auth ) && $aweber_auth != FALSE ) && $aweber_auth != $awConnected && $aweber_auth != $awEnterAuthCode ) {
		
		$aweber_auth = __( 'Aweber is connected!', 'wp-leads-press' );							
		
	} else {
		
		$aweber_auth = __( 'Enter Authorization Code', 'wp-leads-press' );							
		
	}
	

	$options = wp_load_alloptions();


	if( isset( $options['wp_leads_press_aweber_app_id'] ) && !empty( $options['wp_leads_press_aweber_app_id'] ) ) {
		
		$app_id = $options['wp_leads_press_aweber_app_id'];
	
	} else {
		
		$app_id = 'd8fbd769';	
		
	}
		
	$app_id_active = '<a href="https://auth.aweber.com/1.0/oauth/authorize_app/'.$app_id.'" target="_blank" >'.__( 'Click Here', 'wp-leads-press' ).'</a>';
									
	/** Plugin Options Panel*/
	$options_settings = array(
		
		###
		# 	Tab START
		###
		
		array(
			'name' => __( 'Lead Settings', 'wp-leads-press' ),
			'type' => 'opentab',
			
		),	
				
		array(
			'name' => __( 'Starting Bonus Leads', 'wp-leads-press' ),
			'type' => 'title'
		),		
		
		array(
			'name' => __( 'Starting Bonus Leads', 'wp-leads-press' ),
			'desc' => __( 'Max number of random \'Bonus\' leads a user can receive without generating personal leads. (\'-1\' = UNLIMITED \'0\' = NONE!)', 'wp-leads-press' ),
			'std' => 5, // Default Value
			'min' => -1,
			'max' => 1000000,
			'units' => __( 'Starting Leads', 'wp-leads-press' ),
			'id' => 'max_random_leads_allowed',
			'type' => 'number'
		),
		
		array(
			'name' => __( 'Select ID\'s To Receive Random Leads?', 'wp-leads-press' ),
			'desc' => __( 'If checked only the user ID\'s entered below will be eligible to receive random leads. (Optional, leave unchecked to distribute leads/visitors randomly to all members. ', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'select_user_ids',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),		

		array(
			'name' => __( 'Enter User ID\'s', 'wp-leads-press' ),
			'desc' => __( 'Enter each ID separated by a comma, example, \'1,5,7,453\'', 'wp-leads-press' ),
			'std' => 5, // Default Value
			'id' => 'ids_for_random_traffic',
			'type' => 'text'
		),
				
		array(
			'name' => __( 'Personally Referred Leads', 'wp-leads-press' ),
			'type' => 'title'
		),		
			
		array(
			'name' => __( 'Personal Leads Required', 'wp-leads-press' ),
			'desc' => __( 'How many personally referred leads are required before bonus leads earned? (\'0\' = OFF)', 'wp-leads-press' ),
			'std' => 5, // Default Value
			'min' => 0,
			'max' => 1000000,
			'units' => __( 'leads required', 'wp-leads-press' ),
			'id' => 'personally_referred_leads_required',
			'type' => 'number'
		),	
		array(
			'name' => __( 'Bonus Random Leads', 'wp-leads-press' ),
			'desc' => __( 'Give your users bonus random leads each time they refer the value set in \'Personal Leads Required\' setting above. (\'0\' = OFF)', 'wp-leads-press' ),
			'std' => 0, // Default Value
			'min' => 0,
			'max' => 1000000,
			'units' => __( 'Bonus Leads', 'wp-leads-press' ),
			'id' => 'ref_lead_bonus_leads_value',
			'type' => 'number'
		),	
		
		array(
			'name' => __( 'Personally Referred Members', 'wp-leads-press' ),
			'type' => 'title'
		),													
		
		array(
			'name' => __( 'Personal Members Required', 'wp-leads-press' ),
			'desc' => __( 'How many personally referred members are required before bonus leads earned? (\'0\' = OFF)', 'wp-leads-press' ),
			'std' => 5, // Default Value
			'min' => 0,
			'max' => 1000000,
			'units' => __( 'Members required', 'wp-leads-press' ),
			'id' => 'ref_member_count_required',
			'type' => 'number'
		),
		array(
			'name' => __( 'Bonus Random Leads', 'wp-leads-press' ),
			'desc' => __( 'Give your users bonus random leads each time they refer the value set in \'Personal Members Required\' setting above. (\'0\' = OFF)', 'wp-leads-press' ),

			'std' => 0, // Default Value
			'min' => 0,
			'max' => 1000000,
			'units' => __( 'Bonus Leads', 'wp-leads-press' ),
			'id' => 'ref_member_bonus_leads_value',
			'type' => 'number'
		),
		array(
			'name' => __( 'Personally Referred Visits', 'wp-leads-press' ),
			'type' => 'title'
		),					
		array(
			'name' => __( 'Referred Visits Required', 'wp-leads-press' ),
			'desc' => __( 'How many personally referred visits to site are required before bonus leads earned? (\'0\' = OFF)', 'wp-leads-press' ),
			'std' => 5, // Default Value
			'min' => 0,
			'max' => 1000000,
			'units' => __( 'Visits required', 'wp-leads-press' ),
			'id' => 'ref_traffic_count_required',
			'type' => 'number'
		),		
		
		array(
			'name' => __( 'Bonus Random Leads', 'wp-leads-press' ),
			'desc' => __( 'Give your users bonus random leads each time they refer the value set in \'Referred Visits Required\' setting above. (\'0\' = OFF)', 'wp-leads-press' ),
			'std' => 0, // Default Value
			'min' => 0,
			'max' => 1000000,
			'units' => __( 'Bonus Leads', 'wp-leads-press' ),
			'id' => 'ref_traffic_bonus_leads_value',
			'type' => 'number'
		),
		
		array(
			'content' => '<hr>',
			'type' => 'html'
		),				
		
		array(
			'name' => __( 'Override Lead Distribution Rules', 'wp-leads-press' ),
			'type' => 'title'
		),

		array(
			'content' => __( '<p>If you are sending traffic to your site and would like to distribute it randomly to all site members who are active with a company or paid company campaign use "rotation" for the referral link: <br /><br />i.e. http://yoursite.com/landingpage/?rotation=on<br /><br />Leads and traffic generated from "rotation" traffic source will be randomly given out to your members, this is mainly used when you have a co-op campaign running for members and need to distribute leads to them without restrictions set by normal lead distribution rules set above.<br /><br /></p>', 'wp-leads-press' ),
			'type' => 'html'
		),					
		
		array(
			'content' => '<hr>',
			'type' => 'html'
		),													
				
		array(
			'type' => 'closetab',
			'actions' => true
		),
		
		###
		#	Tab END
		###
		###
		#	Tab START
		###
		array(
			'name' => __( 'System Email Settings', 'wp-leads-press' ),
			'type' => 'opentab'
		),
		
		array(
			'name' => __( 'System Email Settings', 'wp-leads-press' ),
			'type' => 'title'
		),
		
		array(
			'name' => __( 'Use SMTP For Sending Outgoing System Emails?', 'wp-leads-press' ),
			'desc' => __( 'If checked SMTP will be used to send emails. (Optional, however if you have a large amount of emails going out using SMTP will improve mailing performance.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'use_smtp',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),	
		
//		array(
//			'name' => __( 'SMTP Debug Output Setting', 'wp-leads-press' ),
//			'desc' => __( '0 = off (for production use) 1 = client messages 2 = client and server messages', 'wp-leads-press' ),
//			'std' => 0, // Default Value
//			'min' => 0,
//			'max' => 2,
//			'units' => __( 'Setting', 'wp-leads-press' ),
//			'id' => 'smtp_debug_setting',
//			'type' => 'number'
//		),	
				
		array(
			'name' => __( 'Hostname', 'wp-leads-press' ),
			'desc' => __( 'Hostname of email server, i.e. "smtp.example.com", "mail.example.com" (Optional: Specify main and backup SMTP server: "smtp1.example.com;smtp2.example.com;" )', 'wp-leads-press' ),
			'std' => __( 'smtp.example.com', 'wp-leads-press' ), // Default Value
			'id' => 'smtp_hostname',
			'type' => 'text'
		),		

		array(
			'name' => __( 'From Email', 'wp-leads-press' ),
			'desc' => __( 'Email to send notices from', 'wp-leads-press' ),
			'std' => __( 'somemail@example.com', 'wp-leads-press' ), // Default Value
			'id' => 'smtp_from_email',
			'type' => 'text'
		),																		

		array(
			'name' => __( 'Return Email', 'wp-leads-press' ),
			'desc' => __( 'Email address for replies', 'wp-leads-press' ),
			'std' => __( 'somemail@example.com', 'wp-leads-press' ), // Default Value
			'id' => 'smtp_reply_email',
			'type' => 'text'
		),	
				
		array(
			'name' => __( 'From Name', 'wp-leads-press' ),
			'desc' => __( 'The name you want to show email being from.', 'wp-leads-press' ),
			'std' => __( 'Your Site, Admin, Ect..', 'wp-leads-press' ), // Default Value
			'id' => 'smtp_from_name',
			'type' => 'text'
		),		
				
		array(
			'name' => __( 'SMTP Port Number', 'wp-leads-press' ),
			'desc' => __( 'Set the SMTP port number - likely to be 25, 465 or 587, check with your host for specific settings.', 'wp-leads-press' ),
			'std' => 25, // Default Value
			'min' => 0,
			'max' => 1000000,
			'units' => __( 'Port Number', 'wp-leads-press' ),
			'id' => 'smtp_port_number',
			'type' => 'number'
		),			
		
		
		array(
			'name' => __( 'SMTP Authentication?', 'wp-leads-press' ),
			'desc' => __( 'If checked SMTP requires authentication and you must fill out username and password below.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'smtp_auth_required',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),	
		
		
		array(
			'name' => __( 'SMTP Username', 'wp-leads-press' ),
			'desc' => __( 'Username to use for SMTP authentication, i.e. yourname@example.com', 'wp-leads-press' ),
			'std' => __( 'yourname@example.com', 'wp-leads-press' ), // Default Value
			'id' => 'smtp_username',
			'type' => 'text'
		),		
		
		
		array(
			'name' => __( 'SMTP Password', 'wp-leads-press' ),
			'desc' => __( 'Password to use for SMTP authentication', 'wp-leads-press' ),
			'std' => __( 'PASSWORD', 'wp-leads-press' ), // Default Value
			'id' => 'smtp_password',
			'type' => 'text'
		),
		
		// Select (dropdown list)
		array(
			'id'      => 'smtp_encryption_type',
			'type'    => 'select',
			'default' => 'tls',
			'name' => __( 'SMTP Encryption Type', 'wp-leads-press' ),
			'desc' => __( 'Encryption type, "tls" or "ssl" accepted', 'wp-leads-press' ),
			'options' => array(
				array(
					'value' => 'none',
					'label'  => __( 'none', 'wp-leads-press' )
				),
				array(
					'value' => 'tls',
					'label'  => __( 'tls', 'wp-leads-press' )
				),
				array(
					'value' => 'ssl',
					'label'  => __( 'ssl', 'wp-leads-press' )
				)
			)
		),
				
		array(
			'content' => '<hr>',
			'type' => 'html'
		),	

		array(
			'type' => 'closetab',
			'actions' => true
		),
		###
		#	Tab END
		###	
		###
		#	Tab START
		###
		array(
			'name' => __( 'System Emails', 'wp-leads-press' ),
			'type' => 'opentab'
		),	
		array(
			'name' => __( 'System Emails', 'wp-leads-press' ),
			'type' => 'title'
		),	
		
		###
		# New Lead Email
		###									
		array(
			'name' => __( 'New Lead Email', 'wp-leads-press' ),
			'type' => 'title'
		),
		array(
			'name' => __( 'Send New Lead Email?', 'wp-leads-press' ),
			'desc' => __( 'If checked new lead email will be sent.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'send_new_lead_email',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),						
		array(
			'name' => __( 'Subject', 'wp-leads-press' ),
			'desc' => __( 'Subject of email sent to new opt-in lead', 'wp-leads-press' ),
			'std' => __( '{LEAD-FIRST-NAME}, Thank you subscribing to learn more about {COMPANY-NAME}.', 'wp-leads-press' ), // Default Value
			'id' => 'new_lead_subject',
			'type' => 'text'
		),
	
		array(
			
			'name' => __( 'Body', 'wp-leads-press' ),
			
			'desc' => __( 'Body of email, HTML is OK!', 'wp-leads-press' ),
			
			'std' => __( '{LEAD-FIRST-NAME},
			
			Thank you for opting in to learn more about {COMPANY-NAME}, join with your referrer, <a href="{CAMPAIGN-DEST-URL}">click here</a>.
			
			Visit our <a href="{SITE-URL}">team website</a> to learn more about our organization and how you can partner with us to get access to our viral team building system at no cost to you, just by joining with our team!
			
			{UNSUBSCRIBE}', 'wp-leads-press' ),
			
			'id' => 'new_lead_body',
			
			'type' => 'textarea',
			
			'rows' => 15
			
		),
		array(
			'name' => __( 'Replacement Codes', 'wp-leads-press' ),
			'type' => 'title'
		),		
		array(
			'content' => __( '<strong>{REF-MEMBER-FIRST-NAME}</strong> : Returns the Referring Member\'s First Name.<br />
						<strong>{REF-MEMBER-LAST-NAME}</strong> : Returns the Referring Member\'s Last Name.<br />
						<strong>{REF-MEMBER-EMAIL}</strong> : Returns the Referring Member\'s email address.<br />
						<strong>{REF-MEMBER-CODE}</strong> : Returns \'?ref=username\' with \'username\' = the ref member username,  <br />												
						<strong>{LEAD-FIRST-NAME}</strong> : Returns the lead\'s First Name.<br />
						<strong>{LEAD-LAST-NAME}</strong> : Returns the lead\'s Last Name.<br />
						<strong>{LEAD-EMAIL}</strong> : Returns the lead email address used to opt-in.<br />
						<strong>{LEAD-PHONE}</strong> : Returns the lead phone number used to opt-in, if provided.<br />
						<strong>{SITE-NAME}</strong> : Returns the site name.<br />
						<strong>{SITE-URL}</strong> : Returns site url address where WPLP is installed.<br />
						<strong>{COMPANY-NAME}</strong> : Returns Company Name of campaign associated with landing page lead was generated on.<br />
						<strong>{CAMPAIGN-DEST-URL}</strong> : Returns Referring Member\'s destination URL of Campaign, if set in their Lead Dashboard.<br />
						<strong>{LANDING-PAGE}</strong> : Returns the landing page URL lead was generated on. <br />
						<strong>{UNSUBSCRIBE}</strong> : Returns an unsubscribe link.', 'wp-leads-press' ),
			'type' => 'html'
		),		
		array(
			'content' => '<hr>',
			'type' => 'html'
		),
		
		###
		# Member New Lead Email
		###	
		array(
			'name' => __( 'Member New Lead Email', 'wp-leads-press' ),
			'type' => 'title'
		),	
		
		array(
			'name' => __( 'Send Member New Lead Email?', 'wp-leads-press' ),
			'desc' => __( 'If checked member new lead email will be sent.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'send_member_new_lead_email',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),					
		array(
			'name' => __( 'Subject', 'wp-leads-press' ),
			'desc' => __( 'Subject of email sent to Member as opt-in leads are generated', 'wp-leads-press' ),
			'std' => __( '{REF-MEMBER-FIRST-NAME}, you received a lead!', 'wp-leads-press' ), // Default Value
			'id' => 'member_new_lead_subject',
			'type' => 'text'
		),
	
		array(
			
			'name' => __( 'Body', 'wp-leads-press' ),
			
			'desc' => __( 'Body of email, HTML is OK!', 'wp-leads-press' ),
			
			'std' => __( '{REF-MEMBER-FIRST-NAME}, you just got a new lead at {SITE-NAME}, <a href="{SITE-URL}">login</a> and contact them now and get them started!', 'wp-leads-press' ),
			
			'id' => 'member_new_lead_body',
			
			'type' => 'textarea',
			
			'rows' => 15
			
		),
		array(
			'name' => __( 'Replacement Codes', 'wp-leads-press' ),
			'type' => 'title'
		),		
		array(
			'content' => __( '<strong>{REF-MEMBER-FIRST-NAME}</strong> : Returns the Referring Member\'s First Name.<br />
						<strong>{REF-MEMBER-LAST-NAME}</strong> : Returns the Referring Member\'s Last Name.<br />
						<strong>{REF-MEMBER-EMAIL}</strong> : Returns the Referring Member\'s email address.<br />
						<strong>{REF-MEMBER-CODE}</strong> : Returns \'?ref=username\' with \'username\' = the ref member username,  <br />												
						<strong>{LEAD-FIRST-NAME}</strong> : Returns the lead\'s First Name.<br />
						<strong>{LEAD-LAST-NAME}</strong> : Returns the lead\'s Last Name.<br />
						<strong>{LEAD-EMAIL}</strong> : Returns the lead email address used to opt-in.<br />
						<strong>{LEAD-PHONE}</strong> : Returns the lead phone number used to opt-in, if provided.<br />												
						<strong>{SITE-NAME}</strong> : Returns the site name.<br />
						<strong>{SITE-URL}</strong> : Returns site url address where WPLP is installed.<br />
						<strong>{COMPANY-NAME}</strong> : Returns Company Name of campaign associated with landing page lead was generated on.<br />
						<strong>{CAMPAIGN-DEST-URL}</strong> : Returns Referring Member\'s destination URL of Campaign, if set in their Lead Dashboard.<br />
						<strong>{LANDING-PAGE}</strong> : Returns the landing page URL lead was generated on.', 'wp-leads-press' ),
			'type' => 'html'
		),		
		array(
			'content' => '<hr>',
			'type' => 'html'
		),			
		
		###
		# new member welcome email
		###
		
		array(
			'name' => __( 'New Member Welcome Email', 'wp-leads-press' ),
			'type' => 'title'
		),
		array(
			'name' => __( 'Send New Member Welcome Email?', 'wp-leads-press' ),
			'desc' => __( 'If checked new member welcome email will be sent.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'send_new_member_welcome_email',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),						
		array(
			'name' => __( 'Subject', 'wp-leads-press' ),
			'desc' => __( 'Subject of email sent to new member', 'wp-leads-press' ),
			'std' => __( '{MEMBER-FIRST-NAME}, welcome to the team!', 'wp-leads-press' ), // Default Value
			'id' => 'welcome_new_member_subject',
			'type' => 'text'
		),
	
		array(
			
			'name' => __( 'Body', 'wp-leads-press' ),
			
			'desc' => __( 'Body of email, HTML is OK!', 'wp-leads-press' ),
			
			'std' => __( '{MEMBER-FIRST-NAME},
			
			Welcome to the team! 
			
			If you have any questions don\'t hesitate to contact your referrer, {REF-MEMBER-EMAIL} for help!', 'wp-leads-press' ),
			
			'id' => 'welcome_new_member_body',
			
			'type' => 'textarea',
			
			'rows' => 15
			
		),
		array(
			'name' => __( 'Replacement Codes', 'wp-leads-press' ),
			'type' => 'title'
		),		
		array(
			'content' => __( '<strong>{REF-MEMBER-FIRST-NAME}</strong> : Returns the Referring Member\'s First Name.<br />
						<strong>{REF-MEMBER-LAST-NAME}</strong> : Returns the Referring Member\'s Last Name.<br />
						<strong>{REF-MEMBER-EMAIL}</strong> : Returns the Referring Member\'s email address.<br />
						<strong>{REF-MEMBER-CODE}</strong> : Returns \'?ref=username\' with \'username\' = the ref member username,  <br />																		
						<strong>{MEMBER-FIRST-NAME}</strong> : Returns the Member\'s First Name.<br />						
						<strong>{SITE-NAME}</strong> : Returns the site name.<br />
						<strong>{SITE-URL}</strong> : Returns site url address where WPLP is installed.<br />
						<strong>{COMPANY-NAME}</strong> : Returns Company Name of campaign associated with landing page lead was generated on.', 'wp-leads-press' ),
			'type' => 'html'
		),		
		array(
			'content' => '<hr>',
			'type' => 'html'
		),															
		
		###
		# New Member Email - Sent to referrer
		###
		array(
			'name' => __( 'Lead Upgraded To Member Email', 'wp-leads-press' ),
			'type' => 'title'
		),
		array(
			'name' => __( 'Send Lead Upgraded To Member Email?', 'wp-leads-press' ),
			'desc' => __( 'If checked lead upgraded to member email will be sent.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'send_lead_upgrade_member_email',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),						
		array(
			'name' => __( 'Subject', 'wp-leads-press' ),
			'desc' => __( 'Subject of email sent to Member as leads upgrade to Member', 'wp-leads-press' ),
			'std' => __( '{REF-MEMBER-FIRST-NAME}, you just had a lead upgrade to member!', 'wp-leads-press' ), // Default Value
			'id' => 'new_lead_to_member_subject',
			'type' => 'text'
		),
	
		array(
			
			'name' => __( 'Body', 'wp-leads-press' ),
			
			'desc' => __( 'Body of email, HTML is OK!', 'wp-leads-press' ),
			
			'std' => __( '{REF-MEMBER-FIRST-NAME},
			
			You just got a new member at {SITE-NAME}, contact them now and get them started!', 'wp-leads-press' ),
			
			'id' => 'new_lead_to_member_body',
			
			'type' => 'textarea',
			
			'rows' => 15
			
		),
		array(
			'name' => __( 'Replacement Codes', 'wp-leads-press' ),
			'type' => 'title'
		),		
		array(
			'content' => __( '<strong>{REF-MEMBER-FIRST-NAME}</strong> : Returns the Referring Member\'s First Name.<br />
						<strong>{REF-MEMBER-LAST-NAME}</strong> : Returns the Referring Member\'s Last Name.<br />
						<strong>{REF-MEMBER-EMAIL}</strong> : Returns the Referring Member\'s email address.<br />
						<strong>{REF-MEMBER-CODE}</strong> : Returns \'?ref=username\' with \'username\' = the ref member username,  <br />																		
						<strong>{MEMBER-FIRST-NAME}</strong> : Returns the new Member\'s First Name.<br />
						<strong>{MEMBER-EMAIL}</strong> : Returns the new Member\'s Email. <br />
						<strong>{LEAD-PHONE}</strong> : Returns the lead phone number used to opt-in, if provided.<br />						
						<strong>{SITE-NAME}</strong> : Returns the site name.<br />
						<strong>{SITE-URL}</strong> : Returns site url address where WPLP is installed.<br />
						<strong>{COMPANY-NAME}</strong> : Returns Company Name of campaign associated with landing page lead was generated on.', 'wp-leads-press' ),
			'type' => 'html'
		),		
		array(
			'content' => '<hr>',
			'type' => 'html'
		),			
		
		###
		# Update affiliate ID
		###
		
		array(
			'name' => __( 'Set Your Affiliate ID Email', 'wp-leads-press' ),
			'type' => 'title'
		),
		array(
			'name' => __( 'Send set your affiliate ID email?', 'wp-leads-press' ),
			'desc' => __( 'If checked set your affiliate ID email will be sent.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'send_set_tracking_id_email',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),						
		array(
			'name' => __( 'Subject', 'wp-leads-press' ),
			'desc' => __( 'Subject of email sent to Member letting them know they missed a lead because their Affiliate ID is not set in the Lead Dashboard.', 'wp-leads-press' ),
			'std' => __( '{REF-MEMBER-FIRST-NAME}, you just missed a lead!', 'wp-leads-press' ), // Default Value
			'id' => 'set_tracking_id_subject',
			'type' => 'text'
		),
	
		array(
			
			'name' => __( 'Body', 'wp-leads-press' ),
			
			'desc' => __( 'Body of email, HTML is OK!', 'wp-leads-press' ),
			
			'std' => __( '{REF-MEMBER-FIRST-NAME},
			
			You would have just received a lead from {SITE-NAME}, you just need to update your Affiliate ID in your Lead Dashboard at {SITE-URL}. Then visit the Lead Dashboard to update your Affiliate Settings with your ID for {COMPANY-NAME} and you will then be eligible to receive leads.
			
			Here is your referrer\'s signup link, {CAMPAIGN-DEST-URL}, use this to join the {COMPANY-NAME} opportunity.
			
			This is the page you opted in to look at the opportunity, {LANDING-PAGE}', 'wp-leads-press' ),
			
			'id' => 'set_tracking_id_body',
			
			'type' => 'textarea',
			
			'rows' => 15
			
		),
		
		array(
			'name' => __( 'Replacement Codes', 'wp-leads-press' ),
			'type' => 'title'
		),		
		
		array(
			'content' => __( '<strong>{REF-MEMBER-FIRST-NAME}</strong> : Returns the Referring Member\'s First Name.<br />
						<strong>{REF-MEMBER-LAST-NAME}</strong> : Returns the Referring Member\'s Last Name.<br />
						<strong>{REF-MEMBER-EMAIL}</strong> : Returns the Referring Member\'s email address.<br />
						<strong>{REF-MEMBER-CODE}</strong> : Returns \'?ref=username\' with \'username\' = the ref member username,  <br />																		
						<strong>{LEAD-FIRST-NAME}</strong> : Returns the lead\'s First Name.<br />
						<strong>{LEAD-LAST-NAME}</strong> : Returns the lead\'s Last Name.<br />
						<strong>{LEAD-PHONE}</strong> : Returns the lead phone number used to opt-in, if provided.<br />						
						<strong>{SITE-NAME}</strong> : Returns the site name.<br />
						<strong>{SITE-URL}</strong> : Returns site url address where WPLP is installed.<br />
						<strong>{COMPANY-NAME}</strong> : Returns Company Name of campaign associated with landing page lead was generated on.<br />
						<strong>{CAMPAIGN-DEST-URL}</strong> : Returns Referring Member\'s destination URL of Campaign, if set in their Lead Dashboard.<br />
						<strong>{LANDING-PAGE}</strong> : Returns the landing page URL lead was generated on.', 'wp-leads-press' ),
			'type' => 'html'
		),	
		
						
		array(
			'content' => '<hr>',
			'type' => 'html'
		),																													
		
		array(
			'type' => 'closetab',
			'actions' => true
		),
		###
		#	Tab END
		###				
		
//		###
//		#	Tab START
//		###
//		array(
//			'name' => __( 'PowerPress Email Settings', 'wp-leads-press' ),
//			'type' => 'opentab'
//		),
//		array(
//			'name' => __( 'PowerPress Email Settings', 'wp-leads-press' ),
//			'type' => 'title'
//		),
//		array(
//			'content' => '<p>PowerPress Emails will be released soon!<br /><br /> Then you will be able to send emails to the entire lead database when new leads are generated and members join your site, with more settings to follow. These emails will help to create viral signups for the affiliate programs you are involved with as they create "Fear of loss" and help motivate your leads to become members.<br /><br />For example, you set an email to go out to all of your leads when 200 leads have been generated on your site to let them know how many leads have been generated since the last email you sent out, which BTW... some of which could have been theirs. Or an email to go out every time  a lead upgrades to member status on your website, both promoting the fact your lead system is working to generate sales/signups for your members.</p>',
//			'type' => 'html'
//		),
//		array(
//			'type' => 'closetab',
//			'actions' => true
//		),
//		###
//		#	Tab END
//		###				
//		
//		###
//		#	Tab START
//		###
//		array(
//			'name' => __( 'PowerPress Emails', 'wp-leads-press' ),
//			'type' => 'opentab'
//		),	
//		array(
//			'name' => __( 'PowerPress Emails', 'wp-leads-press' ),
//			'type' => 'title'
//		),
//		array(
//			'content' => '<p>PowerPress Emails will be released soon!<br /><br /> Then you will be able to send emails to the entire lead database when new leads are generated and members join your site, with more settings to follow. These emails will help to create viral signups for the affiliate programs you are involved with as they create "Fear of loss" and help motivate your leads to become members.<br /><br />For example, you set an email to go out to all of your leads when 200 leads have been generated on your site to let them know how many leads have been generated since the last email you sent out, which BTW... some of which could have been theirs. Or an email to go out every time  a lead upgrades to member status on your website, both promoting the fact your lead system is working to generate sales/signups for your members.</p>',
//			'type' => 'html'
//		),						
////		array(
////			'name' => __( 'Send PowerPress Emails?', 'wp-leads-press' ),
////			'desc' => __( 'If checked PowerPress emails will be sent.', 'wp-leads-press' ),
////			'std' => 'off', // Default Value
////			'id' => 'wplp_send_powerpress_email',
////			'type' => 'checkbox',
////			'label' => __( 'Yes', 'wp-leads-press' )
////		),
////		array(
////			'content' => __('PowerPress emails are sent as new members join and new leads are generated on your site based on the settings you choose.', 'wp-leads-press' ),
////			'type' => 'html'
////		),
////		array(
////			'name' => __( 'Email Sent To All LEADS After a New Member Registers on site', 'wp-leads-press' ),
////			'type' => 'title'
////		),
////		array(
////			'content' => __('Use this email to let them know a new member just joined who might have been on their team, had they taken action and joined before they did.', 'wp-leads-press' ),
////			'type' => 'html'
////		),		
////							
////		array(
////			'name' => __( 'Subject', 'wp-leads-press' ),
////			'desc' => __( 'Subject of email', 'wp-leads-press' ),
////			'std' => 'Enter Subject', // Default Value
////			'id' => 'wplp_powerpress_all_leads_subject_new_member',
////			'type' => 'text'
////		),
////		array(
////			
////			'name' => __( 'Body', 'wp-leads-press' ),
////			
////			'desc' => __( 'Body of email, HTML is OK!', 'wp-leads-press' ),
////			
////			'std' => __( 'Hello,<br /><br />Another member just joined {SITE-NAME}, which could have been on your team had you taken action and joined before they did. Do not let this happen to you again!<br /><br />Find out why our team is growing so fast at, {SITE-URL}!', 'wp-leads-press' ),
////			
////			'id' => 'wplp_powerpress_all_leads_body_new_member',
////			
////			'type' => 'textarea',
////			
////			'rows' => 15
////			
////		),				
//		
//		array(
//			'type' => 'closetab',
//			'actions' => true
//		),
//		###
//		#	Tab END
//		###	


		
		###
		#	Tab START Autoresponder Integration
		###
		array(
			'name' => __( 'Autoresponder Integration', 'wp-leads-press' ),
			'type' => 'opentab'
		),
		
		// Mailster
		array(
			'name' => __( 'Mailster API Integration Settings', 'wp-leads-press' ),
			'type' => 'title'
		),
			
		array(
			'name' => __( 'Add Site Leads to Mailster Campaign?', 'wp-leads-press' ),
			'desc' => __( 'If checked ALL leads generated via WPLP will be added to a general list as set below. You can also add users to lists per campaign under campaign settings to segment your lists by company/campaign if promoting more than one opportunity. Use this List for site updates, talking about how your team building system works for example, then use segmented campaign lists to follow up per opportunity or product promoted by the individual campaign. Remember to only add leads to a list once, do not accidently duplicated by also trying to add from within a campaign directly.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'mailster_api',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),
	
		
		array(
			'name' => __( 'Mailster List ID', 'wp-leads-press' ),
			'desc' => __( 'Enter the List ID to add leads to in Mailster', 'wp-leads-press' ),
			'std' => 'Enter Mailster List ID', // Default Value
			'id' => 'mailster_campaign_name',
			'type' => 'text'
		),	
		
		/**********************  mailster end **************/


		// Get Response
		array(
			'name' => __( 'Get Response API Integration Settings', 'wp-leads-press' ),
			'type' => 'title'
		),
			
		array(
			'name' => __( 'Add Site Leads to Get Response Campaign?', 'wp-leads-press' ),
			'desc' => __( 'If checked ALL leads generated via WPLP will be added to a general list as set below. You can also add users to lists per campaign under campaign settings to segment your lists by company/campaign if promoting more than one opportunity. Use this List for site updates, talking about how your team building system works for example, then use segmented campaign lists to follow up per opportunity or product promoted by the individual campaign. Remember to only add leads to a list once, do not accidently duplicated by also trying to add from within a campaign directly.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'get_response_api',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),
	
		array(
			'name' => __( 'Get Response API Key', 'wp-leads-press' ),
			'desc' => __( 'Enter your Get Response API Key', 'wp-leads-press' ),
			'std' => 'Enter Get Response API Key', // Default Value
			'id' => 'get_response_key',
			'type' => 'text'
		),	
		
		array(
			'name' => __( 'Get Response Campaign Name', 'wp-leads-press' ),
			'desc' => __( 'Enter the campaign name to add leads to at Get Response', 'wp-leads-press' ),
			'std' => 'Enter Get Response Campaign Name', // Default Value
			'id' => 'get_response_campaign_name',
			'type' => 'text'
		),		
		
		
		// Aweber
		array(
			'name' => __( 'Aweber API Integration Settings', 'wp-leads-press' ),
			'type' => 'title'
		),


		array(
			'name' => __( 'Add Site Leads to Aweber Campaign?', 'wp-leads-press' ),
			'desc' => __( 'If checked ALL leads generated via WPLP will be added to Aweber as set below. You can also add users to lists per campaign under campaign settings to segment your lists by company/campaign if promoting more than one opportunity. Use this List for site updates, talking about how your team building system works for example, then use segmented campaign lists to follow up per opportunity or product promoted by the individual campaign. Remember that each list MUST have its own list ID, do not try to add leads to the same list twice, i.e. specifying a list ID here, then duplicating under a campaign setting, this will cause errors within the Aweber API.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'aweber_api',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),

		array(
			'name' => __( 'Custom Aweber APP ID', 'wp-leads-press' ),
			'desc' => __( '#OPTIONAL! Enter your Custom Aweber APP ID, if you have not created an Aweber APP yet, go to: <a href="http://labs.aweber.com">labs.aweber.com</a>, create a free account and an APP to handle AW API requests. OR you can simply use the built in WP Leads Press APP for Aweber and leave this field blank.', 'wp-leads-press' ),
			'std' => 'Enter Aweber APP ID', // Default Value
			'id' => 'aweber_app_id',
			'type' => 'text'
		),	
	
//		array(
//			'name' => __( 'Aweber Consumer Key', 'wp-leads-press' ),
//			'desc' => __( 'Enter your Aweber Consumer Key', 'wp-leads-press' ),
//			'std' => 'someconsumerkeyvalue', // Default Value
//			'id' => 'aweber_consumer_key',
//			'type' => 'text'
//		),	
//
//		array(
//			'name' => __( 'Aweber Consumer Secret', 'wp-leads-press' ),
//			'desc' => __( 'Enter your Aweber Consumer Secret', 'wp-leads-press' ),
//			'std' => 'someconsumersecretvalue', // Default Value
//			'id' => 'aweber_consumer_secret',
//			'type' => 'text'
//		),	
//				
//		array(
//			'name' => __( 'Aweber Account ID', 'wp-leads-press' ),
//			'desc' => __( 'Enter Your Aweber Account ID', 'wp-leads-press' ),
//			'std' => 'Aweber Account ID', // Default Value
//			'id' => 'get_response_account_id',
//			'type' => 'text'
//		),

//			<label>'.__( "Enter Aweber Authorization Code:", "wp-leads-press" ).'</label>												

		array(
			'name' => __( 'Connect to Aweber API', 'wp-leads-press' ),
			'type' => 'title'
		),
		
		array(

			'content' => '

			<p>Get Aweber Authorization Code: '.$app_id_active.'</p>
			<p>Aweber auth code is needed to authorize your site to connect to Aweber to add leads to your marketing lists.</p>
			
			<h4>Enter Aweber Authorization Code Below</h4>
	
			<div class="wplp-inputs" >
			
			<input type="text" class="wplp-aweber-auth" value="'.$aweber_auth.'" size="50" />
			<br />
			<input type="hidden" class="wplp-user-selector" value="admin" />
			<input type="button" class="wplp-connect-aweber-api" value="Connect to Aweber" />		

			</div>
			
			', // end of content

			'type' => 'html'

		),
		
//		array(
//			'name' => __( 'Aweber Authorization Code', 'wp-leads-press' ),
//			'desc' => __( 'Enter your Aweber Auth Code', 'wp-leads-press' ),
//			'std' => 'aweber auth code', // Default Value
//			'id' => 'aweber_auth_code',
//			'type' => 'text'
//		),	

		array(
			'name' => __( 'Aweber List ID', 'wp-leads-press' ),
			'desc' => __( 'Enter Your Aweber List ID ONLY the numerical portion of your List ID.', 'wp-leads-press' ),
			'std' => 'List ID Value', // Default Value
			'id' => 'aweber_list_id',
			'type' => 'text'
		),		
		




		array(
			'name' => __( 'HTML Form Code Autoresponder Settings', 'wp-leads-press' ),
			'type' => 'title'
		),
			
		array(
			'name' => __( 'Add Site Leads to Autoresponder With HTML Form Code?', 'wp-leads-press' ),
			'desc' => __( 'If checked ALL leads generated via WPLP will be added to a general list as set below. You can also add users to lists per campaign under campaign settings to segment your lists by company/campaign if promoting more than one opportunity. Use this List for site updates, talking about how your team building system works for example, then use segmented campaign lists to follow up per opportunity or product promoted by the individual campaign.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'add_leads_general_list',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),
	
		array(
			'name' => __( 'Form Action Url', 'wp-leads-press' ),
			'desc' => __( 'Enter the \'action url\' from the HTML form code provided by your Autoresponder service. i.e. form action="http://www.somedomain.com" method="post", you would enter: http://www.somedomain.com above. Please note you must fill in a complete url, including http:// or https:// to properly configure.', 'wp-leads-press' ),
			'std' => 'http://www.somedomain.com', // Default Value
			'id' => 'ar_form_url',
			'type' => 'text'
		),	

		array(
			'name' => __( 'Form "Email" Field Name', 'wp-leads-press' ),
			'desc' => __( 'Enter the \'EMAIL\' field name from the HTML form code provided by your Autoresponder service. i.e. name="EMAIL", you would enter: EMAIL above', 'wp-leads-press' ),
			'std' => 'EMAIL', // Default Value
			'id' => 'ar_email_field',
			'type' => 'text'
		),	

		array(
			'name' => __( 'Form "Name" Field Name', 'wp-leads-press' ),
			'desc' => __( 'Enter the \'NAME\' field name from the HTML form code provided by your Autoresponder service. i.e. name="NAME", you would enter: NAME above', 'wp-leads-press' ),
			'std' => 'NAME', // Default Value
			'id' => 'ar_name_field',
			'type' => 'text'
		),
				
		array(
			'name' => __( 'Form "First Name" Field Name', 'wp-leads-press' ),
			'desc' => __( 'Enter the \'FNAME\' field name from the HTML form code provided by your Autoresponder service. i.e. name="FNAME", you would enter: FNAME above', 'wp-leads-press' ),
			'std' => 'FNAME', // Default Value
			'id' => 'ar_fname_field',
			'type' => 'text'
		),		
		
		array(
			'name' => __( 'Form "Last Name" Field Name', 'wp-leads-press' ),
			'desc' => __( 'Enter the \'LNAME\' field name from the HTML form code provided by your Autoresponder service. i.e. name="LNAME", you would enter: LNAME above', 'wp-leads-press' ),
			'std' => 'LNAME', // Default Value
			'id' => 'ar_lname_field',
			'type' => 'text'
		),				

		array(
			'name' => __( 'Form "Phone Number" Field Name', 'wp-leads-press' ),
			'desc' => __( 'Enter the \'PHONE\' field name from the HTML form code provided by your Autoresponder service. i.e. name="PHONE", you would enter: PHONE above', 'wp-leads-press' ),
			'std' => 'PHONE', // Default Value
			'id' => 'ar_phone_field',
			'type' => 'text'
		),				
			
		array(
			'name' => __( 'Form Custom Field Name', 'wp-leads-press' ),
			'desc' => __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ),
			'std' => 'LISTNAME', // Default Value
			'id' => 'ar_form_custom_name',
			'type' => 'text'
		),

		array(
			'name' => __( 'Form Custom Field Value', 'wp-leads-press' ),
			'desc' => __( 'Enter the field value from the HTML form code provided by your Autoresponder service. i.e. name ="LISTNAME" and value="SOMELIST", you would enter: SOMELIST above', 'wp-leads-press' ),
			'std' => 'SOMELIST', // Default Value
			'id' => 'ar_form_custom_val',
			'type' => 'text'
		),
		
		array(
			'name' => __( 'Form Custom Field Name 2', 'wp-leads-press' ),
			'desc' => __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ),
			'std' => 'LISTNAME', // Default Value
			'id' => 'ar_form_custom_name1',
			'type' => 'text'
		),

		array(
			'name' => __( 'Form Custom Field Value 2', 'wp-leads-press' ),
			'desc' => __( 'Enter the field value from the HTML form code provided by your Autoresponder service. i.e. name ="LISTNAME" and value="SOMELIST", you would enter: SOMELIST above', 'wp-leads-press' ),
			'std' => 'SOMELIST', // Default Value
			'id' => 'ar_form_custom_val1',
			'type' => 'text'
		),
		
		
		array(
			'name' => __( 'Form Custom Field Name 3', 'wp-leads-press' ),
			'desc' => __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ),
			'std' => 'LISTNAME', // Default Value
			'id' => 'ar_form_custom_name2',
			'type' => 'text'
		),

		array(
			'name' => __( 'Form Custom Field Value 3', 'wp-leads-press' ),
			'desc' => __( 'Enter the field value from the HTML form code provided by your Autoresponder service. i.e. name ="LISTNAME" and value="SOMELIST", you would enter: SOMELIST above', 'wp-leads-press' ),
			'std' => 'SOMELIST', // Default Value
			'id' => 'ar_form_custom_val2',
			'type' => 'text'
		),
		
		array(
			'name' => __( 'Form Custom Field Name 4', 'wp-leads-press' ),
			'desc' => __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ),
			'std' => 'LISTNAME', // Default Value
			'id' => 'ar_form_custom_name3',
			'type' => 'text'
		),

		array(
			'name' => __( 'Form Custom Field Value 4', 'wp-leads-press' ),
			'desc' => __( 'Enter the field value from the HTML form code provided by your Autoresponder service. i.e. name ="LISTNAME" and value="SOMELIST", you would enter: SOMELIST above', 'wp-leads-press' ),
			'std' => 'SOMELIST', // Default Value
			'id' => 'ar_form_custom_val3',
			'type' => 'text'
		),													
		
		array(
			'name' => __( 'Form Custom Field Name 5', 'wp-leads-press' ),
			'desc' => __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ),
			'std' => 'LISTNAME', // Default Value
			'id' => 'ar_form_custom_name4',
			'type' => 'text'
		),

		array(
			'name' => __( 'Form Custom Field Value 5', 'wp-leads-press' ),
			'desc' => __( 'Enter the field value from the HTML form code provided by your Autoresponder service. i.e. name ="LISTNAME" and value="SOMELIST", you would enter: SOMELIST above', 'wp-leads-press' ),
			'std' => 'SOMELIST', // Default Value
			'id' => 'ar_form_custom_val4',
			'type' => 'text'
		),
		
		
		
				
		array(
			'type' => 'closetab',
			'actions' => true
		),
		
		###
		#	Tab END
		###
		
		
		###
		#	Tab START registration settings
		###
		array(
			'name' => __( 'Registration Settings', 'wp-leads-press' ),
			'type' => 'opentab'
		),
		
		array(
			'name' => __( 'Random Registration Settings', 'wp-leads-press' ),
			'type' => 'title'
		),
		array(
			'name' => __( 'Randoms Members as Top Level?', 'wp-leads-press' ),
			'desc' => __( 'If checked users who have not been referred by another member will be placed as top level user in system with no user above them and given the default user\'s links for joining programs. This settng overrides the Random Structured Member Network settings below, if checked.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'random_members_under_noone',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),

		array(
			'name' => __( 'Random Structured Member Network', 'wp-leads-press' ),
			'type' => 'title'
		),
		array(
			'name' => __( 'Use Random Structured Member Network?', 'wp-leads-press' ),
			'desc' => __( 'If checked users who have not been referred by another member will be placed under existing members in a structured network based on the value you set below. This setting MUST be selected for Convert to Forced Network or Top Down Placement - Random Vistors settings to be active.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'smn_on',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),

		array(
			'name' => __( 'Convert to Forced Network?', 'wp-leads-press' ),
			'desc' => __( 'If checked both RANDOM and PERSONALLY referred members will be placed under existing members in the network following the \'Random Registations\' value, this means personally referred members will also be placed under other users, unless another opening front level to the member has been earned with a bonus as set in the referred member bonus section below.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'convert_forced_on',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),
			
		array(
			'name' => __( 'Top Down Placement - Random Visitors?', 'wp-leads-press' ),
			'desc' => __( 'If checked RANDOM referred members will be placed under existing members in the network following the \'Random Registations\' value, rather than selecting a random user with open network position the system will look from top member down for next available position to fill in network.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'force_randoms',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),
						
		array(
			'name' => __( 'RSMN Value', 'wp-leads-press' ),
			'type' => 'title'
		),
		
		array(
			'name' => __( 'Random Registrations', 'wp-leads-press' ),
			'desc' => __( 'This value represents the number of random member registrations each user will receive from system in a structured network, i.e. a value of 2 would place two members under each member of site as random traffic registers on your site. Personally referred members are always given to the member who referred them, this setting only applies to non-referred site traffic.', 'wp-leads-press' ),
			'std' => 1, // Default Value
			'min' => 1,
			'max' => 1000000,
			'units' => __( 'Value', 'wp-leads-press' ),
			'id' => 'smn_value',
			'type' => 'number'
		),
		
		array(
			'name' => __( 'Referred Member Bonus', 'wp-leads-press' ),
			'type' => 'title'
		),													
		
		array(
			'name' => __( 'Personal Members Required', 'wp-leads-press' ),
			'desc' => __( 'How many personally referred members are required before bonus members earned? (\'0\' = OFF)', 'wp-leads-press' ),
			'std' => 5, // Default Value
			'min' => 0,
			'max' => 1000000,
			'units' => __( 'Members required', 'wp-leads-press' ),
			'id' => 'ref_member_count_required_smn',
			'type' => 'number'
		),
		array(
			'name' => __( 'Bonus Random Members', 'wp-leads-press' ),
			'desc' => __( 'Give your users bonus random members each time they refer the value set in \'Personal Members Required\' setting above. (\'0\' = OFF)', 'wp-leads-press' ),
			'std' => 0, // Default Value
			'min' => 0,
			'max' => 1000000,
			'units' => __( 'Bonus Leads', 'wp-leads-press' ),
			'id' => 'ref_member_bonus_members_value_smn',
			'type' => 'number'
		),
		
		array(
			'name' => __( 'Override Default User', 'wp-leads-press' ),
			'type' => 'title'
		),
		
		array(
			'name' => __( 'Default User ID', 'wp-leads-press' ),
			'desc' => __( 'Set this to the User ID #, i.e. "1" (default setting), which you want to show affiliate signup links as the default settings, this is for users who join as top level members when "Random Members as Top Level" is turned on OR where no user above current user has set their affiliate ID for a company being promoted, generates signups for default user. This setting accepts only ONE value, a single ID number and can also be overridden per campaign so that a default user can be selected per campaign.', 'wp-leads-press' ),
			'std' => 1, // Default Value
			'min' => 1,
			'max' => 1000000,
			'units' => __( 'User ID#', 'wp-leads-press' ),
			'id' => 'default_ancestor',
			'type' => 'number'
		),								
				
		array(
			'name' => __( 'Registration Code Settings', 'wp-leads-press' ),
			'type' => 'title'
		),
		
		array(
			'name' => __( 'Require Code?', 'wp-leads-press' ),
			'desc' => __( 'If checked users will need to enter code in order to register as a member of your site. Registration Code is not required if you are using a membership plugin to restrict registration to your site. (NOTE: Code may not work with all membership plugins, depending on if they change the registration form or not, if you find your membership plugin does not work, please submit a support ticket and we will integrate.)', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'require_registration_code',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),	

		array(
			'name' => __( 'Username as Registration Code?', 'wp-leads-press' ),
			'desc' => __( 'If checked users will also be allowed to register when they enter a valid username for registration code, requires \'Require Code\' option to be checked.', 'wp-leads-press' ),
			'std' => 'off', // Default Value
			'id' => 'username_regcode',
			'type' => 'checkbox',
			'label' => __( 'Yes', 'wp-leads-press' )
		),
					
		array(
			'name' => __( 'Registation Code', 'wp-leads-press' ),
			'desc' => __( 'Code members must enter when registering, if \'Username as Registration Code\' is also checked both USERNAMES and the code entered here can be used during registration.', 'wp-leads-press' ),
			'std' => 'Enter a Code', // Default Value
			'id' => 'registration_code',
			'type' => 'text'
		),				
		
		array(
			'type' => 'closetab',
			'actions' => true
		),
		###
		#	Tab END
		###						
		###
		# 	Tab START
		###
		
		array(
			'name' => __( 'Shortcodes', 'wp-leads-press' ),
			'type' => 'opentab',
			
		),
		
		array(
			'content' => __( '<h2>Shortcodes</h2>
						<br /><br />
						<h3>Lead Dashboard</h3>
						<p>[wplp_dashboard] Default Use
						<br />
						<br />[wplp_dashboard company="7,23,75" override="yes" homepage="no" network="no"] Custom Use
						<br />
						<br />Displays Lead Dashboard in page for your users, where they can see their leads, update their tracking ID\'s for opportunites, get referral links for landing pages and see what they need to do to earn additional leads. This is actually the ONLY shortcode you need to setup to allow your users access to all their leads and associated details.
						<br />
						<br /><b>Settings</b> 
						<br />
						<br />company = ID# of company (optional)
						<br />override = "yes" or "no" Used to override hidden campaigns to display, see below for more detials.(optional, no is default)
						<br />homepage = "on" or "off" Used to turn on or off the display of homepage link in Landing Pages section of dashboard. (optional, on is default)
						<br />leads = "on" or "off" Used to turn on or off the "Leads" tab of dashboard. (optional, on is default)
						<br />settings = "on" or "off" Used to turn on or off the "Lead Settings" tab of dashboard. (optional, on is default)
						<br />autoresponder = "on" or "off" Used to turn on or off the "Autoresponder Settings" tab of dashboard. (optional, on is default)												
						<br />landingpages = "on" or "off" Used to turn on or off the "Landing Pages" tab of dashboard. (optional, on is default)						
						<br />bonus = "on" or "off" Used to turn on or off the "Bonus" tab of dashboard. (optional, on is default)
						<br />network = "on" or "off" Used to turn on or off the "Network" tab of dashboard. (optional, on is default)
						<br />emailbuttons = "on" or "off" Used to turn on or off the "Buttons" for emails in the lead list display. (optional, on is default)						
												
						<br />
						<br />You can select a specific company to display, [wplp_dashboard company="5"] or if using for membership integration you can override hidden campaigns and display with [wplp_dashboard company="5" override="yes"]. <br /><br />For example you have a company which you have hidden all campaigns to keep private, then are only allowing paid members to access a page to join a co-op marketing campaign for that company. You would use the override setting to show the hidden campaigns of that company on the page, allowing paid users access. <br /><br /> Another option is [wplp_dashboard homepage="no"], this option turns off the display of the home page of your site as a landing page in the Landing Page Section of the dashboard.
						</p>
						<br />
						<hr>
						<h3>Campaign Link</h3>
						<p>[wplp_campaign_link] Default Use
						<br />
						<br />[wplp_campaign_link campaign="" landingpage="" imglink="" textlink="" newtab=""] Custom Use
						<br />
						<br />Displays link which redirects user to destination URL of campaign without requiring a name or email. Link can simply be used as [wplp_campaign_link] or with optional variables set.
						<br />
						<br /><b>Shortcode imglink is automatically set when you create your landing page, see section below content area of page when creating your landing pages in WordPress to get the code.</b>
						<br />
						<br /><b>Settings</b>
						<br />
						<br />campaign = ID# of campaign. (Set automatically or can be overriden)
						<br />landingpage = ID# of landing page. (Set automatically or can be overriden)
						<br />imglink = url of image for link, can be left blank. There are button images in /assets/images/ in WP Leads Press. Put path to image, i.e. "/wp-content/plugins/wp-leads-press/assets/images/orange_signupnow.png" or to your uploaded image.
						<br />textlink = text to use as link to landing page set for campaign, used for creating in-line links on landing pages. Do not set both an \'imglink\' and a \'textlink\' at the same time, use separate shortcodes for each implementation, one image based shortcode per page and multiple text links can be used.
						<br />newtab = default is \'yes\', if set to \'no\' when clicked visitor will stay on same page, rather than opening a new tab in browser.
						</p>
						<br />
						<hr>
						<h3>Registration Code</h3>
						<p>
						[wplp_registration_code] Default Use
						<br />
						<br />Displays code you set, simply makes it easy for you to insert into a page so that it updates when you change your code, if code is set to required under \'Registration Settings\', this is an optional setting, but is highly recommended to eliminate site spam users and only allow members of the opportunity your are promoting to use your team website and resources.
						</p>
						<br />
						<hr>
						<h3>Show Referring Member</h3>
						<p>
						[wplp_show_ref_member] Default Use
						<br />
						<br />Displays the referring member display name as selected in their user profile or a welcome visitor message if no referrer, this can be edited in /inc/wplp-shortcodes/shortcodes-users.php. This is based on the value of the cookie set on the visitor\'s computer. .
						</p>
						<br />
						<hr>
						<h3>Lead List</h3>
						<p>
						[wplp_lead_list] Default Use
						<br />
						<br />
						[wplp_lead_list company="6,3,9"] Custom Use
						<br />
						<br />Displays the currently logged in users list of leads. 
						<br />
						<br /><b>Settings</b> 
						<br />
						<br />company = ID# of company (optional)
						<br />emailbuttons = "on" or "off" Used to turn on or off the "Buttons" for emails in the lead list display. (optional, on is default)												
						<br />						
						<br />To use, you can either display all companies or you can select only specific companies to display, i.e. [wplp_lead_list company="5,27,32,1"] or to display all companies just use the standard code, [wplp_lead_list]. To get the proper ID for the company, look under Campaigns Section and use the Company ID# associated with each company you want to display.</p>
						<br />
						<hr>												
						<h3>Affiliate Settings</h3>
						<p>
						[wplp_affiliate_settings] Default Use
						<br />
						<br />
						[wplp_affilaite_settings company="88" override="yes"]
						<br />												
						<br />Displays the Affiliate Settings Panel.
						<br />
						<br /><b>Settings</b> 
						<br />
						<br />company = ID# of company (optional)
						<br />override = Used to override hidden campaigns to display, see below for more detials. (optional, no is default)
						<br />
						<br />To use, you can either display all companies or you can select only specific companies to display, i.e. [wplp_affiliate_settings company="5,27,32,1"] or to display all companies just use the standard code, [wplp_affiliate_settings]. To get the proper ID for the company, look under Campaigns Section and use the Company ID# associated with each company you want to display. 
						<br />
						<br />Additionally if you want to show a Company which does not have an active Campaign you can set as [wplp_affiliate_settings company="5" override="yes"] and the company will show even if it does not have any active campaigns associated with it, this is a setting specifically for when integrating with membership plugins.</p>
						<br />
						<hr>


						<h3>Autoresponder Settings</h3>
						<p>
						[wplp_autoresponder_settings] Default Use
						<br />
						<br />
						[wplp_autoresponder_settings company="88" override="yes"]
						<br />												
						<br />Displays the Autoresponder Settings Panel.
						<br />
						<br /><b>Settings</b> 
						<br />
						<br />company = ID# of company (optional)
						<br />override = Used to override hidden campaigns to display, see below for more detials. (optional, no is default)
						<br />
						<br />To use, you can either display all companies or you can select only specific companies to display, i.e. [wplp_autoresponder_settings company="5,27,32,1"] or to display all companies just use the standard code, [wplp_autorespnder_settings]. To get the proper ID for the company, look under Campaigns Section and use the Company ID# associated with each company you want to display. 
						<br />
						<br />Additionally if you want to show a Company which does not have any public campaigns you can set as [wplp_autoresponder_settings company="5" override="yes"] and the company will show even if it does not have any active campaigns associated with it, this is a setting specifically for when integrating with membership plugins to keep non member level users from accessing specific companies or campaigns assigned to a \'sub\' company, i.e. free cammpaign = companyname, paid campaign or membership level = companyname-pro in campaign settings, allowing you to display different campaigns to paid members vs free.</p>
						<br />
						<hr>
						
												
						<h3>Landing Pages</h3>
						<p>
						[wplp_landing_pages] Default Use
						<br />
						<br />
						[wplp_landing_pages company="4" override="yes" homepage="no"] Custom Use
						<br />
						<br />Displays available landing pages with currently logged in users links.
						<br />
						<br /><b>Settings</b> 
						<br />
						<br />company = ID# of company (optional)
						<br />override = Used to override hidden campaigns to display, see below for more detials.(optional, no is default)
						<br />homepage = Used to turn on or off the display of homepage link in Landing Pages section of dashboard. (optional, yes is default)
						<br />						
						<br />You can also only show landing pages for specific companies using [wplp_landing_pages company="5,27,32,1"] or just use the standard code. 
						<br />
						<br />To show landing pages of Companies which have no active campaigns use [wplp_landing_pages company="5" override="yes"], this is a setting specifically for when integrating with membership plugins.
						<br />
						<br />Another option is [wplp_dashboard homepage="no"], this option turns off the display of the home page of your site as a landing page.</p>
						<br />
						<hr>						
						<h3>Bonus Leads</h3>
						<p>
						[wplp_bonus_leads]
						<br />
						<br />Displays the bonus leads and traffic a user has available and the settings for earning more leads and traffic.</p>	
						<br />																	
						<hr>

						<h3>Member Network</h3>
						<p>
						[wplp_member_network]
						<br />
						<br />Displays the logged in users referring member contact info, their personally referred members contact info and the total number of members in the users network.</p>	
						<br />																	
						<hr>

						<h3>Join Links</h3>
						<p>
						[wplp_join_links] - Default use
						<br />
						<br />Displays the logged in users referring member link to join for each program or the next active member in upline network who is active in the program if direct referrer is not a member of "XYZ" program.</p>	
						<br />
						<br /><b>Settings</b> 
						<br />
						<br />company = ID# of company (optional), used to select only the companies you want to show links for, limiting the returned results.
						<br />override = Used to override hidden campaigns to display, see below for more detials.(optional, no is default)
						<br />						
						<br />
						<br />To show links to join of company attached to a hidden campaign, [wplp_join_links company="5" override="yes"], this will show company id=5 and link to join, overriding the settings for the campaign.
						<hr>												
						', 'wp-leads-press' ),
			'type' => 'html'
		),			
		
		array(
			'type' => 'closetab',
			'actions' => false
		),
//		###
//		#	Tab END
//		###					
//				
		###
		#	Tab START
		###
		array(
			'name' => __( 'Membership Plugin Integration', 'wp-leads-press' ),
			'type' => 'opentab'
		),
		
		array(
			'name' => __( 'Membership Plugins', 'wp-leads-press' ),
			'type' => 'title'
		),		
		
		array(
			'content' => __( '<p style="width: 900px;">WPLP can be used with any membership plugin, simply use WPLP shortcodes on pages which have been secured with a membership level requirement, per the membership plugin setup. Now you only give access to members of specific membership levels, paid or free, your choice, to the lead system.
			<br /><br />
			You can also setup co-op campaigns by creating variations of each company you are promoting, such as "CompanyName", "CompanyName Pkg 1", where pkg 1 is a co-op marketing campaign for that company, which has campaigns assigned to it only accesible to paid members.
			<br /><br />
			<strong>To set up:</strong>
			<br /><br />
			1. Create a hidden campaign, with "CompanyName pkg 1" (or any other name you choose) selected for associated company, use "Add New Company" link, on right side of "add new" campaign screen when setting up the campaign to create as normal. 
			<br /><br />We do this because leads can only be received by members who have joined a specific company and/or variations of companies. This creates a secondary tracking method for "CompanyName", thus separating memberships between the two, one being free membership "CompanyName" the other requiring payment "CompanyName Pkg 1" to receive leads and traffic.
			<br /><br />
			2. Create a landing page associated with the campaign.
			<br /><br />
			3. Now create pages you only give access to for upgraded membership with shortcodes showing the hidden campaigns and companies like so:
			<br /><br />
			[wplp_dashboard override="yes" company="1,2,3,4,"]
			<br /><br />
			This is a simple way to setup access to co-op\'s for your members, you can also use individual shortcodes for each section of the dashboard to display as desired. The override variable of yes tells the system to override the hidden campaigns and show them to your user, please see the shortcode section for more details on each shortcode and usage.
			<br /><br />
			4. Send traffic to co-op landing pages using "rotator" as referral link: 
			<br /><br />i.e. http://yoursite.com/landing-page/?ref=rotation
			<br /><br />
			leads and traffic will be randomly distributed to ALL members eligible to receive leads for the company associated with the campaign, regardless of normal bonus leads and traffic settings. Thus if you sign up members of a paid company campaign, they will receive an unlimited number of randomly distributed leads from traffic generated using the "rotator" referral links.</p>', 'wp-leads-press' ),
			'type' => 'html'
		),	

		array(
			'content' => __( '<hr>', 'wp-leads-press' ),
			'type' => 'html'
		),			
			
		
		array(
			'type' => 'closetab',
			'actions' => false
		),
		###
		#	Tab END
		###	


		###
		#	Tab START
		###
		array(
			'name' => __( 'Itthinx Affiliates Plugin Integration', 'wp-leads-press' ),
			'type' => 'opentab'
		),
		
		array(

			'content' => '<h2>Important!</h2><p>These settings ONLY apply if you are using the affiliates plugins by www.itthinx.com. Our recommendation is the Affiliates Enterprise version as this gives you the ability to pay out on an unlimited number of levels to your affiliates.<br /><br />',

			'type' => 'html'

		),			
		
		
		array(

			'name' => __( 'Run As Network?', 'wp-leads-press' ),

			'desc' => __( '(Must have Random Structured Member Network turned on in \'Registration Settings\' for this to be active.) If checked members when they register will have an affiliate cookie set, even if the referrer is not an affiliate, the system will first look for an affiliate who is below the referring user, if no member if found then the new member will be treated as being referred direct by the store/site, in the affiliate system. This will follow the Random Structured Member Network settings you have set under \'Registration Settings\' in WPLP.', 'wp-leads-press' ),

			'std' => 'off', // Default Value

			'id' => 'affiliates_as_network',

			'type' => 'checkbox',

			'label' => __( 'Yes', 'wp-leads-press' )

		),
		
		array(

			'name' => __( 'Active Distribution On?', 'wp-leads-press' ),

			'desc' => __( '(Must have Random Structured Member Network turned on in \'Registration Settings\' for this to be active.) This setting effects when Run as Network functions take action. If checked, when a visitor uses a referral link to site and the referring member is not an affiliate, the system will look for a member in the referring member\'s network to set an affiliate cookie for at the time of the visit, so the referring member will get a cookie set for WPLP and then another user in their network who is an affiliate will be used to set the cookie for the affiliate plugin, during the initial visit, rather than waiting until the user registers on the site, as the \'Affiliates as Network\' setting above does when used with Active Distribution OFF.<br /><br /> Turning this on may slow down page loads for some servers in situations where the system needs to look for a member who is an affiliate and there are a large number of non affiliate members in the system which must be searched and matched within a user\'s network.<br /><br />If this setting is turned off, only during registration will the system look to set the affiliate cookie and not during a regular site visit. All normal site visit functions such as setting the WPLP cookies etc remain uneffected by this setting, only applies to setting cookies for the affiliate plugin.', 'wp-leads-press' ),

			'std' => 'off', // Default Value

			'id' => 'active_distribution_on',

			'type' => 'checkbox',

			'label' => __( 'Yes', 'wp-leads-press' )

		),			

		array(

			'name' => __( 'Orphans To Upline Affiliates?', 'wp-leads-press' ),

			'desc' => __( 'If checked, when a visitor uses a referral link to site and the referring member is not an affiliate, the system will look for an upline member in the referring member\'s network to set the cookie for the affiliate program up to in the system or give to the store if none is found. First Affiliate found, upline to the referring member will be given the affiliate referral. So if both Run As Network and Orphans To Upline Affiliate are selected, the system will first look for a member in the referring member network, then for an upline member if non is found below the refering member and that referring member themselves is not an affliate.', 'wp-leads-press' ),

			'std' => 'off', // Default Value

			'id' => 'orphans_to_upline',

			'type' => 'checkbox',

			'label' => __( 'Yes', 'wp-leads-press' )

		),					
		

		array(
			'type' => 'closetab',
			'actions' => true
		),
		###
		#	Tab END
		###	



		
		




		###
		#	Tab START
		###
		array(
			'name' => __( 'iDevAffiliate Integration', 'wp-leads-press' ),
			'type' => 'opentab'
		),
		
		array(

			'content' => '<h2>Important!</h2><p>These settings ONLY apply if you are using iDevAffiliate Software from www.idevaffiliate.com. <br /><br />',

			'type' => 'html'

		),			

		array(

			'name' => __( 'iDevAffiliate Install Directory', 'wp-leads-press' ),

			'desc' => __( 'Enter the full path to your iDevAffiliate installation, i.e. "yourdomain.com/idevaffiliate/", note to include the trailing \'/\'. ', 'wp-leads-press' ),

			'std' => 'yourdomain.com/idevaffiliates/', // Default Value

			'id' => 'idevaffiliate_install_directory',

			'type' => 'text'

		),

		array(

			'name' => __( 'iDevAffiliate Database Host', 'wp-leads-press' ),

			'desc' => __( 'Enter i.e. \'localhost\', this is standard setting.', 'wp-leads-press' ),

			'std' => 'localhost', // Default Value

			'id' => 'idevaffiliate_datahost',

			'type' => 'text'

		),		
		
		array(

			'name' => __( 'iDevAffiliat Database User', 'wp-leads-press' ),

			'desc' => __( 'Enter iDeveAffiliate Database Username', 'wp-leads-press' ),

			'std' => 'idevaffiliate', // Default Value

			'id' => 'idevaffiliate_dbuser',

			'type' => 'text'

		),		
		
		array(

			'name' => __( 'iDevAffiliat Database Password', 'wp-leads-press' ),

			'desc' => __( 'Enter the idevaffiliate Database Password.', 'wp-leads-press' ),

			'std' => 'passwordtexthere', // Default Value

			'id' => 'idevaffiliate_dbpass',

			'type' => 'text'

		),		
		
		array(

			'name' => __( 'iDevAffiliat Database Name', 'wp-leads-press' ),

			'desc' => __( 'Enter the Database Name.', 'wp-leads-press' ),

			'std' => 'yourdomain.com/idevaffiliates/', // Default Value

			'id' => 'idevaffiliate_dbname',

			'type' => 'text'

		),		
		
		
		array(

			'name' => __( 'ID# of iDevAffiliate "Company"', 'wp-leads-press' ),

			'desc' => __( 'Enter the id number of the company being promoted using iDeveAffiliate affiliate software, i.e. the company used in campaign setting for iDevAffiliate tracked campaign links. The company ID can be found when viewing your campaigns.', 'wp-leads-press' ),

			'std' => '1', // Default Value

			'id' => 'idevaffiliate_company',

			'type' => 'text'

		),		
		
		array(

			'name' => __( 'Run As Network?', 'wp-leads-press' ),

			'desc' => __( '(Must have Random Structured Member Network turned on in \'Registration Settings\' for this to be active.) If checked members when they register will have an affiliate cookie set, even if the referrer is not an affiliate, the system will first look for an affiliate who is below the referring user, if no member if found then the new member will be treated as being referred direct by the store/site, in the affiliate system. This will follow the Random Structured Member Network settings you have set under \'Registration Settings\' in WPLP.', 'wp-leads-press' ),

			'std' => 'off', // Default Value

			'id' => 'idevaffiliate_as_network',

			'type' => 'checkbox',

			'label' => __( 'Yes', 'wp-leads-press' )

		),
		
/* 		array(

			'name' => __( 'Active Distribution On?', 'wp-leads-press' ),

			'desc' => __( '(Must have Random Structured Member Network turned on in \'Registration Settings\' for this to be active.) This setting effects when Run as Network functions take action. If checked, when a visitor uses a referral link to site and the referring member is not an affiliate, the system will look for a member in the referring member\'s network to set an affiliate cookie for at the time of the visit, so the referring member will get a cookie set for WPLP and then another user in their network who is an affiliate will be used to set the cookie for the affiliate plugin, during the initial visit, rather than waiting until the user registers on the site, as the \'Affiliates as Network\' setting above does when used with Active Distribution OFF.<br /><br /> Turning this on may slow down page loads for some servers in situations where the system needs to look for a member who is an affiliate and there are a large number of non affiliate members in the system which must be searched and matched within a user\'s network.<br /><br />If this setting is turned off, only during registration will the system look to set the affiliate cookie and not during a regular site visit. All normal site visit functions such as setting the WPLP cookies etc remain uneffected by this setting, only applies to setting cookies for the affiliate plugin.', 'wp-leads-press' ),

			'std' => 'off', // Default Value

			'id' => 'idevaffiliate_active_distribution_on',

			'type' => 'checkbox',

			'label' => __( 'Yes', 'wp-leads-press' )

		),			

		array(

			'name' => __( 'Orphans To Upline Affiliates?', 'wp-leads-press' ),

			'desc' => __( 'If checked, when a visitor uses a referral link to site and the referring member is not an affiliate, the system will look for an upline member in the referring member\'s network to set the cookie for the affiliate program up to in the system or give to the store if none is found. First Affiliate found, upline to the referring member will be given the affiliate referral. So if both Run As Network and Orphans To Upline Affiliate are selected, the system will first look for a member in the referring member network, then for an upline member if non is found below the refering member and that referring member themselves is not an affliate.', 'wp-leads-press' ),

			'std' => 'off', // Default Value

			'id' => 'idevaffiliate_orphans_to_upline',

			'type' => 'checkbox',

			'label' => __( 'Yes', 'wp-leads-press' )

		),					
		 */

		array(
			'type' => 'closetab',
			'actions' => true
		),
		###
		#	Tab END
		###			







			
		
		//array(
//
//			'name' => __( 'Text input', 'wp-leads-press' ),
//
//			'desc' => __( 'Text input description', 'wp-leads-press' ),
//
//			'std' => 'Default value', // Default Value
//
//			'id' => 'text',
//
//			'type' => 'text'
//
//		),
//
//		array(
//
//			'name' => __( 'Textarea', 'wp-leads-press' ),
//
//			'desc' => __( 'Textarea description', 'wp-leads-press' ),
//
//			'std' => 'Default value', // Default Value
//
//			'id' => 'textarea',
//
//			'type' => 'textarea',
//
//			'rows' => 5
//
//		),
//
//		array(
//
//			'name' => __( 'Checkbox', 'wp-leads-press' ),
//
//			'desc' => __( 'Checkbox description', 'wp-leads-press' ),
//
//			'std' => 'on', // Default Value
//
//			'id' => 'checkbox',
//
//			'type' => 'checkbox',
//
//			'label' => __( 'Checkbox label', 'wp-leads-press' )
//
//		),
//
//		array(
//
//			'name' => __( 'Radio buttons', 'wp-leads-press' ),
//
//			'desc' => __( 'Radio buttons description', 'wp-leads-press' ),
//
//			'options' => array(
//
//				'option1' => __( 'Option 1', 'wp-leads-press' ),
//
//				'option2' => __( 'Option 2', 'wp-leads-press' ),
//
//				'option3' => __( 'Option 3', 'wp-leads-press' )
//
//			),
//
//			'std' => 'option1', // Default Value
//
//			'id' => 'radio',
//
//			'type' => 'radio'
//
//		),
//
//		array(
//
//			'name' => __( 'Select', 'wp-leads-press' ),
//
//			'desc' => __( 'Select description', 'wp-leads-press' ),
//
//			'options' => array(
//
//				'option1' => __( 'Option 1', 'wp-leads-press' ),
//
//				'option2' => __( 'Option 2', 'wp-leads-press' ),
//
//				'option3' => __( 'Option 3', 'wp-leads-press' )
//
//			),
//
//			'std' => 'option1', // Default Value
//
//			'id' => 'select',
//
//			'type' => 'select'
//
//		),
//
//		array(
//
//			'name' => __( 'Title field', 'wp-leads-press' ),
//
//			'type' => 'title'
//
//		),
//
//		array(
//
//			'content' => '<p>Vestibulum nec quam nisl. Nulla facilisi. Etiam placerat tempor rutrum. Fusce pellentesque tellus adipiscing nulla eleifend pretium. In lacinia lectus et sapien elementum eget sollicitudin ante suscipit. Nunc eu arcu nec risus bibendum mattis. Suspendisse nisi magna, <a href="#">pretium in aliquam viverra</a>, cursus tincidunt quam. Ut nec risus elit, vel pellentesque felis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p><p>Fusce venenatis condimentum est, eget gravida erat interdum tristique. In hac habitasse platea dictumst. In hac habitasse platea dictumst. Vestibulum fringilla egestas erat, sit amet ullamcorper nisi placerat vel.</p>',
//
//			'type' => 'html'
//
//		),
//
//		array(
//
//			'name' => __( 'Checkbox group', 'wp-leads-press' ),
//
//			'desc' => __( 'Checkbox group description', 'wp-leads-press' ),
//
//			'options' => array(
//
//				'option1' => __( 'Option 1', 'wp-leads-press' ),
//
//				'option2' => __( 'Option 2', 'wp-leads-press' ),
//
//				'option3' => __( 'Option 3', 'wp-leads-press' )
//
//			),
//
//			'std' => array(
//
//				'option1' => '',
//
//				'option2' => 'on',
//
//				'option3' => 'on',
//
//			),
//
//			'id' => 'checkbox-group',
//
//			'type' => 'checkbox-group'
//
//		),
//
//		array(
//
//			'name' => __( 'Number', 'wp-leads-press' ),
//
//			'desc' => __( 'Number field description', 'wp-leads-press' ),
//
//			'std' => 100, // Default Value
//
//			'min' => 0,
//
//			'max' => 1000,
//
//			'units' => __( 'pixels', 'wp-leads-press' ),
//
//			'id' => 'number',
//
//			'type' => 'number'
//
//		),
//
//		array(
//
//			'name' => __( 'Size', 'wp-leads-press' ),
//
//			'desc' => __( 'Size field description', 'wp-leads-press' ),
//
//			'std' => array( 14, 'px' ), // Default Value
//
//			'min' => 1,
//
//			'max' => 72,
//
//			'units' => array( 'px', 'em', '%', 'pt' ),
//
//			'id' => 'size',
//
//			'type' => 'size'
//
//		),
//
//		array(
//
//			'name' => __( 'Upload', 'wp-leads-press' ),
//
//			'desc' => __( 'Upload field description', 'wp-leads-press' ),
//
//			'std' => '', // Default Value
//
//			'id' => 'upload',
//
//			'type' => 'upload'
//
//		),
//
//		array(
//
//			'name' => __( 'Color picker', 'wp-leads-press' ),
//
//			'desc' => __( 'Color picker description', 'wp-leads-press' ),
//
//			'std' => '#00bb00', // Default Value
//
//			'id' => 'color',
//
//			'type' => 'color'
//
//		),
//
//		array(
//
//			'name' => __( 'Code editor', 'wp-leads-press' ),
//
//			'desc' => __( 'Code editor description', 'wp-leads-press' ),
//
//			'std' => '', // Default Value
//
//			'rows' => 7,
//
//			'id' => 'code',
//
//			'type' => 'code'
//
//		),
	);
	
//	} elseif( $result["status_check"] == "too_many_errors" ) {
//		
//		/** Plugin Options Panel*/
//		$options_settings = array(	
//					
//			###
//			#	Tab START
//			###
//			array(
//				'name' => __( 'License Key', 'wp-leads-press' ),
//				'type' => 'opentab'
//			),
//	//		array(
//	//			'name' => __( 'License Key', 'wp-leads-press' ),
//	//			'desc' => __( 'Enter your WP Leads Press License Key, save and refresh settings page to enable all WPLP settings', 'wp-leads-press' ),
//	//			'std' => 'Enter Your License Key', // Default Value
//	//			'id' => 'license_key',
//	//			'type' => 'text'
//	//		),	
//			
//			array(
//	
//				'content' => '<p>' . __( 'Your Key is not currently activated, there is an issue with your server configuration or security settings as your server is sending requests to our licensing server at an excessive rate and/or with incomplete or missing data, please check with your webhost for issues such as "mod_security" or other security related issues and try again. If after checking with your host you still have issues, please contact WPLP support with your WordPress and cPanel login details for further investigation.', 'wp-leads-press' ) . '.</p>',
//	
//				'type' => 'html'
//	
//			),			
//			
//			array(
//				'type' => 'closetab',
//				'actions' => true
//			),
//			###
//			#	Tab END
//			###			
//			
//	
//		);			
//		
//		
//		
//	} else { // end too many requests 
//
//	/** Plugin Options Panel*/
//	$options_settings = array(	
//				
//		###
//		#	Tab START
//		###
//		array(
//			'name' => __( 'License Key', 'wp-leads-press' ),
//			'type' => 'opentab'
//		),
////		array(
////			'name' => __( 'License Key', 'wp-leads-press' ),
////			'desc' => __( 'Enter your WP Leads Press License Key, save and refresh settings page to enable all WPLP settings', 'wp-leads-press' ),
////			'std' => 'Enter Your License Key', // Default Value
////			'id' => 'license_key',
////			'type' => 'text'
////		),	
//		
//		array(
//
//			'content' => '<p>' . __( 'Your Key has not been activated yet', 'wp-leads-press' ) . '.</p>',
//
//			'type' => 'html'
//
//		),			
//		
//		array(
//			'type' => 'closetab',
//			'actions' => true
//		),
//		###
//		#	Tab END
//		###			
//		
//
//	);	
//	
//	
//	
//	}
		
?>