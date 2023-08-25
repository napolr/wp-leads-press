<?php
#############
//Registration Code - Shows current registration code for site.
#############
function wplp_registration_code($atts) {
	global $wp, $wpdb, $_POST, $_GET;
	//Get all wplp options
	$options = wp_load_alloptions();
	$regCode = $options['wp_leads_press_registration_code'];	
		
	// Check if user is logged in
	if ( is_user_logged_in() ) {	
		$ret = '<p>' . __( 'Send this registration code to your new members: ', 'wp-leads-press' ) . $regCode . '</p>';
		
		return $ret;
	
	
	} else {
			
		$ret = __( 'You must be logged in to view the registration code. Registration on this site is by invitation of current members only.', 'wp-leads-press' );
		
		return $ret;
	
	}
	
}
add_shortcode( 'wplp_registration_code', 'wplp_registration_code' );

function wplp_show_ref_member(){
	
	// Get user as referrer
	if( isset( $_COOKIE['wplp-ref'] ) && !empty( $_COOKIE['wplp-ref'] ) ) {
		
		$ref = $_COOKIE['wplp-ref'];
		
	}
	
	if( isset( $_GET['ref'] ) && !empty( $_GET['ref'] ) ){
		
		$ref = $_GET['ref'];	
		
	}
	
	if( !isset( $_COOKIE['wplp-ref'] ) && ( !isset($_GET['ref'] ) ) ){ // If user was not referred by anyone or no cookie!
			
			$ret = '<div class="wplp-show-ref-member"><h3>' . __( 'Welcome Visitor', 'wp-leads-press' ) . '</h3></div>';	
	
			return $ret;
	
	}	
		
		$field = 'slug';
		$value = $ref;
		$ref = get_user_by( $field, $value ); // Get user obj
		
		// If the user exists
		if( $ref == true ) {	
			
			$ret = '<div class="wplp-show-ref-member"><h3>' . __( 'Referred By: ', 'wp-leads-press' ) . $ref->display_name . '</h3></div>';		
			
			return $ret;	
			
		}
					
		// If the user id does NOT exist
		if( $ref == false ) { 
		
			$ret = '<div class="wplp-show-ref-member"><h3>' . __( 'Welcome Visitor', 'wp-leads-press' ) . '</h3></div>';	
			
			return $ret;			
		
		}
	
}
add_shortcode( 'wplp_show_ref_member', 'wplp_show_ref_member' );

