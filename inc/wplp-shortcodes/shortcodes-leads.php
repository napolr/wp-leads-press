<?php	
//Lead Shortcodes
function wplp_dashboard_display($atts) {
	global $wplp_admin, $wpdb, $current_user, $post_id, $_GET, $_POST;
	
	# Add 'company' attribute
	extract( shortcode_atts( array(
		'company' => 0,
		'override' => 'no',
		'homepage' => 'on',
		'leads' => 'on',
		'settings' => 'on',
		'autoresponder' => 'on',
		'landingpages' => 'on',
		'bonus' => 'on',
		'network' => 'on',
		'emailbuttons' => 'on'
	), $atts ) );
	
	// Check if user is logged in
	if ( is_user_logged_in() ) {
		
		$user_id = $current_user->ID;	
		
		$options = wp_load_alloptions();
		
		//Get users who can have random leads
		$select_ids = $options['wp_leads_press_select_user_ids'];
		$random_ids = $options['wp_leads_press_ids_for_random_traffic'];
		$random_ids = explode(',', $random_ids);	
		
		//$url = $_SERVER['REQUEST_URI'];
		
		//list($url_location, $url_end) = array_merge( explode("?", $url), array( true ) );	
		
		$ret = '<div id="wplp-tabs">';
		$ret .= '<ul>';
		
		if( $leads == 'on' ) {
		
			$ret .= '<li><a href="#tabs-1">'.__( 'Leads', 'wp-leads-press').'</a></li>';
		
		}

		if( $settings == 'on' ) {

			$ret .=	'<li><a href="#tabs-2">'.__( 'Company Settings', 'wp-leads-press' ).'</a></li>';

		}
		
		if( $autoresponder == 'on' ) {

			$ret .=	'<li><a href="#tabs-3">'.__( 'Autoresponder Settings', 'wp-leads-press' ).'</a></li>';

		}
				
		if( $landingpages == 'on' ) {

			$ret .= '<li><a href="#tabs-4">'.__( 'Landing Pages', 'wp-leads-press' ).'</a></li>';
			
		}
		
		if( $bonus == 'on' ) {

			if ( ( $options['wp_leads_press_max_random_leads_allowed'] !=0 ) || ( $options['wp_leads_press_personally_referred_leads_required'] != 0 ) || ( $options['wp_leads_press_ref_member_count_required'] != 0 ) || ( $options['wp_leads_press_ref_traffic_count_required'] != 0 ) ){
				
				
				if( ( $select_ids == "on" ) && ( !in_array( $user_id, $random_ids ) ) ) {
					
					
				} else {
					
					$ret .= '<li><a href="#tabs-5">'.__( 'Bonus Leads', 'wp-leads-press' ).'</a></li>';		
					
				}
			
			}
			
		} // end if bonus = on

		if( $network == 'on' ) {

			$ret .= '<li><a href="#tabs-6">'.__( 'Member Network', 'wp-leads-press' ).'</a></li>';
			
		}		
		
		$ret .= '</ul>';
		
		if( $leads == 'on' ) {
		
			$ret .= '<div id="tabs-1">';	
			$ret .= wplp_lead_list_display($atts);
			$ret .= '</div>'; // End tab 1
		
		}

		if( $settings == 'on' ) {
		
			$ret .= '<div id="tabs-2">';
			$ret .= wplp_affiliate_settings_display($atts);			
			$ret .= '</div>'; // End tab 2 // End affiliate Settings
			
		}

		if( $autoresponder == 'on' ) {

			$ret .= '<div id="tabs-3">';
			$ret .= 		wplp_autoresponder_settings($atts);
			$ret .= '</div>'; // End tab 3
			
		}
		if( $landingpages == 'on' ) {

			$ret .= '<div id="tabs-4">';
			$ret .= wplp_landing_pages_display($atts);
			$ret .= '</div>'; // End tab 3
			
		}
		
		if( $bonus == 'on' ) {
			
			if ( ( $options['wp_leads_press_max_random_leads_allowed'] !=0 ) || ( $options['wp_leads_press_personally_referred_leads_required'] != 0 ) || ( $options['wp_leads_press_ref_member_count_required'] != 0 ) || ( $options['wp_leads_press_ref_traffic_count_required'] != 0 ) ){
				
				if( ( $select_ids == "on" ) && ( !in_array( $user_id, $random_ids ) ) ) {
			
				
				
				} else {
				
					// Earn leads
					$ret .= '<div id="tabs-5">';
					$ret .= wplp_bonus_leads_display();			
					$ret .= '</div>'; // End tab 4
				
				}
				
			}
			
		} // end if bonus = on
		
		if( $network == 'on' ) {

			$ret .= '<div id="tabs-6">';
			$ret .= wplp_member_network($atts);
			$ret .= '</div>'; // End tab 5

		} // end if network == on
		
		$ret .= '</div>'; // End TABS
		
		return $ret;
		
	} else { // End is user Logged in
	
		return __( 'You must be logged in to view Lead Dashboard', 'wp-leads-press' );	
		
	}
	
}

