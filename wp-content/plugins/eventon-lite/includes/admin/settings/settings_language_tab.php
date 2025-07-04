<?php
/**
 * Language Settings 
 *
 * @version		2.4
 * @package		EventON/settings
 * @category	Settings
 * @author 		AJDE
 */

class evo_settings_lang extends EVO_Lang_Settings{

	public $evcal_opt, $evopt, $lang_version, $lang_options, $lang_variations, $uri_parts;

	private $eventon_months = array(1=>'january','february','march','april','may','june','july','august','september','october','november','december');
		
	private $eventon_days = array(1=>'monday','tuesday','wednesday','thursday','friday','saturday','sunday');

	public function __construct($evcal_opt)	{
		$this->evcal_opt = $evcal_opt;
		$this->evopt = get_option('evcal_options_evcal_1');
		$this->lang_version = (!empty($_REQUEST['lang']))? sanitize_text_field($_REQUEST['lang']): 'L1';
		
		$evo_opt_lang = get_option('evcal_options_evcal_2');
		$this->lang_options = (!empty($evo_opt_lang[$this->lang_version]))? $evo_opt_lang[$this->lang_version]:null;

		$this->lang_variations = apply_filters('eventon_lang_variation', array('L1','L2', 'L3'));
		$this->uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	}

	// return content for 
	public function get_content(){
		ob_start(); ?>
		<form class='evo_settings_form' method="post" action=""><?php settings_fields('evcal_field_group'); 
			wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' ); ?>
			<div id="evcal_2" class="postbox evcal_admin_meta curve">	
				<div class="inside">
					<h2 class='evoff_1'><i class='fa fa-language evomarr10'></i> <?php echo ucwords(__('Type in custom language text for front-end calendar','eventon'));?></h2>

					<div class='evodfx evomart10 evo_bordert evo_borderb evomarb20 evopadb10 evopadt10'>
					<?php 

						// process lang select option values
						$_lang_options = array();
						foreach($this->lang_variations as $ll){
							$_lang_options[ $ll] = $ll;
						}

						EVO()->elements->get_element( array(
							'_echo'=> true,
							'type'=> 'select',
							'row_class'=>'evo_lang_select_l',
							'field_class'=> 'evo_lang_selection',
							'options'=> $_lang_options,
							'value'=> $this->lang_version,
							'name'=> __('Select your language','eventon'),
							'tooltip'=> __("You can use this to save different languages for customized text for calendar. Once saved use the shortcode to show calendar text in that customized language. eg. [add_eventon lang=L2]",'eventon'),
							'field_attr'=> array(
								'data-url'=> esc_url( get_admin_url('','admin.php') )
							),
							'id'=>'evo_current_lang'
						));

						?>
						<div class='evo_lang_search'>
							<p><input type='search' class='evo_lang_search_in' placeholder='<?php _e('Search by text string.','eventon');?>'/></p>
						</div>	
					</div>

					
					<div class='evodfx'>
						<div class='evolang_translatable_strings_list evomarr15'>

						<?php
							echo $this->interpret_array( apply_filters('eventon_settings_lang_tab_content',$this->language_variables_array()) );
						?>

						</div>

						<div class='evolang_translate_section'>
							<div class='evopad20 evobr20' style='background-color: var(--evo_color_second);'>
								<div class='evolang_translate_original_box evomarb10 evopadb10'>
									<p class=''><?php _e('Original Text','eventon');?></p>
									<p class='evolang_translate_original evofsi'>-</p>
								</div>
								<div class=''>
									<?php 
									EVO()->elements->get_element( array(
										'_echo'=> true,
										'type'=> 'textarea',
										'row_class'=>'evo_translated_text',
										'field_class'=>'evopad10 evolang_translatable_textfield',
										'value'=> '',
										'name'=> __('Translated Text','eventon'),
										'tooltip'=> __('Select a text string from list and type in the translated version of that text here','eventon'),							
										'id'=>'evo_translated_text',
										'default'=> __('Select a text string from list and type in the translated version of that text here','eventon'),	
										'height'=>'200px;',	
										'field_attr'=> array(
											'data-t0'=> __('Select a text string from list and type in the translated version of that text here','eventon'),
											'data-t1'=> __('No translations available for this text','eventon')
										)
									));
									?>
								</div>
							</div>

							<div class='evomart10 evomarb20 evopad20'>
								<p><i><?php _e('Please use the above field to type in custom language text that will be used to replace the original language text on the front-end of the calendar.','eventon')?><br/><?php _e('NOTE: When editing duplicate text strings, all the other matching text strings will also change to new text string.','eventon');?></i></p>
							</div>
						</div>
					</div>

				</div>
			</div>
			
			<p style='padding:0'><input type="submit" class="evo_admin_btn btn_prime evo_settings_save_btn" value="<?php _e('Save Changes','eventon') ?>" style='margin-top:15px'/></p>

		</form>
		
		<?php
			/**
			 * Language Import and Exporting
			 * @version 0.1
			 * @added 	2.3.2
			 */
		?>
		<div class="evo_lang_export" style='padding-top:10px; margin-top:30px; '>
			<h3><?php esc_html_e('Export & Import Translations','eventon');?></h3>
			<p><i><?php esc_html_e('NOTE: Make sure to save changes after importing. This will import/export the current selected language ONLY. Export using vars will associate text strings with field variable names whereas using text will associate them with text string for the field.','eventon');?></i></p>

			
			<div class='evo_data_upload_holder' style="position: relative;">
				<?php 
				EVO()->elements->print_import_box_html(array(
					'box_id'=>'evo_language_upload',
					'title'=>__('Upload CSV Lanague File Form'),
					'message'=>__('NOTE: You can only upload language data as .csv file'),
					'file_type'=>'.csv',
				));
				?>	

				<a id='evo_lang_import' data-t='var' class='evo_data_upload_trigger evo_admin_btn btn_prime'><?php esc_html_e('Import','eventon');?></a> 
				<a id='evo_lang_export' data-t='var' class='evo_lang_export_btn evo_admin_btn btn_prime'><?php esc_html_e('Export Using Vars','eventon');?></a>
				<a id='evo_lang_export_txt' data-t='txt' class='evo_lang_export_btn evo_admin_btn btn_prime'><?php esc_html_e('Export Using Text','eventon');?></a>
			</div>
		</div>

		

		<?php echo ob_get_clean();
	}

