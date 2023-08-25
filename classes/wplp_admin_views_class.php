<?php 

if ( !class_exists( 'WplpAdminViews' ) ) {


	class WplpAdminViews {
		
		// WPLP Dashboard
		
		// Manage Members
		public static function manage_members() {
	
			//Create an instance WLTMM
			$wplpManageMembers = new Wplp_List_Table_Manage_Members();
			
				//Fetch, prepare, sort, and filter our data...
				if( isset($_POST['s']) ){
					
						$wplpManageMembers->prepare_items($_POST['s']); // For Search box value
						
				} else {
					
						$wplpManageMembers->prepare_items();
						
				}
				
			$message = '';
			
			if ('delete' === $wplpManageMembers->current_action()) {
				
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'wp-leads-press'), count($_REQUEST['user_id'])) . '</p></div>';
			
			}
			
			if ('edit' === $wplpManageMembers->current_action()) {
				
				if( !isset($_REQUEST['user_id']) ){
					
					$_REQUEST['user_id'] = 1;
					
				}
				
				$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items edited: %d', 'wp-leads-press'), count($_REQUEST['user_id'])) . '</p></div>';
			
			}
			
			?>
		<div class="wrap">
			<?php
						
					if( isset( $_GET['action'] ) && ( $_GET['action'] == 'edit' ) ) :
					
					else :
					
			
			?>
			
			<div id="icon-users" class="icon32">
				<br />
			</div>
			<h2>WP Leads Press - Manage Members</h2>
			<?php echo $message ?>
			<div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
				<p>
					Manage your users, here you can also block/unblock members and change a user's unique tracking ID's for each Company they are involved with.
				</p>
			</div>
			
			<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
			
			<div>
				<p>
				<form method="post">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
					<?php $wplpManageMembers->search_box('Search Members', 'wplpManageMembers'); ?>
				</form>
				</p>
			</div>
			<form id="members-filter" method="get">
				
				<!-- For plugins, we also need to ensure that the form posts back to our current page -->
				
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
				
				<!-- Now we can render the completed list table -->
				
				<?php 
					// Show table
					
					$wplpManageMembers->display();
			
				endif;
					
					?>
			</form>
		</div>
		<?php
			
		}		
		
	}

}

?>