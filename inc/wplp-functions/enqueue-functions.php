<?php
// Add Custom CSS and JS
add_action( 'sunrise/assets/register', 'wplp_register_styles_scripts' );	
function wplp_register_styles_scripts(){
	
	// Register styles
	//wp_register_style( $handle, $src, $deps, $ver, $media );
	
	// Register scripts
	//wp_register_script( $handle, $src, $deps, $ver, $in_footer );
	wp_register_script( 'wplp_js_back', plugins_url( '../assets/js/backend.js', dirname(__FILE__) ), array ( 'jquery' ), WPLP_VERSION );
	wp_register_script( 'jquery-ui-core', ABSPATH . '/wp-includes/js/jquery/ui/jquery.ui.core.min.js' );	
	wp_register_script( 'jquery-ui-widget', ABSPATH . '/wp-includes/js/jquery/ui/jquery.ui.widget.min.js' );			
	wp_register_script( 'jquery-ui-button', ABSPATH . '/wp-includes/js/jquery/ui/jquery.ui.button.min.js' );	
	wp_register_script( 'blockUI', 'https://malsup.github.io/jquery.blockUI.js', array ( 'jquery' ), WPLP_VERSION );	
		
}
add_action( 'wp_enqueue_scripts', 'wplp_register_styles_scripts_front' );
function wplp_register_styles_scripts_front(){
	
	// Register scripts

	wp_register_script( 'jquery-ui-core', ABSPATH . '/wp-includes/js/jquery/ui/jquery.ui.core.min.js' );	
	wp_register_script( 'jquery-ui-widget', ABSPATH . '/wp-includes/js/jquery/ui/jquery.ui.widget.min.js' );			
	wp_register_script( 'jquery-ui-button', ABSPATH . '/wp-includes/js/jquery/ui/jquery.ui.button.min.js' );	
	wp_register_script( 'wplp_js_front', plugins_url( '../assets/js/frontend.js', dirname(__FILE__) ), array ( 'jquery' ), WPLP_VERSION );	
	wp_register_script( 'blockUI', 'https://malsup.github.io/jquery.blockUI.js', array ( 'jquery' ), WPLP_VERSION );	
	wp_register_script( 'wplp_js_tabs', plugins_url( '../assets/js/tabs.js', dirname(__FILE__) ), array ( 'jquery' ), WPLP_VERSION );	
}

add_action( 'sunrise/assets/enqueue', 'wplp_enqueue_styles_scripts' );
add_action( 'wp_enqueue_scripts', 'wplp_enqueue_styles_scripts' );
function wplp_enqueue_styles_scripts(){
	
	// enqueue js
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-ui-accordion');
		
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-ui-button' );			
	wp_enqueue_script( 'blockUI', 'https://malsup.github.io/jquery.blockUI.js', array ( 'jquery' ), WPLP_VERSION, false );
	
	wp_enqueue_script( 'wplp_js_back', plugins_url('../assets/js/backend.js', dirname(__FILE__) ), array ( 'jquery' ), WPLP_VERSION, false );	
	
	wp_enqueue_script( 'wplp_js_tabs', plugins_url('../assets/js/tabs.js', dirname(__FILE__) ), array ( 'jquery' ), WPLP_VERSION, false );
	wp_enqueue_style('jquery-ui-style','https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/flick/jquery-ui.css');	
	
	//wp_localize_script( 'wplp_js_tabs', 'wplp_tabs_front' );
	
	// localize js	
	wp_localize_script( 'wplp_js_back', 'wplpajaxobj', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'wplp_form_nonce' ) ) );		

}

add_action( 'wp_enqueue_scripts', 'wplp_enqueue_styles_scripts_front' );
function wplp_enqueue_styles_scripts_front(){
	
	// enqueue js
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-ui-accordion');
		
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-ui-button' );			
	wp_enqueue_script( 'blockUI', 'https://malsup.github.io/jquery.blockUI.js', array ( 'jquery' ), WPLP_VERSION, false );
	wp_enqueue_script( 'wplp_js_front', plugins_url('../assets/js/frontend.js', dirname(__FILE__) ), array ( 'jquery' ), WPLP_VERSION, false );
	wp_enqueue_script( 'wplp_js_tabs', plugins_url('../assets/js/tabs.js', dirname(__FILE__) ), array ( 'jquery' ), WPLP_VERSION, false );
	wp_enqueue_style('jquery-ui-style','https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/flick/jquery-ui.css');	
		
	// localize js/css
	wp_localize_script( 'wplp_js_front', 'wplpajaxobjfront', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'nonce' =>wp_create_nonce( 'wplp_form_nonce_front' ), 'enter_email' => __( 'Please enter a valid email address.', 'wp-leads-press' ), 'wplp_connected' => __( '<h1>Just a moment</h1><br /><br />Processing your request...', 'wp-leads-press' ), 'wplp_deleting' => __( '<h1>Just a moment</h1><br /><br />Deleting Lead...', 'wp-leads-press' ), 'wplp_loading_lead' => __( '<h1>Just a moment</h1><br /><br />Loading Lead Details...', 'wp-leads-press' ), 'wplp_updating_lead' => __( '<h1>Just a moment</h1><br /><br />Updating Lead...', 'wp-leads-press' ), 'wplp_update_affiliate_settings' => __( '<h1>Just a moment</h1><br /><br />Updating your settings...', 'wp-leads-press' ), 'wplp_connect_aweber_api' => __( '<h1>Just a moment</h1><br /><br />Connecting to Aweber...', 'wp-leads-press' ), 'wplp_connect_aweber_api_success' => __( '<h1>Success!</h1><br /><br />Aweber Connected', 'wp-leads-press' ) ) );		
	
}
?>