		function _section_lang_selection(){	
			?>
			<h4><?php esc_html_e('Select your language','eventon');?> <select id='evo_lang_selection' data-url='<?php echo esc_url( get_admin_url('','admin.php') );?>'>		
			<?php
				foreach($this->lang_variations as $lang){
					echo "<option value='". esc_attr( $lang )."' ".(($this->lang_version==$lang)? 'selected="select"':null).">". esc_attr( $lang )."</option>";
				}
			?></select>
			<?php EVO()->elements->tooltips(__("You can use this to save different languages for customized text for calendar. Once saved use the shortcode to show calendar text in that customized language. eg. [add_eventon lang='L2']",'eventon'),'',true);?></h4>

			<?php 
		}

		
		// Language section fields
		function language_variables_array(){
			$output =  array(
				array('type'=>'togheader','name'=>__('Months and Dates','eventon')),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_months()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_3letter_months()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_1letter_months()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_day_names()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_3leter_day_names()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_1leter_day_names()),
					array('type'=>'multibox_open', 'items'=>$this->_array_part_ampm()),
				array('type'=>'togend'),
				array('type'=>'togheader','name'=>__('General Calendar','eventon')),

					array('label'=>'Month','var'=>1),
					array('label'=>'Months','var'=>1),
					array('label'=>'Day','var'=>1),
					array('label'=>'Days','var'=>1),
					array('label'=>'Hour','var'=>1),
					array('label'=>'Hours','var'=>1),
					array('label'=>'Minute','var'=>1),
					array('label'=>'Minutes','var'=>1),
					array('label'=>'Duration','var'=>1),

					array('label'=>'No Events','name'=>'evcal_lang_noeve',),
					array('label'=>'No Events at this time','var'=>1),
					array('label'=>'All Day','name'=>'evcal_lang_allday'),
					array('label'=>'Year Around Event','name'=>'evcal_lang_yrrnd'),
					array('label'=>'Month Long Event','name'=>'evcal_lang_mntlng'),
					array('label'=>'Events','name'=>'evcal_lang_events',),
					array('label'=>'Event','var'=>1),
					array('label'=>'Show More Events','name'=>'evcal_lang_sme',),								
					array('label'=>'Event Tags','name'=>'evo_lang_eventtags',),
					array('label'=>'YES','name'=>'evo_lang_yes',),
					array('label'=>'NO','name'=>'evo_lang_no',),
					array('label'=>'MORE','name'=>'evo_lang_more'),
					array('label'=>'Featured','var'=>1),
					array('label'=>'Cancelled','var'=>1),	
					array('label'=>'Scheduled','var'=>1),	
					array('label'=>'Moved Online','var'=>1),	
					array('label'=>'Postponed','var'=>1),	
					array('label'=>'Rescheduled','var'=>1),	
					array('label'=>'Preliminary','var'=>1),	
					array('label'=>'Tentative','var'=>1),	
					array('label'=>'Repeating Event','var'=>1),	
					array('label'=>'Virtual Event','var'=>1),	
					array('label'=>'Virtual/ Physical Event','var'=>1),	
					array('label'=>'Virtual Event Details','var'=>1),	
					array('label'=>'Live Now','var'=>1),						
					array('label'=>'Join the Event Now','var'=>1),	
					array('label'=>'Password','var'=>1),	
					array('label'=>'Other Access Information','var'=>1),	
					array('label'=>'Event has already taken place','var'=>1),	
					array('label'=>'Event has already started and the access to the event is closed','var'=>1),	
					array('label'=>'Event access information coming soon, Please check back again closer to event start time','var'=>1),		
					
					array('label'=>'Search Events','name'=>'evoSR_001','legend'=>'placeholder for search input fields'),
					array('label'=>'Search Calendar Events','name'=>'evoSR_001a'),
					array('label'=>'Searching','name'=>'evoSR_002'),
					array('label'=>'What do you want to search for?','name'=>'evoSR_003'),
					array('label'=>'Event(s) found','name'=>'evoSR_004'),
					array('label'=>'Download all events as ICS file','var'=>'1'),
					array('label'=>'View in my time','var'=>'1'),
					array('label'=>'Guests','var'=>'1'),
					array('label'=>'Signed in','var'=>'1'),
					array('label'=>'Sign-in','var'=>'1'),
					array('label'=>'Log-in','var'=>'1'),
					array('label'=>'You have left the jitsi meet. Refresh the page to access jitsi meet again.','var'=>'1'),
					array('label'=>'You are the moderator of this event. Access the live stream','var'=>'1'),
					array('label'=>'You are the moderator of this event. Please sign-in to allow viewers to join to this virtual event','var'=>'1'),
					array('label'=>'Waiting for the moderator to join..','var'=>'1'),
					array('label'=>'Join the live video now','var'=>'1'),
					array('label'=>'Join the live stream','var'=>'1'),
					array('label'=>'Event starting shortly..','var'=>'1'),
					
					array('label'=>'The Event Calendar','var'=>'1'),
					array('label'=>'Collection of Events','var'=>'1'),
				array('type'=>'togend'),

				array('type'=>'section',
					'id'=>'healthcare_guidelines',
					'name'=> __('Health Guidelines','eventon'),
					'fields'=> apply_filters('evo_lang_values_healthcare_guidelines', array(
						array('label'=>'Health Guidelines for this Event','var'=>1),	
						array('label'=>'Masks Required','var'=>1),	
						array('label'=>'Temperature Checked At Entrance','var'=>1),
						array('label'=>'Physical Distance Maintained','var'=>1),		
						array('label'=>'Event Area Sanitized','var'=>1),		
						array('label'=>'Outdoor Event','var'=>1),		
						array('label'=>'Vaccination Required','var'=>1),		
						array('label'=>'Other Health Guidelines','var'=>1),	
					))
				),

				array('type'=>'togheader','name'=>__('Now Calendar','eventon')),
					array('label'=>'Events Happening Now','var'=>1),	
					array('label'=>'Coming up Next in','var'=>1),
					array('label'=>'Time Left','var'=>1),		
					array('label'=>'Event Completed','var'=>1),			
				array('type'=>'togend'),
				
				array('type'=>'togheader','name'=>__('Schedule View','eventon')),
					array('label'=>'Schedule','var'=>1),	
					array('label'=>'Until','var'=>1),	
					array('label'=>'From','var'=>1),	
				array('type'=>'togend'),
			);

