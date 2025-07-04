<?php
/**
 * EventON General Calendar Elements
 * @version 2.4
 */

class EVO_General_Elements extends EVO_Elm_Trigs{	

	public $svg;

	public function __construct(){
		include_once 'class-elements-svg.php';
		$this->svg = new EVO_Elements_SVG();
	}

// standard form elements
	public function print_element( $A){
		echo $this->get_element( $A);
	}
	function get_element($A){ 
		$A = array_merge( array(
			'id'=>'',
			'id2'=> '', // @4.9 secondary id
			'index'=>'',// referance index
			'name'=>'',	
			'label'=>'',		
			'hideable'=> false,
			'value'=>'','default'=>'','values'=> array(),'values_array'=> array(),
			'placeholder'=>'',// @4.9
			'value_2'=>'',
			'max'=>'','min'=>'','step'=>'','readonly'=>false, 
			'maxlength'=> '', // 4.9.2
			'TD'=>'eventon', // text domain
			'legend'=>'','tooltip'=>'',
			'tooltip_position'=>'',
			'description'=>'',
			'options'=> false, 'select_multi_options'=> false,
			'type'=>'', 'field_type'=>'text','field_attr'=>array(),'field_class'=> '',
			'reverse_field' => false,
			'afterstatement'=>'',
			'row_class'=>'', 'select_option_class'=>'','unqiue_class'=>'','class_2'=>'',
			'inputAttr'=>'','attr'=>'',
			'nesting_start'=> '', 'nesting_end'=> false, // pass nesting class name
			'row_style'=> '',// pass styles 
			'content'=> '', 'field_after_content'=>'', 'field_before_content'=>'',
			'support_input'=>false,
			'close'=>false,
			'max_images'=>'2', // 4.6
			'interactive'=> true, // 4.6
			'link'=>'',// 4.7
			'_blank'=>false,//4.7
			'trig_data'=> false,//4.7
			'trig_type'=>'', //4.7
			'_echo'=> false,//4.7
			'conditional_subfields'=> null, // @4.9.2

		), $A);
		extract($A);

		$help = new evo_helper();

		// prelim
			// reuses
				$legend_code = !empty($tooltip) ? $this->tooltips($tooltip, $tooltip_position, false): null;
				if(!empty($field_attr) && count($field_attr)>0){
					$field_attr = array_map(function($v,$k){
						return $k .'="'. $v .'"';
					}, array_values($field_attr), array_keys($field_attr));
					
				}
				$field_attr = !empty($field_attr) ? implode(' ', $field_attr) : null;
				$help = new evo_helper();

			// validation
				if(empty($type)) return false;


			// nesting
				$_nesting_start = $_nesting_end = '';
				if(!empty($nesting_start)) $_nesting_start = "<div class='evo_nesting {$nesting_start}'>";
				if( $nesting_end ) $_nesting_end = "</div>";
			
		ob_start();

		if( !empty( $_nesting_start ) ) echo wp_kses_post( $_nesting_start );

		switch($type){
			// notices
			case 'notice':
				echo "<p class='evo_elm_row evo_elm_notice ". esc_attr( $row_class )."' style='" . esc_attr( $row_style )."'>". esc_attr( $name ) ."</p>";
			break;
			case 'static_field': // @since 4.7
			case 'static':
				echo "<p class='evo_elm_row evo_elm_notice {$row_class}' style='{$row_style}'>". $name . "<code class='evomarl10'>". $value . '</code>'. $legend_code ."</p>";
			break;
			case 'section_header':
				echo "<div class='evo_elm_row evopadb10 evopadt10'><p class='evo_elm_header {$row_class} evofz18i evopad0i evomar0i' style='{$row_style}'>". $name .$legend_code ."</p></div>";
			break;
			// custom code field
			case 'custom_code':
			case 'code':
				echo $content;
			break;

			// hidden input field
			case 'hidden':
				$name = (!empty($name)) ? $name : $id;
				echo "<input type='hidden' name='". esc_attr( $name )."' value='". esc_attr( $value ) ."'/>";
			break;

			// GENERAL Text field
			case 'text':
			case 'input':
				echo "<div class='evo_elm_row evoelm_text {$id} {$row_class}' style='{$row_style}'>";
				
				// Placeholder content
			    $placeholder = (!empty($placeholder) || !empty($default)) 
			        ? "placeholder='" . (!empty($placeholder) ? $placeholder : $default) . "'" 
			        : "";


				$show_val = $hideable && !empty($value);
			    $hideable_text = $show_val 
			        ? "<span class='evo_hideable_show' data-t='". __('Hide', $TD) ."'>". __('Show', $TD). "</span>" 
			        : "";
				
				echo "<p class='evo_field_label'>{$name}{$legend_code}{$hideable_text}</p>";
    			echo "<p class='evo_field_container evoposr'>";

    			//$input_value = !empty($value) ? htmlspecialchars($value, ENT_QUOTES) : '';
    			$input_value = !empty($value) ? htmlspecialchars(trim($value, " \t\n\r\0\x0B\xC2\xA0"), ENT_QUOTES) : '';
    			$input_value = html_entity_decode( $input_value );

				if ($show_val && $hideable) {
			        echo "<input class='{$field_class}' type='password' name='{$id}' value='{$input_value}'";
			    } else {
			        echo "<input class='{$field_class}' type='{$field_type}' name='{$id}' "
			            . (!empty($max) ? "max='{$max}' " : "")
			            . (!empty($min) ? "min='{$min}' " : "")
			            . (!empty($step) ? "step='{$step}' " : "")
			            . (!empty($maxlength) ? "maxlength='{$maxlength}' " : "")
			            . ($readonly ? " readonly='true'" : "")
			            . " value='{$input_value}'";
			    }
			    echo " {$placeholder}/>";

				// Character count display when maxlength is set
			    if (!empty($maxlength)) {
			        $current_length = strlen($input_value);
			        echo "<span class='evolm_char_count evoposa evor0 evomart10 evomarr10 evoop7' style='font-size:12px; margin-left:5px;'>{$current_length}/{$maxlength}</span>";
			        echo "<script>
			            document.querySelector('input[name=\"{$id}\"]').addEventListener('input', function(e) {
			                document.querySelector('.evolm_char_count').textContent = e.target.value.length + '/{$maxlength}';
			            });
			        </script>";
			    }

				if(!empty($description)) echo "<em>". $description ."</em>";

				echo "</p></div>";
			break;

			// image
			case 'image':
				$image_id = !empty($value) ? $value: false;

				// image source array
				$img_src = ($image_id)? 	wp_get_attachment_image_src($image_id,'medium'): null;
					$img_src = (!empty($img_src))? $img_src[0]: null;

				$__button_text = ($image_id)? __('Remove Image','eventon'): __('Choose Image','eventon');
				$__button_text_not = ($image_id)? __('Remove Image','eventon'): __('Choose Image','eventon');
				$__button_class = ($image_id)? 'removeimg':'chooseimg';
				?>
				<div class='evo_elm_row evo_metafield_image <?php echo !empty($image_id)?'has_img':'';?> <?php echo $id.' '.$row_class;?>'>
					<p class='evo_field_label'><?php echo $name.$legend_code; ?></p>
					
					<input class='evo_meta_img field <?php echo $id;?> custom_upload_image' name="<?php echo $id;?>" type="hidden" value="<?php echo ($image_id)? $image_id: null;?>" /> 
            		
            		<span class='image_src evoposr evo_hover_op7 evomart5 evobr10'>
            			<span class='evolm_img_actions evoposa evodfx evofx_jc_c evofx_ai_c evoh100p evow100p'>
            				<button class='evolm_img_select_trig evoposa evoboxsn evobgclt evocurp evobrn evow100p evoh100p evoff_2'><?php _e('Select an Image','eventon');?></button>
            				<i class='evoel_img_remove_trig fa fa-times evofx_jc_c evofx_ai_c evobgclw evopad10 evobr50p evofz18 evocurp evo_trans_sc1_07 evo_transit_all'></i>
            			</span>
            			
            			<span class='evoelm_img_holder evobr10 evoh100p evow100p evodb evobgpc evobgsc' style='background-image: url(<?php echo $img_src;?>);'></span>
            		</span>
            		
            	</div>
				<?php
			break;

			

			// color picker field
			case 'colorpicker':

				$vis_input_field = !empty($support_input) && $support_input ? true: false;

				echo "<div class='evo_elm_row {$id} {$row_class}' style='{$row_style}'>";

				echo"<p class='evo_field_label'>".$name.$legend_code. "</p>";
				echo "<p class='evo_field_container ". ( $vis_input_field? 'visi':'') ."'>";
				echo "<em class='evo_elm_color' style='background-color:#{$value}'></em>";

				if($vis_input_field ):
					echo "<input class='evo_elm_hex' type='text' name='{$id}' value='{$value}'/>";
				else:
					echo "<input class='evo_elm_hex' type='hidden' name='{$id}' value='{$value}'/>";
				endif;
				
				//echo "<input class='evo_elm_rgb' type='hidden' name='{$rgb_field_name}' value='{$rgb_num}'/>";

				echo "</p></div>";
			break;

			// bigger color picker @4.5
			case 'colorpicker_2':

				$clean_hex = str_replace('#', '', $value);
				$fcl = !eventon_is_hex_dark( $value ) ? 'ffffff':'000000';

				echo "<div class='evo_elm_row evo_color_selector {$index}' id='{$id}' >
					<p class='evselectedColor evo_set_color' style='background-color:{$value}; color: #{$fcl}'>
						<span class='evcal_color_hex evcal_chex'  >{$value}</span>
						<span class='evo_mb_color_caption'>{$label}</span>
					</p>
					<input class='evo_color_hex' type='hidden' name='evcal_event_color{$index}' value='{$clean_hex}'/>
					<input class='evo_color_n' type='hidden' name='evcal_event_color_n{$index}' value='{$value_2}'/>
				</div>";
			break;

			case 'plusminus':

				echo "<div class='evo_elm_row evoelm_plusminus {$id} {$row_class}' style='{$row_style}'>";

				echo $field_before_content;

				$value = empty($value) ? ($default ?? null) : $value;

				echo"<p class='evo_field_label'>".$name.$legend_code. "</p><p class='evo_field_container evo_field_plusminus_container'>";
				?>
					<span class="evo_plusminus_adjuster">
						<b class="min evo_plusminus_change <?php echo $unqiue_class;?>">-</b>
						<input class='evo_plusminus_change_input <?php echo $class_2.' '. $field_class;?>' type='text' name='<?php echo $id;?>' value='<?php echo $value;?>' data-max='<?php echo $max;?>'/>
						<b class="plu evo_plusminus_change <?php echo $unqiue_class;?> <?php echo (!empty($max) && $max==1 )? 'reached':'';?>">+</b>						
					</span>
				<?php

				echo "</p>";

				echo $field_after_content;

				echo "</div>";

			break;

			// textarea
			case 'textarea':

				$__value = empty( $value ) ? null : wp_kses_post( $value );
				$placeholder = (!empty($default) )? 'placeholder="'. esc_attr( $default ).'"':null;		
				
				echo "<div class='evo_elm_row ". esc_attr( $id )."' style='". esc_attr( $row_style )."'>";
				echo"<p class='evo_field_label'>". esc_html( $name ) .$legend_code . "</p><p class='evo_field_container'>";

				$height = !empty($height)? "height:". esc_attr( $height ):'';
				echo "<textarea class='". esc_attr( $field_class )."' name='". esc_attr( $id )."' style='width:100%; ". esc_attr( $height )."' ". $placeholder .">". $__value ."</textarea>";

				echo "</p></div>";

			break;
			// wysiwyg @2.3
			case 'wysiwyg':

				$action = empty($value)? "<span class='evo_elm_act_on ".(empty($legend_code) ? '':'le')." evo_transit_all evobr5 evodib'><i class='fa fa-align-left evomarr5 evoop5'></i> ".__('Add Content','eventon') ."<i class='fa fa-plus evomarl10 evoop7'></i></span>":'';

				echo "<div class='evo_elm_row trumbowyg {$id} closed {$row_class}' style='{$row_style}'>";
				echo"<p class='evo_field_label'>".$name.$legend_code ."</p>";

				echo $action;

				echo "<p class='evo_field_container' style='display:none'>";
				echo "<textarea class='evoelm_trumbowyg' name='{$id}' style='width:100%; min-height:300px;'>{$value}</textarea>";
				echo "</p>";

				if( !empty($value) ) echo "<div class='evo_field_preview evomarb10 evoop7' style=''>{$value}</div>";

				echo "</div>";

			break;
			
			// Select in a lightbox -- for taxonomy values
			case 'lightbox_select_vals':

				echo "<div class='evo_elm_row evo_elm_lb_select {$row_class}' style='{$row_style}'>";
				// get values to show
					$values = !empty($value)? explode(',', $value): array();

					if(count($values_array) == 0){
						$values_array = array();
						if(!empty($taxonomy)){
							$t = get_terms( array('taxonomy'=> $taxonomy,'hide_empty'=>false));
							if(!empty($t) && !is_wp_error($t)){
								foreach($t as $term){
									$values_array[ $term->term_id ] = $term->name;
								}
							}
						}
					}

				$DATA = '';
				if(count($values_array)>0):
					$data = array(
						'd'=> $values_array,
						'v'=> $values
					);

					$DATA = $help->array_to_html_data( $data );
					
				endif;

				

				$placeholder = (!empty($default) )? 'placeholder="'.$default.'"':null;	

				echo "<div class='evo_elm_lb_fields'  ". $DATA .">";
					if(!$reverse_field) echo"<p class='evo_field_label'>".$name.$legend_code . "</p>";					
					echo "<p class='evo_field_container evo_elm_lb_field'>";
					echo "<input class='evo_elm_lb_field_input {$field_class}' type='{$field_type}' {$field_attr} name='{$id}' {$placeholder} " . 'value="'. $value .'"/>';
					echo "</p>";
					if($reverse_field) echo"<p class='evo_field_label'>".$name.$legend_code . "</p>";				
				echo "</div>";
				echo "</div>";
			break;

			// Select in a lightbox -- for other general values
			case 'lightbox_select_cus_vals':

				echo "<div class='evo_elm_row evo_elm_lb_select {$row_class}' style='{$row_style}'>";
								
				$DATA = '';
				if( is_array($options) && count($options)>0):
					$data = array(
						'd'=> $options,
						'v'=> $values
					);

					$DATA = $help->array_to_html_data( $data );
					
				endif;

				$placeholder = (!empty($default) )? 'placeholder="'.$default.'"':null;	

				echo "<div class='evo_elm_lb_fields' ". $DATA .">";
					if(!$reverse_field) echo"<p class='evo_field_label'>".$name.$legend_code . "</p>";					
					echo "<p class='evo_field_container evo_elm_lb_field'>";
					echo "<input class='evo_elm_lb_field_input {$field_class}' type='{$field_type}' {$field_attr} name='{$id}' {$placeholder} " . 'value="'. $value .'"/>';
					echo "</p>";
					if($reverse_field) echo"<p class='evo_field_label'>".$name.$legend_code . "</p>";				
				echo "</div>";
				echo "</div>";
			break;

			// select row 
			case 'select_row':
					
				// legacy
				if (empty($id) && !empty($name))    $id = $name;    // Legacy: if no id, use name as id

				?>
				<p class='evo_elm_row evo_row_select <?php echo $row_class;?> <?php echo $select_multi_options? 'multi':'';?>' style='<?php echo $row_style;?>'>
					<input type='hidden' name='<?php echo $id;?>' value='<?php echo $value;?>'/>
					
					<?php if(!empty($label)):?> 
						<label style='margin-right: 10px;'><?php echo $label.' '. $legend_code;?></label>
					<?php endif;?>
					
					<span class='values evobr30 evopad10 <?php echo $id;?>'>
					<?php 

					$vals = array();
				

					// Handle default value and current value
				    if(!empty($value)) {
				        $vals = $select_multi_options ? explode(',', $value) : array($value);
				    } elseif(!empty($default)) {
				        $vals = $select_multi_options ? explode(',', $default) : array($default);
				    }

					foreach($options as $F=>$V){

						$selected = '';
						if($select_multi_options){
							if( in_array($F, $vals)) $selected = ' select';
						}else{
							if(!empty($vals) && $F == $vals[0]) $selected = ' select';
						}

						echo "<span value='{$F}' class='evo_row_select_opt opt{$selected} {$select_option_class}'>{$V}</span>";
					}?>
					</span>
				</p><?php

				// conditional subfields (CSF)
				if(!empty($conditional_subfields) && is_array($conditional_subfields)){
					echo "<div class='evoelm_CSF' data-parent-id='{$id}'>";
					foreach($conditional_subfields as $index=> $CSF){

						$visibility = 'hidden';

						// Check if the current value or default value matches any of the CSF's trigger values
						$trigger_values = $CSF['values']; // Array of values that trigger this CSF
            			$check_values = !empty($value) ? explode(',', $value) : (!empty($default) ? explode(',', $default) : []);

            			if (!empty($check_values) && count(array_intersect($trigger_values, $check_values)) > 0) {
			                $visibility = 'visible';
			            }
			            
			            echo "<div class='evoelm_csf_section' data-values='". json_encode($CSF['values']) ."' style='display: ".($visibility === 'visible' ? 'block' : 'none')."'>";
			            echo $this->get_element($CSF['field']);
			            echo "</div>";
					}
					echo "</div>";
				}

			break;

			// check boxes @4.9
			case 'checkbox':
			
				if( !is_array($options)) break;
						
				echo "<div class='evo_elm_row evo_elm_check evomarb10 {$id} {$row_class}' style='{$row_style}'>";
				if( !empty($name)) echo "<p class='evo_field_label'>$name $legend_code</p>"; 

				$_values = is_array($value) ? $value : (is_string($value) ? [$value] : ($default ?? []));

				echo "<div class='evodfx evofx_dr_c evogap5 evoff_2 evofz14 evopadt5'>";

				foreach($options as $option_id => $option_val){

					$is_val = in_array($option_id, $_values) ? true: false;

					echo "<span class='evoelm_check_trig evodfx evofx_dr_r evogap10 evopadb5 evocurp evohoop7' data-id='{$option_id}'>";
					echo "<i class='". ($is_val ? 'fa':'far')." fa-circle".($is_val ? '-check': null)." evofz18'></i>" . $option_val ;
					echo "<input class='{$field_class}' type='hidden' name='{$id}[]' value='". ($is_val ? $option_id: null) ."' data-role='none'/>";
					echo "</span>";

				}

				echo "</div>";
				
				// legend for under the field
				if(!empty( $legend )){
					echo "<br/><i style='opacity:0.6'>".$legend."</i>";
				}

				echo "</div>";						
			break;

			// DROP Down select field
			case 'dropdown':		
			case 'select':		
		
						
				echo "<p class='evo_elm_row evo_elm_select {$id} {$row_class}' style='{$row_style}'>";
				echo "<label>$name $legend_code</label>"; 
				echo "<select class='ajdebe_dropdown {$field_class}' name='".$id."' ". $field_attr .">";

				if(is_array($options)){
					$dropdown_opt = !empty($value)? $value: (!empty($default)? $default :'');	

					foreach($options as $option=>$option_val){
						echo"<option name='".$id."' value='".$option."' "
						.  ( ($option == $dropdown_opt)? 'selected=\"selected\"':null)  .">".$option_val."</option>";
					}	
				}					
				echo  "</select>";
					// legend for under the field
					if(!empty( $legend )){
						echo "<br/><i style='opacity:0.6'>".$legend."</i>";
					}
				echo "</p>";						
			break;

			// dynamic select @4.9
			case 'dynamic_select':

				echo "<div class='evo_elm_row evo_elm_dynamic_select {$id} {$row_class} evoposr evofxaic' style='{$row_style}' >";

					if( !empty($name)) echo "<label class='evoff_2 evomarr5'>$name $legend_code</label>"; 

					$firstKey = array_key_first($options);
					$selected = $options[ $firstKey ];
					if( !empty($value) && isset( $options[ $value ] ) ) $selected = esc_html( $options[ $value ] );

					echo "<button class='evo_elm_dynamic_select_trig evocurp evoposr' aria-haspopup='listbox' aria-expanded='false'>
					<div class='evoelm_ds_in evodfx evofx_dr_r evogap10 evofx_ai_c'>";
						echo "<div class='evoelm_ds_current evofz14 evoff_2' id='selected-option'>" .$selected.  "</div>";
						echo "<i class='fa fa-chevron-down evofz12i'></i>";
					echo "</div>";
					echo "</button>";

					echo "<div class='evodn' data-d='". json_encode( $options ) ."'></div>";
					echo "<input type='hidden' name='{$id}' value='{$firstKey}'/>";

				echo "</div>";

			break;

			// DROP Down select field -- select2
			case 'dropdownS2':					
				
				echo "<div class='evo_elm_row evoelm_sel2 {$id} {$row_class} evoposr evomarb5' style='{$row_style}'>";

				echo "<div class='evoelm_sel2_in evodfx evofxdrr evogap10 evofxaic'>";
			    echo "<label class='evomarb5 evodb'>{$name} {$legend_code}</label>";

			    // Input field with selected text (option_val) as value
			    $selected_key = !empty($value) ? $value : (!empty($default) ? $default : '');
			    $selected_text = '';
			    if (is_array($options) && array_key_exists($selected_key, $options)) {
			        $selected_text = $options[$selected_key];
			    }

			    echo "<div class='evoposr evofx_1'>";
			    	echo "<span class='evoelm_sel2_cur_val evobgcw evopad10-20 evocurp evohoop7 evodfx evobr20 evofxjcsb evofxaic evoborder'>
			    		<em class='evoelm_sel2_cur_v evofsn'>". $selected_text . "</em><i class='fa fa-chevron-down'></i></span>";
				    
				    echo "<input class='evoelm_sel2_val' type='hidden' name='{$id}' value='{$selected_key}' />";

				    // Hidden dropdown list as spans
				    echo "<div class='evoelm_sel2_opt_list evobr20 evodfx evofxdrc evoofh evoposa evobgcw evot0 evow100p' style='display:none; '>";

				    	echo "<span class='evoelm_sel2_op_val evopad10-20 evodfx evofxjcsb evofxaic'><em class='evoelm_sel2_cur_v evofsn'>{$selected_text}</em><i class='evoelm_sel2_hide fa fa-chevron-up evocurp evohoop7'></i></span>";

				    	echo "<div class='evopad5 evobordert evoborderb'>";
				    	echo "<input type='search' class='evoelm_sel2_search evomar0i evopad0-10' name='{$id}_s' value='' placeholder='". __('Type to search...','eventon')."' style='width:100%' />";
					    echo "</div>";

					    echo "<div class='evoelm_sel2_opt_list_in evobgcw'>";
					    if (is_array($options)) {
					        foreach ($options as $option => $option_val) {
					            $is_selected = ($option == $selected_key) ? ' selected' : '';
					            echo "<span class='evoelm_sel2_opt{$is_selected} evofz14 evocurp evodb evopad10 evobordert' data-value='{$option}'>{$option_val}</span>";
					        }
					    }
					     echo "</div>";

				    echo "</div>";
			    echo "</div>";

			    echo "</div>";


			    // Legend under the field
			    if (!empty($legend))     echo "<i class='evodb evomart5' style='opacity:0.6'>{$legend}</i>";
			    echo "</div>";

			break;

			// URL field
			case 'url':
				echo "<div class='evo_elm_row evoelm_url {$id} {$row_class}' style='{$row_style}'>";
				$placeholder = (!empty($default) )? 'placeholder="'.$default.'"':null;				
				
				echo"<p class='evo_field_label'>".$name.$legend_code . "</p>";
				echo "<p class='evo_field_container'>";
					
					echo "<span class='evodfx evogap10 evofx_ai_c'>";
						echo "<input class='{$field_class} evofx_1' type='{$field_type}' name='{$id}'";

						if( $readonly ) echo 'readonly="true"';
						$__values = !empty($value) ? htmlspecialchars( $value , ENT_QUOTES) : '' ;
						echo 'value="'. $__values .'"' . $placeholder."/>";

						echo "<span class='evodfx evofx_dr_r evofx_ai_c'>";
						echo $this->yesno_btn(array(
							'id'=>$id2,
							'var'=> $value_2,
							'input'=> true,
							'label'=> __('Open in new tab'),
						));
						echo "</span>";


					echo "</span>";

					if(!empty($description)) echo "<em>". $description ."</em>";

				echo "</p></div>";
			break;
			

			// YES NO
			case 'yesno':						
				if(empty( $value) ) $value = 'no';
				echo "<p class='evo_elm_row yesno_row {$id} {$row_class}' style='{$row_style}'>".$this->yesno_btn(array(
						'id'=>$id,
						'var'=> $value,
						'afterstatement'=> $afterstatement,
						'input'=> true,
						'guide'=> $tooltip,
						'guide_position'=> $tooltip_position,
						'inputAttr'=>$inputAttr, // @s 4.5.5
						'label'=> $label,
					))."<span class='field_name'>". $name ."{$legend_code}</span>";

					// description text for this field
					if(!empty( $legend )){
						echo"<i class='evoop7 evomart5 evodb'>".$legend."</i>";
					}
				echo'</p>';
			break;
			case 'yesno_btn':						
				if(empty( $value) ) $value = 'no';
				echo "<p class='evo_elm_row yesno_row {$id} {$row_class}' style='{$row_style}'>".

				$this->yesno_btn(array(
					'id'=>$id,
					'var'=> $value,
					'afterstatement'=> $afterstatement,
					'input'=> true,
					'guide'=> $tooltip, 
					'guide_position'=> $tooltip_position,
					'label'=> $label,
					'inputAttr'=>$inputAttr,
					'attr'=>$attr,
				));

				echo'</p>';	
			break;

			// Block button @2.3.3
			case 'block_button':
				if(empty( $value) ) $value = 'no';
				echo "<div class='evo_elm_row evoelm_blockbtn {$id} {$row_class} evocurp evo_transit_all evopad15 evobr20 ".($value=='yes'? 'on':'')."' style='{$row_style}' afterstatement='" . ( $afterstatement ?? '' ) . "' data-id='{$id}'>";
					echo "<i class='evofz18i ".($value=='yes'? 'fa fa-circle-check':' far fa-circle')." evomarb5'></i>";
					// icon as $value_2
					$icon = ( !empty($value_2)) ? "<i class='evoop1i evomarr5 evoop1i fa {$value_2}'></i>": '';

					echo "<p class='evo_field_label'>". $icon . $label . "</p>";
					echo $legend_code;
					if(!empty( $legend )){
						echo"<i class='evoop7 evomart5 evodb'>".$legend."</i>";
					}
					echo "<input class='{$field_class} ' type='hidden' name='{$id}'";					
					echo 'value="'. $value .'"/>';

				echo "</div>";

			break;

			case 'angle_field':						
				$value = empty( $value) ? '0' : (int)$value;
				
				echo "<div class='evo_elm_row angle ". esc_attr( $id )." ". esc_attr( $row_class )." style='". esc_attr( $row_style )."'>
					<div class='evo_elm_ang_hold'>
						<span class='evo_elm_ang_center' style='transform:rotate(". esc_attr( $value )."deg);'>
							<span class='evo_elm_ang_pointer'></span>
						</span>	
					</div>
					<input class='evo_elm_ang_inp' name='". esc_attr( $id )."' value='". esc_attr( $value )."°'/>
				";

					// description text for this field
					if(!empty( $legend )){
						echo"<i style='opacity:0.6; padding-top:8px; display:block'>". esc_html( $legend ) ."</i>";
					}
				echo'</div>';
			break;

			case 'button':
				$data = empty($data) ? '' : $data;
				echo "<p class='evo_elm_row btn ". esc_attr( $id )." ". esc_attr( $row_class )."' style='". esc_attr( $row_style )."'>";
				echo "<a class='evo_btn ". esc_attr( $unqiue_class )."' data-d='". esc_attr( $data )."'>". esc_html( $name )."</a>";
				echo'</p>';
			break;
			// @2.3
			case 'detailed_button':
				$_target = $_blank ? 'target="_blank"' : null;

				$_extra = $_attr_class = '';

				// trigger action
				if( $trig_data && is_array( $trig_data ) && empty($field_after_content) ){
					$_extra .= $this->_process_trigger_data( $trig_data , $trig_type , 'data');
					if( $trig_type == 'trig_lb' || empty($trig_type) ) $_attr_class .= ' evolb_trigger'; 
					if( $trig_type == 'trig_ajax' ) $_attr_class .= ' evo_trigger_ajax_run'; 
				}

				// precontent present
				if( !empty($field_before_content)) $_attr_class .= ' pre';



				$elm_start = ( !empty($link)) ? 
					"<a class='evo_elm_button {$_attr_class} {$field_class}' href='". esc_url( $link ) ."' {$_target} {$_extra}>": 
					"<button class='evo_elm_button {$_attr_class} {$field_class}' {$_extra}>";
				$elm_end = ( !empty($link)) ? '</a>' : '</button>';

				// after content as static button
				if( !empty($field_after_content)){
					$_attr_class .= ' inbtn';
					$elm_start = "<div class='evo_elm_button {$_attr_class} {$row_class}' {$_extra}>";
					$elm_end = '</div>';
				} 

				echo $elm_start;

				if( !empty($field_before_content)) echo "<span class='evo_btn_vis pre'>" . $field_before_content ."</span>";
				echo "<span class='evo_btn_item_details'>";
					echo "<span class='evo_btn_item'>". $name ."</span>";
					if( !empty($description)) echo "<span class='evo_btn_info'>". $description ."</span>";
				echo "</span>";
				if( !empty($content)) echo "<span class='evo_btn_vis'>". $content ."</span>";

				// after content as static button
				if( !empty($field_after_content)){
					
					// if trigger data available for after content button
					if( !empty($trig_data)){
						//print_r($trig_data);
						$_extra .= $this->_process_trigger_data( $trig_data , $trig_type, 'data' );
						if( $trig_type == 'trig_lb' || empty($trig_type) ) $_attr_class .= ' evolb_trigger'; 
						if( $trig_type == 'trig_ajax' ) $_attr_class .= ' evo_trigger_ajax_run'; 
					}
					

					$_elm_start = ( !empty($link)) ? 
						"<a class='evo_btn {$_attr_class} {$field_class}' href='". esc_url( $link ) ."' {$_target} {$_extra}>": 
						"<button class='evo_btn {$_attr_class} {$field_class}' {$_extra}>";
					$_elm_end = ( !empty($link)) ? '</a>' : '</button>';
					echo $_elm_start . $field_after_content .$_elm_end;

				} 
				
				echo $elm_end;

			break;
			case 'icon_select':
				$value = empty( $value) ? '' : $value;
				
				$close_ = $close ? '<em class="ajde_icon_close">X</em>':'';
				
				echo "<p class='evo_elm_row icon faicon'>
						<i class='evo_icons ajde_icons default fa ". esc_attr( $value )." ". (!$close ?'so':'')."' data-val='". esc_attr( $value )."'>". $close_ ."</i> 
						<input type='hidden' name='". esc_attr( $id )."' id='". esc_attr( $id )."' value='". esc_attr( $value )."'></p>";			
				if( !empty($legend)) echo "<p class='description'>". esc_html( $legend ) ."</p>";
			break;
			// afterstatement
				case 'begin_afterstatement': 						
					$yesno_val = (!empty($value))? $value:'no';				
					echo"<div class='evo_elm_afterstatement {$id}' id='{$id}' style='display:".(($yesno_val=='yes')?'block':'none')."'>";
				break;
				case 'end_afterstatement': echo "</div>"; break;
		}

		echo $_nesting_end;

		$output = ob_get_clean();

		if($_echo): echo $output; else: return $output; endif;
	}

