<?php
// Add WPLP custom post type 'Campaign'
// Create Custom Post
add_action( 'init', 'wplp_campaign' );
function wplp_campaign() {
	$labels = array(
		'name'               => _x( 'Campaigns', 'post type general name' ),
		'singular_name'      => _x( 'Campaign', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'Campaign' ),
		'add_new_item'       => __( 'Add New Campaign' ),
		'edit_item'          => __( 'Edit Campaign' ),
		'new_item'           => __( 'New Campaign' ),
		'all_items'          => __( 'All Campaigns' ),
		'view_item'          => __( 'View Campaign' ),
		'search_items'       => __( 'Search Campaigns' ),
		'not_found'          => __( 'No campaigns found' ),
		'not_found_in_trash' => __( 'No campaigns found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Campaigns'
	);
	
		$args = array(
		'labels' => $labels,
		'description' => 'Setup your marketing campaigns here.',
		'public' => false,
		'publicly_queryable' => false, // Don't show the campaign information to public
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => true,
		'menu_position' => null,
		//'supports' => array('title','editor','thumbnail', 'post-formats'),
		'supports' => array('title'),				
		'has_archive' => false
	);
		register_post_type( 'campaign', $args );
}
function wplp_custom_title_text_campaign( $title ){
$screen = get_current_screen();
if ( 'campaign' == $screen->post_type ) {
$title = 'Enter Campaign Name, i.e. \'Company Name - Direct Link\' or some other descriptive title.';
}
return $title;
}
add_filter( 'enter_title_here', 'wplp_custom_title_text_campaign' );
add_filter( 'post_updated_messages', 'wplp_updated_messages' );
function wplp_updated_messages( $messages ) {
	global $post, $post_ID, $wplp_admin;
	$messages['campaign'] = array(
		0 => '', 
		1 => sprintf( __('Campaign updated. <a href="%s">View campaign</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Campaign updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('Campaign restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Campaign published. <a href="%s">View campaign</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Campaign saved.'),
		8 => sprintf( __('Campaign submitted. <a target="_blank" href="%s">Preview campaign</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Campaign scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview campaign</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Campaign draft updated. <a target="_blank" href="%s">Preview campaign</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}
add_filter( 'manage_edit-campaign_columns', 'wplp_campaign_columns' ) ;
function wplp_campaign_columns( $columns ) {
	global $wpleadpress;
	
	$columns = array(
		'cb' 								=>	'<input type="checkbox" />',
		'title' 							=> __( 'Campaign', 'wp-leads-press' ),
		'id' 								=> __( 'ID', 'wp-leads-press' ),						
		'wplp_opportunity' 					=> __( 'Company', 'wp-leads-press' ),
		'wplp_opportunity_id' 				=> __( 'Company ID', 'wp-leads-press' ),		
		'wplp_campaign_url' 				=> __( 'Destination URL', 'wp-leads-press' ),
		'wplp_campaign_active' 				=> __( 'Show Campaign', 'wp-leads-press' ),
		'date' 								=> __( 'Date', 'wp-leads-press' )
	);
	return $columns;
}
add_action( 'manage_campaign_posts_custom_column', 'wplp_manage_campaign_columns', 10, 2 );
function wplp_manage_campaign_columns( $column, $post_id ) {
	global $post, $wplp_admin;
	switch( $column ) {
		
		// If displaying 'id'
		case 'id' :
		
			echo $post_id;
			
		break;
		
		// If displaying 'Opportunity'
		case 'wplp_opportunity' :
			/* Get the genres for the post. */
			$terms = get_the_terms( $post_id, 'wplp_opportunity' );
			/* If terms were found. */
			if ( !empty( $terms ) ) {
				$out = array();
				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'wplp_opportunity' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wplp_opportunity', 'display' ) )
					);
				}
				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
				
			}
			/* If no terms were found, output a default message. */
			else {
				
				__( 'No Companies, you need to create one.'. 'wp-leads-press' );
				
			}
			break;
			
		// If displaying 'Opportunity ID'
		case 'wplp_opportunity_id' :
			/* Get the genres for the post. */
			$terms = get_the_terms( $post_id, 'wplp_opportunity' );
			/* If terms were found. */
			if ( !empty( $terms ) ) {
				$out = array();
				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					
					$out[] = $term->term_id;
				}
				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
				
			}
			/* If no terms were found, output a default message. */
			else {
				
				__( 'No Companies, you need to create one.'. 'wp-leads-press' );
				
			}
			break;			
			
					
		// If displaying the 'Destination URL' column.
		case 'wplp_campaign_url' :
			// Get the post meta. 
			$wplp_campaign_url = get_post_meta( $post_id, 'wplp_campaign_url', true );
			// If no destination URL is found, output a default message.
			if ( empty( $wplp_campaign_url ) )
			
				echo __( 'No URL has been set as the destinaiton for the campaign.', 'wp-leads-press' );
			// If there is a destination URL set, display it.
			else
			
				echo __( $wplp_campaign_url, 'wp-leads-press' );
			break;
		
		case 'wplp_campaign_active' :
		
		
			// Get the post meta. 
			$wplp_campaign_is_active = get_post_meta( $post_id, 'wplp_campaign_is_active', true );
			// If no destination URL is found, output a default message.
			if ( empty( $wplp_campaign_is_active ) ) {

				update_post_meta( $post_id, 'wplp_campaign_is_active', 'yes' );	

				echo __( 'yes' );
				
			} else {
			
				echo __( $wplp_campaign_is_active, 'wp-leads-press' );					 		
				
			}
			
		break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}
// Create taxonomy for custom campaign post
add_action( 'init', 'wplp_taxonomy_campaign_opportunities', 0 );
function wplp_taxonomy_campaign_opportunities() {
	$labels = array(
		'name'              => _x( 'Select Company For Campaign', 'taxonomy general name' ),
		'singular_name'     => _x( 'Company', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Companies' ),
		'all_items'         => __( 'All Companies' ),
		'parent_item'       => __( 'Parent Companies' ),
		'parent_item_colon' => __( 'Parent Company:' ),
		'edit_item'         => __( 'Edit Company' ), 
		'update_item'       => __( 'Update Company' ),
		'add_new_item'      => __( 'Add New Company' ),
		'new_item_name'     => __( 'New Company' ),
		'menu_name'         => __( 'Companies' ),
	);
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
		'single_value' => true,
		'show_ui'	   => true,
		'update_count_callback' => '_update_post_term_count'
	);
	register_taxonomy( 'wplp_opportunity', 'campaign', $args );
}
// Create Meta Boxes for campaign to store values
// Define box
add_action( 'admin_init', 'wplp_campaign_details' );
function wplp_campaign_details() {
	global $wplp_admin;
	
    add_meta_box( 
        'wplp_campaign_details', __( 'Campaign Details', 'wp-leads-press' ), 'wplp_campaign_meta', 'campaign', 'normal', 'high' );
		
		//add_meta_box("wplp_campaign_details", "Campaign URL", "wplp_campaign_meta", "campaign", "normal", "high");
}
// Define Content of meta box
function wplp_campaign_meta( $post ) {
	global $wplp_admin;
	
	wp_nonce_field( plugin_basename( __FILE__ ), 'wplp_campaign_nonce' );
	
	$ret = '<table>';
	
	$ret .= '<tr>';
	$ret .= '<td width="20%">';
	$ret .= '<label><strong>' . __( 'Destination URL:', 'wp-leads-press' ) . '</strong></label>';
	//$ret .= '<p><br /><br /><br /></p>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_campaign_url" id="wplp_campaign_url" type="text" size="40" value="' . wplp_get_campaign_field("wplp_campaign_url") . '">';	

	$ret .= '</td>'; 	

	$ret .= '<td width="2%">';
	$ret .= '</td>';
	
	$ret .= '<td>';
		
	$ret .= '<br />'. __( '(Enter URL up to where your affiliate code begins, i.e. thesite.com/, do not include "http://www.")', 'wp-leads-press' ) . '';
	$ret .= '<br />'. __( 'This is where your leads/traffic are sent after they opt-in or click on a redirection link.', 'wp-leads-press' ) . '';	
	$ret .= '<br /><br /> <strong>'. __( 'For subfolder setups: ', 'wp-leads-press' ) . '</strong>' . __('"thesite.com/join/?aff=" OR "thesite.com/" OR "thesite.com?aff=" etc."', 'wp-leads-press' ) . '';
	$ret .= '<br /> <strong>'. __( 'For sub-domain setups: ', 'wp-leads-press' ) . '</strong>' . __('"thesite.com"', 'wp-leads-press' ) . '';
	$ret .= '</td>';		
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
		
	$ret .= '<tr>';
	$ret .= '<td>';
	$ret .= '<label><strong>' . __( 'URL Trailing Value:', 'wp-leads-press' ) . '</strong> </label>';
	//$ret .= '<p><br /><br /></p>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_campaign_url_trailing_value" id="wplp_campaign_url_trailing_value" type="text" size="40" value="' . wplp_get_campaign_field("wplp_campaign_url_trailing_value") . '">';
	
	$ret .= '</td>'; 	
	
	$ret .= '<td>';
	$ret .= '</td>';	
		
	$ret .= '<td>';		
	$ret .= '<br />'. __( '(Enter additional URL folders, files etc to be added AFTER the userID in sub-folder setups, if applicable. Leave blank for 99% of all destination URL setups, this is for special situations only.)', 'wp-leads-press' ) . '';
	$ret .= '<br /><br /> <strong>'. __( 'For Example: ', 'wp-leads-press' ) . '</strong>' . __('"Your destination URL is structured like, e.g. www.thesite.com/userID/join.php, and you need the "/join.php" added after your userID for the Affiliate Program."', 'wp-leads-press' ) . '';
	$ret .= '</td>';		
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '</tr>';

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
			
	$ret .= '<tr>';	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Is https:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<select name="wplp_campaign_is_https" id="wplp_campaign_is_https">';
	$ret .= '<option value="yes" ' . selected( wplp_get_campaign_field( "wplp_campaign_is_https" ), 'yes', false ) . '>yes</option>';
	$ret .= '<option value="no" ' . selected( wplp_get_campaign_field( "wplp_campaign_is_https" ), 'no', false ) . '>no</option>';
	$ret .= '</select>'; 
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';	

	$ret .= '<td>';		 
	$ret .= ' '. __( 'Yes if destination URL is https, i.e. https://thesite.com OR https://userID.thesite.com', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '</tr>';

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
			
	$ret .= '<tr>';	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Is Subdomain:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<select name="wplp_campaign_is_subdomain" id="wplp_campaign_is_subdomain">';
	$ret .= '<option value="yes" ' . selected( wplp_get_campaign_field( "wplp_campaign_is_subdomain" ), 'yes', false ) . '>yes</option>';
	$ret .= '<option value="no" ' . selected( wplp_get_campaign_field( "wplp_campaign_is_subdomain" ), 'no', false ) . '>no</option>';
	$ret .= '</select>'; 

	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';

	$ret .= '<td>';			 
	$ret .= ' '. __( 'Yes if destination URL is subdomain, i.e. yourID.thesite.com', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
	
	$ret .= '<tr>';	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Show Campaign:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<select name="wplp_campaign_is_active" id="wplp_campaign_is_active">';
	$ret .= '<option value="yes" ' . selected( wplp_get_campaign_field( "wplp_campaign_is_active" ), 'yes', false ) . '>yes</option>';
	$ret .= '<option value="no" ' . selected( wplp_get_campaign_field( "wplp_campaign_is_active" ), 'no', false ) . '>no</option>';
	$ret .= '</select>'; 	

	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';

	$ret .= '<td>';		
	$ret .= ' '. __( 'Yes if you want to show landing pages associated with campaign in Lead Dashboard', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';

//wplp_show_as_affiliate_link
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';

	$ret .= '<tr>';	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Show in Affiliate Settings:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<select name="wplp_show_as_affiliate_link" id="wplp_show_as_affiliate_link">';
	$ret .= '<option value="yes" ' . selected( wplp_get_campaign_field( "wplp_show_as_affiliate_link" ), 'yes', false ) . '>yes</option>';
	$ret .= '<option value="no" ' . selected( wplp_get_campaign_field( "wplp_show_as_affiliate_link" ), 'no', false ) . '>no</option>';
	$ret .= '</select>'; 	

	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';	

	$ret .= '<td>';
	$ret .= ' '. __( 'Yes if you want to show destination url in Affiliate Settings as link for new affiliates to register for Company, otherwise set to no.', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';	


	// Set Default User ID For Campaign
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
	
	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Set Default User ID For Campaign:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_campaign_default_referrer_id" id="wplp_campaign_default_referrer_id" value="'.wplp_get_campaign_field( "wplp_campaign_default_referrer_id" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the user ID# of the user you want as the default referrer for this campaign when no other user is available to be used as the referrer, i.e. when a user is under the system or user 0 ZERO, thus not referred by another user in the system.', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';		
	




	// Set Autoresponder list For Campaign
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr><h2>'.__( 'Autoresponder API Integration Settings', 'wp-leads-press' ).'</h2><hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
	
	// Get Response
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr><h4>'.__( 'Get Response Settings', 'wp-leads-press' ).'</h4><hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
	
	$ret .= '<tr>';	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Use Get Response API?', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';
	$ret .= '</td>';
		
	$ret .= '<td>';
	$ret .= '<select name="wplp_campaign_get_response_api" id="wplp_campaign_get_response_api">';
	$ret .= '<option value="no" ' . selected( wplp_get_campaign_field( "wplp_campaign_get_response_api" ), 'no', false ) . '>no</option>';
	$ret .= '<option value="yes" ' . selected( wplp_get_campaign_field( "wplp_campaign_get_response_api" ), 'yes', false ) . '>yes</option>';	
	$ret .= '</select>'; 	
	$ret .= '</td>';

	$ret .= '<td>';
	$ret .= '</td>';		

	$ret .= '<td>';
	$ret .= ' '. __( 'Yes if you want to add leads generated by campaign to Get Response using API Integration.', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';

	$ret .= '</tr>'; 

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';	
			
	$ret .= '<tr>';
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Get Response API Key:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td colspan="6">';
	$ret .= '<input name="wplp_campaign_get_response_key" id="wplp_campaign_get_response_key" value="'.wplp_get_campaign_field( "wplp_campaign_get_response_key" ).'" size="40" >';
	$ret .= '</td>';	
	$ret .= '</tr>';

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
	
	$ret .= '<tr>';
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Get Response Campaign Name:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td colspan="6">';
	$ret .= '<input name="wplp_campaign_get_response_campaign_name" id="wplp_campaign_get_response_campaign_name" value="'.wplp_get_campaign_field( "wplp_campaign_get_response_campaign_name" ).'" size="40">';
	$ret .= '</td>';	
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
	// Get Response End
	
	
	// Aweber
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr><h4>'.__( 'Aweber Settings', 'wp-leads-press' ).'</h4><hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
	
	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Use Aweber API?', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';
	$ret .= '</td>';
		
	$ret .= '<td>';
	$ret .= '<select name="wplp_campaign_aweber_api" id="wplp_campaign_aweber_api">';
	$ret .= '<option value="no" ' . selected( wplp_get_campaign_field( "wplp_campaign_aweber_api" ), 'no', false ) . '>no</option>';
	$ret .= '<option value="yes" ' . selected( wplp_get_campaign_field( "wplp_campaign_aweber_api" ), 'yes', false ) . '>yes</option>';
	$ret .= '</select>'; 	
	$ret .= '</td>';

	$ret .= '<td>';
	$ret .= '</td>';		

	$ret .= '<td>';
	$ret .= ' '. __( 'Yes if you want to add leads generated by campaign to Aweber using API Integration.', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';

	$ret .= '</tr>'; 

	$ret .= '<tr height="20">';	
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';	
			
	$ret .= '<tr>';
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Aweber List ID:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td colspan="6">';
	$ret .= '<input name="wplp_campaign_aweber_list_id" id="wplp_campaign_aweber_list_id" value="'.wplp_get_campaign_field( "wplp_campaign_aweber_list_id" ).'" size="40" >';
	$ret .= '</td>';	
	
	$ret .= '</tr>';
	
	$ret .= '<tr>';
	
	$ret .= '<td colspan="6">';
	$ret .= __('Only enter the number portion of "awlist0000000" list ID. If you have not already connected to Aweber under Autoresponder Settings in WPLP Settings, please do so, otherwise leads from this campaign will not be added to your selected list at Aweber. Also, remember this list ID must be different from the one set under WPLP settings, you will not be able to add leads to the same list twice at Aweber and this will cause an issue with Aweber API and return an error, causing visitors to not be redirected properly.', 'wp-leads-press');
	$ret .= '<br />';	
	$ret .= '</td>';

	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';	
	// Aweber End		
	
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr><h2>'.__( 'Autoresponder HTML Form Integration Settings', 'wp-leads-press' ).'</h2><hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';	
	
	$ret .= '<tr>';	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Add Leads To Autoresponder List With HTML Form Integration?', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<select name="wplp_campaign_ar_list_on" id="wplp_campaign_ar_list_on">';
	$ret .= '<option value="yes" ' . selected( wplp_get_campaign_field( "wplp_campaign_ar_list_on" ), 'yes', false ) . '>yes</option>';
	$ret .= '<option value="no" ' . selected( wplp_get_campaign_field( "wplp_campaign_ar_list_on" ), 'no', false ) . '>no</option>';
	$ret .= '</select>'; 	

	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';	

	$ret .= '<td>';
	$ret .= ' '. __( 'Yes if you want to add all leads generated by campaign to autoresponder with HTML form code settings below.', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';	

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';
	
	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Action URL:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_campaign_ar_url" id="wplp_campaign_ar_url" value="'.wplp_get_campaign_field( "wplp_campaign_ar_url" ).'" size="40" >';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the \'action url\' from the HTML form code provided by your Autoresponder service. i.e. form action="http://www.somedomain.com" method="post", you would enter: http://www.somedomain.com above. Please note you must fill in a complete url, including http:// or https:// to properly configure.', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';

	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form "Email" Field Name:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_email_field" id="wplp_ar_email_field" value="'.wplp_get_campaign_field( "wplp_ar_email_field" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the \'EMAIL\' field ID from the HTML form code provided by your Autoresponder service. i.e. name="EMAIL", you would enter: EMAIL above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form "Name" Field Name:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_name_field" id="wplp_ar_name_field" value="'.wplp_get_campaign_field( "wplp_ar_name_field" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the \'NAME\' field ID from the HTML form code provided by your Autoresponder service. i.e. name="NAME", you would enter: NAME above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';	

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form "First Name" Field Name:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_fname_field" id="wplp_ar_fname_field" value="'.wplp_get_campaign_field( "wplp_ar_fname_field" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the \'FNAME\' field ID from the HTML form code provided by your Autoresponder service. i.e. name="FNAME", you would enter: FNAME above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';		

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form "Last Name" Field Name:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_lname_field" id="wplp_ar_lname_field" value="'.wplp_get_campaign_field( "wplp_ar_lname_field" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the \'LNAME\' field ID from the HTML form code provided by your Autoresponder service. i.e. name="LNAME", you would enter: LNAME above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';	
	
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form "Phone Number" Field Name:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_phone_field" id="wplp_ar_phone_field" value="'.wplp_get_campaign_field( "wplp_ar_phone_field" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the \'PHONE\' field ID from the HTML form code provided by your Autoresponder service. i.e. name="PHONE", you would enter: PHONE above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';						






	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Name:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_name" id="wplp_ar_custom_field_name" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_name" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';						





	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Value:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_val" id="wplp_ar_custom_field_val" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_val" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. value="SOMELIST", you would enter: "SOMELIST" above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Name 2:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_name1" id="wplp_ar_custom_field_name1" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_name1" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';						

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';

	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Value 2:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_val1" id="wplp_ar_custom_field_val1" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_val1" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. value="SOMELIST", you would enter: "SOMELIST" above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Name 3:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_name2" id="wplp_ar_custom_field_name2" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_name2" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';						

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';

	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Value 3:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_val2" id="wplp_ar_custom_field_val2" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_val2" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. value="SOMELIST", you would enter: "SOMELIST" above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Name 4:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_name3" id="wplp_ar_custom_field_name3" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_name3" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';						

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Value 4:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_val3" id="wplp_ar_custom_field_val3" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_val3" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. value="SOMELIST", you would enter: "SOMELIST" above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';
	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';


	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Name 5:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_name4" id="wplp_ar_custom_field_name4" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_name4" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field name from the HTML form code provided by your Autoresponder service. i.e. name="LISTNAME", you would enter: LISTNAME above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';						

	$ret .= '<tr height="20">';
	$ret .= '<td colspan="6">';
	$ret .= '<hr>';
	$ret .= '</td>';	
	$ret .= '</tr>';

	$ret .= '<tr>';	
	
	$ret .= '<td>';
	$ret .= '<label><b>' . __('Form Custom Field Value 5:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= '<input name="wplp_ar_custom_field_val4" id="wplp_ar_custom_field_val4" value="'.wplp_get_campaign_field( "wplp_ar_custom_field_val4" ).'">';
	
	$ret .= '</td>'; 	

	$ret .= '<td>';
	$ret .= '</td>';
	
	$ret .= '<td>';
	$ret .= ' '. __( 'Enter the custom field value from the HTML form code provided by your Autoresponder service. i.e. value="SOMELIST", you would enter: "SOMELIST" above', 'wp-leads-press' ) . '';
	$ret .= '<br />';		
	$ret .= '</td>';
	
	$ret .= '</tr>';				
//	$ret .= '<tr height="20">';
//	$ret .= '</tr>';
//	
//	$ret .= '<tr>';	
//	$ret .= '<td width="150">';
//	$ret .= '<label><b>' . __('Campaign Shortcode:', 'wp-leads-press') . '</b> </label>';
//	$ret .= '</td>';	
//	
//	$ret .= '<td>';
//	$ret .= "<input type='text' size='40' readonly='readonly' value='[wplp_campaign id=\"". $post->ID ."\"]'>";
//	$ret .= '</td>';
//	$ret .= '</tr>';
	
	$ret .= '</table>';
	
	echo $ret;
}
function wplp_get_campaign_shortcode( $post_id ) {
	global $post;
	$ret = "<input type='text' size='40' readonly='readonly' value='[wplp_campaign id=\"". $post->ID ."\"]'>";
	echo $ret;
	
}
function wplp_get_campaign_field($campaign_field) {
    global $post;
    $custom = get_post_custom( $post->ID );
    if (isset($custom[$campaign_field])) {
        return $custom[$campaign_field][0];
    }
}

function wplp_get_campaign_field_value($campaign_field, $post_id) {

    $custom = get_post_custom( $post_id );
    if (isset($custom[$campaign_field])) {
        return $custom[$campaign_field][0];
    }
}

// Save the post data
add_action( 'save_post', 'wplp_campaign_details_save' );
function wplp_campaign_details_save( $post_id ) {
	global $wp, $post;
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;
	
	if( isset($_POST['wplp_campaign_nonce'] ) ){
		
		$$_POST['wplp_campaign_nonce'] = $_POST['wplp_campaign_nonce'];
		
	} else {
		
		$_POST['wplp_campaign_nonce'] = NULL;	
		
	}
	
	if ( !wp_verify_nonce( $_POST['wplp_campaign_nonce'], plugin_basename( __FILE__ ) ) )
	return;
	
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	
	// Set variables to be saved.
	$campaign_url = $_POST['wplp_campaign_url'];
	$campaign_url_trailing_value = $_POST['wplp_campaign_url_trailing_value'];	
	$campaign_is_https = $_POST['wplp_campaign_is_https'];	
	$campaign_is_subdomain = $_POST['wplp_campaign_is_subdomain'];
	$campaign_is_active = $_POST['wplp_campaign_is_active'];
	$wplp_show_as_affiliate_link = $_POST['wplp_show_as_affiliate_link'];
	$wplp_campaign_default_referrer_id = $_POST['wplp_campaign_default_referrer_id'];

	$wplp_campaign_ar_list_on = $_POST['wplp_campaign_ar_list_on'];
	$wplp_campaign_ar_url = $_POST['wplp_campaign_ar_url'];
	$wplp_ar_email_field = $_POST['wplp_ar_email_field'];
	$wplp_ar_name_field = $_POST['wplp_ar_name_field'];
	$wplp_ar_fname_field = $_POST['wplp_ar_fname_field'];
	$wplp_ar_lname_field = $_POST['wplp_ar_lname_field'];
	$wplp_ar_phone_field = $_POST['wplp_ar_phone_field'];
	$wplp_ar_custom_field_name = $_POST['wplp_ar_custom_field_name'];
	$wplp_ar_custom_field_val = $_POST['wplp_ar_custom_field_val'];
	
	$wplp_ar_custom_field_name1 = $_POST['wplp_ar_custom_field_name1'];
	$wplp_ar_custom_field_val1 = $_POST['wplp_ar_custom_field_val1'];	
	
	$wplp_ar_custom_field_name2 = $_POST['wplp_ar_custom_field_name2'];
	$wplp_ar_custom_field_val2 = $_POST['wplp_ar_custom_field_val2'];

	$wplp_ar_custom_field_name3 = $_POST['wplp_ar_custom_field_name3'];
	$wplp_ar_custom_field_val3 = $_POST['wplp_ar_custom_field_val3'];

	$wplp_ar_custom_field_name4 = $_POST['wplp_ar_custom_field_name4'];
	$wplp_ar_custom_field_val4 = $_POST['wplp_ar_custom_field_val4'];			
	
	$wplp_campaign_get_response_api = $_POST['wplp_campaign_get_response_api'];
	$wplp_campaign_get_response_key = $_POST['wplp_campaign_get_response_key'];
	$wplp_campaign_get_response_campaign_name = $_POST['wplp_campaign_get_response_campaign_name'];

	$wplp_campaign_aweber_api = $_POST['wplp_campaign_aweber_api'];
	$wplp_campaign_aweber_list_id = $_POST['wplp_campaign_aweber_list_id'];
	
	
	// update post meta
	update_post_meta( $post_id, 'wplp_campaign_url', $campaign_url );
	update_post_meta( $post_id, 'wplp_campaign_url_trailing_value', $campaign_url_trailing_value );
	update_post_meta( $post_id, 'wplp_campaign_is_https', $campaign_is_https );	
	update_post_meta( $post_id, 'wplp_campaign_is_subdomain', $campaign_is_subdomain );
	update_post_meta( $post_id, 'wplp_campaign_is_active', $campaign_is_active );	
	update_post_meta( $post_id, 'wplp_show_as_affiliate_link', $wplp_show_as_affiliate_link );
	update_post_meta( $post_id, 'wplp_campaign_default_referrer_id', $wplp_campaign_default_referrer_id );

	update_post_meta( $post_id, 'wplp_campaign_ar_list_on', $wplp_campaign_ar_list_on );
	update_post_meta( $post_id, 'wplp_campaign_ar_url', $wplp_campaign_ar_url );
	update_post_meta( $post_id, 'wplp_ar_email_field', $wplp_ar_email_field );
	update_post_meta( $post_id, 'wplp_ar_name_field', $wplp_ar_name_field );
	update_post_meta( $post_id, 'wplp_ar_fname_field', $wplp_ar_fname_field );	
	update_post_meta( $post_id, 'wplp_ar_lname_field', $wplp_ar_lname_field );
	update_post_meta( $post_id, 'wplp_ar_phone_field', $wplp_ar_phone_field );
	update_post_meta( $post_id, 'wplp_ar_custom_field_name', $wplp_ar_custom_field_name );
	update_post_meta( $post_id, 'wplp_ar_custom_field_val', $wplp_ar_custom_field_val );


	update_post_meta( $post_id, 'wplp_ar_custom_field_name1', $wplp_ar_custom_field_name1 );
	update_post_meta( $post_id, 'wplp_ar_custom_field_val1', $wplp_ar_custom_field_val1 );
	
	update_post_meta( $post_id, 'wplp_ar_custom_field_name2', $wplp_ar_custom_field_name2 );
	update_post_meta( $post_id, 'wplp_ar_custom_field_val2', $wplp_ar_custom_field_val2 );
	
	update_post_meta( $post_id, 'wplp_ar_custom_field_name3', $wplp_ar_custom_field_name3 );
	update_post_meta( $post_id, 'wplp_ar_custom_field_val3', $wplp_ar_custom_field_val3 );
	
	update_post_meta( $post_id, 'wplp_ar_custom_field_name4', $wplp_ar_custom_field_name4 );
	update_post_meta( $post_id, 'wplp_ar_custom_field_val4', $wplp_ar_custom_field_val4 );
					
	update_post_meta( $post_id, 'wplp_campaign_get_response_api', $wplp_campaign_get_response_api );
	update_post_meta( $post_id, 'wplp_campaign_get_response_key', $wplp_campaign_get_response_key );
	update_post_meta( $post_id, 'wplp_campaign_get_response_campaign_name', $wplp_campaign_get_response_campaign_name );

	update_post_meta( $post_id, 'wplp_campaign_aweber_api', $wplp_campaign_aweber_api );	
	update_post_meta( $post_id, 'wplp_campaign_aweber_list_id', $wplp_campaign_aweber_list_id );
	
}


// Add WPLP custom post type 'Lead'
// Create Custom Post
add_action( 'init', 'wplp_lead' );
function wplp_lead() {
	$labels = array(
		'name'               => _x( 'Leads', 'post type general name' ),
		'singular_name'      => _x( 'Lead', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'Lead' ),
		'add_new_item'       => __( 'Add New Lead' ),
		'edit_item'          => __( 'Edit Lead' ),
		'new_item'           => __( 'New Lead' ),
		'all_items'          => __( 'All Leads' ),
		'view_item'          => __( 'View Lead' ),
		'search_items'       => __( 'Search Leads' ),
		'not_found'          => __( 'No leads found' ),
		'not_found_in_trash' => __( 'No leads found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Leads'
	);
	
		$args = array(
		'labels' => $labels,
		'description' => 'View all Leads here.',
		'public' => false,
		'publicly_queryable' => false, // Don't show the Lead information to public
		'show_ui' => true,
		'query_var' => false, //false
		'rewrite' => true,
		'capability_type' => 'post', // Should we allow non admins to use back end of WP to manage leads?
		'hierarchical' => false, //false
		'menu_position' => null,
		//'supports' => array('title','editor','thumbnail', 'post-formats'),
		'supports' => array('title'),				
		'has_archive' => false
	);
	   
	  
	register_post_type( 'lead', $args );	
}
function wplp_custom_title_text_lead( $title ){
	global $wplp_admin;
	
	$screen = get_current_screen();
	if ( 'lead' == $screen->post_type ) {
	$title = 'Enter full name of lead';
	}
	return $title;
	}
	add_filter( 'enter_title_here', 'wplp_custom_title_text_lead' );
	
	add_filter( 'post_updated_messages', 'wplp_updated_messages_lead' );
	function wplp_updated_messages_lead( $messages ) {
		global $post, $post_ID, $wplp_admin;
		$messages['lead'] = array(
			0 => '', 
			1 => sprintf( __('Lead updated. <a href="%s">View lead</a>', 'wp-leads-press' ), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.', 'wp-leads-press' ),
			3 => __('Custom field deleted.', 'wp-leads-press' ),
			4 => __('Lead updated.'),
			5 => isset($_GET['revision']) ? sprintf( __('Lead restored to revision from %s', 'wp-leads-press' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Lead published. <a href="%s">View lead</a>', 'wp-leads-press' ), esc_url( get_permalink($post_ID) ) ),
			7 => __('Lead saved.'),
			8 => sprintf( __('Lead submitted. <a target="_blank" href="%s">Preview lead</a>', 'wp-leads-press' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Lead scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview lead</a>', 'wp-leads-press' ), date_i18n( __( 'M j, Y @ G:i', 'wp-leads-press'  ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Lead draft updated. <a target="_blank" href="%s">Preview lead</a>', 'wp-leads-press' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);
		return $messages;
}
add_filter( 'manage_edit-lead_columns', 'wplp_lead_columns' );
function wplp_lead_columns( $columns ) {
	global $wplp_admin;
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Lead Name', 'wp-leads-press' ),
		
		'author' => __( 'Lead Owner', 'wp-leads-press' ),
		//'wplp_lead_first_name' => __( 'First Name', 'wp-leads-press' ),
		//'wplp_lead_last_name' => __( 'Last Name', 'wp-leads-press' ),
		'wplp_lead_email' => __( 'Email', 'wp-leads-press' ),
		'wplp_lead_phone' => __( 'Phone', 'wp-leads-press' ),
		'wplp_lead_opportunity' => __( 'Company', 'wp-leads-press' ),
		'wplp_lead_status' => __( 'Lead Status', 'wp-leads-press' ),
		'date' => __( 'Date', 'wp-leads-press' )
	);
	return $columns;
}
add_action( 'manage_lead_posts_custom_column', 'wplp_manage_lead_columns', 10, 2 );
function wplp_manage_lead_columns( $column, $post_id ) {
	global $post, $wplp_admin;
	switch( $column ) {
			
		
		case 'wplp_lead_first_name' :
			// Get the post meta. 
			$wplp_lead_first_name = get_post_meta( $post_id, 'wplp_lead_first_name', true );
			// If not found, output a default message.
			if ( empty( $wplp_lead_first_name ) )
			
				echo __( 'Not Provided' );
			// If set, display it.
			else
			
				echo __( $wplp_lead_first_name, 'wp-leads-press' );
			break;
						
		case 'wplp_lead_last_name' :
			// Get the post meta. 
			$wplp_lead_last_name = get_post_meta( $post_id, 'wplp_lead_last_name', true );
			// If not found, output a default message.
			if ( empty( $wplp_lead_last_name ) )
			
				echo __( 'Not Provided', 'wp-leads-press' );
			// If set, display it.
			else
			
				echo __( $wplp_lead_last_name, 'wp-leads-press' );
			break;			
			
		case 'wplp_lead_email' :
			// Get the post meta. 
			$wplp_lead_email = get_post_meta( $post_id, 'wplp_lead_email', true );
			// If not found, output a default message.
			if ( empty( $wplp_lead_email ) )
			
				echo __( 'Not Provided', 'wp-leads-press' );
			// If set, display it.
			else
			
				echo __( $wplp_lead_email, 'wp-leads-press' );
			break;	
		case 'wplp_lead_phone' :
			// Get the post meta. 
			$wplp_lead_phone = get_post_meta( $post_id, 'wplp_lead_phone', true );
			// If not found, output a default message.
			if ( empty( $wplp_lead_phone ) )
			
				echo __( 'Not Provided', 'wp-leads-press' );
			// If set, display it.
			else
			
				echo __( $wplp_lead_phone, 'wp-leads-press' );
			break;	
		case 'wplp_lead_opportunity' :
			// Get the post meta. 
			$wplp_lead_opp = get_post_meta( $post_id, 'wplp_lead_opportunity', true );
			// If not found, output a default message.
			if ( empty( $wplp_lead_opp ) )
			
				echo __( 'Prior To Tracking', 'wp-leads-press' );
			// If set, display it.
			else
			
				echo __( $wplp_lead_opp, 'wp-leads-press' );
			break;						
					
		case 'wplp_lead_status' :
			/* Get the Status for the post. */
			$terms = get_the_terms( $post_id, 'wplp_lead_status' );
			/* If terms were found. */
			if ( !empty( $terms ) ) {
				$out = array();
				// Loop through terms to display, if found
				foreach ( $terms as $term ) {
					
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'wplp_lead_status' => $term->slug ), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wplp_lead_status', 'display' ) )
					);
				}
				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
				
			}
			/* If no terms were found, output a default message. */
			else {
				
				__( 'Not Set'. 'wp-leads-press' );
				
			}
			break;	
				
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}
// Create taxonomy for custom lead post
add_action( 'init', 'wplp_taxonomy_lead_status', 0 );
function wplp_taxonomy_lead_status() {
	global $wplp_admin;
	
	$labels = array(
		'name'              => _x( 'Lead Status', 'taxonomy general name', 'wp-leads-press' ),
		'singular_name'     => _x( 'Status', 'taxonomy singular name', 'wp-leads-press' ),
		'search_items'      => __( 'Search Statuses', 'wp-leads-press' ),
		'all_items'         => __( 'All Statuses', 'wp-leads-press' ),
		'parent_item'       => __( 'Parent Statuses', 'wp-leads-press' ),
		'parent_item_colon' => __( 'Parent Status:', 'wp-leads-press' ),
		'edit_item'         => __( 'Edit Status', 'wp-leads-press' ), 
		'update_item'       => __( 'Update Status', 'wp-leads-press' ),
		'add_new_item'      => __( 'Add New Status', 'wp-leads-press' ),
		'new_item_name'     => __( 'New Status', 'wp-leads-press' ),
		'menu_name'         => __( 'Statuses', 'wp-leads-press' ),
	);
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
		'update_count_callback' => '_update_post_term_count'
		//'single_value' => true
	);
	register_taxonomy( 'wplp_lead_status', 'lead', $args );
	
wp_insert_term(
  __('Subscribed', 'wp-leads-press' ), // the term 
  'wplp_lead_status', // the taxonomy
  array(
    'description'=> __( 'Subscribed lead. (DO NOT DELETE!)', 'wp-leads-press' ),
    'slug' => 'subscribed',
    //'parent'=> $parent_term_id
  )
);
wp_insert_term( 
	__( 'Unsubscribed', 'wp-leads-press' ), // the term 
  	'wplp_lead_status', // the taxonomy
  	array(
    	'description'=> __( 'Unsubscribed lead. (DO NOT DELETE!)', 'wp-leads-press' ),
    	'slug' => 'unsubscribed',
    	//'parent'=> $parent_term_id
  	)
);
wp_insert_term(
  __( 'Member', 'wp-leads-press' ), // the term 
  'wplp_lead_status', // the taxonomy
  array(
    'description'=> __( 'Member of site. (DO NOT DELETE!)', 'wp-leads-press' ),
    'slug' => 'member',
    //'parent'=> $parent_term_id
  )
);
}
add_action( 'admin_init', 'wplp_lead_details' );
function wplp_lead_details() {
	global $wplp_admin;
	
    add_meta_box( 'wplp_lead_details', __( 'Lead Details', 'wp-leads-press' ), 'wplp_lead_meta', 'lead', 'normal', 'high' );
		
}
// Define Content of meta box
function wplp_lead_meta( $post ) {
	global $wplp_admin;
	
	wp_nonce_field( plugin_basename( __FILE__ ), 'wplp_lead_nonce' );
	
	$ret = '<table>';
	$ret .= '<tr>';
	$ret .= '<td width="130">';
	$ret .= '<label><b>' . __( 'Lead Owner:', 'wp-leads-press' ) . '</b> </label>';
	$ret .= '<p></p>';
	$ret .= '</td>';
	
	$ret .= '<td width="130">';
	$ret .= '<input name="wplp_lead_owner" id="wplp_lead_owner" type="text" size="40" value="' . $post->post_author . wplp_get_lead_field("wplp_lead_owner") . '">';
	$ret .= '<br />'. __( 'Enter Lead Owner Member ID# to change ownership of a lead to another site member.', 'wp-leads-press' ) . '';
	$ret .= '</td>';		
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '</tr>';	
	
		
	$ret .= '<tr>';
	$ret .= '<td width="130">';
	$ret .= '<label><b>' . __( 'First Name:', 'wp-leads-press' ) . '</b> </label>';
	$ret .= '<p></p>';
	$ret .= '</td>';
	
	$ret .= '<td width="130">';
	$ret .= '<input name="wplp_lead_first_name" id="wplp_lead_first_name" type="text" size="40" value="' . wplp_get_lead_field("wplp_lead_first_name") . '">';
	$ret .= '<br />'. __( 'Enter Lead First Name', 'wp-leads-press' ) . '';
	$ret .= '</td>';		
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '</tr>';	
	
	$ret .= '<tr>';	
	$ret .= '<td width="130">';
	$ret .= '<label><b>' . __('Last Name:', 'wp-leads-press') . '</b> </label>';
	$ret .= '<br />';	
	$ret .= '</td>';	
	
	$ret .= '<td width="130">';
	$ret .= '<input name="wplp_lead_last_name" id="wplp_lead_last_name" type="text" size="40" value="' . wplp_get_lead_field("wplp_lead_last_name") . '">';
	$ret .= '<br />'. __( 'Enter Lead Last Name', 'wp-leads-press' ) . '';
	$ret .= '</td>';		
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '</tr>';
	
	$ret .= '<tr>';	
	$ret .= '<td width="130">';
	$ret .= '<label><b>' . __('Email Address:', 'wp-leads-press') . '</b> </label>';
	$ret .= '</td>';	
	
	$ret .= '<td width="130">';
	$ret .= '<input name="wplp_lead_email" id="wplp_lead_email" type="text" size="40" value="' . wplp_get_lead_field("wplp_lead_email") . '">';
	$ret .= '<br />'. __( 'Enter Lead Email', 'wp-leads-press' ) . '';
	$ret .= '</td>';		
	$ret .= '</tr>';
	
	$ret .= '<tr height="20">';
	$ret .= '</tr>';
	
	$ret .= '<tr>';	
	$ret .= '<td width="130">';
	$ret .= '<label><b>' . __('Phone Number:', 'wp-leads-press') . '</b> </label>';
	$ret .= '</td>';	
	
	$ret .= '<td width="130">';
	$ret .= '<input name="wplp_lead_phone" id="wplp_lead_phone" type="text" size="40" value="' . wplp_get_lead_field("wplp_lead_phone") . '">';
	$ret .= '<br />'. __( 'Enter Phone Number', 'wp-leads-press' ) . '';
	$ret .= '</td>';		
	$ret .= '</tr>';

	$ret .= '<tr height="20">';
	$ret .= '</tr>';
	
	$ret .= '<tr>';	
	$ret .= '<td width="130">';
	$ret .= '<label><b>' . __('Lead Notes:', 'wp-leads-press') . '</b> </label>';
	$ret .= '</td>';	
	
	$ret .= '<td width="130">';
	$ret .= '<textarea name="wplp_lead_notes" id="wplp_lead_notes" rows="5" cols="70">' . wplp_get_lead_field("wplp_lead_notes") . '</textarea>';
	$ret .= '<br />'. __( 'Enter Lead Notes', 'wp-leads-press' ) . '';
	$ret .= '</td>';		
	$ret .= '</tr>';		
	
	$ret .= '</table>';
	
	echo $ret;
}
function wplp_get_lead_field($lead_field) {
    global $post;
    $custom = get_post_custom( $post->ID );
    if (isset($custom[$lead_field])) {
        return $custom[$lead_field][0];
    }
}
// Save the post data
add_action( 'save_post', 'wplp_lead_details_save' );
function wplp_lead_details_save( $post_id ) {
	global $wp, $post;
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;
	
	if( isset($_POST['wplp_lead_nonce']) ){
		
		$_POST['wplp_lead_nonce'] = $_POST['wplp_lead_nonce'];	
		
	} else {
		
		$_POST['wplp_lead_nonce'] = NULL;
		
	}
	
	if ( !wp_verify_nonce( $_POST['wplp_lead_nonce'], plugin_basename( __FILE__ ) ) )
	return;
	
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	
	// Set variables to be saved.
	$wplp_lead_first_name = $_POST['wplp_lead_first_name'];
	$wplp_lead_last_name = $_POST['wplp_lead_last_name'];
	$wplp_lead_email = $_POST['wplp_lead_email'];
	$wplp_lead_phone = $_POST['wplp_lead_phone'];	
	$wplp_lead_notes = $_POST['wplp_lead_notes'];
	// Update post meta
	update_post_meta( $post_id, 'wplp_lead_first_name', $wplp_lead_first_name );
	update_post_meta( $post_id, 'wplp_lead_last_name', $wplp_lead_last_name );
	update_post_meta( $post_id, 'wplp_lead_email', $wplp_lead_email );
	update_post_meta( $post_id, 'wplp_lead_phone', $wplp_lead_phone );	
	update_post_meta( $post_id, 'wplp_lead_notes', $wplp_lead_notes );
	
	
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('save_post', 'wplp_lead_details_save');
		
		$post_author = $_POST['wplp_lead_owner'];
		
		$post_args = array(
			'ID'           => $post_id,
			'post_author' => $post_author
		);
		
		// update the post, which calls save_post again
		wp_update_post( $post_args );
		// re-hook this function
		add_action('save_post', 'wplp_lead_details_save');
	}
	
}
// Force only ONE opportunity selected per campaign
add_action('add_meta_boxes','wplp_add_meta_boxes',10,2);
function wplp_add_meta_boxes($post_type, $post) {
  ob_start();
}
add_action('dbx_post_sidebar','wplp_dbx_post_sidebar');
function wplp_dbx_post_sidebar() {
	global $post;
	if( $post->post_type == "campaign" || $post->post_type == "lead" ) {
		$html = ob_get_clean();
		$html = str_replace('"checkbox"','"radio"',$html);
		echo $html;
	}
}
// Add meta to pages for connecting page to a campaign for tracking
add_action( 'add_meta_boxes', 'wplp_meta_box_add' ); 
function wplp_meta_box_add() {
	global $wplp_admin;
	//add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args ); 
	$screens = array( 'page' );
	
		foreach ( $screens as $screen ) {
	
			//add_meta_box( 'myplugin_sectionid', __( 'My Post Section Title', 'myplugin_textdomain' ), 'myplugin_inner_custom_box', $screen );
			add_meta_box( 'wplp-campaign-selection', __( 'WP Leads Press Landing Page Settings', 'wp-leads-press'), 'wplp_meta_box_cb', $screen, 'normal', 'default' );  
			
		}	
	
} 
function wplp_meta_box_cb($post) { 
	//global $post_id;
	wp_nonce_field( plugin_basename( __FILE__ ), 'wplp_campaign_page_meta_save_nonce' );
	
	$values = get_post_custom( $post->ID );  
	
	$selected = $values['wplp_campaign_selected'] ? esc_attr( $values['wplp_campaign_selected'][0] ) : '';
		
	// Get the campaign post type
	$query = new WP_Query(array( 'post_status' => 'publish', 'post_type' => 'campaign', 'posts_per_page' => -1));
	$campaigns = $query->get_posts();
	
	?>	
		<table width="100%">
		<tr>
		<td>
        <label for="wplp_campaign_selected"><b>Select Campaign</b></label>  
        <select name="wplp_campaign_selected" id="wplp_campaign_selected"> 	
	<?php	
	
	// If campaigns found
	if ( ! empty( $campaigns ) ) {
		
		?>
        
		    <option value="NUll"<?php selected( $selected, 'NULL' ); ?>><?php _e( 'No Campaign Selected', 'wp-leads-press' ) ?></option>			
		
		<?php
			
		// Display each Campaign as an option
		foreach ( $campaigns as $campaign ) {
		
		?>	
            <option value="<?php echo $campaign->ID; ?>"<?php selected( $selected, $campaign->ID ); ?>><?php echo $campaign->post_title; ?></option>  
		<?php
		}
		
	}
	/* If no terms were found, output a default message. */
	else {
		?>
		<option value="NULL"><?php _e( 'No campaigns, please create a campaign.', 'wp-leads-press' ); ?></option>
		
		<?php
	}
	?>
        </select>
		
		<p><?php _e( 'Select the campaign for landing page.', 'wp-leads-press' ); ?><br /><br /> 
		<?php _e( 'Visitors of site will be sent to the Destination URL of the campaign selected after they submit the opt-in form or click on a the redirect link.', 'wp-leads-press' ); ?></p>
		</td>
		</tr>
        
		<tr>
		<td width="100">
		<label for="wplp_dest_url_override"><b><?php _e('Internal Page Redirect? ', 'wp-leads-press'); ?></b></label>
        
        <?php 
		if( isset( $values['wplp_dest_url_override'][0] ) ){
			
			$values['wplp_dest_url_override'][0] = $values['wplp_dest_url_override'][0];
			
		} else {
			
		$values['wplp_dest_url_override'][0] = NULL;	
			
		}
		?>
        
        <input name="wplp_dest_url_override" id="wplp_dest_url_override" type="text" value="<?php echo $values['wplp_dest_url_override'][0]; ?>" />
        <p><?php _e( 'If you want to send visitor to another page on your site before you send them to the destination URL of the campaign, enter the relative page location on your domain. For example, to send to, www.yousite.com/second-page, enter "/second-page".', 'wp-leads-press' ); ?></p>
        <p><?php _e( 'Set the campaign settings to the same campaign on "/second-page" and use a Campaign Link Shortcode to finally send your visitor to the Destination URL of the campaign.', 'wp-leads-press' ); ?></p>
		</td>
		</tr>  

		<tr>
		<td>
		<h1><?php _e( 'Show Landing Page?', 'wp-leads-press' ); ?></h1>
		<p class="wrap"><?php _e( 'Select if you want to show this page to your members.', 'wp-leads-press' ); ?></p>		
		</td>
		</tr>      	
			
<?php
			// Get the post meta. 
			$wplp_page_is_active = get_post_meta( $post->ID, 'wplp_page_is_active', true );
			// If no destination URL is found, output a default message.
			if ( empty( $wplp_page_is_active ) ) {

				update_post_meta( $post->ID, 'wplp_page_is_active', 'yes' );	

				//echo __( 'yes' );
				
			}
			
?>

		<tr>
		<td width="100">
		<label><b><?php _e('Show Page: ', 'wp-leads-press'); ?></b></label>
		<select name="wplp_page_is_active" id="wplp_page_is_active">
			<option value="yes" <?php selected( $wplp_page_is_active, 'yes', true ); ?>>yes</option>
			<option value="no" <?php selected( $wplp_page_is_active, 'no', true ); ?>>no</option>
		</select>	
		</td>
		</tr>
		
		<tr>
		<td>
		<h1><?php _e( 'Campaign Opt-in Code', 'wp-leads-press' ); ?></h1>
		<p class="wrap"><?php _e( 'Copy and paste the code below into the subscription forms for the landing page as a standard submit form and customize the styling as needed. WP Leads Press also works with many third party landing page creation plugins on the market, which you can use to create your landing pages and then either use the opt-in code or campaign shortcode below.', 'wp-leads-press' ); ?></p>		
		</td>
		</tr>
		
		<?php	
		
		$adminUrl = admin_url( 'images/wpspin_light.gif' );	
		
//		$wplp_form_code = '<form action="' . $url . '/wp-leads-press/inc/wplp-functions/create-lead.php/?wplp_campaign=' . $wplp_campaign_selected . '&wplp_landing_page=' . $post->ID . '" method="POST" id="wplp_create_lead" name="wplp_create_lead">

//		$wplp_form_code = '<form action="[wplp_plugins_url]/wp-leads-press/inc/wplp-functions/create-lead.php/?wplp_campaign=[wplp_selected_campaign]&wplp_landing_page=[wplp_landing_page]" method="post" id="wplp_create_lead" name="wplp_create_lead">

//		$wplp_form_code = '<form action="?sublead=yes&wplp_campaign=[wplp_selected_campaign]&wplp_landing_page=[wplp_landing_page]" method="post" id="wplp_create_lead" name="wplp_create_lead" class="wplp_create_lead">	
		$wplp_form_code = '<form action="?sublead=yes" method="post" id="wplp_create_lead" name="wplp_create_lead" class="wplp-create-lead">	
	[wplp_form_nonce]	
	<input type="hidden" name="wplp_campaign" value=[wplp_selected_campaign]>
	<input type="hidden" name="wplp_landing_page" value=[wplp_landing_page]>
	
	<fieldset>
		<label for="wplp_lead_first_name">Name  <span class="asterisk">*</span></label>
		<input type="text" value="" name="name" id="name">
	</fieldset>
	
	<fieldset>
		<label for="wplp_lead_email">Email:  <span class="asterisk">*</span></label>
		<input type="email" value="" name="email" id="email">
	</fieldset>
	
	<fieldset>
		<label for="wplp_lead_phone">Phone:</label>
		<input type="tel" value="" name="phone" id="phone">
	</fieldset>	

	<fieldset>
		<input type="submit" formtarget="_blank" value="Subscribe" name="wplp_create_lead_submit" id="wplp_create_lead_submit" class="wplp-create-lead">
	</fieldset>
	
</form>';

		$shortcode = wplp_get_campaign_shortcode_page( $post->ID );
//		$shortcode = wplp_campaign_link( $post->ID );
		
		?>
		
		<tr>
		<td>
			<textarea cols="100" rows="25" readonly="readonly"><?php echo $wplp_form_code ?></textarea>
			<p><?php _e( '***The Phone Field is optional and can be removed from the opt-in form if desired.', 'wp-leads-press' ); ?></p> 
			<p><?php _e( 'NOTICE: If you are inserting the opt-in code into a landing page plugin system, such as InstaBuilder and the form code doesn\'t work when submitting a lead, you will need to manually add your Campaign and Landing Page ID\'s to replace the shortcodes [wplp_selected_campaign] and [wplp_landing_page] in order for your form to work properly after pasting the code into the landing page plugin system. This is because the other plugin is interferring with how WP processes shortcodes.', 'wp-leads-press' ); ?><br /><br />
			<h2><?php _e( 'Campaign and Landing Page ID\'s', 'wp-leads-press' ); ?></h2>
			<b><?php _e( 'Your Campaign ID: ', 'wp-leads-press' ); ?></b><?php echo wplp_selected_campaign(); ?> <?php _e( '(If you have updated this post already.) or visit the All Campaigns page to get the ID', 'wp-leads-press' ); ?>
			<br /><b><?php _e( 'Landing Page ID: ', 'wp-leads-press' ); ?></b><?php echo $post->ID; ?>
			</p>
		</td>
		</tr>
		
		<tr>
		
		<td><h1><?php _e( 'Campaign Image Link Shortcode', 'wp-leads-press' ); ?></h1>
			<p><?php _e( 'Use this instead of the form code above if you just want to redirect traffic and not collect a name and email. This shortcode will create a redirect link to the correct destination URL of the campaign selected above, just insert and save. Additional stock images available in the WP Leads Press folder, wp-leads-press/assets/images/ or you can use any image you desire by providing the link to the image url.', 'wp-leads-press' ); ?></p>
			<?php echo $shortcode; ?>
		</td>
		</tr>
		
		</table>
		
	<?php       
}
function wplp_get_landing_page_field($landing_page_field) {
    global $post;
    $custom = get_post_custom( $post->ID );
    if (isset($custom[$landing_page_field])) {
        return $custom[$landing_page_field][0];
    }
}

function wplp_get_campaign_shortcode_page( $post_id ) {
	global $post;
	$url = plugins_url();
	$ret = "<input type='text' size='100' readonly='readonly' value='[wplp_campaign_link imglink=\"" . $url . 'target="_blank"' . "/wp-leads-press/assets/images/orange_signupnow.png\"]'>";
	
	return $ret;
	
}

add_shortcode( 'wplp_selected_campaign', 'wplp_selected_campaign' );
function wplp_selected_campaign(){
	global $post;

//error_log( print_r($post), true );
	
	$wplp_campaign_selected = get_post_meta( $post->ID, 'wplp_campaign_selected', true );

//error_log( print_r($wplp_campaign_selected), true );
	
	return $wplp_campaign_selected;				
}

add_shortcode( 'wplp_landing_page', 'wplp_landing_page' );
function wplp_landing_page(){
	global $post;
	return $post->ID;	
}
add_shortcode( 'wplp_plugins_url', 'wplp_plugins_url' );
function wplp_plugins_url(){
	global $post;
	$url = plugins_url();	
	return $url;	
}
add_shortcode( 'wplp_form_nonce', 'wplp_form_nonce' );
function wplp_form_nonce(){
	
	$fnonce = wp_nonce_field('wplp_create_lead','wplp_form_nonce_front_post');	
	return $fnonce;
}


// Save the post data
add_action( 'save_post', 'wplp_campaign_page_meta_save' );
function wplp_campaign_page_meta_save( $post_id ) {
	//global $wp, $post;
	
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;
	
	if( isset($_POST['wplp_campaign_page_meta_save_nonce']) ){
		
		$_POST['wplp_campaign_page_meta_save_nonce'] = $_POST['wplp_campaign_page_meta_save_nonce']; 	
		
	} else {
		
		$_POST['wplp_campaign_page_meta_save_nonce'] = NULL;
		
	}
	
	if ( !wp_verify_nonce( $_POST['wplp_campaign_page_meta_save_nonce'], plugin_basename( __FILE__ ) ) )
	return;
	
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	
	// Set variables to be saved.
	$campaign_selected = $_POST['wplp_campaign_selected'];
	$wplp_page_is_active = $_POST['wplp_page_is_active'];
	$wplp_dest_url_override = $_POST['wplp_dest_url_override'];
	
	//$campaign_tracking_id = $_POST['campaign_tracking_id'];
	
	// update post meta
	update_post_meta( $post_id, 'wplp_campaign_selected', $campaign_selected );
	update_post_meta( $post_id, 'wplp_page_is_active', $wplp_page_is_active );
	update_post_meta( $post_id, 'wplp_dest_url_override', $wplp_dest_url_override );
	
	//update_post_meta( $post_id, 'campaign_tracking_id', $campaign_tracking_id );
}
// Used for conditional display depending on post type
function is_post_type($type){
    global $wp_query;
    if($type == get_post_type($wp_query->post->ID)) return true;
    return false;
}
// Usage
//if (is_single() && is_post_type('post_type')){
//  //work magic
//} 




//add_action( 'restrict_manage_posts', 'wplp_lead_search_box' );
function wplp_lead_search_box() {
	
	// only add search box on desired custom post_type listings
	global $typenow, $wpdb;
	
	if ($typenow == 'lead') {
	 
		add_filter( "pre_get_posts", "wplp_custom_search_query");
		add_action( "save_post", "wplp_add_title_custom_field");
		
	 }
	 
}

function wplp_custom_search_query( $query ) {
	
	$custom_fields = array(
	// put all the meta fields you want to search for here
	"post_title",
	"_post_title",
	"post_content",
	"author",
	"wplp_lead_email",
	"wplp_lead_phone",
	"wplp_lead_opportunity",
	"wplp_lead_status",
	"date",
	);	
	
	$searchterm = $query->query_vars['s'];
	// we have to remove the "s" parameter from the query, because it will prevent the posts from being found
	
	$query->query_vars['s'] = "";
	
	if ($searchterm != "") {
		
		$meta_query = array('relation' => 'OR');
		
		foreach($custom_fields as $cf) {
			
			array_push($meta_query, array(
			'key' => $cf,
			'value' => $searchterm,
			'compare' => 'LIKE'
			));

		}

	return	$query->set("meta_query", $meta_query);
	
	}
	
}


function wplp_add_title_custom_field($postid){
	
	// since we removed the "s" from the search query, we want to create a custom field for every post_title. I don't use post_content, if you also want to index this, you will have to add this also as meta field.
	update_post_meta($postid, "_post_title", $_POST["post_title"]);

}
 
?>