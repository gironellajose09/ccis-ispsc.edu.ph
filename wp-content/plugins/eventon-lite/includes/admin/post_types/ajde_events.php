<?php
/**
 * Admin functions for the ajde_events post type
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventON/Admin/ajde_events
 * @version     2.3.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class evo_ajde_events{
	public function __construct(){
		add_filter( 'manage_edit-ajde_events_columns', array($this,'eventon_edit_event_columns') );

		// custom filters
		add_action('restrict_manage_posts',array($this,'evo_restrict_manage_posts'));
		add_filter('query_vars', array($this,'_register_query_vars' ));
		add_filter('months_dropdown_results', array($this,'remove_date_filter' ),10,2);
		add_action( 'pre_get_posts', array($this,'_pre_get_posts' ));

		add_filter('update_postmeta_cache', array($this,'limit_meta_cache_for_events'), 10, 3);

		add_action('manage_ajde_events_posts_custom_column', array($this,'eventon_custom_event_columns'), 10, 2 );
		add_filter( 'manage_edit-ajde_events_sortable_columns', array($this,'eventon_custom_events_sort'));
		add_filter( 'request', array($this,'eventon_custom_event_orderby') );

		add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );
		add_filter( 'post_row_actions', array($this,'eventon_duplicate_event_link_row'),10,2 );
		add_action( 'post_submitbox_misc_actions', array($this,'eventon_duplicate_event_post_button') );

		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit' ), 10, 2 );
		add_action( 'quick_edit_custom_box',  array($this,'eventon_admin_event_quick_edit'), 10, 2 );
		add_action( 'admin_enqueue_scripts', array($this,'eventon_admin_events_quick_edit_scripts'), 10 );
		add_action( 'save_post', array($this,'bulk_and_quick_edit_hook'), 10, 2 );
		add_action( 'evo_event_bulk_quick_edit', array( $this, 'bulk_and_quick_edit_save_hook' ), 10, 2 );
	}

	// Columns for events page
		function eventon_edit_event_columns( $existing_columns ) {
			
			// GET event type custom names
			$evcal_opt1= get_option('evcal_options_evcal_1');
			$evt_name = (!empty($evcal_opt1['evcal_eventt']))? $evcal_opt1['evcal_eventt']: esc_html__('Event Type','eventon');
			$evt_name2 = (!empty($evcal_opt1['evcal_eventt2']))? $evcal_opt1['evcal_eventt2']: esc_html__('Event Type 2','eventon');
			
			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) )
				$existing_columns = array();

			unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

			$columns = array();
			$columns["cb"] = "<input type=\"checkbox\" />";
			
			$columns["name"] = esc_html__( 'Event Name', 'eventon' );

			$columns["event_location"] = 	esc_html__( 'Location', 'eventon' );
			$columns["event_organizer"] = 	esc_html__( 'Organizer', 'eventon' );
			$columns["event_type"] = 		$evt_name;
			$columns["event_type_2"] = 		$evt_name2;
			$columns["event_start_date"] = 	esc_html__( 'Start Date', 'eventon' );
			$columns["event_end_date"] = 	esc_html__( 'End Date', 'eventon' );
			
			$columns["evo_featured"] = '<span class="evo_posttable evo_posttable_featured ajdeToolTip L" data-label=" '. esc_html__( 'Featured', 'eventon' ) .'" data-d="'. esc_html__( 'Featured', 'eventon' ) .'"></span>';
			
			$columns["repeat"] = '<span class="evo_posttable evo_posttable_repeat ajdeToolTip L" data-label=" '. esc_html__( 'Event Repeat', 'eventon' ) .'" data-d="'. esc_html__( 'Event Repeat', 'eventon' ) .'"></span>';
			//$columns["date"] = esc_html__( 'Date', 'eventon' );

			$columns = apply_filters('evo_event_columns', $columns);	

			return array_merge( $columns, $existing_columns );
		}

	// Custom filters for all events
		function limit_meta_cache_for_events($meta_type, $object_ids, $meta_keys) {	
		    if ($meta_type === 'post' && is_admin() && isset($_GET['post_type']) && $_GET['post_type'] === 'ajde_events') {	
		        return ['_edit_lock']; // Only cache _edit_lock to satisfy wp_check_post_lock
		    }
		    return $meta_keys;
		}
		function remove_date_filter($A, $post_type){
			if($post_type == 'ajde_events') return array();
			return $A;
		}
		function _register_query_vars( $Q ){
		    //Add these query variables
		    $Q[] = 'event_date_type';
		    $Q[] = 'ev_month';
		     $Q[] = 'event_status';
		    return $Q;
		}
		function evo_restrict_manage_posts() {
			global $typenow;

			if ($typenow !=='ajde_events') return;
			// event date type
	           	$event_date_type = (isset($_GET['event_date_type'])? sanitize_text_field($_GET['event_date_type']):null);
				?>
				<select name="event_date_type">
				    <?php foreach([
				        'all' => __('All (Past and Future)', 'eventon'),
				        'past' => __('Past Events', 'eventon'),
				        'live' => __('Current Events', 'eventon'),
				    ] as $value => $label): ?>
				        <option value="<?= esc_attr($value) ?>" <?= $event_date_type === $value ? 'selected' : '' ?>><?= esc_html($label) ?></option>
				    <?php endforeach; ?>
				</select>				
				<?php

			// event status
				$_es_val = (isset($_GET['event_status'])? sanitize_text_field($_GET['event_status']):null);		           	
				?>
				<select name="event_status">
				    <?php
				    // Start with 'All Event Statuses' as the first option
					$_es_options = ['all' => __('All Statuses', 'eventon')] + EVO()->cal->get_status_array('back');
				    
				    foreach ($_es_options as $value => $label) {
				        printf(
				            '<option value="%s"%s>%s</option>',
				            esc_attr($value), $_es_val === $value ? ' selected' : '', esc_html($label)
				        );
				    }
				    ?>
				</select>
			<?php 

			// event month
				$ev_month = (isset($_GET['ev_month'])? sanitize_text_field($_GET['ev_month']):null);
				?>
				<select name="ev_month">
				    <option value="all"><?= esc_html__('All Months', 'eventon') ?></option>
				    <?php
				    $ev_month = isset($_GET['ev_month']) ? sanitize_text_field($_GET['ev_month']) : null;
				    $DD = EVO()->calendar->DD;
				    $DD->setTimestamp(EVO()->calendar->current_time);
				    $DD->modify('-12 months');

				    for ($x = 0; $x < 25; $x++) {
				        $DD->modify('first day of next month')->setTime(0, 0, 0);
				        $start_unix = $DD->format('U');
				        $DD->modify('last day of this month')->setTime(23, 59, 59);
				        $range = "$start_unix-{$DD->format('U')}";
				        $label = $DD->format('Y-m');
				        ?>
				        <option value="<?= esc_attr($range) ?>" <?= $ev_month === $range ? 'selected' : '' ?>><?= esc_html($label) ?></option>
				        <?php
				    }
				    ?>
				</select>
			<?php
		}
		
		function _pre_get_posts( $query ) {

		    // Only modify main query for 'ajde_events' in admin
		    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'ajde_events') :
		    
		    $query->set('update_post_meta_cache', false);

		    $meta_query = $query->get('meta_query') ?: []; // Default to empty array

		    //Only alter query if custom variable is set.
			    $event_date_type = $query->get('event_date_type');
		        if (!empty($event_date_type)) {
		            $timenow = EVO()->calendar->current_time;
		            $compare = $event_date_type === 'past' ? '<' : ($event_date_type === 'live' ? '>=' : '');
		            if ($compare) {
		                $meta_query[] = [
		                    'key' => 'evcal_erow',
		                    'value' => $timenow,
		                    'compare' => $compare,
		                ];
		            }
		        }

		    // date range filter
			    $ev_month = $query->get('ev_month');
		        if (!empty($ev_month) && $ev_month !== 'all') {
		            $range = explode('-', $ev_month);
		            $meta_query[] = [
		                'key' => 'evcal_erow',
		                'value' => $range[1],
		                'compare' => '<=',
		            ];
		            $meta_query[] = [
		                'key' => 'evcal_srow',
		                'value' => $range[0],
		                'compare' => '>=',
		            ];
		        }

			// Event status filter
        		$event_status = $query->get('event_status');
		        if (!empty($event_status) && $event_status !== 'all') {
		            $meta_query[] = [
		                'key' => '_status',
		                'value' => $event_status,
		                'compare' => '=',
		            ];
		        }

		    // Apply meta query if not empty
	        if (!empty($meta_query))  $query->set('meta_query', $meta_query);

	    	endif;
		}

	// Custom Columns for event page
		function eventon_custom_event_columns( $column , $post_id) {
			global $post;

			$EVENT = new EVO_Event( $post_id);
			$EVENT->load_all_meta();

			$pmv = $EVENT->get_data();

			switch ($column) {
				case has_filter("evo_column_type_{$column}"):

						echo apply_filters("evo_column_type_{$column}", $post_id);

					break;
				case "thumb" :
					//echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . $ajde_events->get_image() . '</a>';
				break;
				
				case "name" :
					$edit_link = get_edit_post_link( $post->ID );
					$title = _draft_or_post_title();
					$post_type_object = get_post_type_object( $post->post_type );
					$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );


					echo "<div class='evoevent_item'>";
						$img_src = EVO()->evo_admin->get_image('thumbnail',false);
						$event_color = eventon_get_hex_color( $EVENT->get_prop('evcal_event_color') );
						
						echo '<a class="evoevent_image" href="' . esc_url( get_edit_post_link( $post_id ) ) . '">';
						
						if($img_src){
							echo '<img class="evoEventCirc" src="' . esc_attr( $img_src )  . '"/>';
						}else{
							echo '<span class="evoEventCirc" style="background-color:' . esc_attr( $event_color ) . '"></span>';
						}
						echo '</a>';

					echo '<div class="evo_item_details">';
					
					
					echo "<span style='display:block'>";
					// event status
					$status = $EVENT->get_event_status();
					if( $status && $status != 'scheduled') 
						echo "<span class='evo_item_status ". esc_attr( $status )."'>". esc_html( $EVENT->get_event_status_l18n( $status ) ) ."</span>";

					// virtual event
					if($EVENT->is_virtual())
						echo "<span class='evo_item_status vir'>". esc_attr( evo_lang( 'Virtual Event' ) )."</span>";

					echo "</span>";
					
					// event name
					if($can_edit_post){
						echo '<strong><a class="row-title" href="'. esc_url( $edit_link ).'">' . esc_html( $title ).'</a>';
					}else{
						echo '<strong>' . esc_html( $title ) .'';
					}

					_post_states( $post );

					echo '</strong>';


					
					if ( $post->post_parent > 0 )
						echo '&nbsp;&nbsp;&larr; <a href="'. esc_url( get_edit_post_link($post->post_parent) ) .'">'. get_the_title($post->post_parent) .'</a>';

					// Excerpt view
					if (isset($_GET['mode']) && $_GET['mode']=='excerpt') echo apply_filters('the_excerpt', $post->post_excerpt);

					// Get actions
						$actions = array();

						$actions['id'] = 'ID: ' . $post->ID;

						if ( $can_edit_post && 'trash' != $post->post_status ) {
							$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( esc_html__( 'Edit this item' ,'eventon') ) . '">' . esc_html__( 'Edit','eventon' ) . '</a>';
							$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( esc_html__( 'Edit this item inline' ) ) . '">' . esc_html__( 'Quick&nbsp;Edit' ) . '</a>';
						}
						if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
							if ( 'trash' == $post->post_status )
								$actions['untrash'] = "<a title='" . esc_attr( esc_html__( 'Restore this item from the Trash','eventon' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . esc_html__( 'Restore' ) . "</a>";
							elseif ( EMPTY_TRASH_DAYS )
								$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( esc_html__( 'Move this item to the Trash','eventon' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . esc_html__( 'Trash' ) . "</a>";
							if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
								$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( esc_html__( 'Delete this item permanently','eventon' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . esc_html__( 'Delete Permanently','eventon' ) . "</a>";
						}
						if ( $post_type_object->public ) {
							if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
								if ( $can_edit_post )
									$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( esc_html__( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . esc_html__( 'Preview','eventon' ) . '</a>';
							} elseif ( 'trash' != $post->post_status ) {
								$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( esc_html__( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . esc_html__( 'View' ) . '</a>';
							}
						}

						$actions = apply_filters( 'post_row_actions', $actions, $post );

					// ROW ACTIONS
						echo '<div class="evo_row_actions row-actions">';
							$i = 0;
							$action_count = sizeof($actions);

							foreach ( $actions as $action => $link ) {
								++$i;
								$sep = ( $i == $action_count ) ? '' : ' | ';
								echo "<span class='". esc_attr( $action )."'>".  wp_kses_post( $link )  . esc_html($sep) ."</span>";
							}
						echo '</div>';
					
					get_inline_data( $post );
				
					
					
					//print_r($event);
					
					/* Custom inline data for eventon */
					echo '<div class="hidden" id="eventon_inline_' . esc_attr( $post->ID ) . '">';
					foreach(  $this->_get_event_edit_values( $EVENT )  as $F=>$V){
						echo "<div class='". esc_attr($F). "'>". esc_html( $V ). "</div>";
					}
					echo "<div class='_menu_order'>". esc_attr( $post->menu_order )."</div>";
					echo '</div>';
					echo '</div><!--.evoevent_item-->';
					
				break;
				
				case "event_type" :		
					if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . esc_attr( $column ) . '=' . esc_attr( $term->slug ) . '&post_type=ajde_events' ) . ' ">' . esc_attr( $term->name ) . '</a>';
						}

						echo wp_kses_post( implode( ', ', $termlist ) );
					}
				break;
				case "event_type_2" :		
					if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . esc_attr($column) . '=' . esc_attr($term->slug) . '&post_type=ajde_events' ) . ' ">' . esc_attr($term->name) . '</a>';
						}

						echo wp_kses_post( implode( ', ', $termlist ) );
					}
				break;
				case "event_location":
				case "event_organizer":
					
					if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . esc_attr($column) . '=' . esc_attr($term->slug) . '&post_type=ajde_events' ) . ' ">' . esc_attr($term->name) . '</a>';
						}

						echo wp_kses_post( implode( ', ', $termlist ) );
					}
						
				break;	

				case "event_start_date":
					
					if( $EVENT->is_year_long() ){
						echo esc_attr( gmdate('Y', $pmv['evcal_srow'][0]) );
					}elseif( $EVENT->is_month_long() ){
						echo esc_attr( date_i18n('F, Y', $pmv['evcal_srow'][0]) );
					}else{
						if(!empty($pmv['evcal_srow'])){
							$_START = eventon_get_editevent_kaalaya($pmv['evcal_srow'][0]);
							if( $EVENT->is_all_day() ){
								echo esc_attr( $_START[0] ) . ' -'. esc_html__('All Day','eventon');
							}else{
								echo esc_attr( $_START[0] ) .' - '.esc_attr($_START[1]).':'.esc_attr($_START[2]). (!empty($_START[3])? esc_attr( $_START[3] ):'');
							}		
							
							
						}else{	echo "--";	}	
					}					
						
				break;		
				
				case "event_end_date":	

					if( $EVENT->is_hide_endtime()){
						echo "--";
						break;
					}
					
					if($EVENT->is_year_long()){
						echo esc_attr( gmdate('Y', $pmv['evcal_srow'][0]) );
					}elseif( $EVENT->is_month_long() ){
						echo esc_attr( date_i18n('F, Y', $pmv['evcal_srow'][0]) );
					}else{
						if(!empty($pmv['evcal_erow'])){	
							$_END = eventon_get_editevent_kaalaya($pmv['evcal_erow'][0]);		
							if( $EVENT->is_all_day() ){
								echo esc_attr($_END[0]). ' -'. esc_html__('All Day','eventon');
							}else{
								echo esc_attr($_END[0]).' - '.esc_attr($_END[1]).':'.esc_attr($_END[2]). (!empty($_END[3])? esc_attr($_END[3]):'');
							}	
						}else{	echo "--";	}
					}		
				break;
				
				case "evo_featured":
					
					$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=eventon-feature-event&eventID=' . esc_attr( $post->ID ) ), 'eventon-feature-event' );
					echo '<a href="' . esc_url( $url ) . '" title="'. esc_html__( 'Toggle featured', 'eventon' ) . '">';
					if ( get_post_meta($post->ID, '_featured', true)=='yes' ) {
						echo '<span class="evo_eventedit_row_ft"></span>';
					} else {
						echo '<span class="evo_eventedit_row_ft notft"></span>';
					}
					echo '</a>';
					
					//echo get_post_meta($post->ID, '_featured', true);		
				break;
				
				case 'repeat':
					
					$repeat = get_post_meta($post->ID, 'evcal_repeat',true);		
					
					if(!empty($repeat) && $repeat=='yes'){
						$repeat_freq = get_post_meta($post->ID, 'evcal_rep_freq',true);
						$output_repeat = '<span class="evo_eventedit_row_rep '. esc_attr($repeat_freq ).'">'. esc_attr( $repeat_freq ) .'</span>';
					}else{
						$output_repeat = '<span class="na">&ndash;</span>';
					}
					
					echo wp_kses_post( $output_repeat );
				break;
				case 'event_lang':
					
					$lang = get_post_meta($post->ID, '_evo_lang',true);	
					
					echo esc_attr( $lang );
				break;
			}
		}

		// get evetn edit fields for quick edit
		// @2.2.13
		function _get_event_edit_values( $EVENT ){
			
			$EV = $EVENT;
			$R = array();

			foreach(array(
				0=>'evcal_start_date',
				'evcal_start_time_hour',
				'evcal_start_time_min',
				'evcal_st_ampm',
				'evcal_end_date',
				'evcal_end_time_hour',
				'evcal_end_time_min',
				'evcal_et_ampm',
				'evcal_location',
				'evcal_organizer',
				'evcal_subtitle',
				'evo_hide_endtime',
				'_featured',
				'evo_exclude_ev',
				'evcal_gmap_gen',
				'evcal_hide_locname',
				'evo_access_control_location',
				'evo_evcrd_field_org',
				'_evo_date_format',
				'_evo_time_format',
			) as $F=>$V){
				$R[ $V ] = esc_html( $EV->get_prop($V) );
			}

			$evcal_date_format = eventon_get_timeNdate_format();
			$R['_evo_date_format'] = $evcal_date_format[1];
			$R['_evo_time_format'] = ($evcal_date_format[2])?'24h':'12h';

			$sunix = $EV->get_prop('evcal_srow');
				$S = eventon_get_editevent_kaalaya($sunix);
				$R['evcal_start_date'] = $S[0];
				$R['evcal_start_time_hour'] = $S[1];
				$R['evcal_start_time_min'] = $S[2];
				if(isset($S[3])) $R['evcal_st_ampm'] = $S[3];

			$eunix = $EV->get_end_time();
				$E = eventon_get_editevent_kaalaya($eunix);
				$R['evcal_end_date'] = $E[0];
				$R['evcal_end_time_hour'] = $E[1];
				$R['evcal_end_time_min'] = $E[2];
				if(isset($E[3])) $R['evcal_et_ampm'] = $E[3];

			$R['_time_ext_type'] = $EV->get_time_ext_type();
			$R['_ev_status'] = $EV->get_event_status();
			$R['_evo_tz'] = $EV->get_timezone_key();

			return $R;
		}
		function eventon_custom_events_sort($columns) {
			$custom = array(
				'event_start_date'		=> 'evcal_start_date',
				'event_end_date'		=> 'evcal_end_date',
				'name'					=> 'title',
				//'evo_featured'			=> 'featured',
				//'repeat'				=> 'repeat',
			);
			return wp_parse_args( $custom, $columns );
		}
		function eventon_custom_event_orderby( $vars ) {
			if (isset( $vars['orderby'] )) :
				if ( 'evcal_start_date' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> 'evcal_srow',
						'orderby' 	=> 'meta_value_num'
					) );
				endif;
				if ( 'evcal_end_date' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> 'evcal_erow',
						'orderby' 	=> 'meta_value'
					) );
				endif;
				if ( 'featured' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> '_featured',
						'orderby' 	=> 'meta_value'
					) );
				endif;

			endif;

			return $vars;
		}

	// Set list table primary column for events
		function list_table_primary_column( $default, $screen_id ) {

			if ( 'edit-ajde_events' === $screen_id ) {
				return 'name';
			}

			return $default;
		}

	// Duplicate event
		function eventon_duplicate_event_link_row($actions, $post) {

			if ( function_exists( 'duplicate_post_plugin_activation' ) ) return $actions;
			
			if ( $post->post_type != 'ajde_events' )	return $actions;

			$post_type = get_post_type_object( $post->post_type );

			if ( current_user_can( $post_type->cap->edit_post, $post->ID ) ){

				$actions['duplicate'] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=duplicate_event&amp;post=' . $post->ID ), 'eventon-duplicate-event_' . $post->ID ) . '" title="' . esc_html__( 'Make a duplicate from this event', 'eventon' )
				. '" rel="permalink">' .  esc_html__( 'Duplicate', 'eventon' ) . '</a>';
			}

			return $actions;
		}
		function eventon_duplicate_event_post_button() {
			global $post;

			if ( function_exists( 'duplicate_post_plugin_activation' ) ) return;
			
			if ( ! is_object( $post ) ) return;

			if ( $post->post_type != 'ajde_events' ) return;

			if ( isset( $_GET['post'] ) ) {
				$notifyUrl = wp_nonce_url( admin_url( "admin.php?action=duplicate_event&post=" . absint( $_GET['post'] ) ), 'eventon-duplicate-event_' . sanitize_text_field( $_GET['post']) );
				?>
				<div class="misc-pub-section" >
					<div id="duplicate-action"><a class="submitduplicate duplication button" href="<?php echo esc_url( $notifyUrl ); ?>"><?php esc_html_e( 'Duplicate this event', 'eventon' ); ?></a></div>
					
				</div>
				<?php
			}
		}

	// Custom quick edit - form
		function eventon_admin_events_quick_edit_scripts( $hook ) {
			global $post_type;

			if ( $hook == 'edit.php' && $post_type == 'ajde_events' )
		    	wp_enqueue_script( 'eventon_quick-edit', AJDE_EVCAL_URL. '/assets/js/admin/quick-edit.js', array('jquery') );
		}
		function eventon_admin_event_quick_edit( $column_name, $post_type ) {
			if ($column_name != 'event_start_date' || $post_type != 'ajde_events') return;

			include_once(EVO()->plugin_path(). '/includes/admin/views/html-quickedit-ajde_events.php');
		}
		function bulk_edit($column_name, $post_type){
			if ($column_name != 'event_start_date' || $post_type != 'ajde_events') return;

			include_once(EVO()->plugin_path(). '/includes/admin/views/html-bulk-edit-ajde_events.php');
		}

		// SAVE QUICK EDIT
		function bulk_and_quick_edit_hook($post_id, $post){
			remove_action( 'save_post', array( $this, 'bulk_and_quick_edit_hook' ) );
			do_action( 'evo_event_bulk_quick_edit', $post_id, $post );
			add_action( 'save_post', array( $this, 'bulk_and_quick_edit_hook' ), 10, 2 );
		}

		function bulk_and_quick_edit_save_hook( $post_id, $post ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;

			if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || 'ajde_events' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) return $post_id;

			//check nonce
			if ( empty( $_REQUEST['eventon_quick_edit_nonce'] ) || ! wp_verify_nonce( $_REQUEST['eventon_quick_edit_nonce'], 'eventon_quick_edit_nonce' ) ) { 
				return $post_id;
			}

			
			$EVENT = new EVO_Event( $post_id);

			if ( ! empty( $_REQUEST['eventon_quick_edit'] ) ) { // WPCS: input var ok.
				$this->quick_edit_save( $post_id, $EVENT );
			} else {
				$this->bulk_edit_save( $post_id, $EVENT );
			}

			return $post_id;			
		}

		// 2.2.13
		private function quick_edit_save($post_id, $EVENT){

			$post_data = EVO()->helper->sanitize_array($_REQUEST);

			// Save fields
			if ( isset( $post_data['evcal_subtitle'] ) ) 
				$EVENT->set_prop('evcal_subtitle',  $post_data['evcal_subtitle'] );

			// start end time
			$proper_time = 	evoadmin_get_unix_time_fromt_post($post_id);

			EVO_Debug($proper_time);

			if ( !empty($proper_time['unix_start']) )
				$EVENT->set_prop('evcal_srow',  $proper_time['unix_start'] );
			
			if ( !empty($proper_time['unix_end']) )
				$EVENT->set_prop('evcal_erow', $proper_time['unix_end']);

			// save adjusted event times
				foreach( array( 'unix_start_ev', 'unix_end_ev', 'unix_vend_ev') as $f){
					if ( !empty($proper_time[ $f ]) ) 
						$EVENT->set_meta(  '_'.$f , $proper_time[ $f ]);
				}

			// yes no fields
			foreach( apply_filters('eventon_quick_save_fields', array(
				'_featured',
				'_ev_status',
				'evo_hide_endtime',
				'evo_span_hidden_end',
				'evo_exclude_ev',
				'evcal_gmap_gen',
				'evcal_hide_locname',
				'evo_access_control_location',
				'evo_evcrd_field_org',
				'_time_ext_type',
				'_evo_tz'
			)) as $field){
				if( empty($post_data[ $field ])) continue;

				$F = ($field == '_ev_status')? '_status': $field;
				$EVENT->set_prop( $F, $post_data[ $field ]  );
			}

		}

		// 2.2.13
		private function bulk_edit_save($post_id, $EVENT){
			$post_data = EVO()->helper->sanitize_array($_REQUEST);

			// yes no fields
			foreach( apply_filters('eventon_quick_save_fields', array(
				'_featured',
				'_ev_status',
				'evo_hide_endtime',
				'evo_span_hidden_end',
				'evo_exclude_ev',
				'evcal_gmap_gen',
				'evcal_hide_locname',
				'evo_access_control_location',
				'evo_evcrd_field_org',
				'_time_ext_type',
				'_evo_tz'
			)) as $field){
				if( empty($post_data[ $field ])) continue;
				if( $post_data[ $field ] == '-') continue; // skip no change values

				$F = ($field == '_ev_status')? '_status': $field;
				$EVENT->set_prop( $F, sanitize_text_field( $post_data[ $field ] )  );
			}
		}
}
new evo_ajde_events();

?>