	public function print_process_multiple_elements( $A){
		echo $this->process_multiple_elements( $A);
	}
	function process_multiple_elements($A){
		$output = '';
		foreach($A as $key=>$AD){
			$output .= $this->get_element( $AD);
		}
		return $output;
	}

	public function populate_field_values( $fields, $data){
		foreach( $fields as $key => $value){

			// field id
				$field_id = $key;				
				if( isset( $value['id'] )) $field_id = $value['id'];
				if( isset( $value['var'] )) $field_id = $value['var'];

			// get field value
				$field_value = '';
				if(empty($value['value'])){

					if(!empty( $field_id ) && !empty( $data[ $field_id ] )){
						if( !is_array($data[ $field_id ]) && !is_object($data[ $field_id ])){
							$field_value = stripslashes(str_replace('"', "'", (esc_attr( $data[ $field_id ] )) ));
						}	

						if( is_array( $data[ $field_id ] ))	{
							$field_value = $data[ $field_id ];
						}				
					}

				}else{	
					$field_value = $value['value'];	
				}


			$fields_processed[ $key ] = $value;
			if( !empty($value['placeholder']) ) $fields_processed[ $key ]['default'] = $value['placeholder'];
			if( !empty($value['legend']) ) $fields_processed[ $key ]['tooltip'] = $value['legend'];
			if( !empty($value['var']) ) $fields_processed[ $key ]['var'] = $field_id;
			$fields_processed[ $key ]['id'] = $field_id;
			$fields_processed[ $key ]['value'] = $field_value;

		}

		return $fields_processed;
	}