			$output = array_merge($output, $this->_array_part_taxonomies());
			$output = array_merge($output, array(
				
				array('type'=>'togheader','name'=>__('Calendar Header','eventon')),
					array(
						'label'=>'Jump Months','name'=>'evcal_lang_jumpmonths',
					),array(
						'label'=>'Jump Months: Month','name'=>'evcal_lang_jumpmonthsM',
					),array(
						'label'=>'Jump Months: Year','name'=>'evcal_lang_jumpmonthsY',
					),array(
						'label'=>'Filter Events','name'=>'evcal_lang_sopt',
					)
					,array(
						'label'=>'Sort By','name'=>'evcal_lang_sort',
					),array(
						'label'=>'Date','name'=>'evcal_lang_sdate',
					),array(
						'label'=>'Posted','name'=>'evcal_lang_sposted',
					),array(
						'label'=>'Title','name'=>'evcal_lang_stitle',
					),array(
						'label'=>__('All','eventon'),'name'=>'evcal_lang_all',
						'placeholder'=>'Sort options all text'
					),array(
						'label'=>__('Current Month','eventon'),'name'=>'evcal_lang_gototoday',
					),
					array('label'=>'Apply Filters','name'=>'evcal_lang_apply_filters'),
					array('label'=>'Past and Future Events','var'=>1),
					array('label'=>'Only Past Events','var'=>1),
					array('label'=>'Only Future Events','var'=>1),
					array('label'=>'Virtual Events','var'=>1),
					array('label'=>'Non Virtual Events','var'=>1),

					array('type'=>'togend'),
				array('type'=>'togheader','name'=>__('Event Card','eventon')),
					array(
						'label'=>__('Location Name','eventon'),'name'=>'evcal_lang_location_name',
					)
					,array('label'=>__('Location','eventon'),'name'=>'evcal_lang_location')
					,array('label'=>__('Event Location','eventon'),'name'=>'evcal_evcard_loc')
					,array(
						'label'=>'Type your address to get directions','name'=>'evcalL_getdir_placeholder',
						'legend'=>'Get directions section'
					),array(
						'label'=>'Click here to get directions',
						'name'=>'evcalL_getdir_title',
						'legend'=>'Get directions section'
					),
					array('label'=>'Time','name'=>'evcal_lang_time'),
					array('label'=>'Future Event Times in this Repeating Event Series','name'=>'evcal_lang_repeats'),
					array('label'=>'Color','name'=>'evcal_lang_scolor',),
					array('label'=>'At (event location)','name'=>'evcal_lang_at',),
					array('label'=>'Event Details','name'=>'evcal_evcard_details',),
					array('label'=>'Organizer','name'=>'evcal_evcard_org',),
					//array('label'=>'Event Organizer','name'=>'evcal_lang_evorg',),
					array(
						'label'=>'Close event button text',
						'name'=>'evcal_lang_close',
					),array(
						'label'=>'More',
						'name'=>'evcal_lang_more',
						'legend'=>'More/less text for long event description'
					),array(
						'label'=>'Less',
						'name'=>'evcal_lang_less',
						'legend'=>'More/less text for long event description'
					),array(
						'label'=>'Buy ticket via Paypal',
						'name'=>'evcal_evcard_tix1',
						'legend'=>'for Paypal'
					),array(
						'label'=>'Buy Now button text',
						'name'=>'evcal_evcard_btn1',
						'legend'=>'for Paypal'
					),array(
						'label'=>'Ticket for the event',
						'name'=>'evcal_evcard_tix2',
						'legend'=>'for eventbrite'
					),array(
						'label'=>'Buy now button',
						'name'=>'evcal_evcard_btn2',
						'legend'=>'for eventbrite'
					),array(
						'label'=>'Event Capacity',
						'name'=>'evcal_evcard_cap',
					),array(
						'label'=>'Learn More about this event',
						'name'=>'evcal_evcard_learnmore',
						'legend'=>'for meetup'
					),array(
						'label'=>'Learn More link text',
						'name'=>'evcal_evcard_learnmore2',
						'legend'=>'for event learn more text'
					),
					array('label'=>'Related Events','var'=>1),
					array('label'=>'Learn More','var'=>1),
					array('label'=>'Login required to see the information','var'=>'1',),
					array('label'=>'Share on facebook','var'=>1),
					array('label'=>'Share on twitter','var'=>1),
					array('label'=>'Share on Linkedin','var'=>1),
					array('label'=>'Share on Pinterest','var'=>1),
					array('label'=>'Share on Whatsapp','var'=>1),
					array('label'=>'Share on Reddit','var'=>1),
					array('type'=>'subheader','label'=>__('Add to calendar Section','eventon')),
						array(
							'label'=>'Calendar','name'=>'evcal_evcard_calncal',			
						),array(
							'label'=>'GoogleCal','name'=>'evcal_evcard_calgcal',			
						),array(
							'label'=>'Add to your calendar',
							'name'=>'evcal_evcard_addics',
							'legend'=>'Hover over text for add to calendar button'
						),array(
							'label'=>'Add to google calendar',
							'name'=>'evcal_evcard_addgcal',
							'legend'=>'Hover over text for add to google calendar button'
						),
						array('label'=>'Event Name','var'=>1),
						array('label'=>'Event Date','var'=>1),
						array('label'=>'Link','var'=>1),
					array('type'=>'togend'),
						array('type'=>'subheader','label'=>__('Custom Meta Fields (if activated)','eventon')),
						array('type'=>'multibox_open', 'items'=>$this->_array_part_custom_meta_field_names()),
					array('type'=>'togend'),
					
				array('type'=>'togend'),

			)); 
		