function wplp_lead_list_display($atts){
	global $_GET, $_POST, $wp, $wpdb, $current_user;

	# Add 'company' attribute
	extract( shortcode_atts( array(
		'company' => 0,
		'emailbuttons' => 'on',
	), $atts ) );
	
	$companies = array();
	$companies = explode(',', $company);
		
	if ( is_user_logged_in() ) {
		
		if ( isset( $_GET['action'] ) ) {
			
			$data = $_GET['action'];
			
			$action_data = 'wplp '.$data;
			
			if ( ( strpos( $action_data, "edit" ) !== false ) || ( strpos( $action_data, "save" ) !== false ) || ( strpos( $action_data, "updated" ) !== false ) || ( strpos( $action_data, "delete" ) !== false ) ) {
				
				list($action, $leadID) = explode( ":", $data, 2 );
				//echo $action; // action selected by user i.e. edit, delete, etc.
				//echo $lead; // lead that action is to be performed on
				
				// Sanitize the lead value to only have lead ID
				list($title, $leadID) = explode( "=", $leadID, 2 );
				//$title holds the lead=
				//$lead holds the lead ID
		
			} else {
				
				$action = $_GET['action'];
			
			}
							
		} else {
			
			$action = '';
			
		}	

		//Get all user leads
		$args = array(
		'author'		   => $current_user->ID,
		'posts_per_page'   => -1,
		'offset'           => 0,
		'category'         => '',
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'lead',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'post_status'      => 'publish',
		'suppress_filters' => true );

		$leads = get_posts( $args );	

		if ( ( $action == 'view' ) || ( $action == '' ) ){		
				
				$ret = '<table id="wplp_lead_list" width="100%">';
								
				$ret .= '<tr>';
				
				$ret .= '<td>';
				$ret .= '<strong>' . __( 'Date', 'wp-leads-press' ) . '</strong>';
				$ret .= '</td>';
		
				$ret .= '<td>';
				$ret .= '<strong>' . __( 'First Name', 'wp-leads-press' ) . '</strong>';
				$ret .= '</td>';
		
				$ret .= '<td>';
				$ret .= '<strong>' . __( 'Last Name', 'wp-leads-press' ) . '</strong>';
				$ret .= '</td>';
				
				$ret .= '<td>';
				$ret .= '<strong>' . __( 'Email', 'wp-leads-press' ) . '</strong>';
				$ret .= '</td>';
				
				$ret .= '<td>';
				$ret .= '<strong>' . __( 'Company', 'wp-leads-press' ) . '</strong>';
				$ret .= '</td>';
				
				$ret .= '<td>';
				$ret .= '<strong>' . __( 'Campaign', 'wp-leads-press' ) . '</strong>';
				$ret .= '</td>';				

				$ret .= '<td>';
				$ret .= '<strong>' . __( 'Landing Page', 'wp-leads-press' ) . '</strong>';
				$ret .= '</td>';				
						
				$ret .= '<td>';
				$ret .= '<strong>' . __( 'Lead Status', 'wp-leads-press' ) . '</strong>';
				$ret .= '</td>';
				
				$ret .= '<td>';
				$ret .= '<strong>' . __( 'Actions', 'wp-leads-press' ) . '</strong>';
				$ret .= '</td>';													
				
				$ret .= '</tr>';	
							
			if( !empty( $leads ) ) {
				
				foreach ( $leads as $lead ) {
					
					// Get meta details of lead				
					$custom_fields = get_post_custom($lead->ID);
										
					//wplp_lead_status
					$lead_status = wp_get_post_terms( $lead->ID, 'wplp_lead_status', array( "fields" => "names" ) );	
															
					if ( !isset( $lead_status ) ) {
						
						$lead_status = 'Not Set';
						
					}
					
					// Get company ID of lead
					$leadCompanyID = get_post_meta( $lead->ID, 'wplp_lead_company_id', true ); 
					
					// Get company Name of lead
					$leadCompanyName = get_post_meta( $lead->ID, 'wplp_lead_opportunity', true );
					
					// Get Campaign ID of lead
					$leadCampaignID = get_post_meta( $lead->ID, 'wplp_lead_campaign', true );
					
					// Get Campaign title
					$leadCampaign = get_post( $leadCampaignID, ARRAY_A );
										
					// Get Landing Page ID of lead
					$leadLandingPageID = get_post_meta( $lead->ID, 'wplp_lead_landing_page', true );
					
					// Get landing page title
					$leadLandingPage = get_post( $leadLandingPageID, ARRAY_A );					
																
					
					
					
					// results
					if ( isset( $companies ) && !empty( $companies ) && $companies[0] != 0 ) {
						
						// Get the company names	
						$companyNames = wplp_get_company_names($companies);

						if ( ( $lead_status[0] != 'Member' ) && ( in_array( $leadCompanyID, $companies ) ) || ( $lead_status[0] != 'Member' ) && ( in_array( $leadCompanyName, $companyNames ) ) ) {
												
									$ret .= '<tr>';
							
									$ret .= '<td>';
									
									// Trim the lead
									$data = $lead->post_date;
									list($date, $time) = explode(" ", $data);		
						
									$ret .=  '' . $date . ''; 
									$ret .= '</td>';
									
									$ret .= '<td>';
									$ret .= '' . $custom_fields['wplp_lead_first_name'][0] . '';
									$ret .= '</td>';
									
									$ret .= '<td>';
									$ret .= '' . $custom_fields['wplp_lead_last_name'][0] . '';
									$ret .= '</td>';		
									
									if ( $emailbuttons != 'on' ) {
		
										$ret .= '<td>';
										$ret .= '<a href="mailto:' . $custom_fields['wplp_lead_email'][0] . '">' . $custom_fields['wplp_lead_email'][0] . '</a>';
										$ret .= '</td>';
									
									} else {
									
										$ret .= '<td>';
										$ret .= '<form method="GET" action="mailto:' . $custom_fields['wplp_lead_email'][0] . '">';
										$ret .= '<input type="submit" value="'. __( 'Email Lead', 'wp-leads-press' ) .'">';
										$ret .= '</form>';
										$ret .= '</td>';							
									
									}									
									
									if( $custom_fields['wplp_lead_opportunity'][0] == '' ) {
										
										$ret .= '<td>';
										$ret .= 'Prior To Tracking';
										$ret .= '</td>';
										
									} else {				
					
										$ret .= '<td>';
										$ret .= '' . $custom_fields['wplp_lead_opportunity'][0] . '';
										$ret .= '</td>';
									
									}
									
									$ret .= '<td>';
									$ret .= '' . $leadCampaign['post_title'] . '';
									$ret .= '</td>';

									$ret .= '<td>';
									$ret .= '' . $leadLandingPage['post_title'] . '';
									$ret .= '</td>';
																		
									$ret .= '<td>';
									$ret .= '' . $lead_status[0] . '';
									$ret .= '</td>';																			
																		
									$ret .= '<td class="wplp-inputs">';
				
									$ret .= '<input type="hidden" id="wplp_lead_id" name="wplp_lead_id" value="'. $lead->ID . '" />';
									
									$ret .= '<input type="button" class="wplp-view-edit-lead" value="View/Edit" />';
						
									$ret .= '<input type="button" class="wplp-delete-lead" value="Delete" />';
									
									$ret .= '</td>';
							
									$ret .= '</tr>';
							} // end comparison.

									
					} else {// end check if company ID's set in shortcode				
					
						if ( $lead_status[0] != 'Member' ) {

							$ret .= '<tr>';
					
							$ret .= '<td>';
							
							// Trim the lead
							$data = $lead->post_date;
							list($date, $time) = explode(" ", $data);		
				
							$ret .=  '' . $date . ''; 
							$ret .= '</td>';
							
							$ret .= '<td>';
							$ret .= '' . $custom_fields['wplp_lead_first_name'][0] . '';
							$ret .= '</td>';
							
							$ret .= '<td>';
							$ret .= '' . $custom_fields['wplp_lead_last_name'][0] . '';
							$ret .= '</td>';		
							
							if ( $emailbuttons != 'on' ) {

								$ret .= '<td>';
								$ret .= '<a href="mailto:' . $custom_fields['wplp_lead_email'][0] . '">' . $custom_fields['wplp_lead_email'][0] . '</a>';
								$ret .= '</td>';
							
							} else {
							
								$ret .= '<td>';
								$ret .= '<form method="GET" action="mailto:' . $custom_fields['wplp_lead_email'][0] . '">';
								$ret .= '<input type="submit" value="'. __( 'Email Lead', 'wp-leads-press' ) .'">';
								$ret .= '</form>';
								$ret .= '</td>';							
							
							}
							
							if( isset( $custom_fields['wplp_lead_opportunity'][0] ) && ( !empty( $custom_fields['wplp_lead_opportunity'][0] ) ) ){			
				
									$ret .= '<td>';
									$ret .= '' . $custom_fields['wplp_lead_opportunity'][0] . '';
									$ret .= '</td>';
																
							} else {
								
									$ret .= '<td>';
									$ret .= 'Prior To Tracking';
									$ret .= '</td>';							
								
							}

							$ret .= '<td>';
							$ret .= '' . $leadCampaign['post_title'] . '';
							$ret .= '</td>';

							$ret .= '<td>';
							$ret .= '' . $leadLandingPage['post_title'] . '';
							$ret .= '</td>';
														
							$ret .= '<td>';
							$ret .= '' . $lead_status[0] . '';
							$ret .= '</td>';							
					
							$ret .= '<td class="wplp-inputs">';
		
							$ret .= '<input type="hidden" id="wplp_lead_id" name="wplp_lead_id" value="'. $lead->ID . '" />';
							
							$ret .= '<input type="button" class="wplp-view-edit-lead" value="View/Edit" />';
				
							$ret .= '<input type="button" class="wplp-delete-lead" value="Delete" />';
							
							$ret .= '</td>';
					
							$ret .= '</tr>';
							
							
						}
					
					}// end else
				
				} // end foreach
				
			} // end if !empty leads
				
				$ret .= '</table>';
				
				//OK lets give a button to download all leads
				

				
				//$leads_array = $leads;
				//$leads_array = wplp_object_to_array($leads_array);
				//$leads = wplp_convert_to_csv($leads_array, 'lead_report.csv', ',');
				//$ret .= '<div class="wplp-inputs">';
				//$ret .= '<input type="button" class="wplp-get-lead-list" value="Download Leads as CSV" />';
				//$ret .= '</div>';
				//$ret .= '<div class="service-container" data-service="'.wplp_convert_to_csv($leads_array, 'lead_report.csv', ',').';>">';

				//$ret .= '</div>';					
				
		} // End view
		
		if ( ( $action == 'edit' ) || ( $action == 'save' ) || ( $action == 'updated' ) ){
			
			$custom_fields = get_post_custom($leadID);

			// Get Campaign ID of lead
			$leadCampaignID = get_post_meta( $leadID, 'wplp_lead_campaign', true );
			
			// Get Campaign title
			$leadCampaign = get_post( $leadCampaignID, ARRAY_A );
								
			// Get Landing Page ID of lead
			$leadLandingPageID = get_post_meta( $leadID, 'wplp_lead_landing_page', true );
			
			// Get landing page title
			$leadLandingPage = get_post( $leadLandingPageID, ARRAY_A );	
														
			$url = $_SERVER['REQUEST_URI'];
			
			list($url_location, $url_end) = explode("?", $url, 2);				
			
			$ret = '<<< <a class="button" href="' . $url_location . '">Back</a>';
	
			$ret .= '<h2>' . __( 'Edit Lead', 'wp-leads-press' ) . '</h2>';		
			$ret .= '<p>' . __( 'Update information and click Update Lead button.', 'wp-leads-press' ) . '</p>';
			
			$ret .= '<form id="wplp_edit_lead_form" name="wplp_edit_lead_form" class="wplp-edit-lead-form" action="#" method="post">';
	
			$ret .= '<table id="wplp_edit_lead">';				
							
			$ret .= '<input type="hidden" id="wplp_lead_home_page" name="wplp_lead_home_page" value="' . $url_location . '?' . $url_end . '" />';
			$ret .= '<input type="hidden" id="wplp_lead_id_update" name="wplp_lead_id_update" value="' . $leadID . '" />';
			
			$ret .= '<tr>';
			$ret .= '<td>';
			$ret .= '<label for="wplp_lead_first_name">' .__('First Name:', 'wp-leads-press' ) . '</label>';
			$ret .= '</td>';
			
			$ret .= '<td>';

			if( isset($custom_fields['wplp_lead_first_name'][0]) ) {
			
				$ret .= '<input type="text" id="wplp_lead_first_name" name="wplp_lead_first_name" value="' . $custom_fields['wplp_lead_first_name'][0] . '"></input>';
	
			} else {
				
				$ret .= '<input type="text" id="wplp_lead_first_name" name="wplp_lead_first_name" value=""></input>';				
				
			}
			$ret .= '</td>';
			$ret .= '</tr>';
			
			$ret .= '<tr>';
			$ret .= '<td>';
			$ret .= '<label for="wplp_lead_last_name">' . __( 'Last Name:', 'wp-leads-press' ) . '</label>';
			$ret .= '</td>';				
			
			$ret .= '<td>';

			if( isset($custom_fields['wplp_lead_last_name'][0]) ) {
			
				$ret .= '<input type="text" id="wplp_lead_last_name" name="wplp_lead_last_name" value="' . $custom_fields['wplp_lead_last_name'][0] . '"></input>';
	
			} else {
				
				$ret .= '<input type="text" id="wplp_lead_last_name" name="wplp_lead_last_name" value=""></input>';				
				
			}
	
			$ret .= '</td>';
			$ret .= '</tr>';
			
			$ret .= '<tr>';
			$ret .= '<td>';
			$ret .= '<label for="wplp_lead_email">' . __( 'Email:', 'wp-leads-press' ) . '</label>';
			$ret .= '</td>';	
							
			$ret .= '<td>';

			if( isset($custom_fields['wplp_lead_email'][0]) ) {
			
				$ret .= '<input type="text" id="wplp_lead_email" name="wplp_lead_email" value="' . $custom_fields['wplp_lead_email'][0] . '"></input>';
	
			} else {
				
				$ret .= '<input type="text" id="wplp_lead_email" name="wplp_lead_email" value=""></input>';					
				
			}
			
			$ret .= '</td>';
			$ret .= '</tr>';
			
			$ret .= '<tr>';
			$ret .= '<td>';
			$ret .= '<label for="wplp_lead_phone">' . __( 'Phone Number:', 'wp-leads-press' ) . '</label>';
			$ret .= '</td>';
							
			$ret .= '<td>';
			if( isset($custom_fields['wplp_lead_phone'][0]) ) {

				$ret .= '<input type="text" id="wplp_lead_phone" name="wplp_lead_phone" value="' . $custom_fields['wplp_lead_phone'][0] . '"></input>';
	
			} else {
				
				$ret .= '<input type="text" id="wplp_lead_phone" name="wplp_lead_phone" value=""></input>';				
				
			}
			$ret .= '</td>';
			$ret .= '</tr>';									
			
			$ret .= '<tr>';
			$ret .= '<td>';
			$ret .= '<label for="wplp_lead_notes">' . __( 'Notes:', 'wp-leads-press' ) . '</label>';
			$ret .= '</td>';	
											
			$ret .= '<td>';
			
			if( isset($custom_fields['wplp_lead_notes'][0]) ) {
			
				$ret .= '<textarea id="wplp_lead_notes" name="wplp_lead_notes" cols="50" rows="10">' . $custom_fields['wplp_lead_notes'][0] . '</textarea>';
				
			} else {
				
				$ret .= '<textarea id="wplp_lead_notes" name="wplp_lead_notes" cols="50" rows="10"></textarea>';
					
			}
			
			$ret .= '</td>';
			$ret .= '</tr>';						

			$ret .= '<tr>';
			
			$ret .= '<td>';
			$ret .= '<label for="wplp_campaign_title">' . __( 'Campaign:', 'wp-leads-press' ) . '</label>';
			$ret .= '</td>';
						
			$ret .= '<td>';
			$ret .= '' . $leadCampaign['post_title'] . '';
			$ret .= '</td>';
			
			$ret .= '</tr>';

			$ret .= '<tr>';

			$ret .= '<td>';
			$ret .= '<label for="wplp_lead_landing_page">' . __( 'Landing Page:', 'wp-leads-press' ) . '</label>';
			$ret .= '</td>';
						
			$ret .= '<td>';
			$ret .= '' . $leadLandingPage['post_title'] . '';
			$ret .= '</td>';
			
			$ret .= '</tr>';
						
			$ret .= '<tr>';
			
			$lead = get_post( $leadID );
							
			$ret .= '<td>';
			$ret .= '<label for="wplp_lead_status">' . __( 'Lead Status:', 'wp-leads-press' ) . '</label>';
			$ret .= '</td>';				
											
			$ret .= '<td>';			
			$ret .= '' . custom_taxonomies_terms($leadID) .'';
			$ret .= '</td>';
			$ret .= '</tr>';			
			
			$ret .= '<tr>';
			$ret .= '<td';
			$ret .= '<label for="wplp_lead_save">' . __( 'Update Lead:', 'wp-leads-press' ) . '</label>';
			$ret .= '</td>';				
			
			$ret .= '<td class="wplp-inputs">';
			$ret .= '<input type="submit" id="wplp_lead_save" name="wplp_lead_save" class="wplp-lead-save" value="Update Lead">';
			$ret .= '</td>';
			$ret .= '</tr>';		
			
			$ret .= '</table>';		
				
			$ret .= '</form>';
	
	}// end edit
			
		return $ret;
		
	} else {
		
		return __( 'You must be logged in to view your leads.', 'wp-leads-press' );	
		
	}
	
}
function wplp_get_company_names($companies){	

	$out = array();
	
	if ( !empty( $companies ) ) {

		foreach ( $companies as $company ) {
		  
			$companyObject = get_term_by('id', $company, 'wplp_opportunity');
			$out[] = $companyObject->name;
		
		}
		
	}
	
  return $out;
	
}
// get taxonomies terms links
function custom_taxonomies_terms($leadID){
	// get post by post id
	$post = get_post( $leadID );
	 
	// get post type by post
	$post_type = $post->post_type;
	
	//wplp_lead_status
	$lead_status = wp_get_post_terms( $leadID, 'wplp_lead_status', array("fields" => "names") );	
	//print_r($status);	
	
	// get taxonomy for lead
	$taxonomies = get_object_taxonomies( $post_type, 'objects' );
	//print_r($taxonomies);
	
	// Get all taxonomies for leads
	$args=array(
	'name' => 'wplp_lead_status'
	);
	$output = 'objects'; // or objects
	$status_options = get_taxonomies( $args, $output );
	
	$taxonomy = 'wplp_lead_status';
	$args = array(
	'orderby'       => 'name', 
	'order'         => 'ASC',
	'hide_empty'    => false, 
	); 
	$status_options = get_terms( $taxonomy, $args );
	
	$out = array();
	
	
	if ( !empty( $status_options ) ) {

		$out[] = '<select name="wplp_lead_status" id="wplp_lead_status">';
		  
		foreach ( $status_options as $status ) {
		  
			$out[] = '<option value="' . $status->term_id . '"' . selected( $lead_status[0], $status->name, false ) . '>' . $status->name . '</option>' . $status->name;
		
		}
		  
		$out[] = "</select>";
	
	}
	
  return implode('', $out );

}

