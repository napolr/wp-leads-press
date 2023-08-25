<?php
#############
//Unsubscribe Lead
#############
function wplp_unsubscribe_lead($unsubEmail){
	global $wp, $wpdb, $_POST, $_GET;

	//Get all Posts(leads) with postmeta = email address of lead passed in URL parameters ?unsub=you@email.com
	$args = array(
		'author'		   => '',
		'posts_per_page'   => -1,
		'offset'           => 0,
		'category'         => '',
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => 'wplp_lead_email',
		'meta_value'       => $unsubEmail,
		'post_type'        => 'lead',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'post_status'      => 'publish',
		'suppress_filters' => true );
	
	if ( $leads = get_posts( $args ) ) {
		
		//For each post/lead, set post status to - unsubscribed
		
		foreach ( $leads as $lead ){
			
			//Lead ID
			$post_id = $lead->ID;
			
			//Set to unsubscribed.
			$term = 'Unsubscribed';
			$taxonomy = 'wplp_lead_status';

			//Get the ID of the term required for term_id as is heirarchy.
			$term_id = term_exists( $term, $taxonomy );
			
			wp_set_post_terms( $post_id, $term_id, $taxonomy );
			
			//Set to trash, send no more email, yet store to resurrect if needed.
			wp_trash_post( $post_id );
			
		}
		
		//Show message to client stating email is unsubscribed.	
		//return '<div style="width: 500px; margin-left: auto; margin-right: auto;">' . _e( 'Your email address has been unsubscribed.', 'wp-leads-press' ) . '</div>';
		echo '<script>alert("' . __( 'Your email address has been unsubscribed.', 'wp-leads-press' ) . '")</script>';
	
	} else { 
		
		echo '<script>alert("' . __( 'The email address submitted is not on our list, please try again.', 'wp-leads-press' ) . '")</script>';	
	
	}
		
}
?>