			// single events
			$singleEvents = array(
				array('type'=>'togheader','name'=>'Single Events'),
				array('label'=>'Login','var'=>'1',),
				array('label'=>'You must login to see this event','var'=>'1'),
				array('label'=>'This is a repeating event','var'=>'1'),
				array('type'=>'togend'),
			);

			$output = array_merge($output, $singleEvents);
			
			return $output;
		}
			function _array_part_months(){
				$output = array();
				for($x=1; $x<13; $x++){
					$output['evcal_lang_'.$x] = $this->eventon_months[$x];
				}
				return $output;
			}
			function _array_part_3letter_months(){
				$output = array();
				for($x=1; $x<13; $x++){
					$output['evo_lang_3Lm_'.$x] = substr($this->eventon_months[$x],0,3);
				}
				return $output;
			}
			function _array_part_1letter_months(){
				$output = array();
				for($x=1; $x<13; $x++){
					$output['evo_lang_1Lm_'.$x] = substr($this->eventon_months[$x],0,1);					
				}
				return $output;
			}
			function _array_part_day_names(){
				$output = array();
				for($x=1; $x<8; $x++){
					$output['evcal_lang_day'.$x] = $this->eventon_days[$x];					
				}
				return $output;
			}
			function _array_part_3leter_day_names(){
				$output = array();
				for($x=1; $x<8; $x++){
					$output['evo_lang_3Ld_'.$x] = substr($this->eventon_days[$x],0,3);					
				}
				return $output;
			}
			function _array_part_1leter_day_names(){
				$output = array();
				for($x=1; $x<8; $x++){
					$output['evo_lang_1Ld_'.$x] = substr($this->eventon_days[$x],0,1);					
				}
				return $output;
			}
			function _array_part_ampm(){
				$output = array();
				$output['evo_lang_am'] = 'am';
				$output['evo_lang_pm'] = 'pm';
				$output['evo_lang_am2'] = 'AM';
				$output['evo_lang_pm2'] = 'PM';
				return $output;
			}
			function _array_part_taxonomies(){
				$output =  array();
				
				$event_type_names = evo_get_ettNames($this->evopt);
				$ett_verify = evo_get_ett_count($this->evopt);

				$output[] =array('type'=>'togheader','name'=>'Event Type Categories');

				foreach( eventon_get_valid_ett() as $key => $val){

					$default = $event_type_names[$key];
					$output[] = array('label'=>$default, 'name'=>'evcal_lang_et'.$key);

					// each term of taxonomy
					$ab = $key==1?'':'_'.$key;
					
					$taxonomy = 'event_type' . $ab;
					$terms = get_terms(array(
					    'taxonomy'   => $taxonomy,
					    'hide_empty' => false,
					));					

					$termitem = array();
					if(!empty($terms)){
						foreach($terms as $term){
							$var = 'evolang_'.'event_type'.$ab.'_'.$term->term_id;
							$termitem[$var] = (!empty($this->lang_options[$var]))?  $this->lang_options[$var]: $term->name;
						}
					}
					if(!empty($termitem)){
						$output[] = array('type'=>'multibox_open', 'items'=>$termitem);
					}
				}

				$output[] = array('label'=>'Event Location', 'name'=>'evcal_lang_evloc');
				$output[] = array('label'=>'Events at this location', 'var'=>'1');
				$output[] = array('label'=>'Event Organizer', 'name'=>'evcal_lang_evorg');
				$output[] = array('label'=>'Events by this organizer', 'var'=>'1');
				$output[] = array('label'=>'Event Tag', 'var'=>'1');

				$output[] = array('type'=>'togend');
				return $output;
			}

			
			function _array_part_custom_meta_field_names(){
				$output = array();
				$cmd_verify = evo_retrieve_cmd_count($this->evopt);

				for($x=1; $x<($cmd_verify+1); $x++){
					$default = $this->evopt['evcal_ec_f'. esc_attr( $x ) .'a1'];
					$output['evcal_cmd_'. esc_attr( $x ) ] = array('default'=>((!empty($this->lang_options['evcal_cmd_'.$x]))?  $this->lang_options['evcal_cmd_'. esc_attr( $x ) ]: ''), 'placeholder'=>$default);
				}
				return $output;
			}

}

?>