<?php
// Update lead		
add_action( 'wp_ajax_nopriv_wplp_update_lead', 'wplp_update_lead' );
add_action( 'wp_ajax_wplp_update_lead', 'wplp_update_lead' );
function wplp_update_lead(){
	global $wp, $wpdb, $_GET, $_POST;
	
	check_ajax_referer('wplp_form_nonce_front'); // Check for our nonce from AJAX
	
	if( isset( $_POST['wplp_lead_id_update'] ) ){
		
		$lead = $_POST['wplp_lead_id_update'];
		
	}
	
	if( isset( $_POST['wplp_lead_home_page'] ) ){
		
		$url = $_POST['wplp_lead_home_page'];
		
	}
	
	$custom_fields = get_post_custom($lead);
	
	// Update the post meta
	if( isset( $_POST['wplp_lead_first_name'] ) ) {

		if( !isset( $custom_fields['wplp_lead_first_name'] ) ) {
		
			$custom_fields['wplp_lead_first_name'] = 'no value';
	
		}			
		
		update_post_meta($lead, 'wplp_lead_first_name', $_POST['wplp_lead_first_name'], $custom_fields['wplp_lead_first_name'][0]);
	
	}
	
	if( isset($_POST['wplp_lead_last_name'])){

		if( !isset( $custom_fields['wplp_lead_last_name'] ) ) {
		
			$custom_fields['wplp_lead_last_name'] = 'no value';
	
		}			
		
		update_post_meta($lead, 'wplp_lead_last_name', $_POST['wplp_lead_last_name'], $custom_fields['wplp_lead_last_name'][0]);
		
	}
	
	if( isset($_POST['wplp_lead_email'])){

		if( !isset( $custom_fields['wplp_lead_email'] ) ) {
		
			$custom_fields['wplp_lead_email'] = 'no value';
	
		}		
		
		update_post_meta($lead, 'wplp_lead_email', $_POST['wplp_lead_email'], $custom_fields['wplp_lead_email'][0]);
		
	}
	
	if( isset( $_POST['wplp_lead_phone'] )){
		
		if( !isset( $custom_fields['wplp_lead_phone'] ) ) {
		
			$custom_fields['wplp_lead_phone'] = 'no value';
	
		}
		
		update_post_meta($lead, 'wplp_lead_phone', $_POST['wplp_lead_phone'], $custom_fields['wplp_lead_phone'][0]);
	
	}
	
	if( isset( $_POST['wplp_lead_notes'] )){
		
		if( !isset( $custom_fields['wplp_lead_notes'] ) ) {
		
			$custom_fields['wplp_lead_notes'] = 'no value';
	
		}

		update_post_meta($lead, 'wplp_lead_notes', $_POST['wplp_lead_notes'], $custom_fields['wplp_lead_notes'][0]);					
			
		
	}
	
	if( isset( $_POST['wplp_lead_status'])){
		
		$term = $_POST['wplp_lead_status'];
		
	}
	
	$taxonomy = 'wplp_lead_status';
	wp_set_post_terms( $lead, $term, $taxonomy );
	
	//$url_location = $url;
	$url_location =	htmlspecialchars($url);
	
	echo $url_location;
	
	die();
		
}// end save

// Delete Action
add_action( 'wp_ajax_nopriv_wplp_delete_lead', 'wplp_delete_lead' );
add_action( 'wp_ajax_wplp_delete_lead', 'wplp_delete_lead' );
function wplp_delete_lead(){
	global $wp, $wpdb, $_GET, $_POST;
	
	check_ajax_referer('wplp_form_nonce_front'); // Check for our nonce from AJAX
	$leadID = $_POST['wplp_lead_id'];
	wp_trash_post( $leadID );
	
	die();
}
// View Action
add_action( 'wp_ajax_nopriv_wplp_view_lead', 'wplp_view_lead' );
add_action( 'wp_ajax_wplp_view_lead', 'wplp_view_lead' );
function wplp_view_lead(){
	global $wp, $wpdb, $_GET, $_POST;
	check_ajax_referer('wplp_form_nonce_front'); // Check for our nonce from AJAX
	
	$lead = $_POST['wplp_lead_id'];
	$url_location = '?action=edit:lead=' . $lead;
	
	echo $url_location;
		
	die();
	
}

add_action( 'wp_ajax_nopriv_wplp_convert_to_csv', 'wplp_convert_to_csv' );
add_action( 'wp_ajax_wplp_convert_to_csv', 'wplp_convert_to_csv' );
function wplp_convert_to_csv(){
	global $_GET, $_POST, $wp, $wpdb, $current_user;

	//check_ajax_referer('wplp_form_nonce_front'); // Check for our nonce from AJAX

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

		$input_array = get_posts( $args );    
		$output_file_name = 'lead_report.csv';
		$delimiter = ',';	
	
	/** open raw memory as file, no need for temp files */
    $temp_memory = fopen('php://memory', 'w');
    /** loop through array  */
    foreach ($input_array as $line) {
        /** default php csv handler **/
        fputcsv($temp_memory, $line, $delimiter);
    }
    /** rewrind the "file" with the csv lines **/
    fseek($temp_memory, 0);
    /** modify header to be downloadable csv file **/
    header('Content-Type: application/csv');
    header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
    /** Send file to browser for download */
    fpassthru($temp_memory);
	
	//return true;
	die();
}
?>