###
# display form to enter affiliate ID's and to join opportunity
###

function wplp_affiliate_settings_display($atts) {
	global $wplp_admin, $wpdb, $current_user, $post_id, $_GET, $_POST;
	# Add 'company' attribute
	extract( shortcode_atts( array(
		'company' => 0,
		'override' => 'no',
		//'imglink' => ""
	), $atts ) );
	
	$company = explode(', ', $company);
	
	$companyID = $company;
			
	$options = wp_load_alloptions();
	
		// Check if user is logged in
		if ( is_user_logged_in() ) {
				
			if ( isset( $_GET['action'] ) ) {
				
				$data = $_GET['action'];
				
				$action_data = 'wplp '.$data;
				
				if ( ( strpos( $action_data, "save" ) !== false ) || ( strpos( $action_data, "updated" ) !== false ) ) {
					
					list($action, $lead) = explode( ":", $data, 2 );
					
					// Sanitize the lead value to only have lead ID
					list($title, $lead) = explode( "=", $lead, 2 );
					//$title holds the lead=
					//$lead holds the lead ID
			
				} else {
					
					$action = $_GET['action'];
				
				}
								
			} else {
				
				$action = '';
				
			}	
			
			// Check for passed in company ids
			if( $companyID != 0 ) {
				
				// Get data of companies
				$taxonomies = 'wplp_opportunity';
				$args = array(
					'orderby'       => 'name', 
					'order'         => 'ASC',
					'hide_empty'    => true, 
					'exclude'       => array(), 
					'exclude_tree'  => array(), 
					'include'       => $companyID[0],
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
				
			} else {
				
				// Get data of companies
				$taxonomies = 'wplp_opportunity';
				$args = array(
					'orderby'       => 'name', 
					'order'         => 'ASC',
					'hide_empty'    => true, 
					'exclude'       => array(), 
					'exclude_tree'  => array(), 
					//'include'       => $company[0],
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
				
			}
						
			# Get data from user table
			$query='SELECT * FROM '.$wpdb->prefix.'users WHERE ID = ' . $current_user->ID . '';
			$data = $wpdb->get_results($query, 'ARRAY_A');
			$userName = $data[0]['user_login'];
			
			//Get meta data for user
			$user_id = $current_user->ID;
			$metaData = get_user_meta($user_id);	
			
			$referrer = $metaData['wplp_referrer_id'][0];
								
			$ret = '<table class="wplp-affiliate-settings">';
									
			$ret .= '<form id="wplp_update_affiliate" name="wplp_update_affiliate" action="#" method="post">';
			
			$ret .= '<tr>';	
			$ret .= '<td><b>' . __( 'Company', 'wp-leads-press' ) . '</b></td><td><b>' . __( 'Affiliate ID', 'wp-leads-press' ) . '</b></td><td></td>';
			$ret .= '</tr>';
			
			if( !empty( $companies ) ) {
					
				foreach ( $companies as $company ) {
					
					$user_id = $current_user->ID;
					$metaData = get_user_meta($user_id);
					
					$trackingID = 'wplp_tracking_id_'.$company->slug;
					
					if( isset($metaData[$trackingID][0]) ){
						
						$trackingID = $metaData[$trackingID][0];
						
					} else {
						
						$trackingID = NULL;	
						
					}
					
					// Do check to see if campaign is primary dest url
					

					if( $companyID != 0 || $override == 'yes' ) { // If company is manually selected and 'allow override' is yes show campaigns
						
						
						if( $override == 'yes' ) {
							
							// Get all campaigns for the company/opp		
							$args = array(
								 
								'posts_per_page' => -1,
								'orderby' => 'title',
								'order' => 'ASC',
								'post_type' => 'campaign',
								'wplp_opportunity' => $company->slug,
								//'post_status' => 'publish',
								'meta_query' => array(
									'relation' => 'AND',
									array(
										'key' => 'wplp_campaign_is_active',
										'value' => array( 'yes', 'no' ),
									),
									
									array(
										'key' => 'wplp_show_as_affiliate_link',
										'value' => 'yes',
									)
									
								)
							
							);
							
							$campaigns = get_posts( $args );
						
						} else {

							// Get all campaigns for the company/opp		
							$args = array(
								 
								'posts_per_page' => -1,
								'orderby' => 'title',
								'order' => 'ASC',
								'post_type' => 'campaign',
								'wplp_opportunity' => $company->slug,
								//'post_status' => 'publish',
								'meta_query' => array(
								'relation' => 'AND',
									array(
										'key' => 'wplp_campaign_is_active',
										'value' => 'yes',
									),
								
									array(
										'key' => 'wplp_show_as_affiliate_link',
										'value' => 'yes',
									)
								)
								
							);
							
							$campaigns = get_posts( $args );										
							
						}
						
					} else {
						
						// Get all campaigns for the company/opp		
						$args = array(
							 
							 'posts_per_page' => -1,
							 'orderby' => 'title',
							 'order' => 'ASC',
							 'post_type' => 'campaign',
							 'wplp_opportunity' => $company->slug,
							 //'post_status' => 'publish',
							 'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => 'wplp_campaign_is_active',
									'value' => 'yes',
								),
								
								array(
									'key' => 'wplp_show_as_affiliate_link',
									'value' => 'yes',
								)
								
							 )
						);
						
						$campaigns = get_posts( $args );				
							
					} 
				
					// if there are active campaigns for a company, show company
					if( $campaigns == TRUE ) {
		
						$ret .= '<tr>';
						$ret .= '<td>' . $company->name . '</td>';
						$ret .= "<td><input type='text' value='".$trackingID."' name='wplp_tracking_id_".$company->slug."' id='wplp_tracking_id_".$company->slug."' size='20' /></td>";
																			
					}
					
					if( !empty( $campaigns ) ) {

						$ret .= '<td>';
												
						foreach ( $campaigns as $campaign ) {	
						
							$campaign_url = get_post_meta( $campaign->ID, 'wplp_campaign_url', true );
							$campaign_url_trailing_value = get_post_meta ( $campaign->ID, 'wplp_campaign_url_trailing_value', true );	
							
							$campaign_is_subdomain = get_post_meta( $campaign->ID, 'wplp_campaign_is_subdomain', true );
							$campaign_is_https = get_post_meta( $campaign->ID, 'wplp_campaign_is_https', true );
							
							// Get the company associated with campaign
							$taxonomy = 'wplp_opportunity';
							$campaign_opportunity = array();
							$campaign_opportunity = get_the_terms( $campaign->ID, $taxonomy );
								
							//Get meta data for user
							$user_id = $current_user->ID;
							$metaData = get_user_meta($user_id);	
							
							$referrer = $metaData['wplp_referrer_id'][0];
					 
							// Get the ref user tracking ID for opportunity
							$key = 'wplp_tracking_id_' . $company->slug;
							$wplp_ref_tracking_id = get_user_meta($referrer, $key, true); 
							
							// If $wplp_ref_tracking_id == FALSE Get affiliate ID of first ancestor of refferrer who has affiliate ID set for the opportunity.
							// also check if ancestor is banned, then get another upline referrer
							if($wplp_ref_tracking_id == FALSE || wplp_user_banned( $referrer ) ) {
								
								// Get the ancestors of user
								$ancestors = wplp_get_ancestors( $user_id );
																
								// Get default referrer value for campaign
								$campaign_default_referrer_ID =  wplp_get_campaign_field_value('wplp_campaign_default_referrer_id', $campaign->ID);
								
								if( isset( $campaign_default_referrer_ID ) && !empty( $campaign_default_referrer_ID ) ){
										
									$defaultUser = $campaign_default_referrer_ID;
								
								} elseif( isset($options['wp_leads_press_default_ancestor'] ) && !empty( $options['wp_leads_press_default_ancestor'] ) && empty( $campaign_default_referrer_ID ) ){
									
									$defaultUser = $options['wp_leads_press_default_ancestor'];
									
								} else {
									
									$defaultUser = 1;	
									
								}								
								
								// Get default ancestor value, used to give out default join links.								
								if ( ! wplp_get_ancestors( $user_id ) ){
									
									$ancestors = array( $defaultUser );		
									
								}
								
								if( !empty( $ancestors ) ){
									
									foreach( $ancestors as $ancestor ) {
										
										// Get the ref user tracking ID for opportunity
										$key = 'wplp_tracking_id_' . $company->slug;
										$wplp_ref_tracking_id = get_user_meta($ancestor, $key, true);											
										
										// Check if ancestor has id set and is not blocked
										if( $wplp_ref_tracking_id == TRUE && ! wplp_user_banned($ancestor) ) {										
											
											// first ancestor to work, break out of foreach
											break;
											
										} // end if $wplp_ref_tracking_id == TRUE
									
									} // end foreach $ancestors as $ancestor
									
								}// end !empty ancestors
								
							} // end if $wplp_ref_tracking_id == FALSE
							
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
								
								$wplp_redirect_url = $preUrl . $campaign_url . $wplp_ref_tracking_id . $campaign_url_trailing_value;
								
							}
							
							$ret .= '<a href="' . $wplp_redirect_url . '" class="ui-button ui-button-text ui-corner-all wplp-button" target="_blank">Join Now</a>';
							
							
							
						} // End foreach campaign as campaign
						
						$ret .= '</td>';

						$ret .= '</tr>';						
						
					} // end if !empty campaigns						
						
				} // For each company as company						
			
			} // End if !empty Companies
			
		$ret .= '<tr>';
		
		$ret .= '<td>';
		$ret .= '<label for="wplp_affiliate_save">' . __( 'Update Affiliate ID Settings:', 'wp-leads-press' ) . '</label>';
		$ret .= '</td>';				
		
		$ret .= '<td class="wplp-inputs">';
		$ret .= '<input type="submit" id="wplp_affiliate_save" name="wplp_affiliate_save" class="wplp-affiliate-save" value="' . __( 'Update Settings', 'wp-leads-press' ) . '">';
		$ret .= '</td>';
		
		$ret .= '<td>';
		$ret .= '&nbsp;';
		$ret .= '</td>';		
		
		$ret .= '</tr>';
		
		$ret .= '</form>';	
	
		$ret .= '</table>';	
			
		return $ret; // Return the list	
		
		} else { // End if user is logged in
				
			return __( 'You must be logged in to edit your settings.', 'wp-leads-press' );
				
		}
}