	// @since 4.3.5
	function print_hidden_inputs( $array){
		foreach( $array as $name=>$value){
			echo "<input type='hidden' name='". esc_attr( $name )."' value='". esc_attr( $value )."'>";
		}
	}



// date time selector
	function print_date_time_selector($A){
		$D = array(
			'disable_date_editing'=> false,
			'minute_increment'=> 1,
			'time_format'=> 'H:i:s',
			'date_format'=> 'Y/m/d',
			'date_format_hidden'=>'Y/m/d',
			'unix'=> '',				
			'type'=>'start',
			'subtype'=>'',
			'assoc'=>'reg',
			'names'=>true,
			'rand'=>'',
			'time_opacity'=> 1,
			'selector'=>'both', // both, date, time
		);
		$A = array_merge($D, $A);

		extract($A);

		$rand = (empty($rand))? wp_rand(10000,99999): $rand;

		$hr24 = false;

		if(!empty($time_format) && ( strpos($time_format, 'H')!== false || strpos($time_format, 'G') !== false ) )   $hr24 = true;

		// processings
		$unix = !empty($unix)? (int)$unix : current_time('timestamp');
		
		$DD =  new DateTime();
		$DD->setTimezone( EVO()->calendar->timezone0 );
		$DD->setTimestamp( $unix);

		$date_val = $DD->format( $date_format );
		$date_val_x = $DD->format(  $date_format_hidden );
		$hour = $DD->format( ($hr24? 'H':'h') );
		$minute = $DD->format( 'i');
		$ampm = $DD->format( 'a');

		echo "<span class='evo_date_time_select ". esc_attr( $type )." ". esc_attr( $subtype )."' data-id='". esc_attr( $rand )."' data-unix='". esc_attr( $unix )."'> ";

			
		if($selector != 'time' ):

			$__class = ($disable_date_editing?'':"datepicker". esc_attr( $type ). "date");
			$__class .= ($assoc != 'rp'? 'req':'')." ". esc_attr( $type ). " evo_dpicker ";
			$__class .= ' '. esc_attr( $subtype );

			echo " <span class='evo_date_edit'>
				<input id='evo_". esc_attr( $type ). "_date_". esc_attr( $rand ). "' class='". esc_attr($__class) ."'  readonly='true' type='text' data-role='none' name='event_". esc_attr( $type ). "_date' value='". esc_attr( $date_val ) ."' data-assoc='". esc_attr( $assoc ). "' />	
				<input type='hidden' name='event_". esc_attr( $type )."_dateformat' value='". esc_attr( $date_format )."'/>

				<input type='hidden' name='".($names? "event_". esc_attr( $type )."_date_x":'')."' class='evo_". esc_attr( $type )."_alt_date alt_date' value='". esc_attr( $date_val_x )."'/>
				<input type='hidden' class='alt_date_format' name='event_". esc_attr( $type ). "_dateformat_alt' value='". esc_attr( _evo_dateformat_PHP_to_jQueryUI($date_format_hidden) ) ."'/>

			</span>";

		endif;

		if($selector != 'date' ):
			echo "<span class='evo_time_edit' style='opacity:". esc_attr( $time_opacity ) . "}'>
				<span class='time_select'>";
				if($disable_date_editing){
					echo "<span>". esc_html( $hour ) ."</span>";
				}else{													
					echo "<select class='evo_time_select _". esc_attr( $type )."_hour' name='".($names? "_". esc_attr( $type )."_hour":'')."' data-role='none'>";

					for($x=1; $x< ($hr24? 25:13 );$x++){	
						$y = ($hr24)? sprintf("%02d",($x-1)): $x;							
						echo "<option value='". esc_attr( $y )."'".(($hour==$y)?'selected="selected"':'').">". esc_html( $y )."</option>";
					}
					echo "</select>";
				}
				echo "</span>";

				echo "<span class='time_select'>";
				if($disable_date_editing){
					echo "<span>". esc_html( $minute ) ."</span>";
				}else{	
					echo "<select class='evo_time_select _". esc_attr( $type )."_minute' name='".($names? "_". esc_attr( $type )."_minute":'')."' data-role='none'>";

					$minute_adjust = (int)(60/$minute_increment);
					for($x=0; $x<$minute_adjust;$x++){
						$min = $minute_increment * $x;
						$min = ($min<10)?('0'.$min):$min;
						echo "<option value='". esc_attr( $min )."'".(($minute==$min)?'selected="selected"':'').">". esc_html( $min )."</option>";
					}
					echo "</select>";
				}
				echo "</span>";

				// AM PM
				if(!$hr24){
					echo "<span class='time_select'>";
					if($disable_date_editing){
						echo "<span>". esc_html( $ampm ) ."</span>";
					}else{	
						echo "<select name='".($names? "_". esc_attr( $type )."_ampm":'')."' class='_". esc_attr( $type )."_ampm ampm_sel'>";													
						foreach(array('am'=> evo_lang_get('evo_lang_am','AM'),'pm'=> evo_lang_get('evo_lang_pm','PM') ) as $f=>$sar){
							echo "<option value='". esc_attr( $f ) ."' ".(($ampm==$f)?'selected="selected"':'').">". esc_html( $sar )."</option>";
						}							
						echo "</select>";
						echo "</span>";
					}
				}
				
			echo "</span>";
		endif;

		echo "</span>";
	}

// ONLY time selector
	function print_time_selector($A){
		$D = array(
			'disable_date_editing'=> false,
			'minute_increment'=> 1,
			'time_format'=> 'H:i:s',
			'minutes'=> 0,		
			'var'=>'_unix',		
			'type'=> 'hm', // (hm) hour/min OR (tod) time of day
		);
		$A = array_merge($D, $A);

		extract($A);

		$hr24 = false;
		if(!empty($time_format) && strpos($time_format, 'H')!== false) $hr24 = true;

		$unix = $minutes * 60;

		// processings
		$hour = gmdate( ($hr24? 'H':'h'), $unix);
		$minute = gmdate( 'i', $unix);
		$ampm = gmdate( 'a', $unix);

		echo "<span class='evo_date_time_select time_select ". esc_attr( $type )."' > 
			<span class='evo_time_edit'>
				<input type='hidden' name='". esc_attr( $var )."' value='". esc_attr( $unix )."'/>
				<span class='time_select'>";
				if($disable_date_editing){
					echo "<span>". esc_html( $hour ) ."</span>";
				}else{													
					echo "<select class='evo_timeselect_only _hour' name='_hour' data-role='none'>";

					for($x=1; $x< ($hr24? 25:13 );$x++){	
						$y = ($hr24)? sprintf("%02d",($x-1)): $x;							
						echo "<option value='". esc_attr( $y )."'".(($hour==$y)?'selected="selected"':'').">". esc_html( $y )."</option>";
					}
					echo "</select>";
				}
				echo " Hr </span>";

				echo "<span class='time_select'>";
				if($disable_date_editing){
					echo "<span>". esc_html( $minute ) ."</span>";
				}else{	
					echo "<select class='evo_timeselect_only _minute' name='_minute' data-role='none'>";

					$minute_adjust = (int)(60/$minute_increment);
					for($x=0; $x<$minute_adjust;$x++){
						$min = $minute_increment * $x;
						$min = ($min<10)?('0'.$min):$min;
						echo "<option value='". esc_attr( $min ). "'".(($minute==$min)?'selected="selected"':'').">". esc_html( $min )."</option>";
					}
					echo "</select>";
				}
				echo " Min </span>";

				// AM PM
				if(!$hr24 && $type == 'tod'){
					echo "<span class='time_select'>";
					if($disable_date_editing){
						echo "<span>". esc_html( $ampm ) ."</span>";
					}else{	
						echo "<select name='_ampm' class='evo_timeselect_only _ampm'>";													
						foreach(array(
							'am'=> evo_lang_get('evo_lang_am','AM'),
							'pm'=> evo_lang_get('evo_lang_pm','PM') ) as $f=>$sar
						){
							echo "<option value='". esc_attr( $f )."' ".(($ampm==$f)?'selected="selected"':'').">". esc_html( $sar ) ."</option>";
						}							
						echo "</select>";
						echo "</span>";
					}
				}
				
			echo "</span>
		</span>";
	}

