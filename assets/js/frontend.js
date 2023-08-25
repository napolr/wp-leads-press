jQuery(document).ready(function($) {

	// AJAX for create lead forms
	$(".wplp-create-lead").submit( function() { 	
		
		// Check to confirm at least the email field is filled in and valid
		var $email = $('#email'); //change form to id or containment selector
		var re = /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm;
		if ($email.val() == '' || !re.test($email.val()))
		{
			alert( wplpajaxobjfront.enter_email );
		    return false;
		}
		
		// Don't allow access to the page while we process data.
		$.blockUI({ 
			
			//message: '<h1>Just a moment</h1><br /><br />You are being connected now...', 
			message: wplpajaxobjfront.wplp_connected, 
			css: { 
                top:  ($(window).height() - 400) /2 + 'px', 
                left: ($(window).width() - 400) /2 + 'px', 
                width: '400px',
				Height: '250px', 
				padding: '10px'
            } 
			
			});		
	

		// Convert to name value pairs			
		// Define a data object to send to our PHP		
				
			$.fn.serializeObject = function() {

				var arrayData, objectData;
				arrayData = this.serializeArray();
				objectData = {};

				$.each(arrayData, function() {
					var value;
			
				if (this.value != null) {
				
				  value = this.value;
				
				} else {
				
				  value = '';
				
				}
			
				if (objectData[this.name] != null) {
				
					if (!objectData[this.name].push) {
					
					objectData[this.name] = [objectData[this.name]];
					
					}
					
					objectData[this.name].push(value);
					
					} else {
					
					objectData[this.name] = value;
					
					}
			  
				});
				
			  return objectData;					

			};			
						
		var data = $("#wplp_create_lead").serializeObject(); //the dynamic form elements.

		//alert(JSON.stringify(data));		  
			
		data.action = "wplp_create_lead"; //the action to call
		data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
		data.ajaxUsed = "yes";
		
        // Define the URL for the AJAX to call
        var url = wplpajaxobjfront.ajaxurl; 
		
		//alert( JSON.stringify( data ) );
		//alert( JSON.stringify( url ) );

		$.post(url, data, function(response) {
			
			//alert(response);
	
			window.location.href = response;
			$.unblockUI();
			//window.open(response,"_blank","","");

		});
		
		return false;	//Do not remove!
    
	});
	
	
	
	
	
	
	// AJAX for traffic redirect links
//	$(".wplp-traffic-redirect").submit( function() { 	
//		
//		// Don't allow access to the page while we process data.
//		$.blockUI({ 
//			
//			//message: '<h1>Just a moment</h1><br /><br />You are being connected now...', 
//			message: wplpajaxobjfront.wplp_connected,
//			css: { 
//                top:  ($(window).height() - 400) /2 + 'px', 
//                left: ($(window).width() - 400) /2 + 'px', 
//                width: '400px',
//				Height: '250px', 
//				padding: '10px'
//            } 
//			
//			});		
//	
//
//		// Convert to name value pairs			
//		// Define a data object to send to our PHP		
//				
//			$.fn.serializeObject = function() {
//
//				var arrayData, objectData;
//				arrayData = this.serializeArray();
//				objectData = {};
//
//				$.each(arrayData, function() {
//					var value;
//			
//				if (this.value != null) {
//				
//				  value = this.value;
//				
//				} else {
//				
//				  value = '';
//				
//				}
//			
//				if (objectData[this.name] != null) {
//				
//					if (!objectData[this.name].push) {
//					
//					objectData[this.name] = [objectData[this.name]];
//					
//					}
//					
//					objectData[this.name].push(value);
//					
//					} else {
//					
//					objectData[this.name] = value;
//					
//					}
//			  
//				});
//				
//			  return objectData;					
//
//			};			
//						
//		var data = $(".wplp-traffic-redirect-form").serializeObject(); //the dynamic form elements.
//
//		//alert(JSON.stringify(data));		  
//			
//		data.action = "wplp_traffic_redirect"; //the action to call
//		data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
//		data.ajaxUsed = "yes";
//		
//        // Define the URL for the AJAX to call
//        var url = wplpajaxobjfront.ajaxurl; 
//		
//		alert( JSON.stringify( data ) );
//		//alert( JSON.stringify( url ) );
//
//		$.post(url, data, function(response) {
//			
//			//alert(response);
//	
//			window.location.href = response;
//			$.unblockUI();
//			//window.open(response,"_blank","","");
//
//		});
//		
//		return false;	//Do not remove!
//    
//	});	


	$('td.wplp-inputs input, div.wplp-inputs input, span.wplp-inputs input').click(function(){	


		// AJAX for traffic redirect links
		if( $(this).is('.wplp-traffic-redirect') ) {
			
			// Don't allow access to the page while we process data.
			$.blockUI({ 
				
				//message: '<h1>Just a moment</h1><br /><br />You are being connected now...', 
				message: wplpajaxobjfront.wplp_connected,
				css: { 
					top:  ($(window).height() - 400) /2 + 'px', 
					left: ($(window).width() - 400) /2 + 'px', 
					width: '400px',
					Height: '250px', 
					padding: '10px'
				} 
				
				});		
		
	
			// Define data object to sent to PHP
			data = new Object();
				
			//alert(JSON.stringify(data));		  
				
			data.action = "wplp_traffic_redirect"; //the action to call
			data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
			data.ajaxUsed = "yes";

			data.wplp_campaign = $(this).siblings('input[type="hidden"]').val();
			data.wplp_landing_page = $(this).siblings('input[type="hidden"]').val();
			
			// Define the URL for the AJAX to call
			var url = wplpajaxobjfront.ajaxurl; 
			
			//alert( JSON.stringify( data ) );
			//alert( JSON.stringify( url ) );
	
			$.post(url, data, function(response) {
				
				//alert(response);
		
				window.location.href = response;
				$.unblockUI();
				//window.open(response,"_blank","","");
	
			});
			
		}
		
		
		
		
		// if the button class is
		if( $(this).is('.wplp-delete-lead') ) {
			/// delete
			
			// Don't allow access to the page while we process data.
			$.blockUI({ 
			
			//message: '<h1>Just a moment</h1><br /><br />Deleting Lead...', 
			message: wplpajaxobjfront.wplp_deleting,
			css: { 
                top:  ($(window).height() - 400) /2 + 'px', 
                left: ($(window).width() - 400) /2 + 'px', 
                width: '400px',
				Height: '250px', 
				padding: '10px'
            } 
			
			});	
			
			data = new Object();
			data.wplp_lead_id = $(this).siblings('input[type="hidden"]').val();

			//alert(JSON.stringify(data));		  
			
			data.action = "wplp_delete_lead"; //the action to call
			data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
			data.ajaxUsed = "yes";
		
			// Define the URL for the AJAX to call
			var url = wplpajaxobjfront.ajaxurl; 
		
			//alert( JSON.stringify( data ) );
			//alert( JSON.stringify( url ) );

			$.post(url, data, function(response) {
			
				//alert(response);
				window.location.reload();
				//window.location.href = response;
				$.unblockUI();
				//window.open(response,"_blank","","");

			});

		} 
		
		
		
		


		
		// if the button class is
		if( $(this).is('.wplp-view-edit-lead') ) {
			/// delete

			// Don't allow access to the page while we process data.
			$.blockUI({ 
			
			//message: '<h1>Just a moment</h1><br /><br />Loading Lead Details...',
			message: wplpajaxobjfront.wplp_loading_lead, 
			css: { 
                top:  ($(window).height() - 400) /2 + 'px', 
                left: ($(window).width() - 400) /2 + 'px', 
                width: '400px',
				Height: '250px', 
				padding: '10px'
            } 
			
			});	
						
			data = new Object();
			data.wplp_lead_id = $(this).siblings('input[type="hidden"]').val();

			//alert(JSON.stringify(data));		  
			
			data.action = "wplp_view_lead"; //the action to call
			data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
			data.ajaxUsed = "yes";
		
			// Define the URL for the AJAX to call
			var url = wplpajaxobjfront.ajaxurl; 
		
			//alert( JSON.stringify( data ) );
			//alert( JSON.stringify( url ) );

			$.post(url, data, function(response) {
			
				//alert(response);
				//window.location.reload();
				window.location.href = response;
				$.unblockUI();
				//window.open(response,"_blank","","");

			});
		
		} 
		
		
		
		
		// if the button class is
		if( $(this).is('.wplp-connect-aweber-api') ) {
			/// connect to Aweber API	

			$.blockUI({ 
			
			//message: '<h1>Just a moment</h1><br /><br />Loading Lead Details...',
			message: wplpajaxobjfront.wplp_connect_aweber_api, 
			css: { 
                top:  ($(window).height() - 400) /2 + 'px', 
                left: ($(window).width() - 400) /2 + 'px', 
                width: '400px',
				Height: '250px', 
				padding: '10px'
            } 
			
			});	
						
			data = new Object();
			//data.wplp_lead_id = $(this).siblings('input[type="hidden"]').val();
			//data.wplp_aweber_auth = $(this).siblings('input[type="text" class="wplp-aweber-auth"]').val();
			data.wplp_aweber_auth = $("input").filter(".wplp-aweber-auth").val();
			//data.wplp_aweber_auth = $(this).siblings('input[type="text"]').val();
			data.wplp_user_selector = $(this).siblings('input[class="wplp-user-selector"]').val();
			data.wplp_ref_user_id = $(this).siblings('input[class="wplp-ref-user-id"]').val();
				
			//alert(JSON.stringify(data));		  
			
			data.action = "wplp_connect_aweber"; //the action to call
			data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
			data.ajaxUsed = "yes";
		
			// Define the URL for the AJAX to call
			var url = wplpajaxobjfront.ajaxurl; 
		
			//alert( JSON.stringify( data ) );
			//alert( JSON.stringify( url ) );

			$.post(url, data, function(response) {
			
				//alert(response);
				//window.location.reload();
				//window.location.href = response;
				$.unblockUI();
				//alert( 'Connection Successful!' );
				alert( JSON.stringify( response ) );
				//window.open(response,"_blank","","");

			});

		} 		
		
		
		if( $(this).is('.wplp-lead-save') ) {


			// Don't allow access to the page while we process data.
			$.blockUI({ 
				
				//message: '<h1>Just a moment</h1><br /><br />Updating lead...', 
				message: wplpajaxobjfront.wplp_updating_lead,
				css: { 
					top:  ($(window).height() - 400) /2 + 'px', 
					left: ($(window).width() - 400) /2 + 'px', 
					width: '400px',
					Height: '250px', 
					padding: '10px'
				} 
				
			});		
		
	
			// Convert to name value pairs			
			// Define a data object to send to our PHP		
					
				$.fn.serializeObject = function() {
	
					var arrayData, objectData;
					arrayData = this.serializeArray();
					objectData = {};
	
					$.each(arrayData, function() {
						var value;
				
					if (this.value != null) {
					
					  value = this.value;
					
					} else {
					
					  value = '';
					
					}
				
					if (objectData[this.name] != null) {
					
						if (!objectData[this.name].push) {
						
						objectData[this.name] = [objectData[this.name]];
						
						}
						
						objectData[this.name].push(value);
						
						} else {
						
						objectData[this.name] = value;
						
						}
				  
					});
					
				  return objectData;					
	
				};			
							
			var data = $(".wplp-edit-lead-form").serializeObject(); //the dynamic form elements.
	
			//alert(JSON.stringify(data));		  
				
			data.action = "wplp_update_lead"; //the action to call
			data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
			data.ajaxUsed = "yes";
			
			// Define the URL for the AJAX to call
			var url = wplpajaxobjfront.ajaxurl; 
			
			//alert( JSON.stringify( data ) );
			//alert( JSON.stringify( url ) );
	
			$.post(url, data, function(response) {
				
				//alert(response);
		
				window.location.href = response;
				$.unblockUI();
				//window.open(response,"_blank","","");
	
			});

		}









		if( $(this).is('.wplp-get-lead-list') ) {	  
			
			var data = "data";	
			data.action = "wplp_convert_to_csv"; //the action to call
			data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
			data.ajaxUsed = "yes";
			
			// Define the URL for the AJAX to call
			var url = wplpajaxobjfront.ajaxurl; 
			
			//alert( JSON.stringify( data ) );
			//alert( JSON.stringify( url ) );
	
			$.post(url, data, function(response) {
				
				alert(response);
		
				window.location.href = response;
				//$.unblockUI();
				//window.open(response,"_blank","","");
	
			});

		}



		
		
		if( $(this).is('.wplp-affiliate-save') ) {


			// Don't allow access to the page while we process data.
			$.blockUI({ 
				
				//message: '<h1>Just a moment</h1><br /><br />Updating your settings...', 
				message: wplpajaxobjfront.wplp_update_affiliate_settings,
				css: { 
					top:  ($(window).height() - 400) /2 + 'px', 
					left: ($(window).width() - 400) /2 + 'px', 
					width: '400px',
					Height: '250px', 
					padding: '10px'
				} 
				
			});		
		
	
			// Convert to name value pairs			
			// Define a data object to send to our PHP		
					
				$.fn.serializeObject = function() {
	
					var arrayData, objectData;
					arrayData = this.serializeArray();
					objectData = {};
	
					$.each(arrayData, function() {
						var value;
				
					if (this.value != null) {
					
					  value = this.value;
					
					} else {
					
					  value = '';
					
					}
				
					if (objectData[this.name] != null) {
					
						if (!objectData[this.name].push) {
						
						objectData[this.name] = [objectData[this.name]];
						
						}
						
						objectData[this.name].push(value);
						
						} else {
						
						objectData[this.name] = value;
						
						}
				  
					});
					
				  return objectData;					
	
				};			
							
			var data = $("#wplp_update_affiliate").serializeObject(); //the form ID
	
			//alert(JSON.stringify(data));		  
				
			data.action = "wplp_update_affiliate"; //the action to call
			data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
			data.ajaxUsed = "yes";
			
			// Define the URL for the AJAX to call
			var url = wplpajaxobjfront.ajaxurl; 
			
			//alert( JSON.stringify( data ) );
			//alert( JSON.stringify( url ) );
	
			$.post(url, data, function(response) {
				
				//alert(response);
		
				window.location.href = response;
				$.unblockUI();
				//window.open(response,"_blank","","");
	
			});

		}
		
		





		if( $(this).is('.wplp-autoresponder-save') ) { // submit button class


			// Don't allow access to the page while we process data.
			$.blockUI({ 
				
				//message: '<h1>Just a moment</h1><br /><br />Updating your settings...', 
				message: wplpajaxobjfront.wplp_update_affiliate_settings,
				css: { 
					top:  ($(window).height() - 400) /2 + 'px', 
					left: ($(window).width() - 400) /2 + 'px', 
					width: '400px',
					Height: '300px', 
					padding: '10px'
				} 
				
			});		
		
	
			// Convert to name value pairs			
			// Define a data object to send to our PHP		
					
				$.fn.serializeObject = function() {
	
					var arrayData, objectData;
					arrayData = this.serializeArray();
					objectData = {};
	
					$.each(arrayData, function() {
						var value;
				
					if (this.value != null) {
					
					  value = this.value;
					
					} else {
					
					  value = '';
					
					}
				
					if (objectData[this.name] != null) {
					
						if (!objectData[this.name].push) {
						
						objectData[this.name] = [objectData[this.name]];
						
						}
						
						objectData[this.name].push(value);
						
						} else {
						
						objectData[this.name] = value;
						
						}
				  
					});
					
				  return objectData;					
	
				};			
							
			var data = $("#wplp_update_autoresponder").serializeObject(); //the dynamic form elements. enter form ID
	
			//alert(JSON.stringify(data));		  
				
			data.action = "wplp_autoresponder_save"; //the action to call i.e. function
			data._ajax_nonce = wplpajaxobjfront.nonce; // This is the name of the nonce setup in the localize_script
			data.ajaxUsed = "yes";
			
			// Define the URL for the AJAX call
			var url = wplpajaxobjfront.ajaxurl; 
			
			//alert( JSON.stringify( data ) );
			//alert( JSON.stringify( url ) );
	
			$.post(url, data, function(response) {
				
				//alert(response);
				window.location.href = response;
				$.unblockUI();
				//window.open(response,"_blank","","");
	
			});

		}			
		
		
		
		return false;	//Do not remove!
    
	});
	
	function hasClass(elem, className) {
		return new RegExp(' ' + className + ' ').test(' ' + elem.className + ' ');
	}
	
		
	$(function() {
		
		$( ".wplp-button" )
		  .button()
		  .click(function( event ) {
			//event.preventDefault();
		  });
	  
	});	

	
}); //End doc ready