function wplp_join_links($atts) {
	global $wplp_admin, $wpdb, $current_user, $post_id, $_GET, $_POST;
	# Add 'company' attribute
	extract( shortcode_atts( array(
		'company' => 0,
		'override' => 'no',
		//'imglink' => ""
	), $atts ) );
	
	$company = explode(', ', $company);
	
	$companyID = $company;
			
	$options = wp_load_alloptions();
	
		// Check if user is logged in
		if ( is_user_logged_in() ) {
			
			// Check for passed in company ids
			if( $companyID != 0 ) {
				
				// Get data of companies
				$taxonomies = 'wplp_opportunity';
				$args = array(
					'orderby'       => 'name', 
					'order'         => 'ASC',
					'hide_empty'    => true, 
					'exclude'       => array(), 
					'exclude_tree'  => array(), 
					'include'       => $companyID[0],
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
				
			} else {
				
				// Get data of companies
				$taxonomies = 'wplp_opportunity';
				$args = array(
					'orderby'       => 'name', 
					'order'         => 'ASC',
					'hide_empty'    => true, 
					'exclude'       => array(), 
					'exclude_tree'  => array(), 
					//'include'       => $company[0],
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
				
			}
						
			# Get data from user table
			$query='SELECT * FROM '.$wpdb->prefix.'users WHERE ID = ' . $current_user->ID . '';
			$data = $wpdb->get_results($query, 'ARRAY_A');
			$userName = $data[0]['user_login'];
			
			//Get meta data for user
			$user_id = $current_user->ID;
			$metaData = get_user_meta($user_id);	
			
			$referrer = $metaData['wplp_referrer_id'][0];
								
			$ret = '<table class="wplp-join-links">';
			//$ret .= '<p>' . __('Join program(s) below.', 'wp-leads-press' ) . '</p>';
									
			$ret .= '<tr>';	
			//$ret .= '<td><b>' . __( 'Company', 'wp-leads-press' ) . '</b></td><td><b>' . __( 'Click To Join: ', 'wp-leads-press' ) . '</b></td>';
			$ret .= '<td><b>' . __( 'Company', 'wp-leads-press' ) . '</b></td><td></td>';			
			$ret .= '</tr>';
			
			if( !empty( $companies ) ) {
					
				foreach ( $companies as $company ) {
					
					$user_id = $current_user->ID;
					$metaData = get_user_meta($user_id);
					
					$trackingID = 'wplp_tracking_id_'.$company->slug;
					
					if( isset($metaData[$trackingID][0]) ){
						
						$trackingID = $metaData[$trackingID][0];
						
					} else {
						
						$trackingID = NULL;	
						
					}
										

					if( $companyID != 0 || $override == 'yes' ) { // If company is manually selected and 'allow override' is yes show campaigns
						
						
						if( $override == 'yes' ) {
							
							// Get all campaigns for the company/opp		
							$args = array(
								 
								'posts_per_page' => -1,
								'orderby' => 'title',
								'order' => 'ASC',
								'post_type' => 'campaign',
								'wplp_opportunity' => $company->slug,
								//'post_status' => 'publish',
								'meta_query' => array(
									'relation' => 'AND',
									array(
										'key' => 'wplp_campaign_is_active',
										'value' => array( 'yes', 'no' ),
									),
									
									array(
										'key' => 'wplp_show_as_affiliate_link',
										'value' => 'yes',
									)
									
								)
							
							);
							
							$campaigns = get_posts( $args );
						
						} else {

							// Get all campaigns for the company/opp		
							$args = array(
								 
								'posts_per_page' => -1,
								'orderby' => 'title',
								'order' => 'ASC',
								'post_type' => 'campaign',
								'wplp_opportunity' => $company->slug,
								//'post_status' => 'publish',
								'meta_query' => array(
								'relation' => 'AND',
									array(
										'key' => 'wplp_campaign_is_active',
										'value' => 'yes',
									),
								
									array(
										'key' => 'wplp_show_as_affiliate_link',
										'value' => 'yes',
									)
								)
								
							);
							
							$campaigns = get_posts( $args );										
							
						}
						
					} else {
						
						// Get all campaigns for the company/opp		
						$args = array(
							 
							 'posts_per_page' => -1,
							 'orderby' => 'title',
							 'order' => 'ASC',
							 'post_type' => 'campaign',
							 'wplp_opportunity' => $company->slug,
							 //'post_status' => 'publish',
							 'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => 'wplp_campaign_is_active',
									'value' => 'yes',
								),
								
								array(
									'key' => 'wplp_show_as_affiliate_link',
									'value' => 'yes',
								)
								
							 )
						);
						
						$campaigns = get_posts( $args );				
							
					} 
				

				
					// if there are active campaigns for a company, show company
					if( $campaigns == TRUE ) {
		
						$ret .= '<tr>';
						$ret .= '<td>' . $company->name . '</td>';
							
					
					}
					
					if( !empty( $campaigns ) ) {
												
						$ret .= '<td>';						
						
						foreach ( $campaigns as $campaign ) {	
						
							$campaign_url = get_post_meta( $campaign->ID, 'wplp_campaign_url', true );
							$campaign_url_trailing_value = get_post_meta ( $campaign->ID, 'wplp_campaign_url_trailing_value', true );	
							
							$campaign_is_subdomain = get_post_meta( $campaign->ID, 'wplp_campaign_is_subdomain', true );
							$campaign_is_https = get_post_meta( $campaign->ID, 'wplp_campaign_is_https', true );
							
							// Get the company associated with campaign
							$taxonomy = 'wplp_opportunity';
							$campaign_opportunity = array();
							$campaign_opportunity = get_the_terms( $campaign->ID, $taxonomy );
								
							//Get meta data for user
							$user_id = $current_user->ID;
							$metaData = get_user_meta($user_id);	
							
							$referrer = $metaData['wplp_referrer_id'][0];
					 
							// Get the ref user tracking ID for opportunity
							$key = 'wplp_tracking_id_' . $company->slug;
							$wplp_ref_tracking_id = get_user_meta($referrer, $key, true); 
							
							// If $wplp_ref_tracking_id == FALSE Get affiliate ID of first ancestor of refferrer who has affiliate ID set for the opportunity.
							// also check if ancestor is banned, then get another upline referrer
							if($wplp_ref_tracking_id == FALSE || wplp_user_banned( $referrer ) ) {
//							if( empty( $wplp_ref_tracking_id ) || wplp_user_banned( $referrer ) ) {
								
								// Get the ancestors of user
								$ancestors = wplp_get_ancestors( $user_id );
								
								// Get default referrer value for campaign
								$campaign_default_referrer_ID =  wplp_get_campaign_field_value('wplp_campaign_default_referrer_id', $campaign->ID);
								//$campaign_default_referrer_ID = get_the_terms( $campaign->ID, 'wplp_campaign_default_referrer_id' );
								
								if( isset( $campaign_default_referrer_ID ) && !empty( $campaign_default_referrer_ID ) ){
										
									$defaultUser = $campaign_default_referrer_ID;
								
								} elseif( isset($options['wp_leads_press_default_ancestor'] ) && !empty( $options['wp_leads_press_default_ancestor'] ) && empty( $campaign_default_referrer_ID ) ){
									
									$defaultUser = $options['wp_leads_press_default_ancestor'];
									
								} else {
									
									$defaultUser = 1;	
									
								}	
								
								
								if ( ! wplp_get_ancestors( $user_id ) ){
									
									$ancestors = array( $defaultUser );		
									
								}
								
								if( !empty( $ancestors ) ){
									
									foreach( $ancestors as $ancestor ) {
										
										// Get the ref user tracking ID for opportunity
										$key = 'wplp_tracking_id_' . $company->slug;
										$wplp_ref_tracking_id = get_user_meta($ancestor, $key, true);											
										
										// Check if ancestor has id set and is not blocked
										if( $wplp_ref_tracking_id == TRUE && ! wplp_user_banned($ancestor) ) {										
											
											// first ancestor to work, break out of foreach
											break;
											
										} // end if $wplp_ref_tracking_id == TRUE
									
									} // end foreach $ancestors as $ancestor
									
								}// end !empty ancestors
								
							} // end if $wplp_ref_tracking_id == FALSE
							
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
								
								$wplp_redirect_url = $preUrl . $campaign_url . $wplp_ref_tracking_id . $campaign_url_trailing_value;
								
							}
							
							
							// Check if user has ancestors above them
							$ret .= '<a href="' . $wplp_redirect_url . '" class="ui-button ui-button-text ui-corner-all wplp-button" target="_blank">Join Now</a>';
	
						} // End foreach campaign as campaign
						
						$ret .= '</td>';
						
						$ret .= '</tr>';						
						
					} // end if !empty campaigns
											
				} // For each company as company						
			
			} // End if !empty Companies
		
		$ret .= '<tr>';
		
		$ret .= '</tr>';
			
		$ret .= '</table>';	
			
		return $ret; // Return the list	
		
		} else { // End if user is logged in
				
			return __( 'You must be logged in to get your links to join programs.', 'wp-leads-press' );
				
		}
}
add_shortcode( 'wplp_join_links', 'wplp_join_links' );