	// @2.2.9
	function _get_date_picker_data(){
		$date_format = ( EVO()->cal->check_yn('evo_usewpdateformat','evcal_1') ) ? esc_attr( get_option('date_format') ) : 'Y/m/d';

		return array(
			'date_format' => esc_attr( $date_format ),
			'js_date_format' => esc_attr( _evo_dateformat_PHP_to_jQueryUI( $date_format  ) ),
			'time_format' =>  esc_attr( EVO()->calendar->time_format ) ,
			'sow'=> esc_attr( get_option('start_of_week') ),
		);
	}
	function _print_date_picker_values(){			
		$data_str = wp_json_encode($this->_get_date_picker_data());

		echo "<div class='evo_dp_data' data-d='".  $data_str ."'></div>";
	}

// Yes No Buttons
	public function print_yesno_btn( $args =''){
		echo $this->yesno_btn( $args );
	}
	function yesno_btn($args=''){
		$defaults = array(
			'id'=>'',
			'var'=>'', // the value yes/no
			'no'=>'',
			'default'=>'',
			'input'=>false,
			'inputAttr'=>'',
			'label'=>'',
			'guide'=>'',
			'guide_position'=>'',
			'abs'=>'no',// absolute positioning of the button
			'attr'=>'', // array
			'afterstatement'=>'',
			'nesting'=>false
		);
		
		$args = shortcode_atts($defaults, $args);

		extract($args);

		$_attr = $no = '';

		if(!empty($args['var'])){
			$args['var'] = (is_array($args['var']))? $args['var']: strtolower($args['var']);
			$no = ($args['var']	=='yes')? 
				 null: 
				 ( (!empty($args['default']) && $args['default']=='yes')? null:'NO');
		}else{
			$no = (!empty($args['default']) && $args['default']=='yes')? null:'NO';
		}


		if(!empty($args['attr'])){
			foreach($args['attr'] as $at=>$av){
				$_attr .= esc_attr( $at ) .'="'. esc_attr( $av ) .'" ';
			}
		}

		// afterstatement
			if(!empty($args['afterstatement'])){
				$_attr .= 'afterstatement="' . esc_attr( $args['afterstatement'] ) .'"';
			}
			
		// input field
		$input = '';
		if($args['input']){
			$input_value = (!empty($args['var']))? 
				$args['var']: (!empty($args['default'])? esc_attr( $args['default'] ) :'no');

			// Attribut values for input field
			$inputAttr = '';
			if(!empty($args['inputAttr'])){
				foreach($args['inputAttr'] as $at=>$av){
					$inputAttr .= esc_attr( $at ).'="'. esc_attr( $av ).'" ';
				}
			}

			// input field
			$input = "<input id='". esc_attr( $args['id'] ). "_input' {$inputAttr} type='hidden' name='". esc_attr( $args['id'] )."' value='". esc_attr( $input_value )."'/>";
		}

		$guide = '';
		if(!empty($args['guide'])){
			$guide = $this->tooltips($args['guide'], esc_attr( $args['guide_position'] ) );
		}

		$label = '';
		if(!empty($args['label']))
			$label = "<label class='ajde_yn_btn_label evo_elm' for='". esc_attr( $args['id'] )."_input'>". esc_html( $args['label'] )."{$guide}</label>";

		// nesting
			$nesting_start = $nesting_end = '';
			if($args['nesting']){
				$nesting_start = "<p class='yesno_row'>";
				$nesting_end = "</p>";
			}

		return $nesting_start.'<span id="'. esc_attr( $args['id'] ) .'" class="evo_elm ajde_yn_btn '.($no? 'NO':null).''.(($args['abs']=='yes')? ' absolute':null).'" '. $_attr.'><span class="btn_inner" style=""><span class="catchHandle"></span></span></span>'.$input.$label.$nesting_end;
	}

// DEFAULT CSS style colors @since 4.3
	function get_def_css(){
		$preset_data = apply_filters('evo_elm_def_css', array(
			'evo_color_1' => '202124',
			'evo_color_2' => '656565',
			'evo_color_link' => '656565',
			'evo_color_prime' => '00aafb',
			'evo_color_second' => 'fed584',
			'evo_font_1' => "'Poppins', sans-serif",
			'evo_font_2' => "'Noto Sans',arial",
			'evo_cl_w' => "ffffff",
		));
		return $preset_data;
	}

// Preloading animation html 4.6
	function get_preload_html($data = array()){
		$D = array_merge(  array(
			'pclass'=>'',// extra parent class
			'styles'=>'',// extra styles to holder
			's'=> array(
				'multiply'=> 1,
				array('w'=>'50%', 'h'=>'50%','m'=>3),
				array('w'=>'100%', 'h'=>'50%'),				
			),
			'echo' => true,
			'animation_style'=>'swipe',// swipe or blink
		), $data );

		extract( $D );

		$multiply = isset( $s['multiply'] ) ? $s['multiply'] : 1;

		ob_start();
		echo "<div class='evo_loading_bar_holder h100 {$pclass} {$animation_style}' style='{$styles}'>";

		for( $x = 0; $x< $multiply ; $x++){
			foreach( $s as $SS ){	
			
				if( isset( $SS['nesting'])){
					echo "<div class='nest nest1 {$SS['nesting']}'>";
				}
				if( is_array($SS)){
					if( isset( $SS['w'])){
						
						$M = isset( $SS['m'] ) ? (int)$SS['m'] : 1; // if multiple passed						
						for($y = 0; $y<$M; $y++){
							echo $this->get_preload_one( $SS);
						}
						
						continue;
					}

					$DR = isset( $SS['dr'] ) ? $SS['dr']:'' ;
					$GAP = isset( $SS['gap'] ) ? $SS['gap']:'' ;

					echo "<div class='nesthold {$DR} g{$GAP}'>";

					foreach( $SS as $SS2){
						if( isset( $SS2['nesting'])){
							echo "<div class='nest nest2 {$SS2['nesting']}'>";
						}
						if( is_array($SS2)){
							if( isset( $SS2['w'])){

								$M2 = isset( $SS2['m'] ) ? (int)$SS2['m'] : 1; // if multiple passed
								for($y2 = 0; $y2<$M2; $y2++){
									echo $this->get_preload_one( $SS2);
								}

								continue;
							}
							foreach( $SS2 as $SS3){
								if( isset( $SS3['nesting'])){
									echo "<div class='nest {$SS3['nesting']}'>";
								}
								if( is_array($SS3)){

									$M3 = isset( $SS3['m'] ) ? (int)$SS3['m'] : 1; // if multiple passed
									for($y3 = 0; $y3<$M3; $y3++){
										echo $this->get_preload_one( $SS3);
									}
									
								}
								if( isset( $SS3['nesting']))	echo "</div>"; // close nesting
							}
						}
						if( isset( $SS2['nesting']))	echo "</div>"; // close nesting
					}

					echo "</div>";
					
				}
				
				if( isset( $SS['nesting']))	echo "</div>"; // close nesting
			}
		}
		

		echo "</div>";

		$O = ob_get_clean();

		if($echo){ echo $O; }else{ return $O; }
	}
	private function get_preload_one( $data ){
		extract ( $data );

		$MB = !empty($mb) ? 'margin-bottom:'. $mb .'px;' :'';

		return "<div class='evo_loading_bar wid_{$w} hi_{$h}' style='width:{$w}; height:{$h}; {$MB}'></div>";
	}

