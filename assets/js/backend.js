jQuery(document).ready(function($) {

	$("#wplp_edit_member_submit").click( function() {
 	
        // define
		
		var numbers = /^[0-9]+$/;

		var wplp_referrer_id = $("#wplp_referrer_id").val();
		
		var wplp_user_id = $("#ID").val();

		// Validate fields START

		if( !wplp_referrer_id.match(numbers) ) {

			alert("Please enter a numeric value");

			return false;			

		}
		
		if( wplp_user_id == wplp_referrer_id ) {
			
			alert("Referrer ID# value cannot be same as User ID#, to place a user under no referrer, enter the number '0'." );
			
			return false;
			
		}
		
		// Validate fields END		 
		
		$("#ajax-loading-edit-member").css("visibility", "visible");

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
						
		var data = $("#wplp_edit_member_form").serializeObject(); //the dynamic form elements.
		
		//alert(JSON.stringify(data));		  
			
		data.action = "wplp_edit_member_info"; //the action to call
		data._ajax_nonce = wplpajaxobj.nonce; // This is the name of the nonce setup in the localize_script
		
        // Define the URL for the AJAX to call
        var url = wplpajaxobj.ajaxurl; 
		
		//alert( JSON.stringify( data ) );
		//alert( JSON.stringify( url ) );

		$.post(url, data, function(response) {
			
			$("#ajax-loading-edit-member").css("visibility", "hidden");

			//alert(response);

		});
		
		return false;	//Do not remove!
    
	});
	






	$('td.wplp-inputs input, div.wplp-inputs input, span.wplp-inputs input').click(function(){	
		
		
		// if the button class is
		if( $(this).is('.wplp-connect-aweber-api') ) {
			/// connect to Aweber API	

			$.blockUI({ 
			
			//message: '<h1>Just a moment</h1><br /><br />Loading Lead Details...',
			message: wplpajaxobj.wplp_connect_aweber_api, 
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
			//data.wplp_ref_user_id = $(this).siblings('input[class="wplp-ref-user-id"]').val();
				
			//alert(JSON.stringify(data));		  
			
			data.action = "wplp_connect_aweber"; //the action to call
			data._ajax_nonce = wplpajaxobj.nonce; // This is the name of the nonce setup in the localize_script
			data.ajaxUsed = "yes";
		
			// Define the URL for the AJAX to call
			var url = wplpajaxobj.ajaxurl; 
		
			//alert( JSON.stringify( data ) );
			//alert( JSON.stringify( url ) );

			$.post(url, data, function(response) {
			
				//alert(response);
				//window.location.reload();
				//window.location.href = response;
				$.unblockUI();
				//alert( 'Connection Successful!' );
				window.location.reload();
				//alert( JSON.stringify( response ) );
				//window.open(response,"_blank","","");

			});

		} 						
		
		return false;	//Do not remove!
    
	});
	
	
		
	
}); // end doc ready