function wplp_landing_pages_display($atts){
	global $wplp_admin, $wpdb, $current_user, $post_id, $_GET, $_POST;
	
	# Add 'company' attribute
	extract( shortcode_atts( array(
		'company' => 0,
		'override' => 'no',
		'homepage' => 'on',
	), $atts ) );
	
	$company = explode(',', $company);

	// find meta of posts with 'wplp_campaign_selected' get value of post ID
	// for each campaign display posts with matching post id of campaign
	
	// Get all campaigns which are active.
	$args = array(
		'post_type' => 'campaign',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
		'meta_query' => array(
			array(
				'key' => 'wplp_campaign_is_active',
				'value' => 'yes'	
			)
		)
	);		
	
	// Check for passed in opp ids
	//if( $company != 0 ) {
	if ( isset( $company ) && !empty( $company ) && $company[0] != 0 ) {
		// Get all campaigns which are active.
		$args = array(
			'post_type' => 'campaign',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'wplp_campaign_is_active',
					'value' => 'yes'	
				)
			),
			'tax_query' => array(
				array(
					'taxonomy' => 'wplp_opportunity',
					'field' => 'id',
					'terms' => $company,
				)
			)
		);
		
		if ( $override == 'yes' ) {
	
			// Get all campaigns, active or inactive.
			$args = array(
				'post_type' => 'campaign',
				'posts_per_page' => -1,
				'orderby' => 'title',
				'order' => 'ASC',
				'meta_query' => array(
					array(
						'key' => 'wplp_campaign_is_active',
						'value' => array( 'yes', 'no' ),	
					)
				),
				'tax_query' => array(
					array(
						'taxonomy' => 'wplp_opportunity',
						'field' => 'id',
						'terms' => $company,
					)
				)
			);		
			
		} // end if override = yes
		
	}
	
	$campaigns = new WP_Query( $args );	
	
	$opps = $campaigns->get_posts();
	
	$campaigns = $campaigns->get_posts();	
	
	# Get data from user table
	//$query='SELECT * FROM '.$wpdb->prefix.'users WHERE ID = ' . $current_user->ID . '';
	//$data = $wpdb->get_results($query, 'ARRAY_A');
	//$userName = $data[0]['user_login'];
	
	//Get meta data for user
	$user_id = $current_user->ID;
	$metaData = get_user_meta($user_id);			
	$referrer = $metaData['wplp_referrer_id'][0];
	$site_url = get_site_url();
	$home_page_ref_link = $site_url . '/?ref=' . $current_user->user_nicename;
	
	if ( is_user_logged_in() ) {
			
		$ret = '<div id="wplp-landing-page-links">';
			
		$ret .= '<h2>' . __( 'Landing Page Links', 'wp-leads-press' ) . '</h2>';
		$ret .= '<p>' . __( 'Send traffic to these links to generate leads and signups.', 'wp-leads-press' ) . '</p>';
		
		if ( $homepage == 'on' ) {
		
			$ret .= '<p><b>' . __( 'Home Page Link: ', 'wp-leads-press' ) . '</b><a href="' . $home_page_ref_link . '">' . $home_page_ref_link . '</a></p>';			
		
		}
				
		if( !empty( $opps ) ) {
		
			foreach ( $opps as $opp ) {
				
				$user_id = $current_user->ID;
				$metaData = get_user_meta($user_id);
				
				$ret .= '<div id="accordion">';
								
				$ret .= '<h4><b>'.$opp->post_title.' ' . __( '', 'wp-leads-press' ) . '</b></h4>';
				$ret .= '<p>';
				
				$ret .= '<b>' . __( 'Your Referral Links', 'wp-leads-press' ) . '</b><br  /><br />';
												
					// For each loop to pull the posts for each campaign
					foreach ( $campaigns as $campaign ) {
		
						// Get all posts
						$args = array(
							'post_type' => 'page',
							'posts_per_page' => -1,
							'orderby' => 'title',
							'order' => 'ASC',
							'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => 'wplp_campaign_selected',
									'value' => $opp->ID,
									//'compare' => 'LIKE'
								),
								
								array(
									'key' => 'wplp_page_is_active',
									'value' => 'yes',
	
								)
							)
		
						);
						
						$landing_pages = new WP_Query( $args );
						$landing_pages = $landing_pages->get_posts();
						
						if ( isset( $landing_pages ) && !empty( $landing_pages ) ) {
									
								foreach ( $landing_pages as $landing_page ) {
									
									// Gets landing page campaign ID
									$landing_page_campaign_selected = get_post_meta( $landing_page->ID, 'wplp_campaign_selected', TRUE );	//ID of meta value	
									
									if ( $campaign->ID == $landing_page_campaign_selected ) {				
		
										// Gets name of company for campaign
										//$terms = wp_get_post_terms( $post_id, $taxonomy, $args );
										$terms = wp_get_post_terms( $landing_page_campaign_selected, 'wplp_opportunity', array("fields" => "all") );		
											
//			error_log( print_r($terms), true );
									
										// Get the name of company 
										$opp_company = wp_get_post_terms( $opp->ID, 'wplp_opportunity', array("fields" => "all") );
										
//			error_log( print_r($opp_company), true );
							
										// And if campaign is set for post					
										if ( $terms[0]->slug == $opp_company[0]->slug ){
																					
											$ret .= '<span style="display: block; width: 30%; float: left; clear: left">' . $landing_page->post_title . '</span>';
											
											if ( ( $landing_page->ID = $landing_page_campaign_selected ) && ( $terms[0]->slug = $opp_company[0]->slug ) ) {
																						
												$data = $landing_page->guid;
												list($link_url, $endOfUrl) = explode("?", $data);
												
												$ret .= '<span style="display: block; width: 65%; float: left; margin-left: 1%; clear: right;">';
												$ret .= '<a href="' . $link_url . $landing_page->post_name . '/?ref=' . $current_user->user_nicename . '">' . $link_url . $landing_page->post_name . '/?ref=' . $current_user->user_nicename . '</a>';	
												$ret .= '</span><br /><br />';
																							
											}
										
										} //end if 
									
									} // end if campaign selected == campaign->ID
									
								} // end foreach landing pages and landing page
								
						} // end if $landing_pages isset
										
					} // end foreach campaigns as campaign
				
				$ret .= '</p>';
						
				$ret .= '</div>'; // End div accordion
							
			} // end foreach opps as opp
		
		} // end if !empty opps		
			
			
		//$ret .= '</form>';	
		$ret .= '</div>'; // end div wplp-landing-page-links	
		return $ret;
		
	} // End if user is logged in
			
	return __( 'You must be logged in to view your landing pages.', 'wp-leads-press' );
	
}

function wplp_bonus_leads_display(){
	global $wplp_admin, $wpdb, $current_user, $post_id, $_GET, $_POST;
	
	if ( is_user_logged_in() ) {
		
		//Get all wplp options
		$options = wp_load_alloptions();
		
		//Get starting leads
		$randomLeadsMax = $options['wp_leads_press_max_random_leads_allowed'];
		
		//Get users who can have random leads
		$select_ids = $options['wp_leads_press_select_user_ids'];
		$random_ids = $options['wp_leads_press_ids_for_random_traffic'];
		$random_ids = explode(',', $random_ids);
		
		//Get meta data for user
		$user_id = $current_user->ID;
		$metaData = get_user_meta($user_id);
		
		$bonusLeadsValue = $options['wp_leads_press_ref_lead_bonus_leads_value'];
		
		$bonusMembersValue = $options['wp_leads_press_ref_member_bonus_leads_value'];
		$bonusVisitsValue = $options['wp_leads_press_ref_traffic_bonus_leads_value'];
		
		$req_personal_leads = $options['wp_leads_press_personally_referred_leads_required']; 
		$refLeads = $metaData['wplp_ref_lead_count'][0];
		
		$value = $req_personal_leads-$refLeads;
		
		if ( $value == 0 || empty($value) ) {
			
			$req_personal_leads = $req_personal_leads;	
			
		} else {
		
			$req_personal_leads = $req_personal_leads-$refLeads;
		
		}
			
		
		$req_personal_members = $options['wp_leads_press_ref_member_count_required']; 
		
		//$refmembers = isset($metaData['wplp_ref_member_count'][0] ) ? $metaData['wplp_ref_member_count'] : 0;
		$refmembers = $metaData['wplp_ref_member_count'][0];
			
		$value = $req_personal_members-$refmembers;
		
		if ( $value == 0 || empty($value) ) {
			
			$req_personal_members = $req_personal_members;	
			
		} else {
		
		$req_personal_members = $req_personal_members-$refmembers;	
		
	
		}
		
		//Required Visits
		$req_personal_visits = $options['wp_leads_press_ref_traffic_count_required']; 
		
		if( isset( $metaData['wplp_ref_traffic_count'][0] ) ){
			
			$refVisits = $metaData['wplp_ref_traffic_count'][0];	
			
		} else {
			
			$refVisits = 0;
			
		}
		
		$value = $req_personal_visits-$refVisits;
		
		if ( $value == 0 || empty($value) ) {
			
			$req_personal_visits = $req_personal_visits;	
			
		} else {
		
			$req_personal_visits = $req_personal_visits-$refVisits;				
			
		}					
		
		// Return results	
		if ( $options['wp_leads_press_max_random_leads_allowed'] !=0 ) {
		
			$ret = '<h2>' . __( 'Earn More Leads/Visits!', 'wp-leads-press' ) . '</h2>';
		
		} else {
			
			$ret = '<h2>' . __( 'Bonus Leads Earned', 'wp-leads-press' ) . '</h2>';	
			$ret .= '<p>' . __( 'As random traffic comes into the site it is distributed to our members, there is no limit to the amount of traffic you can earn by sending traffic to your landing pages, turning into leads, signups or sales.', 'wp-leads-press' ) . '</p>';
			
			
		}
		
		if( ( $select_ids == "on" ) && ( !in_array( $user_id, $random_ids ) ) ) {
			
			return __( 'You are not eligible to receive bonus leads.', 'wp-leads-press' );
			
		}
		
		if ( ( $options['wp_leads_press_personally_referred_leads_required'] != 0 ) || ( $options['wp_leads_press_ref_member_count_required'] != 0 ) || ( $options['wp_leads_press_ref_traffic_count_required'] != 0 ) || ( $options['wp_leads_press_max_random_leads_allowed'] !=0 ) ){			
		
		
			$ret .= wplp_bonus_leads_traffic();
			
			if ( $options['wp_leads_press_max_random_leads_allowed'] !=0 ) {
				
				$ret .= '<p>' . __( 'We get you started with ' . $randomLeadsMax . ' bonus leads or visitors from our search engine traffic to help you build your business. <br /><br />***Leads and visitors from search engine results are distributed randomly to our members as generated.***', 'wp-leads-press' ) . '</p>';	
			
				$ret .= '<p>' . __( 'Earning bonus leads and traffic is easy, simply use the website, driving traffic to your landing page links and you will earn bonuses as shown below:', 'wp-leads-press' ) . '</p>';
			
			}
		
		}
		
		if ( $options['wp_leads_press_max_random_leads_allowed'] !=0 ) {
			
			if( $options['wp_leads_press_personally_referred_leads_required'] != 0 ){
				
				$ret .= '<p><b>' . __( 'Bonus Earned When You Generate Personal Leads:', 'wp-leads-press' ) . '</b><br /><br />' . __( 'Referred Leads Required For Next Bonus: ', 'wp-leads-press' ) . '[' . $req_personal_leads . '] <br />' . __( 'Bonus Amt: ', 'wp-leads-press' ) . ' [' . $bonusLeadsValue . '] <br /></p>';
			
			}
	
			if( $options['wp_leads_press_ref_member_count_required'] != 0 ){
	
				$ret .= '<p><b>' . __( 'Bonus Earned When You Generate Site Members:', 'wp-leads-press' ) . '</b><br /><br />' . __( 'Referred Members Required For Next Bonus: ', 'wp-leads-press' ) . ' ' . '[' . $req_personal_members . '] <br />' . __( 'Bonus Amt: ', 'wp-leads-press' ) . ' [' . $bonusMembersValue . '] <br /></p>';
				
			}
	
			if( $options['wp_leads_press_ref_traffic_count_required'] != 0 ){
	
				$ret .= '<p><b>' . __( 'Bonus Earned When You Generate Site Visits:', 'wp-leads-press' ) . '</b><br /><br />' . __( 'Referred Visits Required For Next Bonus: ', 'wp-leads-press' ) . ' ' . '[' . $req_personal_visits . '] <br />' . __( 'Bonus Amt: ', 'wp-leads-press' ) . ' [' . $bonusVisitsValue . '] <br /></p>';
	
			}
			
		} // end if options max random leads != 0
			
			
		return $ret;
		
	} else {
			
		return __( 'You must be logged in to view your bonus leads and visits.', 'wp-leads-press' );	
			
	}
	
}

