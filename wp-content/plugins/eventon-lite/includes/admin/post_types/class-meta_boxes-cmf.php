<?php
/**
 *	Event edit custom meta field data
 *	@version 2.3
 */

$metabox_array = array();
$p_id = get_the_ID();

$EVENT = new EVO_Event( $p_id );

// Custom Meta fields for events
	$_has_cmf = false;
	$num = evo_calculate_cmd_count( EVO()->cal->get_op('evcal_1') );
	for($x =1; $x<=$num; $x++){	
		if(!eventon_is_custom_meta_field_good($x)) continue;

		$_has_cmf = true;

		$fa_icon_class = EVO()->cal->get_prop('evcal__fai_00c'.$x);		

		$visibility_type = (!empty($evcal_opt1['evcal_ec_f'.$x.'a4']) )? $evcal_opt1['evcal_ec_f'.$x.'a4']:'all' ;

		$metabox_array[] = array(
			'id'=>'evcal_ec_f'.$x.'a1',
			'variation'=>'customfield',
			'name'=>	EVO()->cal->get_prop('evcal_ec_f'.$x.'a1'),		
			'iconURL'=> $fa_icon_class,
			'iconPOS'=>'',
			'x'=>$x,
			'visibility_type'=>$visibility_type,
			'type'=>'code',
			'content'=>'',
			'slug'=>'evcal_ec_f'.$x.'a1'
		);
	}

if( $_has_cmf ):
	echo "<div class='evo_metaboxes_cmf evopad20'>";

	EVO()->elements->get_element( array(
		'type'=>'detailed_button', '_echo'=> true,
		'name'=>__('Custom Meta Fields','evotx'),
		'description'=>__('Configure Custom Meta Fields settings','evotx'),
		'field_after_content'=> "Configure",
		'row_class'=>'evo_bordern evomar0',
		'trig_data'=> array(
			'uid'=>'evo_cmf_settings',
			'lb_class' =>'config_cmf_data',
			'lb_title'=> __('Configure Custom Meta Fields Settings','evotx'),	
			'ajax_data'=>array(
				'a'=>'eventon_get_secondary_settings',
				'event_id'=>		$EVENT->ID,
				'setitngs_file_key' => 'cmf_settings',
			),
		),
	));
	
	echo "</div>";
else:
	echo '<p class="pad20"><span class="evomarb10" style="display:block">' . __('You do not have any custom meta fields activated.') . '</span><a class="evo_btn" href="'. get_admin_url(null, 'admin.php?page=eventon#evcal_009','admin') .'">'. __('Activate Custom Meta Fields','eventon') . '</a></p>';
endif;
