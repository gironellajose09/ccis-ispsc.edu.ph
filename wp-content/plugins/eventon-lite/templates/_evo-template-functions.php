<?php
/**
 *	EventON Template functions for template system
 *	@version 2.4
 */

defined( 'ABSPATH' ) || exit;


add_action('eventon_before_header','evotemp_before_header');
add_action('eventon_before_main_content','evotemp_before_main_content');
add_action('eventon_single_content_wrapper','evotemp_content_wrapper');

add_action('eventon_single_after_loop','evotemp_after_loop');

add_action('eventon_before_single_event','evotemp_before_single_event');
add_action('eventon_before_event_content','evotemp_before_single_event_content');
add_action('eventon_before_single_event_summary','evotemp_before_single_event_summary');
add_action('eventon_single_event_summary','evotemp_single_event_summary');

add_action('eventon_after_main_content','evotemp_after_main_content');
add_action('eventon_single_after_loop','evotemp_single_event_after_loop');
add_action('eventon_after_event_content','evotemp_after_event_content');
add_action('eventon_after_single_event_summary','evotemp_after_single_event_summary');
add_action('eventon_after_single_event','evotemp_after_single_event');


add_action('evo_taxlb_upcoming_events', 'evo_taxlb_upcoming_events', 10, 2);
function evo_taxlb_upcoming_events( $taxonomy_slug, $temp_data){
	?>
	<h3 class="evotax_term_subtitle location_subtitle "><?php evo_lang_e('Upcoming Events');?></h3>						
	<?php 

	$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style','evcal_1') == 'white'? '0':'2';

	$shortcode = apply_filters('evo_tax_archieve_page_shortcode', 
		'[add_eventon_list number_of_months="5" '.$taxonomy_slug.'='.$temp_data->term_id.' hide_mult_occur="no" hide_empty_months="yes" eventtop_style="'. $eventtop_style.'" ux_val="3a" lang="'. EVO()->lang .'"]', 
		$taxonomy_slug,
		$temp_data->term_id
	);
	echo do_shortcode($shortcode);

}

add_filter( 'post_class', 'evo_event_post_class', 30, 3 );
function evo_event_post_class($class='', $event = null){
	global $post, $event;
	
	if( $post->post_type == 'ajde_events' && !empty($event)){
		$class[] = 'evo_event_content';
		$class[] = $event->ID;
	}
	
	return $class;
}

function evotemp_before_header(){
	global $post, $wp_query;

	$RI = 0;
	$L = 'L1';

	if( isset($_GET['ri']))	$RI = (int)$_GET['ri'];
	if( isset($_GET['l'])) $L = sanitize_text_field( $_GET['l'] );

	$EVENT = evo_setup_event_data( $post, $RI);

	
	// support passing URL like ..../var/ri-2.l-L2/
		if(isset($wp_query->query["var"])){
			$_url_var = $wp_query->query["var"];
			
			$url_var = explode('.', $_url_var);
			$vars = array();
			
			foreach($url_var as $var){
				$split = explode('-', $var);

				// RI
				if($split[0] == 'ri') $RI = (int)$split[1];
				if($split[0] == 'l') $L = $split[1];

				$EVENT->ri = $RI;					
			}

			evo_set_global_lang($L); // set global language

			// virtual event access
			if($_url_var == 'event_access'){					
				
				$vir_url = $EVENT->get_vir_url();
				
				if($vir_url){
					wp_redirect( $vir_url ); exit;
				} 
			}
		}

	if( !evo_current_theme_is_fse_theme() )  get_header('events');
}
function evotemp_before_main_content(){
	wp_enqueue_style( 'evo_single_event');		
	EVO()->frontend->load_evo_scripts_styles();		

}

	// when the post is called put event data into global
	function evo_setup_event_data( $post, $RI=0){

		unset( $GLOBALS['event'] );

		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'ajde_events' ), true ) ) {
			return;
		}

		$GLOBALS['event'] = new EVO_Event($post->ID, '', $RI , true, $post);

		return $GLOBALS['event'];
	}