function wplp_bonus_leads_traffic(){
	global $current_user;
	
	//Get all wplp options
	$options = wp_load_alloptions();
	
	//Get meta data for user
	$user_id = $current_user->ID;
	$metaData = get_user_meta($user_id);			
	
	//Get values
	if( isset($metaData['wplp_bonus_leads'][0] ) ){
		
		$bonus_leads_earned = $metaData['wplp_bonus_leads'][0];
		
	} else {
		
		$bonus_leads_earned = 0;
		
	}
	
	$max_random = $options['wp_leads_press_max_random_leads_allowed'];
	
	if( isset($metaData['wplp_total_random_lead_count'][0] ) ) {
		
		$total_random = $metaData['wplp_total_random_lead_count'][0];
		
	} else {
		
		$total_random = 0;	
		
	}
		
	$random_leads_remaining = $max_random - $total_random + $bonus_leads_earned;
	
	if( $random_leads_remaining <= 0 ) {
		
		$random_leads_remaining = 0;
		
	}
				
	//$url = $_SERVER['REQUEST_URI'];
	//list($url_location, $url_end) = explode("?", $url, 2);	
	
	$out = array();
	
	$out[] = '<table width="100%">';
	
	$out[] = '<th>';
	
	$out[] = '<tr>';
	
	if ( ( $options['wp_leads_press_max_random_leads_allowed'] != -1 ) || ( $options['wp_leads_press_personally_referred_leads_required'] != 0 ) || ( $options['wp_leads_press_ref_member_count_required'] != 0 ) || ( $options['wp_leads_press_ref_traffic_count_required'] != 0 ) ){
			
			if( isset($options['wp_leads_press_max_random_leads_allowed']) ) {			
			
				if( $options['wp_leads_press_max_random_leads_allowed'] != -1 ) {
			
					$bonusVal = '<b>' . __( 'Bonus Leads/Visits Remaining:', 'wp-leads-press' ) . '</b> [' . $random_leads_remaining . '] ';
			
				} else {
					
					$bonusVal = '<b>' . __( 'Bonus Leads/Visits Remaining:', 'wp-leads-press' ) . '</b> [UNLIMITED] ';
				
				}
				
				$out[] = $bonusVal;
				
				
			}
	
	} 
	

	
	$out[] = '</td>';		
										
	$out[] = '</tr>';
	
	$out[] = '</th>';
 
	$out[] = '</table>';
	
	return implode( '', $out );
	
}



