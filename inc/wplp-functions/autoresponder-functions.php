<?php

function wplp_httpPost( $url, $params ){
	
  $postData = '';
  
   //create name value pairs seperated by &
   foreach( $params as $key => $value ){
	    
      $postData .= $key . '='.$value.'&'; 
   
   }
   
   rtrim( $postData, '&' );
 
    $ch = curl_init();  
 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false); 
   	curl_setopt($ch, CURLOPT_POST, count($postData));	
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
 
    $output = curl_exec($ch);
 
	//error_log( $postData );
	//error_log( print_r( $output, true ). 'output' );
	//error_log( print_r( $ch, true ) );
 
    curl_close($ch);
    //return $output; //Retuning output causes error in browser 414 error url too long
 
}

?>