	function get_preload_standalone( $data ){

	}

	// @s 4.6
	function get_preload_map(){
		return "
		<span class='evo_map_load_out evoposr evodb evobr15'>
		<i class='fa fa-map-marker evoposa'></i><span class='evo_map_load evoposr evodb'>					
					<i class='a'></i>
					<i class='b'></i>
					<i class='c'></i>
					<i class='d'></i>
					<i class='e'></i>
					<i class='f'></i>
				</span></span>";
	}

// EventCard Items -- @version 4.9
	public function print_eventcard_box_header($data){
		extract(array_merge( array(
			'row_class'=> '',
			'header_data_attr'=> array(),
			'icon_key'=> '',
			'icon_class'=> '',
			'title'=> '',
		), $data) );

		$help = new evo_helper();

		$header_data = '';
		if( count($header_data_attr)>0 ) $header_data = $help->array_to_html_data( $header_data_attr);

		echo  "<div id='evo_vendor' class='evo_metarow_vendor evorow evcal_evdata_row bordb evcal_evrow_sm".$row_class."' {$header_data}>
					<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon( $icon_key , $icon_class  )."'></i></span>
					<div class='evcal_evdata_cell'>";
				echo "<h3 class='evo_h3'>". $title ."</h3>";

	}

// HTML for grid 
	// @version 4.7
	public function get_grid_content($structure){
		
		extract( $structure );

		echo "<div class='evo_grid'>";

		foreach( $structure as $boxes => $boxdata ){

			$sizes = '12,12,12';
			if( isset($boxdata['sizes'])) $sizes = $boxdata['sizes'];
			list($large, $medium, $small) = explode(',', $sizes);

			echo "<div class='evo_grid_box evo_spanL_{$large} evo_spanM_{$medium} evo_spanS_{$small}'>";

			echo "<div class='evo_tile'>";
			echo "<div class='evo_tile_content' style='".( isset($boxdata['styles']) ? esc_html($boxdata['styles']): '' )."'>";

				if( isset( $boxdata['header'] )):

					echo "<div class='evo_tile_header'>";

					echo $boxdata['header'];

					echo "</div>";	
				endif;

				if( isset( $boxdata['body'] )):

					echo "<div class='evo_tile_body'>";

					echo $boxdata['body'];

					echo "</div>";

				endif;

				if( isset( $boxdata['footer'] )):
					echo "<div class='evo_tile_footer'>";

					echo $boxdata['footer'];

					echo "</div>";
				endif;
			
			echo "</div>";
			echo "</div>";

			echo "</div>";
		}

		echo "</div>";
		
	}

// General Settings Element support - 4.6 @updated 4.7.2
	function _get_settings_content( $data ){

		ob_start();

		$args = array(
			'hidden_fields'=> array(),
			'form_class'=>'',
			'container_class'=>'',
			'fields'=> array(),
			'save_btn_data'=> array(),
			'nonce_action'=>'eventon',// nonce field name
			'footer_btns'=> array(
				'save_changes'=> array(
					'label'=> __('Save Changes','eventon'),
					'data'=> array(),
					'class'=> 'evo_btn evolb_trigger_save',
					'href'=>'',
					'target'=> ''
				)
			)
		);

		$args = array_merge($args, $data);
		extract($args);

		?>
		<div class='<?php echo $container_class;?> evolb_form_out'>
			<form class='<?php echo $form_class;?> evolb_form'>
				<div class='evo_form_body'>
				<?php 

				// include nonce field
				wp_nonce_field( $nonce_action, 'evo_noncename' );

				if( is_array($hidden_fields) && count($hidden_fields) >0 ) $this->print_hidden_inputs( $hidden_fields);
					
				echo $this->process_multiple_elements( $fields );

				?>
				</div>
				<div class='evo_form_footer'>
				<?php

					$this->_print_settings_footer_btns( $footer_btns );

				?>	
				</div>

			</form>
		</div>
		<?php 
		return ob_get_clean();
	}
	function _print_settings_footer_btns($arr){
		$A = array_merge(array(
			'save_changes'=> array(
				'label'=> __('Save Changes','eventon'),
				'data'=> array(),
				'class'=> 'evo_btn evolb_trigger_save',
				'href'=>'',
				'target'=> ''
			)
		), $arr);
		?>
		<p class='evopadt20'>					
			<?php 
			foreach( $A as $btn):
				if(!isset( $btn['label'] )) continue;
				$href = isset($btn['href']) && !empty( $btn['href'] )? 'href="'. $btn['href'] .'"':'';
				$target = isset($btn['target']) && !empty( $btn['target'] ) ? 'target="'. $btn['target'] .'"' : '';

				?><a <?php echo $href; echo $target;?> class='<?php echo $btn['class'];?>' data-d='<?php echo json_encode($btn['data']);?>' style=''><?php echo $btn['label'];?></a>
			<?php endforeach;?>
			
		</p>	
		<?php 
	}

