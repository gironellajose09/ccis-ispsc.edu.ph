<?php
/**
 * Admin taxonomy functions
 *
 *
 * @author 		Ashan Jay
 * @category 	Admin
 * @package 	eventon/Admin/Taxonomies
 * @version     L 2.2.13
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVO_Taxonomies extends EVO_Taxonomies_editor{

	private $help;
	function __construct(){
		add_action( 'admin_init', array($this,'eventon_taxonomy_admin' ));
		add_action( 'event_type_pre_add_form', array($this, 'event_type_description' ));
		add_action( 'admin_init', array($this, 'eventon_add_tax') );
		add_action( 'admin_init', array($this, 'editor_ajax_calls') );

		// event type 1
		add_filter( 'manage_edit-event_type_columns', array($this,'event_type_edit_columns'),5 );
		add_filter( 'manage_event_type_custom_column', array($this,'event_type_custom_columns'),5,3 );

		// event type 2
		add_filter( 'manage_edit-event_type_2_columns', array($this,'event_type_edit_columns'),5 );
		add_filter( 'manage_event_type_2_custom_column', array($this,'event_type_custom_columns'),5,3 );

		add_action( 'event_type_add_form_fields', array($this,'evo_tax_add_new_meta_field_et1'), 10, 2 );

		add_action( 'event_type_edit_form_fields', array($this,'evo_tax_edit_new_meta_field_et1'), 10, 2 );
		add_action( 'edited_event_type', array($this,'evo_tax_save_new_meta_field_et1'), 10, 2 ); 
		add_action( 'create_event_type', array($this,'evo_tax_save_new_meta_field_et1'), 10, 2 );

		// event location
			add_action("event_location_term_edit_form_top", array($this,'location_edit_form_top'), 10 ,2);
			add_filter("manage_edit-event_location_columns", array($this,'eventon_evLocation_theme_columns')); 
			add_filter("manage_event_location_custom_column", array($this,'eventon_manage_evLocation_columns'), 10, 3);
			add_action( 'event_location_add_form_fields', array($this,'eventon_taxonomy_add_new_meta_field'), 10, 2 );
	 		add_action( 'event_location_edit_form_fields', array($this,'eventon_taxonomy_edit_meta_field'), 10, 2 );
	 		add_action( 'edited_event_location', array($this,'evo_save_taxonomy_custom_meta'), 10, 2 );  
			add_action( 'create_event_location', array($this,'evo_save_taxonomy_custom_meta'), 10, 2 );
			add_action( 'event_location_edit_form', array($this,'tax_viewpage_btn'), 10, 2 );
			add_action( 'event_organizer_edit_form', array($this,'tax_viewpage_btn'), 10, 2 );

		// event organizer
			add_action("event_organizer_term_edit_form_top", array($this,'organizer_edit_form_top'), 10 ,2); 
			add_filter("manage_edit-event_organizer_columns", array($this,'eventon_evorganizer_theme_columns'));  
			add_filter("manage_event_organizer_custom_column", array($this,'eventon_manage_evorganizer_columns'), 10, 3); 
			add_action( 'event_organizer_add_form_fields', array($this,'eventon_taxonomy_add_new_meta_field_org'), 10, 2 );
			add_action( 'event_organizer_edit_form_fields', array($this,'eventon_taxonomy_edit_meta_field_org'), 10, 2 );
			add_action( 'edited_event_organizer', array($this,'evo_save_taxonomy_custom_meta'), 10, 2 );  
			add_action( 'create_event_organizer', array($this,'evo_save_taxonomy_custom_meta'), 10, 2 );

		$this->help = EVO()->helper;
	}

	// other settings
		function eventon_taxonomy_admin(){
			global $pagenow;
			if($pagenow =='edit-tags.php' && !empty($_GET['taxonomy']) 
				&& ($_GET['taxonomy']=='event_location' || 
				$_GET['taxonomy']=='event_organizer' )
				&& !empty($_GET['post_type']) 
				&& $_GET['post_type']=='ajde_events'){
				wp_enqueue_media();
			}


		}
		function event_type_description() {
			printf(  __( 'Event Type Categories can be edited, deleted and updated in this page. <br/>More Information: <a href="%s" target="_blank">Learn how to use event types to do more with eventON</a>', 'eventon' ),
			'http://www.myeventon.com/documentation/how-to-use-event-types-to-do-more/' );
		}

	// event types
		function eventon_add_tax(){
			$options = get_option('evcal_options_evcal_1');
			for($x=3; $x <= evo_max_ett_count(); $x++){
				if(!empty($options['evcal_ett_'.$x]) && $options['evcal_ett_'.$x]=='yes'){
					add_filter( "manage_edit-event_type_{$x}_columns", array($this,'event_type_edit_columns'),5 );
					add_filter( "manage_event_type_{$x}_custom_column", array($this,'event_type_custom_columns'),5,3 );
				}
			}
		}

		// Columms
		function event_type_edit_columns($defaults){

			unset( $defaults['description']);

		    $defaults['cb'] = "<input type=\"checkbox\" />";
		    $defaults["name"] = esc_html__( 'Name', 'eventon' );
		    $defaults['event_type_id'] = esc_html__('ID');
		    return $defaults;
		} 
		function event_type_custom_columns($value, $column_name, $id){
			if($column_name == 'event_type_id'){
				$t_id = (int)$id;
				$term_meta = get_option( "evo_et_taxonomy_$t_id" );
				
				$term_color = (!empty($term_meta['et_color']))? 
					'<span class="evoterm_color" style="background-color:#'. esc_attr( $term_meta['et_color'] ).'"></span>':false;

				$term_icon = (!empty($term_meta['et_icon']))? 
					'<span class="evoterm_icon"><i class="fa '. esc_attr( $term_meta['et_icon'] ).'"></i></span>':false;
				?>
				<span class="term_id"><?php echo esc_attr( $t_id );?></span><?php echo $term_color . $term_icon;?><span class='clear'></span>
				<?php
			}
		}

		// add term page
			function evo_tax_add_new_meta_field_et1() {
				// this will add the custom meta field to the add new term page
				?>
				<div class="form-field" id='evo_et1_color'>				
					<p class='evo_et1_color_circle' hex='bbbbbb'></p>
					<label for="term_meta[et_color]"><?php esc_html_e( 'Color', 'eventon' ); ?></label>
					<input type="hidden" name="term_meta[et_color]" id="term_meta[et_color]" value="">
					<p class="description"><?php esc_html_e( 'Pick a color','eventon' ); ?></p>
				</div>
				<?php 

				EVO()->elements->print_get_icon_html();
				?>
				<div class="form-field " id='evo_evnet_type_icon'>				
				<?php 
					EVO()->elements->print_element(array(
						'type'=>'icon_select',
						'id'=>'term_meta[et_icon]',
						'value'=>'',
						'legend'=> esc_html__( 'Select an Icon','eventon' )
					));
				?>	
				</div>
				<?php
			}
		// Edit term page
			function evo_tax_edit_new_meta_field_et1($term) {		 
				// put the term ID into a variable
					$t_id = $term->term_id;
				 
					// retrieve the existing value(s) for this meta field. This returns an array
					$term_meta = get_option( "evo_et_taxonomy_$t_id" ); 
					
					EVO()->elements->print_get_icon_html();
				?>
				<tr class="form-field">
				<th scope="row" valign="top"><label for="term_meta[et_color]"><?php  esc_html_e( 'Color', 'eventon' ); ?></label></th>
					<td id='evo_et1_color'>
						<?php $__this_value = !empty( $term_meta['et_color'] ) ? esc_attr( $term_meta['et_color'] ) : ''; ?>
						<p class='evo_et1_color_circle' hex='<?php echo esc_attr( $__this_value );?>' style='background-color:#<?php echo esc_attr( $__this_value );?>'></p>
						<input type="hidden" name="term_meta[et_color]" id="term_meta[et_color]" value="<?php echo esc_attr( $__this_value );?>">
						<p class="description"><?php  esc_html_e( 'Pick a color','eventon' ); ?></p>
					</td>
				</tr>
				<tr class="form-field">
				<th scope="row" valign="top"><label for="term_meta[et_icon]"><?php esc_html_e( 'Icon', 'eventon' ); ?></label></th>
					<td id='evo_et1_icon'>
						<?php $__this_value = ( !empty($term_meta['et_icon']) ) ? esc_attr( $term_meta['et_icon'] ) : ''; 


							EVO()->elements->print_element(array(
								'type'=>'icon_select',
								'id'=>'term_meta[et_icon]',
								'value'=> esc_attr( $__this_value ),
								'close'=>true,
								'legend'=> esc_html__( 'Select an Icon','eventon' )
							));
						?>
					</td>
				</tr>
			<?php
			}
		// Save extra taxonomy fields callback function.
			function evo_tax_save_new_meta_field_et1( $term_id ) {
				$postdata = $this->help->sanitize_array($_POST);

				if ( isset( $postdata['term_meta'] ) ) {
					$t_id = $term_id;
					$term_meta = get_option( "evo_et_taxonomy_$t_id" );
					$cat_keys = array_keys( $postdata['term_meta'] );
					foreach ( $cat_keys as $key ) {
						if ( isset ( $postdata['term_meta'][$key] ) ) {

							if( !is_array($term_meta)) $term_meta = array();

							$term_meta[$key] = $postdata['term_meta'][$key];
						}
					}
					// Save the option array.
					update_option( "evo_et_taxonomy_$t_id", $term_meta );
				}
			}  

	// Get taxonomy terms list as array - updated @4.2
		function get_event_tax_fields_array($tax, $event_tax_term='', $field_type =''){
			
			$postdata = $this->help->sanitize_array($_POST);

			$is_new = (isset($postdata['type']) && $postdata['type']=='new')? true: false;

			$tax_fields = apply_filters('evo_taxonomy_form_fields_array', array(
				'event_location'=> array(
					'term_name'=>array(
						'type'=>'text',
						'name'=> esc_html__('Location Name','eventon'),
						'placeholder'=>'eg. Irving City Park',
						'value'=> ($event_tax_term? esc_html( $event_tax_term->name ):''),
						'var'=>	'term_name',
						'legend'=> ($is_new?'':'NOTE: If you change the location name, it will create a new location.')
					),
					'description'=>array(
						'type'=>'textarea',
						'name'=>esc_html__('Location Description','eventon'),
						'var'=>'description',
						'value'=> ($event_tax_term? $event_tax_term->description:''),				
					),
					'location'=>array(
						'type'=>'text',
						'name'=>esc_html__('Location Address','eventon'),
						'placeholder'=>'eg. 12 Rue de Rivoli, Paris',
						'var'=>'location_address'				
					),
					'location_city'=>array(
						'type'=>'text',
						'name'=>esc_html__('Location City (Optional)','eventon'),
						'var'=>'location_city',
						'nesting_start'=> 'evo_loc_post'				
					),
					'location_state'=>array(
						'type'=>'text',
						'name'=>esc_html__('Location State (Optional)','eventon'),
						'var'=>'location_state'				
					),
					'location_country'=>array(
						'type'=>'text',
						'name'=>esc_html__('Location Country (Optional)','eventon'),
						'var'=>'location_country',
						'nesting_end'=> true				
					),
					'evcal_lat'=>array(
						'type'=>'text',
						'name'=>esc_html__('Latitude','eventon'),	
						'var'=> 'location_lat',
						'nesting_start'=> 'evo_loc_post'	
					),'evcal_lon'=>array(
						'type'=>'text',
						'name'=>esc_html__('Longitude','eventon'),
						'var'=> 'location_lon',
						'nesting_end'=> true					
					),
					'location_getdir_latlng'=>array(
						'type'=>'yesno',
						'name'=>esc_html__('Use Lat/Lng for get directions location','eventon'),
						'var'=> 'location_getdir_latlng'					
					),
					'evcal_location_link'=>array(
						'type'=>'text',
						'name'=>__('Link for Location','eventon'),	
						'var'=>'evcal_location_link'					
					),
					'evcal_location_link_target'=>array(
						'type'=>'yesno',
						'name'=>esc_html__('Open location link in new window','eventon'),
						'var'=> 'evcal_location_link_target'					
					),
					'loc_phone'=>array(
						'type'=>'text',
						'name'=>'Phone Number',	
						'var'=>'loc_phone'	,'nesting_start'=> 'evo_elm_row_50'						
					),
					'loc_email'=>array(
						'type'=>'text',
						'name'=>'Email Address',	
						'var'=>'loc_email'	, 'nesting_end'=> true					
					),
					'evo_loc_img'=>array(
						'type'=>'image',
						'name'=>esc_html__('Location Image','eventon'),
						'var'=>	'evo_loc_img'	
					),
					'location_type'=>array(
						'type'=>'select',
						'name'=>'Location Type',	
						'var'=>'location_type',
						'options'=> array(
							'place'=> esc_html__('Physical Location','eventon'),
							'virtual'=> esc_html__('Virtual Location','eventon'),
						)					
					),
				),
				'event_organizer'=> array(
					'term_name'=>array(
						'type'=>'text',
						'name'=>esc_html__('Organizer Name','eventon'),
						'placeholder'=>'eg. Electronic Entertainments',
						'value'=> ($event_tax_term? $event_tax_term->name:''),
						'var'=>	'term_name',
						'legend'=> ($is_new?'':'NOTE: If you change the organizer name, it will create a new organizer.')
					),
					'description'=>array(
						'type'=>'textarea',
						'name'=>esc_html__('Organizer Description','eventon'),
						'var'=>'description',
						'value'=> ($event_tax_term? $event_tax_term->description:''),				
					),
					'description2'=>array(
						'type'=>'wysiwyg',
						'name'=>esc_html__('Organizer Secondary Description','eventon'),
						'var'=>'description2',				
					),
					
					'evo_org_img'=>array(
						'type'=>'image',
						'name'=>esc_html__('Organizer Image','eventon'),
						'var'=>	'evo_org_img'	
					),
					'evcal_org_contact_e'=> array(
						'var'=> 'evcal_org_contact_e','type'=>'text',
						'name'=>esc_html__( 'Email Address', 'eventon' ),
						'desc'=>esc_html__( 'Enter Organizer Email Address','eventon' ),
						'nesting_start'=> 'evo_emailX'	
					),
					'evcal_org_contact_phone'=> array(
						'var'=> 'evcal_org_contact_phone','type'=>'text',
						'name'=>esc_html__( 'Phone Number', 'eventon' ),
						'desc'=>esc_html__( 'Enter Organizer Phone Number','eventon' ),
						'nesting_end'=> true	
					),
					'evcal_org_address'=>array(
						'type'=>'text',
						'name'=>esc_html__('Organizer Physical Address','eventon'),	
						'var'=> 'evcal_org_address'	,
					),
					'evcal_org_contact'=>array(
						'type'=>'text',
						'name'=>esc_html__('Other General Contact Details','eventon'),
						'var'=>'evcal_org_contact'				
					),
					
					'evcal_org_tw'=> array(
						'var'=> 'evcal_org_tw','type'=>'text',
						'name'=>esc_html__( 'Twitter/X Link', 'eventon' ),
						'desc'=>esc_html__( 'Link to organizer Twitter/X page','eventon' ),
						'nesting_start'=> 'evo_org'		
					),
					'evcal_org_ig'=> array(
						'var'=> 'evcal_org_ig','type'=>'text',
						'name'=>esc_html__( 'Instagram Link', 'eventon' ),
						'desc'=>esc_html__( 'Link to organizer Instagram page','eventon' ),
						'nesting_end'=> true	
					),					
					'evcal_org_yt'=> array(
						'var'=> 'evcal_org_yt','type'=>'text',
						'name'=>esc_html__( 'Youtube Link', 'eventon' ),
						'desc'=>esc_html__( 'Link to organizer Youtube page','eventon' ),
						'nesting_start'=> 'evo_org'		
					),
					'evcal_org_wa'=> array(
						'var'=> 'evcal_org_wa','type'=>'text',
						'name'=>esc_html__( 'WhatsApp Link', 'eventon' ),
						'desc'=>esc_html__( 'Link to organizer WhatsApp page','eventon' ),
						'nesting_end'=> true	
					),
					'evcal_org_tt'=> array(
						'var'=> 'evcal_org_tt','type'=>'text',
						'name'=>esc_html__( 'TikTok Link', 'eventon' ),
						'desc'=>esc_html__( 'Link to organizer TikTok page','eventon' ),
						'nesting_start'=> 'evo_org'		
					),
					'evcal_org_fb'=> array(
						'var'=> 'evcal_org_fb','type'=>'text',
						'name'=>esc_html__( 'Facebook Link', 'eventon' ),
						'desc'=>esc_html__( 'Link to organizer facebook page','eventon' ),
						'nesting_end'=> true	
					),
					'evcal_org_ln'=> array(
						'var'=> 'evcal_org_ln','type'=>'text',
						'name'=>esc_html__( 'Linkedin Link', 'eventon' ),
						'desc'=>esc_html__( 'Link to organizer Linkedin page','eventon' ),
						'nesting_start'=> 'evo_org'		
					),
					'evcal_org_exlink'=>array(
						'var'=> 'evcal_org_exlink','type'=>'text',
						'name'=>esc_html__('Organizer Link','eventon'),
						'var'=> 'evcal_org_exlink'	,
						'nesting_end'=> true					
					),
					'_evocal_org_exlink_target'=>array(
						'type'=>'yesno',
						'name'=>esc_html__('Open link in new window','eventon'),	
						'var'=>'_evocal_org_exlink_target'					
					),
					
				)

			), $tax, $event_tax_term);


			return isset($tax_fields[ $tax ]) ? $tax_fields[ $tax ]: array();

		}

	// TAXONOMY - event location
		// pre edit form
			function location_edit_form_top($tag, $taxonomy){
				if( !isset( $tag->name ) ) return;
				echo esc_html__('Location ID') . ': #'. esc_attr( $tag->term_id );
			}
		// remove some columns		
			function eventon_evLocation_theme_columns($theme_columns) {
			    $new_columns = array(
			        'cb' => '<input type="checkbox" />',
			        //'id' => esc_html__('ID','eventon'),
			        'name' => esc_html__('Location','eventon'),
			        'event_location_details' => esc_html__('Info','eventon'),
			        //'event_location' => esc_html__('Address','eventon'),
			        //'ev_lonlat' => esc_html__('Lon/Lat','eventon'),
			        'posts' => esc_html__('Count','eventon'),
					//      'description' => esc_html__('Description'),
			        'slug' => esc_html__('Slug'),
			    );			    
			    //return array_merge($theme_columns, $new_columns);
			    return $new_columns;
			}

		// Add event location address field 
			function eventon_manage_evLocation_columns($out, $column_name, $term_id) {
			    $term_meta = evo_get_term_meta( 'event_location',$term_id );
			    switch ($column_name) {
			        case 'event_location_details': 
			        	$term = get_term_by('id', $term_id, 'event_location'); 

			        	//get data
			        	$type = !empty($term_meta['location_type'])? $term_meta['location_type']:false;
			        	$imgID = !empty($term_meta['evo_loc_img'])? $term_meta['evo_loc_img']:false;
			        	$ADDRESS = !empty($term_meta['location_address']) ? 
			        		stripslashes(esc_attr( $term_meta['location_address'] )) : '-';

			        	$lon = (!empty($term_meta['location_lon']))? esc_attr( $term_meta['location_lon'] ) : false;
			        	$lat = (!empty($term_meta['location_lat']))? esc_attr( $term_meta['location_lat'] ) : false;	
			        	$locLink = (!empty($term_meta['evcal_location_link']))? esc_attr( $term_meta['evcal_location_link'] ) :false;

			        	// image
			        		$img_url = ($imgID)? wp_get_attachment_image_src($imgID,'thumbnail'):false;
			        		$imgHTML = ($img_url)? "<p class='evotax_location_image'><img src='". esc_attr( $img_url[0] )."'/></p>":'';

			        	$out = $imgHTML;


			        	// location type
			        	if( $type == 'virtual'){
			        		$out .= "<span class='location_type'>". esc_html__('Virtual','eventon') ."</span>";
			        	}

			        	$out .= "<p class='evotax_location_info'>";

			        	if( $type != 'virtual') 
			        		$out .= "<b>".esc_html__('ADDRESS','eventon').':</b> '. esc_html( $ADDRESS )."<br/>";
			        	if($lon && $lat) 
			        		$out .= "<b>LAT/LON:</b> ". esc_html($lat)."/". esc_html($lon)."<br/>";
			        	$out .= "<b>ID: </b>". esc_attr( $term_id )." <br/>";

			        	if($locLink)
			        		$out .= "<b>LINK: </b>". esc_url( $locLink )." <br/>";

			        	$out .= "</p>";
			        break;
			        case 'event_location': 
			        	$out = "<p>". $term_meta['location_address'] ? esc_attr( $term_meta['location_address'] ) : ''."</p>";
			        break;
			        case 'ev_lonlat': 
			        	$lon = (!empty($term_meta['location_lon']))? esc_attr( $term_meta['location_lon'] ) : '-';
			        	$lat = (!empty($term_meta['location_lat']))? esc_attr( $term_meta['location_lat'] ) : '-';			        	
			        	$out = "<p>{$lon} / {$lat}</p>";
			        break;
			        case 'id': $out = $term_id; break;	

			       	default:
			            break;
			    }
			    return $out;    
			}
		// add term page
			function eventon_taxonomy_add_new_meta_field() {
				// this will add the custom meta field to the add new term page
				?>
				<div class="form-field">
					<label for="term_meta[location_address]"><?php esc_html_e( 'Location Address', 'eventon' ); ?></label>
					<input type="text" name="term_meta[location_address]" id="term_meta[location_address]" value="">
					<p class="description"><?php esc_html_e( 'Enter a location address','eventon' ); ?></p>
				</div>
				<div class="form-field">
					<label for="term_meta[location_lat]"><?php esc_html_e( 'Latitude', 'eventon' ); ?></label>
					<input type="text" name="term_meta[location_lat]" id="term_meta[location_lat]" value="">
					<p class="description"><?php esc_html_e( '(Optional) latitude for address','eventon' ); ?></p>
				</div>
				<div class="form-field">
					<label for="term_meta[location_lon]"><?php esc_html_e( 'Longitude', 'eventon' ); ?></label>
					<input type="text" name="term_meta[location_lon]" id="term_meta[location_lon]" value="">
					<p class="description"><?php esc_html_e( '(Optional) longitude for address','eventon' ); ?></p>
				</div>
				<div class="form-field">
					<p><span class='yesno_row evo'>
						<?php 	
						EVO()->elements->print_yesno_btn(array(
							'id'=>'term_meta[location_getdir_latlng]', 
							'var'=> '',
							'input'=> true,
							'label'=>esc_html__('Use Lat/Lng for get directions location','eventon')
						));?>											
					</span></p>
				</div>
				<div>
					<p><?php esc_html_e('NOTE: LatLong will be auto generated for address provided for faster google map drawing. If location marker is not correct feel free to edit the LatLong values to correct location marker coordinates above. Location address field is REQUIRED for this to work. <a href="https://itouchmap.com/?r=latlong" target="_blank">Find LanLat for address</a>','eventon');?></p>
				</div>
				<div class="form-field">
					<label for="term_meta[evcal_location_link]"><?php esc_html_e( 'Location Link', 'eventon' ); ?></label>
					<input type="text" name="term_meta[evcal_location_link]" id="term_meta[evcal_location_link]" value="" placeholder='http://'>
					<p class="description"><?php esc_html_e( 'Enter a location link','eventon' ); ?></p>
					<p><span class='yesno_row evo'>
						<?php 	
						EVO()->elements->print_yesno_btn(array(
							'id'=>'term_meta[evcal_location_link_target]', 
							'var'=> '',
							'input'=>true,
							'label'=>esc_html__('Open location link in new window','eventon')
						));?>											
					</span></p>
				</div>
				
				<div class="form-field evo_metafield_image">
					<label for="term_meta[evo_loc_img]"><?php _e( 'Image', 'eventon' ); ?></label>

					<?php 

						echo EVO()->elements->get_element(array(
							'type'=>'image',
							'id'=>'term_meta[evo_loc_img]',
							'value'=> '',
						));
					?>
					<p class="description"><?php esc_html_e( '(Optional) Location Image','eventon' ); ?></p>
				</div>

				<?php 
				// additional fields
					foreach($this->get_event_tax_fields_array('event_location') as $field=>$value){
						if(in_array($field, array('term_name','description','location', 'evcal_lat','evcal_lon','evcal_location_link','evo_loc_img','submit','location_getdir_latlng','evcal_location_link_target' ))) continue;

						?>
						<div class="form-field">
							<label for="term_meta[<?php echo esc_attr( $field );?>]"><?php echo esc_attr( $value['name'] ); ?></label>
							<input type="text" name="term_meta[<?php echo esc_attr( $field );?>]" id="term_meta[<?php echo esc_attr( $field );?>]" value="">
						</div>
						<?php
					}

				?>
				<div class="form-field ">
					<label for="term_meta[location_type]"><?php esc_html_e( 'Location Type', 'eventon' ); ?></label>
					<select type="text" name="term_meta[location_type]" id="term_meta[location_type]">
						<option value='place'><?php esc_html_e('Physical Location','eventon');?></option>
						<option value='virtual'><?php esc_html_e('Virtual Location','eventon');?></option>
					</select>
				</div>

				<?php

			}

		// edit tag page footer
			function tax_viewpage_btn($tag, $tax){
				$link = get_term_link( $tag , $tax);
				echo "<p><a class='evo_admin_btn' href='". esc_url( $link ) ."'>".esc_html__('VIEW','eventon')."</a></p>";
			}
		
		// Edit term page
			function eventon_taxonomy_edit_meta_field($term) {
			 		 

				// put the term ID into a variable
				$t_id = $term->term_id;
			 
				// retrieve the existing value(s) for this meta field. This returns an array
				
				$term_meta = evo_get_term_meta('event_location',$t_id);
				//$term_meta = get_option( "taxonomy_$t_id" ); 

				?>
				
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_address]"><?php esc_html_e( 'Location Address', 'eventon' ); ?></label></th>
					<td>
						<input type="text" name="term_meta[location_address]" id="evo_admin_location_address" value="<?php echo !empty($term_meta['location_address'] ) ? esc_attr( stripslashes($term_meta['location_address']) ) : ''; ?>">
						<p class="description"><?php esc_html_e( 'Enter a location address','eventon' ); ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_lat]"><?php esc_html_e( 'Latitude', 'eventon' ); ?></label></th>
					<td>
						<input type="text" name="term_meta[location_lat]" id="term_meta[location_lat]" value="<?php echo  !empty($term_meta['location_lat']) ? esc_attr( $term_meta['location_lat'] ) : ''; ?>">
						<p class="description"><?php esc_html_e( '(Optional) latitude for address','eventon' ); ?></p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_lon]"><?php esc_html_e( 'Longitude', 'eventon' ); ?></label></th>
					<td>
						<input type="text" name="term_meta[location_lon]" id="term_meta[location_lon]" value="<?php echo !empty($term_meta['location_lon']) ? esc_attr( $term_meta['location_lon'] ) : ''; ?>">
						<p class="description"><?php esc_html_e( '(Optional) longitude for address','eventon' ); ?></p>

						<div style='padding-top:20px'>
							<?php if( EVO()->cal->get_prop('evo_gmap_api_key', 'evcal_1')):?>
								<p><?php echo wp_kses_post('<b>NOTE:</b> LatLong will be auto generated for address provided for faster google map drawing. If location marker is not correct feel free to edit the LatLong values to correct location marker coordinates above. Location address field is REQUIRED for this to work. <a href="https://itouchmap.com/?r=latlong" target="_blank">Find LanLat Coordinates for address in here</a>','eventon');?></p>
								<p style='padding-top:10px'><a class='evo_auto_gen_latlng evo_admin_btn'><?php esc_html_e('Generate Location Coordinates','eventon');?></a></p>

							<?php else:?>
								<p><?php echo wp_kses_post('<b>NOTE:</b> You must set Google Maps API key via EventON Settings > Google Maps API for auto generation of location coordinates to work. <a href="https://itouchmap.com/?r=latlong" target="_blank">Find LanLat Coordinates for address in here</a>','eventon');?></p>
							<?php endif;?>
						</div>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_getdir_latlng]"><?php esc_html_e('Use Lat/Lang for get location directions','eventon'); ?></label></th>
					<td>
						<p><span class='yesno_row evo'>
							<?php 	
							$location_getdir_latlng = $this->termmeta($term_meta,'location_getdir_latlng');
							
							EVO()->elements->print_yesno_btn(array(
								'id'=>'term_meta[location_getdir_latlng]', 
								'var'=> esc_attr( $location_getdir_latlng ),
								'input'=>true,
								'label'=>	esc_html__('Use Lat/Lang for get location directions' ,'eventon')
							));?>											
						</span></p>
					</td>
				</tr>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[evcal_location_link]"><?php esc_html_e( 'Location Link', 'eventon' ); ?></label></th>
					<td>
						<input type="text" name="term_meta[evcal_location_link]" id="term_meta[evcal_location_link]" value="<?php echo !empty($term_meta['evcal_location_link']) ? esc_attr( $term_meta['evcal_location_link'] ) : ''; ?>" placeholder='http://'>
						<p><span class='yesno_row evo'>
							<?php 	
							$evcal_location_link_target = $this->termmeta($term_meta,'evcal_location_link_target');
							
							EVO()->elements->print_yesno_btn(array(
								'id'=>		'term_meta[evcal_location_link_target]', 
								'var'=>		esc_attr( $evcal_location_link_target ),
								'input'=>	true,
								'label'=>	esc_html__('Open location link in new window','eventon')
							));?>											
						</span></p>

						
						<p class="description"><?php esc_html_e( '(Optional) Location Link','eventon' ); ?></p>
					</td>
				</tr>
				
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[evo_loc_img]"><?php esc_html_e( 'Image', 'eventon' ); ?></label></th>

					<td class='evo_metafield_image'>
						<?php 

						echo EVO()->elements->get_element(array(
							'type'=>'image',
							'id'=>'term_meta[evo_loc_img]',
							'value'=> ( !empty( $term_meta['evo_loc_img'] ) ? $term_meta['evo_loc_img'] : null ),
						));
							
						?>
						<p class="description"><?php _e( '(Optional) Location Image','eventon' ); ?></p>
					</td>
				</tr>
				
			<?php 
				// additional fields
					foreach($this->get_event_tax_fields_array('event_location') as $field=>$value){
						if(in_array($field, array('term_name','description','location', 'evcal_lat','evcal_lon','evcal_location_link','evo_loc_img','submit','location_getdir_latlng','evcal_location_link_target' ))) continue;

						?>
						<tr class="form-field">
							<th scope="row" valign="top"><label for="term_meta[<?php echo esc_attr($field);?>]"><?php echo esc_html( $value['name'] ); ?></label></th>
							<td>
								<input type="text" name="term_meta[<?php echo esc_attr($field);?>]" id="term_meta[<?php echo esc_attr($field);?>]" value="<?php echo !empty($term_meta[$field]) ? esc_attr( $term_meta[$field] ) : ''; ?>">
							</td>
						</tr>
						<?php
					}

				?>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="term_meta[location_type]"><?php esc_html_e( 'Location Type', 'eventon' ); ?></label></th>
					<td>
						<select type="text" name="term_meta[location_type]" id="term_meta[location_type]">
							<option value='place' <?php echo (!empty($term_meta['location_type']) && $term_meta['location_type'] == 'place')? 'selected':'';?>><?php esc_html_e('Physical Location','eventon');?></option>
							<option value='virtual' <?php echo (!empty($term_meta['location_type']) && $term_meta['location_type'] == 'virtual')? 'selected':'';?>><?php esc_html_e('Virtual Location','eventon');?></option>
						</select>						
					</td>
				</tr>

				<?php
			}
				
	// TAXONOMY Event Organizer
		// pre edit form
			function organizer_edit_form_top($tag, $taxonomy){
				if( !isset( $tag->name ) ) return;
				echo esc_html__('Organizer ID') . ': #'. esc_attr( $tag->term_id );
			}
		// remove some columns
			function eventon_evorganizer_theme_columns($theme_columns) {
			    $new_columns = array(
			        'cb' => '<input type="checkbox" />',
			        'id' => esc_html__('ID','eventon'),
			        'name' => esc_html__('Organizer','eventon'),
			        //'names' => esc_html__('Organizer','eventon'),
			        'contact' => esc_html__('Contact Info','eventon'),
			        'posts' => esc_html__('Count','eventon'),
					//      'description' => esc_html__('Description'),
			        'slug' => esc_html__('Slug')
			        );
			    return $new_columns;
			}
		// Add event organizer columns
			function eventon_manage_evorganizer_columns($out, $column_name, $term_id) {
			  	$term_meta = evo_get_term_meta( 'event_organizer', $term_id );
			    switch ($column_name) {
			        case 'contact': 
			        	$address = !empty($term_meta['evcal_org_address'])? 
			        		stripslashes(esc_attr( $term_meta['evcal_org_address'] )): false;
			        	$contact = !empty($term_meta['evcal_org_contact'])? 
			        		stripslashes(esc_attr( $term_meta['evcal_org_contact'] )): false;
			        	$out = "<p>".$contact.$address."</p>";
			        break;
			        case 'id': 
			        	$out = $term_id;
			        break;
			        case 'names':
			        	ob_start();

			        	$out = ob_get_clean();
			        break;			       
			        default:
			            break;
			    }
			    return $out;    
			}
		// add term page
			function eventon_taxonomy_add_new_meta_field_org() {
				// this will add the custom meta field to the add new term page


				// additional fields
					foreach($this->get_event_tax_fields_array('event_organizer') as $field=>$value){

						if( in_array( $field , array('term_name','description', '_evocal_org_exlink_target','submit') ) ) continue;
						
						if($value['type'] == 'image'):
						?>
							<div class="form-field ">
								<div class='evo_tax_img_holder'>
								<?php
									EVO()->elements->get_element(array(
										'_echo'=>true,
										'type'=>'image',
										'id'=> 'term_meta[evo_org_img]',
										'value'=> '',
										'name'=> __( '(Optional) Organizer Image','eventon' ),
									));
									?>
								</div>								
								
							</div>
						<?php else:?>
							<div class="form-field">
								<label for="term_meta[<?php echo esc_attr($field);?>]"><?php echo esc_html($value['name']); ?></label>
								<input type="text" name="term_meta[<?php echo esc_attr($field);?>]" id="term_meta[<?php echo esc_attr($field);?>]" value="">
							</div>
						<?php
						endif;
					}

				do_action('evo_organizer_add_term_fields');?>
				
			<?php
			}
		// Edit term
			function eventon_taxonomy_edit_meta_field_org($term) {	
	 
				// put the term ID into a variable
				$t_id = $term->term_id;
			 
				// retrieve the existing value(s) for this meta field. This returns an array
				$term_meta = evo_get_term_meta( 'event_organizer', $t_id );
				
				
				//  fields
					foreach($this->get_event_tax_fields_array('event_organizer') as $field=>$value){

						if( in_array( $field , array('term_name','description', '_evocal_org_exlink_target','submit') ) ) continue;
						
						if($field == 'evcal_org_exlink'):
						?>
							<tr class="form-field">
							<th scope="row" valign="top"><label for="term_meta[evcal_org_exlink]"><?php esc_html_e( 'Link to custom organizer page', 'eventon' ); ?></label></th>
							<td>
								<input type="text" name="term_meta[evcal_org_exlink]" id="term_meta[evcal_org_exlink]" value="<?php echo !empty($term_meta['evcal_org_exlink'])  ? esc_url( $term_meta['evcal_org_exlink'] ) : ''; ?>">
								<?php 	
									
									EVO()->elements->print_element(array(
										'type'=>'yesno_btn',
										'id'=> 'term_meta[_evocal_org_exlink_target]', 
										'value'=>	esc_attr( $this->termmeta($term_meta,'_evocal_org_exlink_target') ),
										'label'=> esc_html__('Open organizer link in new window','eventon')
									));
									
								?>

								<p class="description"><?php esc_html_e( 'Use this field to link organizer to other user profile pages','eventon' ); ?></p>
							</td>
							</tr>

						<?php continue; endif;

						if( $value['type'] == 'wysiwyg' ):
							//print_r($value);
						?>
							<tr class="form-field">
							<th scope="row" valign="top"><label for="term_meta[evcal_org_exlink]"><?php echo esc_html( $value['name'] ); ?></label></th>
							<td>
								
								<?php
								$value['id'] = 'term_meta['.$value['var'] .']';
								$value['value'] = $this->termmeta($term_meta, $value['var'] );
								
								EVO()->elements->print_element( $value );
								?>

								<p class="description"><?php esc_html_e( 'You can type HTML content into this description field, it will be used as organizer description','eventon' ); ?></p>
							</td>
							</tr>

						<?php continue; endif;?>

						<?php if($value['type'] == 'image'):?>
							<tr class="form-field">
							<th scope="row" valign="top"><label for="term_meta[evo_org_img]"><?php esc_html_e( 'Image', 'eventon' ); ?></label></th>
							<td class=''>
								<div class='evo_tax_img_holder'>
								<?php
									$img_id = !empty($term_meta[ 'evo_org_img' ]) ? $term_meta[ 'evo_org_img' ] : null;						

									EVO()->elements->get_element(array(
										'_echo'=>true,
										'type'=>'image',
										'id'=> "term_meta[evo_org_img]",
										'value'=> $img_id,
										'name'=> '',
									));
									?>
								</div>

								
								<p class="description"><?php _e( '(Optional) Organizer Image','eventon' ); ?></p>
							</td>
							</tr>

						<?php continue; endif;?>

						<tr class="form-field">
							<th scope="row" valign="top"><label for="term_meta[<?php echo esc_attr($field);?>]"><?php echo esc_html( $value['name'] ); ?></label></th>
							<td>
								<input type="text" name="term_meta[<?php echo esc_attr( $field );?>]" id="term_meta[<?php echo esc_attr($field);?>]" value="<?php echo !empty($term_meta[$field]) ? esc_attr( $term_meta[$field] ) : ''; ?>">
							</td>
						</tr>
						<?php
					}

				do_action('evo_organizer_edit_term_fields', $t_id, $term_meta);?>				
			<?php
			}
		
	// Save extra taxonomy fields callback function.
		function evo_save_taxonomy_custom_meta( $term_id , $oo) {
			$postdata = $this->help->sanitize_array( $_POST );

			$san_html_fields = array('description','description2');			

			if ( isset( $postdata['term_meta'] ) ) {
				$t_id = $term_id;

				$taxonomy = $postdata['taxonomy'];
				
				//$term_meta = get_option( "taxonomy_$t_id" );
				$term_meta = array();
				
				$cat_keys = array_keys( $postdata['term_meta'] );
				foreach ( $cat_keys as $key ) {

					if( in_array($key, $san_html_fields)){
						$postdata = $this->help->sanitize_html($_POST);
					}

					if( in_array($key, array('location_lon','location_lat')) ) continue;

					if($key=='location_address'){
						// location lat long override
						if($key == 'location_address' && empty($postdata['term_meta']['location_lon'] )){
							$latlon = eventon_get_latlon_from_address($postdata['term_meta']['location_address']);
						}
						// longitude
						$term_meta['location_lon'] = (!empty($postdata['term_meta']['location_lon']))?
							$postdata['term_meta']['location_lon']:
							(!empty($latlon['lng'])? floatval($latlon['lng']): null);

						// latitude
						$term_meta['location_lat'] = (!empty($postdata['term_meta']['location_lat']))?
							$postdata['term_meta']['location_lat']:
							(!empty($latlon['lat'])? floatval($latlon['lat']): null);						
					}

					$term_meta[$key] = (isset($postdata['term_meta'][$key]))?
						$postdata['term_meta'][$key]:null;

				}

				

				// Save the option array.
				// /update_option( "taxonomy_$t_id", $term_meta );
				evo_save_term_metas($taxonomy, $t_id, $term_meta);
			}
		}  

	// Supporting functions
		function termmeta($term_meta, $var){
			return !empty( $term_meta[$var] ) ? 
				stripslashes(str_replace('"', "'", (esc_attr( $term_meta[$var] )) )) : 
				null;
		}
}
	
?>