function evotemp_content_wrapper(){

	?>
	<div class='evo_page_content <?php echo EVO()->cal->check_yn('evosm_1','evcal_1') ? 'evo_se_sidarbar':null;?>'>
	<?php
}
function evotemp_after_loop(){

}
function evotemp_after_main_content(){
	if( !evo_current_theme_is_fse_theme() ) get_footer('events');
}
function evotemp_before_single_event(){}
function evotemp_before_single_event_content(){

	global $event;

	// if password protected event
	//if( $event->is_password_required() ) echo 'Password Protected event';

	$event_id = get_the_ID();
	$json = apply_filters('evo_event_json_data',array(), $event_id);

	$_class_attr = array();

	// eventtop style
	$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style');
	if(!$eventtop_style) $eventtop_style = 'color';

	$_class_attr[] = $eventtop_style;

	$_class_attr[] = 'etttc_'. EVO()->cal->get_ett_color_prop();
	if( EVO()->cal->check_yn( 'evo_rtl','evcal_1') ) $_class_attr[] =  'evortl';

	?>
	<div id='evcal_single_event_<?php echo esc_attr( get_the_ID() );?>' class='ajde_evcal_calendar eventon_single_event evo_sin_page <?php echo  esc_attr( implode(' ', $_class_attr) );?>' data-eid='<?php echo esc_attr( $event_id );?>' data-l='<?php echo esc_attr( EVO()->lang );?>' data-j='<?php echo wp_json_encode($json);?>'>
	<?php

	// event data 
	$event_map_data = $event->get_event_data_for_gmap();
	$help = EVO()->helper;

	// deprecating ?><div class='evo-data' <?php $help->print_array_to_html_data( $event_map_data );?>></div>
	<div class='evo_cal_data' data-sc='<?php echo wp_json_encode($event_map_data);?>'></div>
			<?php

	// calendar month header
	$repeati = $event->ri;
	$lang = EVO()->lang;	

	$formatted_time = eventon_get_formatted_time( $event->get_event_time() , $event->tz );	
	$header_text =  get_eventon_cal_title_month($formatted_time['n'], $formatted_time['Y'], $lang);

	
	// if show month year header
	if( EVO()->cal->check_yn('evosm_show_monthyear')):
		?><div id='evcal_head' class='calendar_header'><p id='evcal_cur'><?php echo esc_attr( $header_text );?></p></div><?php
	endif;
}


function evotemp_before_single_event_summary(){}


// event summary content @2.2.18
function evotemp_single_event_summary(){
	global $event;

	// eventtop style
	$eventtop_style = EVO()->cal->get_prop('evosm_eventtop_style');
	if(!$eventtop_style) $eventtop_style = 'immersive';



	$single_events_args = apply_filters('eventon_single_event_page_data',array(
		'etc_override'=>'no',
		'eventtop_style'=> ($eventtop_style == 'color'? 2:0),
		'eventtop_layout_style'=> EVO()->cal->get_prop('evosm_eventtop_layout_style')
	));

	// override event color on page with event typ color @since 2.2.18
		if( EVO()->cal->check_yn('evosm_etc_override') ) $single_events_args['etc_override'] = 'yes';

	$content =  EVO()->calendar->get_single_event_data( $event->ID, EVO()->lang, $event->ri, $single_events_args);		

	// login only access
	$thisevent_onlylogged_cansee = $event->check_yn('_onlyloggedin');
	// pluggable access restriction to event
		$continue_with_page_content = apply_filters('evo_single_page_access', true , $thisevent_onlylogged_cansee);
	// stop single event page from loading without access 4.6
		if( !$continue_with_page_content ) return;

	if( $thisevent_onlylogged_cansee && !is_user_logged_in() ):

		echo "<p class='evo_single_event_noaceess'>". esc_attr( evo_lang('You must login to see this event') ) ."<br/><a class='button' href=". 
		esc_url( wp_login_url( $event->get_permalink() ) ) ." title='". esc_attr( evo_lang('Login') ) ."'>". esc_attr( evo_lang('Login') ) ."</a></p>";

	else:

		// repeat header
		echo $event->get_repeat_header_html();

		/*?><h1 class='evosin_event_title'><?php echo $event->get_title();?></h1><?php*/
			
		echo $content[0]['content'];
	endif;
}
function evotemp_after_event_content(){

	// comments section
	if( !EVO()->cal->check_yn('evosm_comments_hide')){

		if( !evo_current_theme_is_fse_theme() ):			
		?>
		<div id='eventon_comments'><?php comments_template( '', true );	?></div>
		<?php
		endif;
	}

	?></div><!---ajde_evcal_calendar--><?php 
}
function evotemp_after_single_event_summary(){}
function evotemp_single_event_after_loop(){

	// side bar
	if(EVO()->cal->check_yn( 'evosm_1','evcal_1')){
		if ( is_active_sidebar( 'evose_sidebar' ) ){

			if( !evo_current_theme_is_fse_theme() ):
			?>
			<div class='evo_page_sidebar'>
				<ul id="sidebar">
					<?php dynamic_sidebar( 'evose_sidebar' ); ?>
				</ul>
			</div>
			<?php
			endif;
		}
	}
	?></div><!-- evo_page_content--><?php
}
function evotemp_after_single_event(){}

function evo_get_page_header(){
	if( !evo_current_theme_is_fse_theme() )	get_header();
}
function evo_get_page_footer(){
	if( !evo_current_theme_is_fse_theme() )	get_footer();
}
function evo_get_page_sidebar(){
	if( !evo_current_theme_is_fse_theme() ){
		echo "<div class='evo_sidebar'>";
		get_sidebar();
		echo "</div>";
	}
}