	// settings toggle with nested settings start
	// @since 4.7 @u
	function _print_settings_toggle_nester_start( $data){
		extract( array_merge(array(
			'id'=>'',
			'value'=>'',
			'value_yn'=> false,
			'afterstatement'=>'',			
			'tooltip'=>'',			
			'label'=>'',	
			'toggle_class'=>'',		
		), $data) );
		?>
		<div class='<?php echo $toggle_class;?>'>			
			<div class=''>
				<?php 
					EVO()->elements->get_element(
						array(
							'type'=>'yesno_btn','_echo'=> true,
							'id'=> $id,
							'value'=> $value, 
							'afterstatement'=> $afterstatement,
							'tooltip'=> $tooltip,
							'label'=> $label,
						)
					);
				?>
			</div>
			<div class='innersection' id='<?php echo $afterstatement;?>' style='display:<?php echo $value_yn ? 'block':'none';?>'>
				<div class='evo_edit_field_box'>	
		<?php 
	}
	function _print_settings_toggle_nester_close(){
		?>
		</div>
			</div>
		</div>
		<?php
	}

// settings row items with edit/delete button and item data
// @since 4.7.2
	function row_item_ul( $extra_class = '' , $sortable = false ){
		?>
		<ul class='evoelm_settings_row <?php echo $extra_class;?> <?php echo $sortable ? 'evosortable':'';?>'>
		<?php 
	}	
	function row_item_ul_end(){ echo "</ul>";}