function wplp_member_network(){
	global $wp, $bp, $wpdb, $current_user, $post_id, $_GET, $_POST;

	if ( is_user_logged_in() ) {	
	
		// Get current users id
		$user_id = $current_user->ID;
		
		// Get current user direct upline
		$user_referrer_id	= wplp_get_parent_user_id($user_id);
		
		// Get referring user information
		$field = 'id';
		$value = $user_referrer_id;
		$user_referrer = get_user_by( $field, $value ); // Get user obj	
		
		###
		# Rewrite this section, show different info if referred by '0' vs. referred by FALSE.
		###
		
		if ( ( $user_referrer == false ) || ( $user_referrer->ID == 0 ) ){
			
			$user_referrer_obj = new stdClass();
			$user_referrer_obj->user_nicename = __( 'Direct', 'wp-leads-press' );
			$user_referrer_obj->user_email = 'NA';
				
		}
		
		
		if( function_exists( 'buddypress' ) ){
	
			### use member id to get buddypress user profile info
			//$user_referrer_id = implode(',', $user_referrer_id );
	
			if ( ( $user_referrer == false ) || ( $user_referrer->ID == 0 ) ){
					
				$ret = '<h2>'. __( 'Your Referrer: ', 'wp-leads-press' ) . $user_referrer_obj->user_nicename . '</h2>';
				$ret .= '<p><strong>'. __( 'Contact: ', 'wp-leads-press' ) . $user_referrer_obj->user_email . '</strong></p>'; 
					
			} else {
				
				$ret = '<h2>'. __( 'Your Referrer: ', 'wp-leads-press' ) .'</h2>';
				
				$args = array( 'include' => $user_referrer_id, 'per_page' => 1, 'type' => 'alphabetical' );
				$ret .= wplp_bp_has_members_sponsor( $args );
				
			}
		
		} else {
			

			if ( ( $user_referrer == false ) || ( $user_referrer->ID == 0 ) ){
					
				$ret = '<h2>'. __( 'Your Referrer: ', 'wp-leads-press' ) . $user_referrer_obj->user_nicename . '</h2>';
				$ret .= '<p><strong>'. __( 'Contact: ', 'wp-leads-press' ) . $user_referrer_obj->user_email . '</strong></p>'; 
					
			} else {			
				
			$ret = '<h2>'. __( 'Your Referrer: ', 'wp-leads-press' ) . $user_referrer->user_nicename . '</h2>';
			$ret .= '<p><strong>'. __( 'Contact: ', 'wp-leads-press' ) .'<a href="mailto:'.$user_referrer->user_email.'">'.$user_referrer->user_email.'</a></strong></p>'; 
			
			}
			
		}
		
		$ret .= '<br />';
	
		// Get total number of children below member
		$children = wplp_get_children($user_id);
		
		//$user_network = array_column($user_network[0], 'user_id');
		
		// Get data of personally referred members
		$personal_members = wplp_get_network_personals($user_id);
		
		// Get count of personals
		
		if( !is_null( $personal_members ) ){
		
			$user_personal_count = count($personal_members);
			
			//$member_ids = implode(",",$personal_members);
			$member_ids = wplp_array_values_recursive( 'user_id', $personal_members );
			
			//check if $member_ids is an array
			if( is_array( $member_ids ) ){
				
				$member_ids = implode( ',', $member_ids );			
				
			} else {
				
				$member_ids = $member_ids;	
				
			}
			
		} else {
			
			$user_personal_count = 0;
			$member_ids = 0;
			
		}
		
		

		if( $member_ids != 0 ) {
					
			// Get referred user information
			$args = array(
				'blog_id'      => $GLOBALS['blog_id'],
				'include'      => $member_ids,
				'exclude'      => array(),
				'orderby'      => 'ID',
				'order'        => 'ASC',
				'number'       => '',
				'count_total'  => false,
				'fields'       => 'all',
			 );
			
			$members = get_users( $args );	
		
		} else {
			
			$members = NULL;
			
		}
		
		// Get total number of users in network
		$user_network_total = wplp_get_network_total($user_id);
		$user_network_count = count($user_network_total);	
		
		$ret .= '<h2>'. __( 'Your Total Network: ', 'wp-leads-press' ) .  $user_network_count . '</h2>';	
		$ret .= '<p>'. __( 'Your Personals: ', 'wp-leads-press' ) .  $user_personal_count . '</p>';		
		//$ret .= '<p>'. __( 'Personally Referred Members Contact Info: ', 'wp-leads-press' ).'</p>';
	
		//$ret .= '<pre>'.print_r($members, true ).'</pre>';	
	
		$ret .= '<table>';
				
		if( function_exists( 'buddypress' ) ){
			// Do BuddyPress Stuff
			
			### use member id to get buddypress user profile info
			$args = array( 'include' => $member_ids, 'per_page' => 10, 'type' => 'alphabetical' );
			$ret .= wplp_bp_has_members( $args );		
		
		} else {
			
			
			if( $members != NULL ) {
				
				$ret .= '<tr>';	
				$ret .= '<td>ID</td><td>Name</td><td>Email</td>';
				$ret .= '</tr>';		
	
				foreach( $members as $member ){
				
					$ret .= '<tr>';
					$ret .= '<td>'.$member->ID.'</td>';			
					$ret .= '<td>'.$member->display_name.'</td>';
					$ret .= '<td><a href="mailto:'.$member->user_email.'">'.$member->user_email.'</a></td>';
					$ret .= '</tr>';
							
				}	
			
			} else {
				
				$ret .= __( 'You have no referred members yet!', 'wp-leads-press' );	
				
			}
			
		}
		
		$ret .= '</table>';
			
		return $ret;
		
	} else {
		
		return __( 'You must be logged in to view your Member Network', 'wp-leads-press' );	
		
	}
	
}
add_shortcode( 'wplp_member_network', 'wplp_member_network' );
?>