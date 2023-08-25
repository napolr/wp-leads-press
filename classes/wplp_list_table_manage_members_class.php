<?php
/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table_Base')){
	
/**
 * Base class for displaying a list of items in an ajaxified HTML table.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */

/**
 * Base class for displaying a list of items in an ajaxified HTML table.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
	class WP_List_Table_Base {
	
		/**
		 * The current list of items
		 *
		 * @since 3.1.0
		 * @var array
		 * @access protected
		 */
		var $items;
	
		/**
		 * Various information about the current table
		 *
		 * @since 3.1.0
		 * @var array
		 * @access private
		 */
		var $_args;
	
		/**
		 * Various information needed for displaying the pagination
		 *
		 * @since 3.1.0
		 * @var array
		 * @access private
		 */
		var $_pagination_args = array();
	
		/**
		 * The current screen
		 *
		 * @since 3.1.0
		 * @var object
		 * @access protected
		 */
		var $screen;
	
		/**
		 * Cached bulk actions
		 *
		 * @since 3.1.0
		 * @var array
		 * @access private
		 */
		var $_actions;
	
		/**
		 * Cached pagination output
		 *
		 * @since 3.1.0
		 * @var string
		 * @access private
		 */
		var $_pagination;
	
		/**
		 * Constructor. The child class should call this constructor from its own constructor
		 *
		 * @param array $args An associative array with information about the current table
		 * @access protected
		 */
		function __construct( $args = array() ) {
			$args = wp_parse_args( $args, array(
				'plural' => '',
				'singular' => '',
				'ajax' => false,
				'screen' => null,
			) );
	
			$this->screen = convert_to_screen( $args['screen'] );
	
			add_filter( "manage_{$this->screen->id}_columns", array( $this, 'get_columns' ), 0 );
	
			if ( !$args['plural'] )
				$args['plural'] = $this->screen->base;
	
			$args['plural'] = sanitize_key( $args['plural'] );
			$args['singular'] = sanitize_key( $args['singular'] );
	
			$this->_args = $args;
	
			if ( $args['ajax'] ) {
				// wp_enqueue_script( 'list-table' );
				add_action( 'admin_footer', array( $this, '_js_vars' ) );
			}
		}
	
		/**
		 * Checks the current user's permissions
		 * @uses wp_die()
		 *
		 * @since 3.1.0
		 * @access public
		 * @abstract
		 */
		function ajax_user_can() {
			die( 'function WP_List_Table::ajax_user_can() must be over-ridden in a sub-class.' );
		}
	
		/**
		 * Prepares the list of items for displaying.
		 * @uses WP_List_Table::set_pagination_args()
		 *
		 * @since 3.1.0
		 * @access public
		 * @abstract
		 */
		function prepare_items() {
			die( 'function WP_List_Table::prepare_items() must be over-ridden in a sub-class.' );
		}
	
		/**
		 * An internal method that sets all the necessary pagination arguments
		 *
		 * @param array $args An associative array with information about the pagination
		 * @access protected
		 */
		function set_pagination_args( $args ) {
			$args = wp_parse_args( $args, array(
				'total_items' => 0,
				'total_pages' => 0,
				'per_page' => 0,
			) );
	
			if ( !$args['total_pages'] && $args['per_page'] > 0 )
				$args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );
	
			// redirect if page number is invalid and headers are not already sent
			if ( ! headers_sent() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && $args['total_pages'] > 0 && $this->get_pagenum() > $args['total_pages'] ) {
				wp_redirect( add_query_arg( 'paged', $args['total_pages'] ) );
				exit;
			}
	
			$this->_pagination_args = $args;
		}
	
		/**
		 * Access the pagination args
		 *
		 * @since 3.1.0
		 * @access public
		 *
		 * @param string $key
		 * @return array
		 */
		function get_pagination_arg( $key ) {
			if ( 'page' == $key )
				return $this->get_pagenum();
	
			if ( isset( $this->_pagination_args[$key] ) )
				return $this->_pagination_args[$key];
		}
	
		/**
		 * Whether the table has items to display or not
		 *
		 * @since 3.1.0
		 * @access public
		 *
		 * @return bool
		 */
		function has_items() {
			return !empty( $this->items );
		}
	
		/**
		 * Message to be displayed when there are no items
		 *
		 * @since 3.1.0
		 * @access public
		 */
		function no_items() {
			_e( 'No items found.' );
		}
	
		/**
		 * Display the search box.
		 *
		 * @since 3.1.0
		 * @access public
		 *
		 * @param string $text The search button text
		 * @param string $input_id The search input id
		 */
		function search_box( $text, $input_id ) {
			if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
				return;
	
			$input_id = $input_id . '-search-input';
	
			if ( ! empty( $_REQUEST['orderby'] ) )
				echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
			if ( ! empty( $_REQUEST['order'] ) )
				echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
			if ( ! empty( $_REQUEST['post_mime_type'] ) )
				echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
			if ( ! empty( $_REQUEST['detached'] ) )
				echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
			?>
            
                <p class="search-box">
                    <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
                    <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
                    <?php submit_button( $text, 'button', false, false, array('id' => 'search-submit') ); ?>
                    
                </p>
			
			<?php
		}
	
		/**
		 * Get an associative array ( id => link ) with the list
		 * of views available on this table.
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @return array
		 */
		function get_views() {
			return array();
		}
	
		/**
		 * Display the list of views available on this table.
		 *
		 * @since 3.1.0
		 * @access public
		 */
		function views() {
			$views = $this->get_views();
			/**
			 * Filter the list of available list table views.
			 *
			 * The dynamic portion of the hook name, $this->screen->id, refers
			 * to the ID of the current screen, usually a string.
			 *
			 * @since 3.5.0
			 *
			 * @param array $views An array of available list table views.
			 */
			$views = apply_filters( "views_{$this->screen->id}", $views );
	
			if ( empty( $views ) )
				return;
	
			echo "<ul class='subsubsub'>\n";
			foreach ( $views as $class => $view ) {
				$views[ $class ] = "\t<li class='$class'>$view";
			}
			echo implode( " |</li>\n", $views ) . "</li>\n";
			echo "</ul>";
		}
	
		/**
		 * Get an associative array ( option_name => option_title ) with the list
		 * of bulk actions available on this table.
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @return array
		 */
		function get_bulk_actions() {
			return array();
		}
	
		/**
		 * Display the bulk actions dropdown.
		 *
		 * @since 3.1.0
		 * @access public
		 */
		function bulk_actions() {
			if ( is_null( $this->_actions ) ) {
				$no_new_actions = $this->_actions = $this->get_bulk_actions();
				/**
				 * Filter the list table Bulk Actions drop-down.
				 *
				 * The dynamic portion of the hook name, $this->screen->id, refers
				 * to the ID of the current screen, usually a string.
				 *
				 * This filter can currently only be used to remove bulk actions.
				 *
				 * @since 3.5.0
				 *
				 * @param array $actions An array of the available bulk actions.
				 */
				$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
				$this->_actions = array_intersect_assoc( $this->_actions, $no_new_actions );
				$two = '';
			} else {
				$two = '2';
			}
	
			if ( empty( $this->_actions ) )
				return;
	
			echo "<select name='action$two'>\n";
			echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions' ) . "</option>\n";
	
			foreach ( $this->_actions as $name => $title ) {
				$class = 'edit' == $name ? ' class="hide-if-no-js"' : '';
	
				echo "\t<option value='$name'$class>$title</option>\n";
			}
	
			echo "</select>\n";
	
			submit_button( __( 'Apply' ), 'action', false, false, array( 'id' => "doaction$two" ) );
			echo "\n";
		}
	
		/**
		 * Get the current action selected from the bulk actions dropdown.
		 *
		 * @since 3.1.0
		 * @access public
		 *
		 * @return string|bool The action name or False if no action was selected
		 */
		function current_action() {
			if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
				return $_REQUEST['action'];
	
			if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
				return $_REQUEST['action2'];
	
			return false;
		}
	
		/**
		 * Generate row actions div
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @param array $actions The list of actions
		 * @param bool $always_visible Whether the actions should be always visible
		 * @return string
		 */
		function row_actions( $actions, $always_visible = false ) {
			$action_count = count( $actions );
			$i = 0;
	
			if ( !$action_count )
				return '';
	
			$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
			foreach ( $actions as $action => $link ) {
				++$i;
				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
				$out .= "<span class='$action'>$link$sep</span>";
			}
			$out .= '</div>';
	
			return $out;
		}
	
		/**
		 * Display a monthly dropdown for filtering items
		 *
		 * @since 3.1.0
		 * @access protected
		 */
		function months_dropdown( $post_type ) {
			global $wpdb, $wp_locale;
	
			$months = $wpdb->get_results( $wpdb->prepare( "
				SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
				FROM $wpdb->posts
				WHERE post_type = %s
				ORDER BY post_date DESC
			", $post_type ) );
	
			/**
			 * Filter the 'Months' drop-down results.
			 *
			 * @since 3.7.0
			 *
			 * @param object $months    The months drop-down query results.
			 * @param string $post_type The post type.
			 */
			$months = apply_filters( 'months_dropdown_results', $months, $post_type );
	
			$month_count = count( $months );
	
			if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
				return;
	
			$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
	?>
			<select name='m'>
				<option<?php selected( $m, 0 ); ?> value='0'><?php _e( 'Show all dates' ); ?></option>
	<?php
			foreach ( $months as $arc_row ) {
				if ( 0 == $arc_row->year )
					continue;
	
				$month = zeroise( $arc_row->month, 2 );
				$year = $arc_row->year;
	
				printf( "<option %s value='%s'>%s</option>\n",
					selected( $m, $year . $month, false ),
					esc_attr( $arc_row->year . $month ),
					/* translators: 1: month name, 2: 4-digit year */
					sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
				);
			}
	?>
			</select>
	<?php
		}
	
		/**
		 * Display a view switcher
		 *
		 * @since 3.1.0
		 * @access protected
		 */
		function view_switcher( $current_mode ) {
			$modes = array(
				'list'    => __( 'List View' ),
				'excerpt' => __( 'Excerpt View' )
			);
	
	?>
			<input type="hidden" name="mode" value="<?php echo esc_attr( $current_mode ); ?>" />
			<div class="view-switch">
	<?php
				foreach ( $modes as $mode => $title ) {
					$class = ( $current_mode == $mode ) ? 'class="current"' : '';
					echo "<a href='" . esc_url( add_query_arg( 'mode', $mode, $_SERVER['REQUEST_URI'] ) ) . "' $class><img id='view-switch-$mode' src='" . esc_url( includes_url( 'images/blank.gif' ) ) . "' width='20' height='20' title='$title' alt='$title' /></a>\n";
				}
			?>
			</div>
	<?php
		}
	
		/**
		 * Display a comment count bubble
		 *
		 * @since 3.1.0
		 * @access protected

		 *
		 * @param int $post_id
		 * @param int $pending_comments
		 */
		function comments_bubble( $post_id, $pending_comments ) {
			$pending_phrase = sprintf( __( '%s pending' ), number_format( $pending_comments ) );
	
			if ( $pending_comments )
				echo '<strong>';
	
			echo "<a href='" . esc_url( add_query_arg( 'p', $post_id, admin_url( 'edit-comments.php' ) ) ) . "' title='" . esc_attr( $pending_phrase ) . "' class='post-com-count'><span class='comment-count'>" . number_format_i18n( get_comments_number() ) . "</span></a>";
	
			if ( $pending_comments )
				echo '</strong>';
		}
	
		/**
		 * Get the current page number
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @return int
		 */
		function get_pagenum() {
			$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;
	
			if( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
				$pagenum = $this->_pagination_args['total_pages'];
	
			return max( 1, $pagenum );
		}
	
		/**
		 * Get number of items to display on a single page
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @return int
		 */
		function get_items_per_page( $option, $default = 20 ) {
			$per_page = (int) get_user_option( $option );
			if ( empty( $per_page ) || $per_page < 1 )
				$per_page = $default;
	
			/**
			 * Filter the number of items to be displayed on each page of the list table.
			 *
			 * The dynamic hook name, $option, refers to the per page option depending
			 * on the type of list table in use. Possible values may include:
			 * 'edit_comments_per_page', 'sites_network_per_page', 'site_themes_network_per_page',
			 * 'themes_netework_per_page', 'users_network_per_page', 'edit_{$post_type}', etc.
			 *
			 * @since 2.9.0
			 *
			 * @param int $per_page Number of items to be displayed. Default 20.
			 */
			return (int) apply_filters( $option, $per_page );
		}
	
		/**
		 * Display the pagination.
		 *
		 * @since 3.1.0
		 * @access protected
		 */
		function pagination( $which ) {
			if ( empty( $this->_pagination_args ) )
				return;
	
			extract( $this->_pagination_args, EXTR_SKIP );
	
			$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';
	
			$current = $this->get_pagenum();
	
			$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	
			$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );
	
			$page_links = array();
	
			$disable_first = $disable_last = '';
			if ( $current == 1 )
				$disable_first = ' disabled';
			if ( $current == $total_pages )
				$disable_last = ' disabled';
	
			$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
				'first-page' . $disable_first,
				esc_attr__( 'Go to the first page' ),
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				'&laquo;'
			);
	
			$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
				'prev-page' . $disable_first,
				esc_attr__( 'Go to the previous page' ),
				esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
				'&lsaquo;'
			);
	
			if ( 'bottom' == $which )
				$html_current_page = $current;
			else
				$html_current_page = sprintf( "<input class='current-page' title='%s' type='text' name='paged' value='%s' size='%d' />",
					esc_attr__( 'Current page' ),
					$current,
					strlen( $total_pages )
				);
	
			$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
			$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';
	
			$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
				'next-page' . $disable_last,
				esc_attr__( 'Go to the next page' ),
				esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
				'&rsaquo;'
			);
	
			$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
				'last-page' . $disable_last,
				esc_attr__( 'Go to the last page' ),
				esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
				'&raquo;'
			);
	
			$pagination_links_class = 'pagination-links';
			if ( ! empty( $infinite_scroll ) )
				$pagination_links_class = ' hide-if-js';
			$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';
	
			if ( $total_pages )
				$page_class = $total_pages < 2 ? ' one-page' : '';
			else
				$page_class = ' no-pages';
	
			$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";
	
			echo $this->_pagination;
		}
	
		/**
		 * Get a list of columns. The format is:
		 * 'internal-name' => 'Title'
		 *
		 * @since 3.1.0
		 * @access protected
		 * @abstract
		 *
		 * @return array
		 */
		function get_columns() {
			die( 'function WP_List_Table::get_columns() must be over-ridden in a sub-class.' );
		}
	
		/**
		 * Get a list of sortable columns. The format is:
		 * 'internal-name' => 'orderby'
		 * or
		 * 'internal-name' => array( 'orderby', true )
		 *
		 * The second format will make the initial sorting order be descending
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @return array
		 */
		function get_sortable_columns() {
			return array();
		}
	
		/**
		 * Get a list of all, hidden and sortable columns, with filter applied
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @return array
		 */
		function get_column_info() {
			if ( isset( $this->_column_headers ) )
				return $this->_column_headers;
	
			$columns = get_column_headers( $this->screen );
			$hidden = get_hidden_columns( $this->screen );
	
			$sortable_columns = $this->get_sortable_columns();
			/**
			 * Filter the list table sortable columns for a specific screen.
			 *
			 * The dynamic portion of the hook name, $this->screen->id, refers
			 * to the ID of the current screen, usually a string.
			 *
			 * @since 3.5.0
			 *
			 * @param array $sortable_columns An array of sortable columns.
			 */
			$_sortable = apply_filters( "manage_{$this->screen->id}_sortable_columns", $sortable_columns );
	
			$sortable = array();
			foreach ( $_sortable as $id => $data ) {
				if ( empty( $data ) )
					continue;
	
				$data = (array) $data;
				if ( !isset( $data[1] ) )
					$data[1] = false;
	
				$sortable[$id] = $data;
			}
	
			$this->_column_headers = array( $columns, $hidden, $sortable );
	
			return $this->_column_headers;
		}
	
		/**
		 * Return number of visible columns
		 *
		 * @since 3.1.0
		 * @access public
		 *
		 * @return int
		 */
		function get_column_count() {
			list ( $columns, $hidden ) = $this->get_column_info();
			$hidden = array_intersect( array_keys( $columns ), array_filter( $hidden ) );
			return count( $columns ) - count( $hidden );
		}
	
		/**
		 * Print column headers, accounting for hidden and sortable columns.
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @param bool $with_id Whether to set the id attribute or not
		 */
		function print_column_headers( $with_id = true ) {
			list( $columns, $hidden, $sortable ) = $this->get_column_info();
	
			$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$current_url = remove_query_arg( 'paged', $current_url );
	
			if ( isset( $_GET['orderby'] ) )
				$current_orderby = $_GET['orderby'];
			else
				$current_orderby = '';
	
			if ( isset( $_GET['order'] ) && 'desc' == $_GET['order'] )
				$current_order = 'desc';
			else
				$current_order = 'asc';
	
			if ( ! empty( $columns['cb'] ) ) {
				static $cb_counter = 1;
				$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
					. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
				$cb_counter++;
			}
	
			foreach ( $columns as $column_key => $column_display_name ) {
				$class = array( 'manage-column', "column-$column_key" );
	
				$style = '';
				if ( in_array( $column_key, $hidden ) )
					$style = 'display:none;';
	
				$style = ' style="' . $style . '"';
	
				if ( 'cb' == $column_key )
					$class[] = 'check-column';
				elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
					$class[] = 'num';
	
				if ( isset( $sortable[$column_key] ) ) {
					list( $orderby, $desc_first ) = $sortable[$column_key];
	
					if ( $current_orderby == $orderby ) {
						$order = 'asc' == $current_order ? 'desc' : 'asc';
						$class[] = 'sorted';
						$class[] = $current_order;
					} else {
						$order = $desc_first ? 'desc' : 'asc';
						$class[] = 'sortable';
						$class[] = $desc_first ? 'asc' : 'desc';
					}
	
					$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
				}
	
				$id = $with_id ? "id='$column_key'" : '';
	
				if ( !empty( $class ) )
					$class = "class='" . join( ' ', $class ) . "'";
	
				echo "<th scope='col' $id $class $style>$column_display_name</th>";
			}
		}
	
		/**
		 * Display the table
		 *
		 * @since 3.1.0
		 * @access public
		 */
		function display() {
			
			extract( $this->_args );
		
			$this->display_tablenav( 'top' );
		
			?>
			<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>" cellspacing="0">
			<thead>
			<tr>
				<?php $this->print_column_headers(); ?>
			</tr>
			</thead>
			
			<tfoot>
			<tr>
				<?php $this->print_column_headers( false ); ?>
			</tr>
			</tfoot>
			
			<tbody id="the-list"<?php if ( $singular ) echo " data-wp-lists='list:$singular'"; ?>>
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
			</table>
			<?php
				$this->display_tablenav( 'bottom' );
		}
	
		/**
		 * Get a list of CSS classes for the <table> tag
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @return array
		 */
		function get_table_classes() {
			return array( 'widefat', 'fixed', $this->_args['plural'] );
		}
	
		/**
		 * Generate the table navigation above or below the table
		 *
		 * @since 3.1.0
		 * @access protected
		 */
		function display_tablenav( $which ) {
			if ( 'top' == $which )
				wp_nonce_field( 'bulk-' . $this->_args['plural'] );
	?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
	
			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions(); ?>
			</div>
	<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
	?>
	
			<br class="clear" />
		</div>
	<?php
		}
	
		/**
		 * Extra controls to be displayed between bulk actions and pagination
		 *
		 * @since 3.1.0
		 * @access protected
		 */
		function extra_tablenav( $which ) {}
	
		/**
		 * Generate the <tbody> part of the table
		 *
		 * @since 3.1.0
		 * @access protected
		 */
		function display_rows_or_placeholder() {
			if ( $this->has_items() ) {
				$this->display_rows();
			} else {
				list( $columns, $hidden ) = $this->get_column_info();
				echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
				$this->no_items();
				echo '</td></tr>';
			}
		}
	
		/**
		 * Generate the table rows
		 *
		 * @since 3.1.0
		 * @access protected
		 */
		function display_rows() {
			foreach ( $this->items as $item )
				$this->single_row( $item );
		}
	
		/**
		 * Generates content for a single row of the table
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @param object $item The current item
		 */
		function single_row( $item ) {
			static $row_class = '';
			$row_class = ( $row_class == '' ? ' class="alternate"' : '' );
	
			echo '<tr' . $row_class . '>';
			$this->single_row_columns( $item );
			echo '</tr>';
		}
	
		/**
		 * Generates the columns for a single row of the table
		 *
		 * @since 3.1.0
		 * @access protected
		 *
		 * @param object $item The current item
		 */
		function single_row_columns( $item ) {
			list( $columns, $hidden ) = $this->get_column_info();
	
			foreach ( $columns as $column_name => $column_display_name ) {
				$class = "class='$column_name column-$column_name'";
	
				$style = '';
				if ( in_array( $column_name, $hidden ) )
					$style = ' style="display:none;"';
	
				$attributes = "$class$style";
	
				if ( 'cb' == $column_name ) {
					echo '<th scope="row" class="check-column">';
					echo $this->column_cb( $item );
					echo '</th>';
				}
				elseif ( method_exists( $this, 'column_' . $column_name ) ) {
					echo "<td $attributes>";
					echo call_user_func( array( $this, 'column_' . $column_name ), $item );
					echo "</td>";
				}
				else {
					echo "<td $attributes>";
					echo $this->column_default( $item, $column_name );
					echo "</td>";
				}
			}
		}
	
		/**
		 * Handle an incoming ajax request (called from admin-ajax.php)
		 *
		 * @since 3.1.0
		 * @access public
		 */
		function ajax_response() {
			$this->prepare_items();
	
			extract( $this->_args );
			extract( $this->_pagination_args, EXTR_SKIP );
	
			ob_start();
			if ( ! empty( $_REQUEST['no_placeholder'] ) )
				$this->display_rows();
			else
				$this->display_rows_or_placeholder();
	
			$rows = ob_get_clean();
	
			$response = array( 'rows' => $rows );
	
			if ( isset( $total_items ) )
				$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );
	
			if ( isset( $total_pages ) ) {
				$response['total_pages'] = $total_pages;
				$response['total_pages_i18n'] = number_format_i18n( $total_pages );
			}
	
			die( json_encode( $response ) );
		}
	
		/**
		 * Send required variables to JavaScript land
		 *
		 * @access private
		 */
		function _js_vars() {
			$args = array(
				'class'  => get_class( $this ),
				'screen' => array(
					'id'   => $this->screen->id,
					'base' => $this->screen->base,
				)
			);
	
			printf( "<script type='text/javascript'>list_args = %s;</script>\n", json_encode( $args ) );
		}
	}

}