add_shortcode( 'wplp_autoresponder_settings', 'wplp_autoresponder_settings' );
function wplp_autoresponder_settings($atts) {
	global $wplp_admin, $wpdb, $current_user, $post_id, $_GET, $_POST;
	# Add 'company' attribute
	extract( shortcode_atts( array(
		'company' => 0,
		'override' => 'no',
	), $atts ) );
	
	$company = explode(', ', $company);
	
	$companyID = $company;
			
	$options = wp_load_alloptions();
	
		// Check if user is logged in
		if ( is_user_logged_in() ) {
					
			// Check for passed in company ids
			if( $companyID != 0 ) {
				
				// Get data of companies
				$taxonomies = 'wplp_opportunity';
				$args = array(
					'orderby'       => 'name', 
					'order'         => 'ASC',
					'hide_empty'    => true, 
					'exclude'       => array(), 
					'exclude_tree'  => array(), 
					'include'       => $companyID[0],
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
				
			} else {
				
				// Get data of companies
				$taxonomies = 'wplp_opportunity';
				$args = array(
					'orderby'       => 'name', 
					'order'         => 'ASC',
					'hide_empty'    => true, 
					'exclude'       => array(), 
					'exclude_tree'  => array(), 
					//'include'       => $company[0],
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
				
			}
						
			# Get data from user table
			$query='SELECT * FROM '.$wpdb->prefix.'users WHERE ID = ' . $current_user->ID . '';
			$data = $wpdb->get_results($query, 'ARRAY_A');
			$userName = $data[0]['user_login'];
			
			//Get meta data for user
			$user_id = $current_user->ID;
			$metaData = get_user_meta($user_id);	
			
			$referrer = $metaData['wplp_referrer_id'][0];
								
			$ret = '<div class="wplp-autoresponder-settings">';
			
			$ret .= '<h2>' . __ ( 'Autoresponder Integration',  'wp-leads-press' ) .'</h2>';			
			$ret .= '<p>' .__( 'Use either API or HTML Form Integration method', 'wp-leads-press' ) . '</p>';

									
			$ret .= '<form id="wplp_update_autoresponder" name="wplp_update_autoresponder" action="#" method="post">';
			
			if( !empty( $companies ) ) {
					
				foreach ( $companies as $company ) {
					
					$user_id = $current_user->ID;
					$metaData = get_user_meta($user_id);
					
					// Add values for ar form integration
					$campaign_ar_list_on = 'wplp_campaign_ar_list_on_'.$company->slug;					
					$form_action_url = 'wplp_form_action_url_'.$company->slug;
					$form_name = 'wplp_form_name_'.$company->slug;
					$form_fname = 'wplp_form_fname_'.$company->slug;
					$form_lname = 'wplp_form_lname_'.$company->slug;
					$form_email = 'wplp_form_email_'.$company->slug;
					$form_phone = 'wplp_form_phone_'.$company->slug;
					$form_custom_name = 'wplp_form_custom_name_'.$company->slug;
					$form_custom_val = 'wplp_form_custom_val_'.$company->slug;

					$form_custom_name1 = 'wplp_form_custom_name1_'.$company->slug;
					$form_custom_val1 = 'wplp_form_custom_val1_'.$company->slug;
					
					$form_custom_name2 = 'wplp_form_custom_name2_'.$company->slug;
					$form_custom_val2 = 'wplp_form_custom_val2_'.$company->slug;
					
					$form_custom_name3 = 'wplp_form_custom_name3_'.$company->slug;
					$form_custom_val3 = 'wplp_form_custom_val3_'.$company->slug;
					
					$form_custom_name4 = 'wplp_form_custom_name4_'.$company->slug;
					$form_custom_val4 = 'wplp_form_custom_val4_'.$company->slug;																				
				
 					if( isset($metaData[$campaign_ar_list_on][0]) ){
						
						$campaign_ar_list_on = $metaData[$campaign_ar_list_on][0];
						
					} else {

						$campaign_ar_list_on = NULL;

					}
					
					
					if( isset($metaData[$form_action_url][0]) ){
						
						$form_action_url = $metaData[$form_action_url][0];
						
					} else {

						$form_action_url = NULL;

					}
					
					
					if( isset($metaData[$form_name][0]) ){
						
						$form_name = $metaData[$form_name][0];
						
					} else {

						$form_name = NULL;

					}

					if( isset($metaData[$form_fname][0]) ){
						
						$form_fname = $metaData[$form_fname][0];
						
					} else {

						$form_fname = NULL;

					}

					if( isset($metaData[$form_lname][0]) ){
						
						$form_lname = $metaData[$form_lname][0];
						
					} else {

						$form_lname = NULL;

					}

					if( isset($metaData[$form_email][0]) ){
						
						$form_email = $metaData[$form_email][0];
						
					} else {

						$form_email = NULL;

					}

					if( isset($metaData[$form_phone][0]) ){
						
						$form_phone = $metaData[$form_phone][0];
						
					} else {

						$form_phone = NULL;

					}																														

					if( isset($metaData[$form_custom_name][0]) ){
						
						$form_custom_name = $metaData[$form_custom_name][0];
						
					} else {

						$form_custom_name = NULL;

					}

					if( isset($metaData[$form_custom_val][0]) ){
						
						$form_custom_val = $metaData[$form_custom_val][0];
						
					} else {

						$form_custom_val = NULL;

					}

					if( isset($metaData[$form_custom_name1][0]) ){
						
						$form_custom_name1 = $metaData[$form_custom_name1][0];
						
					} else {

						$form_custom_name1 = NULL;

					}

					if( isset($metaData[$form_custom_val1][0]) ){
						
						$form_custom_val1 = $metaData[$form_custom_val1][0];
						
					} else {

						$form_custom_val1 = NULL;

					}
					
					
					if( isset($metaData[$form_custom_name2][0]) ){
						
						$form_custom_name2 = $metaData[$form_custom_name2][0];
						
					} else {

						$form_custom_name2 = NULL;

					}

					if( isset($metaData[$form_custom_val2][0]) ){
						
						$form_custom_val2 = $metaData[$form_custom_val2][0];
						
					} else {

						$form_custom_val2 = NULL;

					}
					
					if( isset($metaData[$form_custom_name3][0]) ){
						
						$form_custom_name3 = $metaData[$form_custom_name3][0];
						
					} else {

						$form_custom_name3 = NULL;

					}

					if( isset($metaData[$form_custom_val3][0]) ){
						
						$form_custom_val3 = $metaData[$form_custom_val3][0];
						
					} else {

						$form_custom_val3 = NULL;

					}
					
					if( isset($metaData[$form_custom_name4][0]) ){
						
						$form_custom_name4 = $metaData[$form_custom_name4][0];
						
					} else {

						$form_custom_name4 = NULL;

					}

					if( isset($metaData[$form_custom_val4][0]) ){
						
						$form_custom_val4 = $metaData[$form_custom_val4][0];
						
					} else {

						$form_custom_val4 = NULL;

					}					
																				
					// HTML Form Integration - END



					// API Integration - START
					
					// Get Response API					
					$campaign_get_response_api = 'wplp_campaign_get_response_api_'.$company->slug;					
					$campaign_get_response_key = 'wplp_campaign_get_response_key_'.$company->slug;
					$campaign_get_response_campaign_name = 'wplp_campaign_get_response_campaign_name_'.$company->slug;
					
					if( isset($metaData[$campaign_get_response_api][0]) ){
						
						$campaign_get_response_api = $metaData[$campaign_get_response_api][0];
						
					} else {

						$campaign_get_response_api = NULL;

					}					
					
					if( isset($metaData[$campaign_get_response_key][0]) ){
						
						$campaign_get_response_key = $metaData[$campaign_get_response_key][0];
						
					} else {

						$campaign_get_response_key = NULL;

					}					

					if( isset($metaData[$campaign_get_response_campaign_name][0]) ){
						
						$campaign_get_response_campaign_name = $metaData[$campaign_get_response_campaign_name][0];
						
					} else {

						$campaign_get_response_campaign_name = NULL;

					}					
					
					// Aweber API					
					$campaign_aweber_api = 'wplp_campaign_aweber_api_'.$company->slug;					
					$campaign_aweber_account_id = 'wplp_campaign_aweber_account_id_'.$company->slug;
					$campaign_aweber_list_id = 'wplp_campaign_aweber_list_id_'.$company->slug;
					
					if( isset($metaData[$campaign_aweber_api][0]) ){
						
						$campaign_aweber_api = $metaData[$campaign_aweber_api][0];
						
					} else {

						$campaign_aweber_api = NULL;

					}					
					
					if( isset($metaData[$campaign_aweber_account_id][0]) ){
						
						$campaign_aweber_account_id = $metaData[$campaign_aweber_account_id][0];
						
					} else {

						$campaign_aweber_account_id = NULL;

					}					

					if( isset($metaData[$campaign_aweber_list_id][0]) ){
						
						$campaign_aweber_list_id = $metaData[$campaign_aweber_list_id][0];
						
					} else {

						$campaign_aweber_list_id = NULL;

					}
					// Aweber End					

										
					// API Integration - END
										

					
										

					if( $companyID != 0 || $override == 'yes' ) { // If company is manually selected and 'allow override' is yes show campaigns
						
						
						if( $override == 'yes' ) {
							
							// Get all campaigns for the company/opp		
							$args = array(
								 
								'posts_per_page' => -1,
								'orderby' => 'title',
								'order' => 'ASC',
								'post_type' => 'campaign',
								'wplp_opportunity' => $company->slug,
								//'post_status' => 'publish',
								'meta_query' => array(
									'relation' => 'AND',
									array(
										'key' => 'wplp_campaign_is_active',
										'value' => array( 'yes', 'no' ),
									),
									
									array(
										'key' => 'wplp_show_as_affiliate_link',
										'value' => array( 'yes', 'no' ),
									)
									
								)
							
							);
							
							$campaigns = get_posts( $args );
						
						} else {

							// Get all campaigns for the company/opp		
							$args = array(
								 
								'posts_per_page' => -1,
								'orderby' => 'title',
								'order' => 'ASC',
								'post_type' => 'campaign',
								'wplp_opportunity' => $company->slug,
								//'post_status' => 'publish',
								'meta_query' => array(
								'relation' => 'AND',
									array(
										'key' => 'wplp_campaign_is_active',
										'value' => 'yes',
									),
								
									array(
										'key' => 'wplp_show_as_affiliate_link',
										'value' => 'yes',
									)
								)
								
							);
							
							$campaigns = get_posts( $args );										
							
						}
						
					} else {
						
						// Get all campaigns for the company/opp		
						$args = array(
							 
							 'posts_per_page' => -1,
							 'orderby' => 'title',
							 'order' => 'ASC',
							 'post_type' => 'campaign',
							 'wplp_opportunity' => $company->slug,
							 //'post_status' => 'publish',
							 'meta_query' => array(
								'relation' => 'AND',
								array(
									'key' => 'wplp_campaign_is_active',
									'value' => 'yes',
								),
								
								array(
									'key' => 'wplp_show_as_affiliate_link',
									'value' => 'yes',
								)
								
							 )
						);
						
						$campaigns = get_posts( $args );				
							
					} 


										
					// if there are active campaigns for a company, show company
					if( $campaigns == TRUE ) {
						
						$ret .= '<h4>' . $company->name . ' - ' . __( 'Settings', 'wp-leads-press' ) . '</h4>';						
						
						// Start API Integration
						$ret .= '<div id="accordion">';
								
						$ret .= '<h4>' . $company->name . ' - ' . __( 'Autoresponder API Integration', 'wp-leads-press' )  . '</h4>';
						
						$ret .= '<p>';
						
						$ret .= '<strong>'.__( 'Use Get Response API?', 'wp-leads-press' ).'</strong>';
							
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';
						$ret .= '<select name="wplp_campaign_get_response_api_'.$company->slug.'">';
						$ret .= '<option value="no"' . selected( $campaign_get_response_api, 'no', false ) . '>no</option>';
						$ret .= '<option value="yes"' . selected( $campaign_get_response_api, 'yes', false ) . '>yes</option>';
						$ret .= '</select>';
						$ret .= '</span>';						
						$ret .= '<br /><br />';	

						$ret .= '<strong>'.__( 'Get Response API Key:', 'wp-leads-press' ).'</strong>';						
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';
						$ret .= "<input type='text' value='".$campaign_get_response_key."' name='wplp_campaign_get_response_key_".$company->slug."' id='wplp_campaign_get_response_key_".$company->slug."' size='25' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the API key provided by Get Response.';
						$ret .= '<br /><br />';

						$ret .= '<strong>'.__( 'Get Response Campaign Name:', 'wp-leads-press' ).'</strong>';						
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';
						$ret .= "<input type='text' value='".$campaign_get_response_campaign_name."' name='wplp_campaign_get_response_campaign_name_".$company->slug."' id='wplp_campaign_get_response_campaign_name_".$company->slug."' size='25' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter Get Response Campaign Name you want to add opt-ins to.';
						$ret .= '<br /><br />';
						
						// Aweber API
						
						$ret .= '<span style="display:block; background-color: #f5f5f5;">';
						$ret .= '&nbsp;';
						$ret .= '</span>';
						$ret .= '<br />';
						
						$ret .= '<strong>'.__( 'Use Aweber API?', 'wp-leads-press' ).'</strong>';
						
						$aweber_auth = get_user_meta($user_id,  'wplp_aweber_auth', true );
												
						$awConnected = 'Aweber is connected!';
						$awEnterAuthCode = 'Enter Authorization Code';
							
						if( ( isset( $aweber_auth ) && $aweber_auth != FALSE ) && $aweber_auth != $awConnected && $aweber_auth != $awEnterAuthCode ) {
							 							
							$aweber_auth = __( 'Aweber is connected!', 'wp-leads-press' );							
							
						} else {
							
							$aweber_auth = __( 'Enter Authorization Code', 'wp-leads-press' );							
							
						}
												
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';
						$ret .= '<select name="wplp_campaign_aweber_api_'.$company->slug.'">';
						$ret .= '<option value="no"' . selected( $campaign_aweber_api, 'no', false ) . '>no</option>';
						$ret .= '<option value="yes"' . selected( $campaign_aweber_api, 'yes', false ) . '>yes</option>';
						$ret .= '</select>';
						$ret .= '</span>';						
						
						$ret .= '<br /><br />';	

						$ret .= '<strong>'.__( 'Get Aweber Authorization Code:', 'wp-leads-press' ).'</strong>';												
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';
						
						
						// Get Aweber API app ID settings
						if( isset( $options['wp_leads_press_aweber_app_id'] ) && !empty( $options['wp_leads_press_aweber_app_id'] ) ) {
							
							$app_id = $options['wp_leads_press_aweber_app_id'];
						
						} else {
							
							$app_id = 'd8fbd769';	
							
						}
							
						$ret .= '<a href="https://auth.aweber.com/1.0/oauth/authorize_app/'.$app_id.'" target="_blank" >'.__( 'Click Here', 'wp-leads-press' ).'</a>';						
						
						$ret .= '</span>';
						
						$ret .= '<br /><br />';
						
						$ret .= __( 'Click link to get your Aweber Authorization code, then enter below.', 'wp-leads-press' );
						
						$ret .= '<br /><br />';

						$ret .= '<strong>'.__( 'Enter Aweber Authorization Code:', 'wp-leads-press' ).'</strong>';												
						$ret .= '<span class="wplp-inputs" style="display:block; float: right; margin-right: 10%; clear: both;">';
						$ret .= '<input type="text" class="wplp-aweber-auth" value="'.$aweber_auth.'" size="25" />';
						$ret .= '</span>';
						
						$ret .= '<br /><br />'.__( 'If Aweber does not show connected, enter the API authorization code provided by Aweber and connect using "Connect to Aweber" button.', 'wp-leads-press' );
						$ret .= '<br /><br />';

						$ret .= '<span class="wplp-inputs">'; // wplp-inputs start

						$ret .= '<input type="hidden" class="wplp-user-selector" value="user" />';
						$ret .= '<input type="hidden" class="wplp-ref-user-id" value="'.$current_user->ID.'" />';
						$ret .= '<input type="button" class="wplp-connect-aweber-api" value="Connect to Aweber" />';

						$ret .= '</span>'; // wplp-inputs end

						$ret .= '<br /><br />';
						$ret .= __( 'After connecting to Aweber, enter your Aweber List ID to add contacts to. After adding info, click "Update Settings" button at bottom of the page to store values.', 'wp-leads-press' );
						$ret .= '<br /><br />';

//						$ret .= '<strong>'.__( 'Aweber Account ID:', 'wp-leads-press' ).'</strong>';						
//						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';
//						$ret .= "<input type='text' value='".$campaign_aweber_account_id."' name='wplp_campaign_aweber_account_id_".$company->slug."' id='wplp_campaign_aweber_account_id_".$company->slug."' size='20' />";						
//						$ret .= '</span>';
//						
//						$ret .= '<br /><br />';

						$ret .= '<strong>'.__( 'Aweber List ID:', 'wp-leads-press' ).'</strong>';						
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';
						$ret .= "<input type='text' value='".$campaign_aweber_list_id."' name='wplp_campaign_aweber_list_id_".$company->slug."' id='wplp_campaign_aweber_list_id_".$company->slug."' size='25' />";						
						$ret .= '</span>';

						$ret .= '<br /><br />';

						$ret .= __( 'Get list id after logging into your aweber account by clicking on a list under "Manage Lists" and looking for: awlistXXXXXXXX under "List Name" and entering ONLY the numbers for the Aweber List ID .', 'wp-leads-press' );												

						$ret .= __( 'After adding info, click "Update Settings" button at bottom of the page to store values.', 'wp-leads-press' );
																		
						$ret .= '<br /><br />';

						$ret .= '<span style="display:block; background-color: #f5f5f5;">';
						$ret .= '&nbsp;';
						$ret .= '</span>';
						$ret .= '<br />';
									
						$ret .= '</div>'; // end div accordion 
						

						// End API Integration
						
						
						// HTML Form Code Integration
						$ret .= '<div id="accordion">';
						
						$ret .= '<h4>' . $company->name . ' - ' . __( 'HTML Form Code Integration', 'wp-leads-press' ) . '</h4>';						

						$ret .= '<p>';
						$ret .= '<strong>'.__( 'Add Leads To Autoresponder?', 'wp-leads-press' ).'</strong>';
							
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';
						$ret .= '<select name="wplp_campaign_ar_list_on_'.$company->slug.'">';
						$ret .= '<option value="no"' . selected( $campaign_ar_list_on, 'no', false ) . '>no</option>';
						$ret .= '<option value="yes"' . selected( $campaign_ar_list_on, 'yes', false ) . '>yes</option>';
						$ret .= '</select>';
						$ret .= '</span>';						
						$ret .= '<br /><br />';						
						                    						
						//$ret .= '<p>';
						$ret .= '<strong>'.__( 'Form Action URL:', 'wp-leads-press' ).'</strong>';						
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';
						$ret .= "<input type='text' value='".$form_action_url."' name='wplp_form_action_url_".$company->slug."' id='wplp_form_action_url_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the \'action url\' from the HTML form code provided by your Autoresponder service. i.e. form action="http://www.somedomain.com. be sure to include the \'http:// or https://\' when entering the URL to send opt-in data to.';
						$ret .= '<br /><br />';
											
						//$ret .= '<p>';
						$ret .= '<strong>'.__( 'Form "Name" Field Name:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_name."' name='wplp_form_name_".$company->slug."' id='wplp_form_name_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the \'NAME\' field value from the HTML form code provided by your Autoresponder service. i.e. name="NAME", you would enter: NAME above.';
						$ret .= '<br /><br />';

						//$ret .= '<p>';
						$ret .= '<strong>'.__( 'Form "First Name" Field Name:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_fname."' name='wplp_form_fname_".$company->slug."' id='wplp_form_fname_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the \'FNAME\' field value from the HTML form code provided by your Autoresponder service. i.e. name="FNAME", you would enter: FNAME above.';
						$ret .= '<br /><br />';

						//$ret .= '<p>';
						$ret .= '<strong>'.__( 'Form "Last Name" Field Name:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_lname."' name='wplp_form_lname_".$company->slug."' id='wplp_form_lname_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the \'LNAME\' field value from the HTML form code provided by your Autoresponder service. i.e. name="LNAME", you would enter: LNAME above.';						
						$ret .= '<br /><br />';

						//$ret .= '<p>';
						$ret .= '<strong>'.__( 'Form "Email" Field Name:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_email."' name='wplp_form_email_".$company->slug."' id='wplp_form_email_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the \'EMAIL\' field value from the HTML form code provided by your Autoresponder service. i.e. name="EMAIL", you would enter: EMAIL above.';
						$ret .= '<br /><br />';

						//$ret .= '<p>';
						$ret .= '<strong>'.__( 'Form "Phone" Field Name:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_phone."' name='wplp_form_phone_".$company->slug."' id='wplp_form_phone_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the \'PHONE\' field value from the HTML form code provided by your Autoresponder service. i.e. name="PHONE", you would enter: PHONE above.';						
						$ret .= '<br /><br />';

						$ret .= '<strong>'.__( 'Custom Form Field Name:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_name."' name='wplp_form_custom_name_".$company->slug."' id='wplp_form_custom_name_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter a custom field name value from the HTML form code provided by your Autoresponder Service. i.e. name="LISTNAME", you would enter: LISTNAME above., ';						
						$ret .= '<br /><br />';

						$ret .= '<strong>'.__( 'Custom Form Field Value:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_val."' name='wplp_form_custom_val_".$company->slug."' id='wplp_form_custom_val_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME" value="SOMELIST", you would enter: SOMELIST above.';						
						$ret .= '<br /><br />';					
											




						$ret .= '<strong>'.__( 'Custom Form Field Name 2:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_name1."' name='wplp_form_custom_name1_".$company->slug."' id='wplp_form_custom_name1_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter a custom field name value from the HTML form code provided by your Autoresponder Service. i.e. name="LISTNAME", you would enter: LISTNAME above., ';						
						$ret .= '<br /><br />';

						$ret .= '<strong>'.__( 'Custom Form Field Value 2:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_val1."' name='wplp_form_custom_val1_".$company->slug."' id='wplp_form_custom_val1_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME" value="SOMELIST", you would enter: SOMELIST above.';						
						$ret .= '<br /><br />';	
						
						
						$ret .= '<strong>'.__( 'Custom Form Field Name 3:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_name2."' name='wplp_form_custom_name2_".$company->slug."' id='wplp_form_custom_name2_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter a custom field name value from the HTML form code provided by your Autoresponder Service. i.e. name="LISTNAME", you would enter: LISTNAME above., ';						
						$ret .= '<br /><br />';

						$ret .= '<strong>'.__( 'Custom Form Field Value 3:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_val2."' name='wplp_form_custom_val2_".$company->slug."' id='wplp_form_custom_val2_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME" value="SOMELIST", you would enter: SOMELIST above.';						
						$ret .= '<br /><br />';	
						
						$ret .= '<strong>'.__( 'Custom Form Field Name 4:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_name3."' name='wplp_form_custom_name3_".$company->slug."' id='wplp_form_custom_name3_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter a custom field name value from the HTML form code provided by your Autoresponder Service. i.e. name="LISTNAME", you would enter: LISTNAME above., ';						
						$ret .= '<br /><br />';

						$ret .= '<strong>'.__( 'Custom Form Field Value 4:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_val3."' name='wplp_form_custom_val3_".$company->slug."' id='wplp_form_custom_val3_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME" value="SOMELIST", you would enter: SOMELIST above.';						
						$ret .= '<br /><br />';	
						
						$ret .= '<strong>'.__( 'Custom Form Field Name 5:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_name4."' name='wplp_form_custom_name4_".$company->slug."' id='wplp_form_custom_name4_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter a custom field name value from the HTML form code provided by your Autoresponder Service. i.e. name="LISTNAME", you would enter: LISTNAME above., ';						
						$ret .= '<br /><br />';

						$ret .= '<strong>'.__( 'Custom Form Field Value 5:', 'wp-leads-press' ).'</strong>';
						$ret .= '<span style="display:block; float: right; margin-right: 10%; clear: both;">';						
						$ret .= "<input type='text' value='".$form_custom_val4."' name='wplp_form_custom_val4_".$company->slug."' id='wplp_form_custom_val4_".$company->slug."' size='20' />";
						$ret .= '</span>';
						$ret .= '<br /><br />Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME" value="SOMELIST", you would enter: SOMELIST above.';						
						$ret .= '<br /><br />';	
						
																																			
						$ret .= '</p>'; // end container p
						
						$ret .= '</div>'; // end of accordion

					}
											
						
				} // For each company as company						
			
			} // End if !empty Companies
			
		$ret .= '<div>';
		$ret .= '<br />';
		$ret .= '<div class="wplp-inputs">';		
		$ret .= '<input type="submit" id="wplp_autoresponder_save" name="wplp_autoresponder_save" class="wplp-autoresponder-save" value="' . __( 'Update Settings', 'wp-leads-press' ) . '">';
		$ret .= '</div>';		
		$ret .= '</div>';
				
		$ret .= '</form>';	
	
		$ret .= '</div>'; // end of table...

		$ret .= '<h2>Example Autoresponder Form Code</h3>';
		$ret .= '<pre>';
		
		$ret .= htmlspecialchars('
<!-- Begin Signup Form -->
<form action="https://someaddress.com" method="post" name="something" target="_blank">

<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
<input type="text" value="" name="FNAME" class="" id="mce-FNAME">
<input type="text" value="" name="LNAME" class="" id="mce-LNAME">
<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">

</form>
<!-- End Signup Form -->');
							
		$ret .= '</pre>';		
			
		return $ret; // Return the list	

		
		} else { // End if user is logged in
				
			return __( 'You must be logged in to edit your settings.', 'wp-leads-press' );
				
		}
}

//}

//$WPLPShortcodes = new WPLPShortcodes;


# Display user's leads and management dashboard.
add_shortcode( 'wplp_dashboard', 'wplp_dashboard_display' );
//add_shortcode( 'wplp_dashboard', array( WPLPShortcodes::get_instance(), 'wplp_dashboard_display' ) );

add_shortcode( 'wplp_lead_list', 'wplp_lead_list_display' );
//add_shortcode( 'wplp_lead_list', array( WPLPShortcodes::get_instance(), 'wplp_lead_list_display' ) );	

add_shortcode( 'wplp_affiliate_settings', 'wplp_affiliate_settings_display' );
//add_shortcode( 'wplp_affiliate_settings', array( WPLPShortcodes::get_instance(), 'wplp_affiliate_settings_display' ) );	

add_shortcode( 'wplp_landing_pages', 'wplp_landing_pages_display' );
//add_shortcode( 'wplp_landing_pages', array( WPLPShortcodes::get_instance(), 'wplp_landing_pages_display' ) );

add_shortcode( 'wplp_bonus_leads', 'wplp_bonus_leads_display' );
//add_shortcode( 'wplp_bonus_leads', array( WPLPShortcodes::get_instance(), 'wplp_bonus_leads_display' ) );	
?>