	function row_item_li($data){
		extract( array_merge(array(
			'item_id'=>'',
			'extra_classes'=>'',
			'name'=>'',
			'other_data'=> array(), 'other_data_row'=> false,
			'edit_data'=> array(),
			'delete_data'=> array(),
		), $data) );

		echo "<li data-id='". esc_attr( $item_id ) ."' class='evodfx evofx_jc_sb evoposr ". esc_attr( $extra_classes ). ( $other_data_row ? ' od_row':null ) ."'>";

		echo "<span class='evodfx evofxww evogap10'>";
			echo "<span class='name evofw700'>" . esc_html( $name) ."</span>";
			if( count( $other_data)> 0){
				echo "<span class='other_data'>";

				foreach( $other_data as $other_data_item ){
					echo "<span class='evodfx evogap5'>";
						echo "<span class=''>". $other_data_item[0]. "</span>";
						echo "<span class='evofw700'>". $other_data_item[1]. "</span>";
					echo "</span>";
				}

				echo "</span>";
			}
		echo "</span>";
		echo "<span class='actions evodfx'>";

			if( count( $edit_data)> 0){
				echo "<i class='edit evolb_trigger fa fa-pencil' ". $this->_process_trigger_data( $edit_data , 'trig_lb','data' )."></i>";
			}
			if( count( $delete_data)> 0){
				echo "<i class='delete evo_trigger_ajax_run fa fa-times' ". $this->_process_trigger_data( $delete_data , 'trig_ajax' ,'data') ."></i>";
			}

		echo "</span>";

		echo "</li>";
	}
	
// SVG icons
	public function get_icon($name){
		if( $name == 'live'){
			return '<svg version="1.1" x="0px" y="0px" viewBox="0 0 73 53" enable-background="new 0 0 100 100" xmlns="http://www.w3.org/2000/svg"><g transform="matrix(1, 0, 0, 1, -13.792313, -23.832699)"><g><path  d="M75.505,25.432c-0.56-0.578-1.327-0.906-2.132-0.913c-0.008,0-0.015,0-0.022,0    c-0.796,0-1.56,0.316-2.123,0.88l-0.302,0.302c-1.156,1.158-1.171,3.029-0.033,4.206c5.274,5.451,8.18,12.63,8.18,20.214    c0,7.585-2.905,14.764-8.18,20.214c-1.141,1.178-1.124,3.054,0.037,4.211l0.303,0.302c0.562,0.561,1.324,0.875,2.118,0.875    c0.009,0,0.018,0,0.026,0c0.803-0.007,1.569-0.336,2.128-0.912C81.95,68.158,85.5,59.39,85.5,50.121    C85.5,40.853,81.95,32.085,75.505,25.432z"/><path d="M20.928,50.121c0-7.583,2.905-14.762,8.18-20.214c1.14-1.177,1.124-3.051-0.036-4.209l-0.303-0.302    c-0.563-0.562-1.325-0.877-2.12-0.877c-0.008,0-0.017,0-0.025,0c-0.804,0.007-1.571,0.335-2.13,0.913    C18.049,32.085,14.5,40.853,14.5,50.121c0,9.269,3.549,18.037,9.995,24.689c0.56,0.578,1.327,0.906,2.131,0.913    c0.008,0,0.016,0,0.024,0c0.795,0,1.559-0.315,2.121-0.879l0.303-0.303c1.158-1.158,1.174-3.03,0.035-4.207    C23.833,64.884,20.928,57.705,20.928,50.121z"/><path  d="M65.611,36.945c-0.561-0.579-1.33-0.907-2.136-0.913c-0.006,0-0.013,0-0.019,0    c-0.799,0-1.565,0.319-2.128,0.886l-0.147,0.148c-1.151,1.159-1.164,3.026-0.028,4.201c2.311,2.387,3.583,5.532,3.583,8.854    c0,3.323-1.272,6.468-3.582,8.854c-1.137,1.175-1.125,3.042,0.027,4.201l0.147,0.148c0.562,0.567,1.329,0.886,2.128,0.886    c0.006,0,0.013,0,0.019,0c0.806-0.005,1.575-0.334,2.136-0.912c3.44-3.551,5.335-8.23,5.335-13.177    C70.946,45.175,69.052,40.496,65.611,36.945z"/><path d="M38.812,37.06l-0.148-0.148c-0.562-0.563-1.326-0.879-2.121-0.879c-0.008,0-0.016,0-0.024,0    c-0.804,0.006-1.571,0.335-2.131,0.913c-3.439,3.55-5.333,8.229-5.333,13.176c0,4.947,1.894,9.627,5.334,13.177    c0.559,0.577,1.327,0.905,2.131,0.912c0.008,0,0.016,0,0.023,0c0.795,0,1.559-0.315,2.121-0.879l0.148-0.148    c1.158-1.158,1.173-3.03,0.035-4.208c-2.31-2.387-3.583-5.53-3.583-8.854c0-3.322,1.272-6.467,3.583-8.854    C39.986,40.09,39.971,38.217,38.812,37.06z"/></g><circle cx="50" cy="50.009" r="6.5"/> </g></svg>';
		}
	}

// Tool Tips updated 2.3
// central tooltip generating function
	function tooltips($content, $position='', $echo = false, $handleClass= false, $class = ''){
		// tool tip position
			if(!empty($position)){
				$L = ' L';
				
				if($position=='UL')
					$L = ' UL';
				if($position=='U')
					$L = ' U';
			}else{
				$L = null;
			}

		$output = "<span class='evotooltip ajdeToolTip{$L} fa". ($handleClass? ' handle':'')." {$class}' data-d='{$content}' data-handle='{$handleClass}'></span>";

		if(!$echo)
			return $output;			
		
		echo $output;
	}
	function echo_tooltips($content, $position=''){
		$this->tooltips($content, $position,true);
	}
	public function print_tooltips($content ='' , $position=''){
		if( empty($content)) return;
		$this->tooltips($content, $position,true);
	}
	
	

// Icon Selector -@updated 4.5.2

