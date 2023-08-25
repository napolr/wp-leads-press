<?php

function wplp_add_contact_get_response($name, $email, $key, $campaign_name){
	
	require_once('GetResponseAPI.class.php');
	
	// API Key
	$api = new GetResponse( $key );
	
	// Get campaign by name
	$campaign = $api->getCampaignByName($campaign_name);
	
	// Add Contact to Campaign
	
	$addContact = $api->addContact($campaign, $name, $email, $action = 'standard', $cycle_day = 0, $customs = array());
	
	//var_dump($campaign_name, $campaign, $addContact);

}

?>