<?php
#############
//traffic redirect //Sends traffic to correct sign up link when clicked
#############
function wplp_campaign_link($atts) {
	global $post, $wp, $wpdb, $_POST, $_GET;
	
	# Add 'opp' attribute
	extract( shortcode_atts( array(
		'campaign' => '',
		'landingpage' => '',
		'imglink' => '',
		'buttontext' => '',
		'textlink' => 0,
		'newtab' => 'yes'
		), $atts ) );
		
	if ( $campaign == '' ){
		
		$campaign = get_post_meta( $post->ID, 'wplp_campaign_selected', true );
		
		if ( ! isset( $campaign ) ){
				
			$campaign = 1;
			
		}
		
	}
	
	if ( $landingpage == '' ){
		
		$landingpage = $post->ID;
		
		if ( ! isset( $landingpage ) ){
			
			$landingpage = 1;
			
		}
		
	}

	if ( $buttontext == '' ){
		
		$buttontext = __( 'Click To Join', 'wp-leads-press' );	
		
	}
	
	//Create Form to send post data for traffic redirect
	
	$url = plugins_url();
	$newtab='yes';
	
	if ( $textlink == '0' ) {
		
		$ret = wplp_form_nonce();
		
		$ret .= '<div class="wplp-inputs">';		
		$ret .= "<input type='hidden' name='wplp_campaign' value='" . $campaign . "'>";
		$ret .= "<input type='hidden' name='wplp_landing_page' value='" . $landingpage . "'>";
		
			
		if ( $imglink == '' ) {
				$newtab='yes';			
				if( $newtab == 'no' ){
					 
					$ret .= '<input type="button" value="'.$buttontext.'" name="wplp_traffic_redirect" class="wplp-traffic-redirect">';
					
				}
             
				if( $newtab == 'yes' ){
					 
				 	$ret .= '<input type="button" formtarget="_blank" value="'.$buttontext.'" name="wplp_traffic_redirect" class="wplp-traffic-redirect">';
					//$ret .= '<input type="button" formtarget="_blank" value="'.$buttontext.'" name="wplp_traffic_redirect" class="wplp-traffic-redirect">';
					
					
					
				}				
							
		} else {
				$newtab='yes';			
				if( $newtab == 'no'){
					
					$ret .= '<input type="image" value="'.$buttontext.'" src="' . $imglink . '" name="wplp_traffic_redirect" class="wplp-traffic-redirect">';
				
				}
				
				if( $newtab == 'yes' ){
				
					$ret .= '<input type="image" value="'.$buttontext.'" src="' . $imglink . '" formtarget="_blank" name="wplp_traffic_redirect" class="wplp-traffic-redirect">';
					//$ret .= '<input type="image" value="'.$buttontext.'" src="' . $imglink . '" target="_blank" name="wplp_traffic_redirect" class="wplp-traffic-redirect">';
				//	$ret = '<a href="?wplp_campaign='. $campaign .'&wplp_landing_page='.$landingpage.'&subtraffic=yes" name="wplp_traffic_redirect" class="wplp-traffic-redirect" target="_blank">'. $textlink .'</a>';

	
				}
							
		}	
			
		$ret .= '</div>';
		

	}
	
	// Lets create a link that can be clicked to do the same...
	if ( $textlink != '0' ) {
	

		$newtab='yes';
		if( $newtab == 'no' ){
			
			$ret = '<a href="?wplp_campaign='. $campaign .'&wplp_landing_page='.$landingpage.'&subtraffic=yes" name="wplp_traffic_redirect" class="wplp-traffic-redirect" target="_blank">'. $textlink .'</a>';
			
		}
$newtab='yes';
		if( $newtab == 'yes' ){
			
			$ret = '<a href="?wplp_campaign='. $campaign .'&wplp_landing_page='.$landingpage.'&subtraffic=yes" name="wplp_traffic_redirect" class="wplp-traffic-redirect" target="_blank">'. $textlink .'</a>';
	        $errmsg="ret=" . $ret;
			BugFu:log($errmsg);
		}		
	
	}
	
	// Lets create a link which is just the destination URL
	// need to grab the destination URL of the $campaign then pass the raw url, sans the user ID in the link.
//	if ( $textlink != '0' ) {
//	
//		//$ret .= '<fieldset>';
//		//$ret .= '<input type="image" value="'.$textlink.'" alt="'.$textlink.'" formtarget="_blank" name="wplp_traffic_redirect" class="wplp-traffic-redirect">';
//		//$ret .= '<a href="?subtraffic=yes" target="_blank" name="wplp_traffic_redirect" class="wplp-traffic-redirect" >'. $textlink . '</a>';
//		$ret = '<a href="?wplp_campaign='. $campaign .'&wplp_landing_page='.$landingpage.'&subtraffic=yes" name="wplp_traffic_redirect" class="wplp-traffic-redirect" target="_blank">'. $s .'</a>';
//		//$ret .= '</fieldset>';
//	
//	}		
	
	return $ret;
	
}
add_shortcode( 'wplp_campaign_link', 'wplp_campaign_link' );
?>