	// @since 4.5.2
	public function print_get_icon_html(){
		echo $this->get_icon_html();
	}
	function get_icon_html(){
		include_once( AJDE_EVCAL_PATH.'/assets/fonts/fa_fonts.php' );

		ob_start();

		?>
		<div id='evo_icons_data' style='display:none'>
			<p class='evo_icon_search_bar evomar0'>
				<input id='evo_icon_search' type='search' class='evo_icon_search' placeholder='<?php _e('Search icons by name','eventon');?>'/></p>
			<div class="evo_icon_selector fai_in">
				<ul class="faicon_ul">
				<?php
				// $font_ passed from incldued font awesome file above
				if(!empty($font_)){
					foreach($font_ as $fa){
						echo "<li data-v='". esc_attr( $fa )."'><i data-name='". esc_attr( $fa )."' class='fa ".esc_attr( $fa )."' title='". esc_attr( $fa )."'></i></li>";
					}
				}
				?>						
			</ul>
		</div></div>
		<?php
		return ob_get_clean();
	}
	function get_font_icons_data(){
		include_once( AJDE_EVCAL_PATH.'/assets/fonts/fa_fonts.php' );
		return $font_;
	}

// Import box +@version 4.3.5
	function print_import_box_html($args){
		$defaults = array(
			'box_id'=>'',
			'title'=>'',
			'message'=>'',
			'file_type'=>'.csv',
			'button_label'=> __('Upload','eventon'),
			'type'=>'popup',
		);
		$args = !empty($args)? array_merge($defaults, $args): $defaults;

		extract($args);

		?>
		<div class='evo_data_upload_window <?php echo esc_attr( $type );?>' data-id="<?php echo esc_attr( $box_id );?>" id='import_box' style='display:<?php echo $type == 'popup'? 'none':'';?>'>
			<span id="close" class='evo_data_upload_window_close'>X</span>
			<form id="evo_settings_import_form" action="" method="POST" data-link='<?php echo esc_url( AJDE_EVCAL_PATH );?> '>
					
				<h3 style='padding-bottom: 10px'><?php echo esc_html( $title );?></h3>
				<p ><i><?php echo wp_kses_post( $message );?></i></p>
				
				<input style=''type="file" id="file-select" name="settings[]" multiple="" accept="<?php echo esc_attr( $file_type );?>" data-file_type='<?php echo esc_attr( $file_type );?>'>
				
				<p><button type="submit" id="upload_settings_button" class='upload_settings_button evo_admin_btn btn_prime'><?php echo esc_html( $button_label );?></button></p>
			</form>
			<p class="msg" style='display:none'><?php _e('File Uploading','eventon');?></p>
		</div>
		<?php
	}

// wp Admin Tables
	function start_table_header($id, $column_headers, $args=''){ 

		$defaults = array(
			'classes'=>'',
			'display'=>'table'
		);
		$args = !empty($args)? array_merge($defaults, $args): $defaults;
		?>
		<table id="<?php echo esc_attr( $id );?>" class='evo_admin_table <?php echo !empty($args['classes'])? esc_attr( implode(' ',$args['classes']) ) :'';?>' style='display:<?php echo esc_attr( $args['display'] );?>'>
			<thead width="100%">
				<tr>
					<?php
					foreach($column_headers as $key=>$value){
						// width for column
						$width = (!empty($args['width'][$key]))? 'width="'. esc_attr( $args['width'][$key] ).'px"':'';
						echo "<th id='". esc_attr( $key ). "' class='column column-". esc_attr( $key )."' ". wp_kses_post( $width ).">". wp_kses_post( $value ) ."</th>";
					}
					?>
				</tr>
			</thead>
			<tbody id='list_items' width="100%">
		<?php
	}
	function table_row($data='', $args=''){
		$defaults = array(
			'classes'=>'',
			'tr_classes'=>'',
			'tr_attr'=>'',
			'colspan'=>'none'
		);
		$args = !empty($args) ?array_merge($defaults, $args): $defaults;

		// attrs
			$tr_attr = '';
			if(!empty($args['tr_attr']) && sizeof($args['tr_attr'])>0){
				foreach($args['tr_attr'] as $key=>$value){
					$tr_attr .= esc_attr( $key ) ."='". wp_kses_post( $value ) ."' ";
				}
			}
		
		if($args['colspan']=='all'){
			echo "<tr class='colspan-row ".(!empty($args['tr_classes'])? esc_attr( implode(' ',$args['tr_classes']) ) :'')."' ". esc_attr( $tr_attr ) .">";
			echo "<td class='column span_column ".(!empty($args['classes'])? esc_attr( implode(' ',$args['classes']) ) :'')."' colspan='". esc_attr( $args['colspan_count'] )."'>". wp_kses_post( $args['content'] ) ."</td>";
		}else{
			echo "<tr class='regular-row ".(!empty($args['tr_classes'])? esc_attr( implode(' ',$args['tr_classes']) ):'') ."' ". esc_attr( $tr_attr ).">";
			foreach($data as $key=>$value){
			
				echo "<td class='column column-". esc_attr( $key )." ".(!empty($args['classes'])? esc_attr( implode(' ',$args['classes']) ):'')."'>". wp_kses_post( $value )."</td>";
			}
		}
		
		echo "</tr>";
	}
	function table_footer(){
		?>
		</tbody>
		</table>
		<?php
	}



// styles and scripts
	function register_styles_scripts(){
		wp_register_style( 'evo_elements',EVO()->assets_path.'css/lib/elements.css',array(), EVO()->version);
		wp_register_script( 'evo_elements_js',EVO()->assets_path.'js/lib/elements.js',array(), EVO()->version, true);
	}
	function enqueue(){
		wp_enqueue_style( 'evo_elements' );
		wp_enqueue_script( 'evo_elements_js' );
	}

// shortcode generator - only in admin side
	function register_shortcode_generator_styles_scripts(){
		wp_register_style( 'evo_shortcode_generator',EVO()->assets_path.'lib/shortcode_generator/shortcode_generator.css',array(), EVO()->version);
		wp_register_script( 'evo_shortcode_generator_js',EVO()->assets_path.'lib/shortcode_generator/shortcode_generator.js',array(), EVO()->version, true);
	}
	function enqueue_shortcode_generator(){
		wp_enqueue_style( 'evo_shortcode_generator' );
		wp_enqueue_script( 'evo_shortcode_generator_js' );
	}

// Color picker
	function load_colorpicker(){
		wp_enqueue_style('colorpicker_styles');
		wp_enqueue_script('backender_colorpicker');
	}
	function register_colorpicker(){
		wp_register_script('backender_colorpicker',EVO()->assets_path.'lib/colorpicker/colorpicker.js' ,array('jquery'),EVO()->version, true);
		wp_register_style( 'colorpicker_styles',EVO()->assets_path.'lib/colorpicker/colorpicker_styles.css','',EVO()->version);
	}

}