###
# Start of WPLP extended WP_List_Table CLASS
###

class Wplp_List_Table_Manage_Members extends WP_List_Table_Base {
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    public function __construct(){
        global $status, $page;
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'Member',     //singular name of the listed records
            'plural'    => 'Members',    //plural name of the listed records
            'ajax'      => true       //does this table support ajax?
        ) );
		
		return $this;
    }
    function column_default($item, $column_name){
        switch($column_name){
            case 'ID':
            case 'user_login':
			
			case 'wplp_ban_status':
            return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    /** ************************************************************************
     * This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td>
     **************************************************************************/
    function column_id($item){
        global $wpdb;
		
		$query='SELECT meta_value FROM '.$wpdb->prefix.'usermeta WHERE meta_key IN ("wplp_ban_status") AND user_id = '.$item["ID"].'';
		$status = $wpdb->get_var($query);
		
		if($status == null) {
			
			$status = 'u';
		
		}
        //Build row actions
		
		if( !isset ( $_REQUEST['paged'] ) ){
			
			$_REQUEST['paged'] = 1;
		
		}
		
        $actions = array(
            'edit'=>sprintf('<a href="?page=%s&action=%s&member=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
			'block'=>sprintf('<a href="?page=%s&action=%s&member=%s&wplp_ban_status=%s&paged=%s">(Un)Block</a>',$_REQUEST['page'],'block',$item['ID'], $status, $_REQUEST['paged'])
            //'delete'=>sprintf('<a href="?page=%s&action=%s&member=%s">Delete</a>',$_REQUEST['page'],'delete',$item['user_id'])
        );
        
        //Return the title contents
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item['ID'],
            /*$2%s*/ $this->row_actions($actions)
        );
    }
	
    function column_wplp_referrer_id($item){
		global $wpdb;
		
        $query='SELECT meta_value FROM '.$wpdb->prefix.'usermeta WHERE meta_key IN ("wplp_referrer_id") AND user_id = '.$item["ID"].'';
		$data = $wpdb->get_var($query);
        //Build row actions
		
		return $data;
    } 
	
	 
	function column_wplp_ban_status($item){
		global $wpdb;
		
        $query='SELECT meta_value FROM '.$wpdb->prefix.'usermeta WHERE meta_key IN ("wplp_ban_status") AND user_id = '.$item["ID"].'';
		$data = $wpdb->get_var($query);
		
        //Build row actions
		
		if ($data == 'u' ) {
			
			$data = '<span style="color:green;">Not Blocked</span>';
		
		}
		
		if ($data == 'b' ) {
			
			$data = '<span style="color:red;">Blocked</span>';
		
		}
		
		if ($data == null ) {
			
			$data = '<span style="color:green;">Not Blocked</span>';
			
		}
			
		
		return $data;
    }
	
		
    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td>
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    
    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. 
	 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
                
                
    function get_columns(){
		
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text

            'ID'     => 'ID#',

            'user_login'    => 'Username',
			
			'wplp_referrer_id' => 'Referrer ID',
			
			'wplp_ban_status' => 'Block Status'
        );
		
		return $columns;
    }
	
    
    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'user_id'     => array('user_id',true),     //true means it's already sorted
			'login_id'	=> array('login_id', false)
        );
        return $sortable_columns;
    }
    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
           // 'delete'    => 'Delete',
			'block'		=> 'Block/Unblock'
        );
        return $actions;
    }   
    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
	 
    function process_bulk_action() {
	
        //Detect when a bulk action is being triggered...
		
		# Delete Action
//        if( 'delete'===$this->current_action() ) {
//            foreach($_GET['member'] as $member) {
//                //$member will be a string containing the ID of the member
//                //i.e. $member = "123";
//                //so you can process the id however you need to.
//                delete_this_member($member); // Must create this function, need to move children under 
//											// new parent or null the parent position in the matrix to 
//											// leave matrix structure in place which makes more sense.
//            }
//		}
			
		# (un)Block Action
        if( 'block'===$this->current_action() ) {
			global $wpdb, $_GET;
			
			$members = $_GET["member"];
			
			if( is_array($members) == false ){
				
				// Pull user ban status
					$member = $members;
					
					$user_id = $_GET["member"];
					$key = 'wplp_ban_status';
					$status = get_user_meta($user_id, $key, true);
					if ($status == 'u' ) {
						
						$status = 'b';
						
						update_user_meta($member, 'wplp_ban_status', $status);
						
						$status = 'u';
						
					}
					
					if ($status == 'b' ) {
								
						$status = 'u';
					
						update_user_meta($member, 'wplp_ban_status', $status);
							
						$status = 'b';
					}
					
					if ($status == null ) {
						
						$status = 'b';
						
						update_user_meta($member, 'wplp_ban_status', $status);
						
					}
			
			} else {
			
				foreach( $members as $member ) {
					
					// Pull each user's ban status
					$user_id = $_GET["member"];
					$key = 'wplp_ban_status';
					$status = get_user_meta($user_id, $key, true);
					if ($status == 'u' ) {
						
						$status = 'b';
						
						update_user_meta($member, 'wplp_ban_status', $status);
						//update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
						$status = 'u';
						
					}
					
					if ($status == 'b' ) {
							
						$status = 'u';
					
						update_user_meta($member, 'wplp_ban_status', $status);
							
						$status = 'b';
					}
					
					if ($status == null ) {
						
						$status = 'b';
						
						update_user_meta($member, 'wplp_ban_status', $status);
						
					}
					
				}
			
			}
            //wp_die('Member Blocked or they would be if we had this function complete! Manage block/unblocked status by editing member directly for now.');
        }
		# Edit Action		
		if( 'edit'===$this->current_action() ) {
			global $wpdb, $_GET, $_POST;

			// Get data of opportunities
			$taxonomies = 'wplp_opportunity';
			$args = NULL;
			$opps = get_terms( $taxonomies, $args );
			
			# Get data from user table
        	$query='SELECT * FROM '.$wpdb->prefix.'users WHERE ID = '.$_GET["member"].'';
			$data = $wpdb->get_results($query, 'ARRAY_A');
			$userName = $data[0]['user_login'];
			
			//Get meta data for user
			$user_id = $_GET["member"];
			
			$key = NULL;
			$metaData = get_user_meta($user_id, $key, false);			
			$referrer = $metaData['wplp_referrer_id'][0];
			
			if( isset($metaData['wplp_ban_status']) && !empty($metaData['wplp_ban_status'] ) ){
				
				$blockVal = $metaData['wplp_ban_status'][0];
				
			} else {
				
				$blockVal = NULL;	
				
			}
		
				if ($blockVal == 'u' ) {
					
					$blockVal = 'Not Blocked';
				
				}
				
				if ($blockVal == 'b' ) {
					
					$blockVal = 'Blocked';
				
				}
				
				if ($blockVal == null) {
					
					$blockVal = 'Not Blocked';
					
				}
			
?>      
       </form> <!-- close sunrise form tag --> 
		<div class="wrap" style="width: 600px;">
       
	    	<div id="icon-users" class="icon32"><br/></div>
        		<h2>WP Leads Press - Edit Member</h2>
		
				<?php 
				
				if( isset($message) ) {
					
					echo $message;
					
				}
				
				?>
        		
                <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
            		<p>
			    
                        Change values below then click on the update button to save.
            
            		</p>
		        </div>
        		<div>&nbsp;</div>
				
	<form id="wplp_edit_member_form" method="post" action=""> 
<?php /*?>	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
<?php */?>	       
		<table class="widefat">
				<thead>
                
					<tr>
						
                        <th>Option</th>
                        <th>Value</th>
                       
					</tr>
                    
				</thead>
                
				<tfoot>
					
                    <tr>
                        <th></th>
						<th></th>
					</tr>
			
            	</tfoot>
			
            	<tbody>       
                        
					<tr>
						<td>ID#</td>
                        <td><input type='text' value='<?php echo $user_id;?>' name='ID' id='ID' size='20' readonly="readonly" />*</td>
                    </tr>
                    
                    <tr>
                    	<td>Username</td>
						<td><input type='text' value='<?php echo $userName;?>' name='user_login' id='user_login' size='20' readonly="readonly" />*</td>
                    </tr>
                    
                    <tr>
                    	<td>Referrer ID#</td>    
					 	<td><input type='text' value='<?php echo $referrer;?>' name='wplp_referrer_id' id='wplp_referrer_id' size='20' /></td>
                    </tr>
					<?php
					
					foreach ( $opps as $opp ) {
						
						$user_id = $_GET["member"];
						$metaData = get_user_meta($user_id, $key, false);
						
						$trackingID = 'wplp_tracking_id_'.$opp->slug;
						
						if( isset( $metaData[$trackingID][0] ) == true ) {
													
							$trackingID = $metaData[$trackingID][0];
						
						} else {
							
							$trackingID = NULL;	
							
						}
						
						if( ! empty( $trackingID ) ) {
							
							$trackingID = $trackingID;
								
						} else {
							
							$trackingID = '';
							
						}
						
						$ret = '<tr>';
						$ret .= '<td>'.$opp->name . __(' Affiliate ID', 'wp-leads-press' ) . '</td>';
						$ret .= "<td><input type='text' value='".$trackingID."' name='wplp_tracking_id_".$opp->slug."' id='wplp_tracking_id_".$opp->slug."' size='20' /></td>";
						$ret .= '</tr>';
						
						echo $ret;		
					}	
					?>
					
                    
                    <tr>
                    	<td>Block User?</td>       
                        <td><select name="block_unblock" id="block_unblock">
                            	<option value="b" <?php if('Blocked' == $blockVal) echo "selected" ?>>Blocked</option>
                                <option value="u" <?php if('Not Blocked' == $blockVal) echo "selected" ?>>Not Blocked</option>
                       	    </select>
                        </td>                                                
                    </tr>                            
                   
				</tbody>
		</table>
        *Not editable, for reference only.
	
    
    
	<br />
		
		<?php wp_nonce_field('wplp_edit_member_info','wplp_form_nonce');
		
		$other_attributes = array( 'id' => 'wplp_edit_member_submit' );
		submit_button( 'Update', 'primary', 'wplp_edit_member_submit', false, $other_attributes ); ?>
        
		<!--input type="submit" class="button button-highlighted" value="Update" id="wplp_edit_member_submit" name="wplp_edit_member_submit" /-->
        <img src="<?php echo admin_url( 'images/wpspin_light.gif' ) ?>" id="ajax-loading-edit-member" style="visibility:hidden;" alt="" />
 
 
	</form>  

    </div>
	
	<form><!-- add to create open tag for sunrise close form tag -->
<?php			
        }
    }
	
    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items($search = NULL) {
        global $wpdb;
    
        $per_page = 15;
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable); 
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
		# Database query and sorting
		
		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'ID'; //If no sort, default to ID
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
		$query='SELECT * FROM '.$wpdb->prefix.'users ORDER BY '. $orderby . ' ' .$order;
		$data = $wpdb->get_results($query, ARRAY_A);
     
	    /* If the value is not NULL, do a search for it. */
    	if( $search != NULL ){
           
            // Trim Search Term
            $search = trim($search);
           
            /* Notice how you can search multiple columns for your search term easily, and return one data set */
//            $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."users WHERE user_id LIKE '%%%s%%' OR wplp_referrer_id LIKE 
//			'%%%s%%' OR login_id LIKE '%%%s%%' ORDER BY user_id ASC", $search, $search, $search, $search), ARRAY_A);
            $data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."users WHERE ID LIKE '%%%s%%' OR user_login LIKE '%%%s%%' OR user_nicename LIKE '%%%s%%' ORDER BY ID ASC", $search, $search, $search ), ARRAY_A );
			
    	}
			
			//$bit = print_r($data, true );
			//error_log('### data ### - ' . $bit );
	
	
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering.
         */
        $total_items = count($data);
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);     
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data; 
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}
?>