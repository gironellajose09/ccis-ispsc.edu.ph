<?php 
/**
 * Add to calendar
 * @version 2.3
 * @fullversion 4.7.4
 */



// ics link
	$event_id = $EVENT->ID;  // Example event ID
	$repeat_interval_index = $EVENT->ri;  // Example repeat interval index
	$nonce = wp_create_nonce('export_event_nonce');
	$__ics_url = home_url("/export-events/{$event_id}_{$repeat_interval_index}/?nonce={$nonce}");



	$O = (object)array(
		'location_name'=> !empty($location_name)?$location_name:'',
		'location_address'=> !empty($location_address)?$location_address:'',
		'etitle'=> $event_title,
		'excerpt'=> $event_excerpt_txt
	);

	
	$__googlecal_link = $EVENT->get_addto_googlecal_link(
		$O->location_name,
		$O->location_address
	);


// which options to show for add to calendar
	$addCaloptions = !empty($evOPT['evo_addtocal'])? $evOPT['evo_addtocal']: 'all';
	$addCalContent = '';

// add to cal section
	switch($addCaloptions){
		case 'ics':
			$addCalContent = "<a href='{$__ics_url}' class='evo_ics_nCal' title='".eventon_get_custom_language($evoOPT2, 'evcal_evcard_addics','Add to your calendar')."'>".eventon_get_custom_language($evoOPT2, 'evcal_evcard_calncal','Calendar')."</a>";
		break;
		case 'gcal':
			$addCalContent = "<a href='". $__googlecal_link. "' target='_blank' class='evo_ics_gCal' title='".eventon_get_custom_language($evoOPT2, 'evcal_evcard_addgcal','Add to google calendar')."'>".eventon_get_custom_language($evoOPT2, 'evcal_evcard_calgcal','GoogleCal')."</a>";
		break;
		case 'all':
			$addCalContent = "<a href='{$__ics_url}' class='evo_ics_nCal' title='".eventon_get_custom_language($evoOPT2, 'evcal_evcard_addics','Add to your calendar')."'>".eventon_get_custom_language($evoOPT2, 'evcal_evcard_calncal','Calendar')."</a>".
				"<a href='{$__googlecal_link}' target='_blank' class='evo_ics_gCal' title='".eventon_get_custom_language($evoOPT2, 'evcal_evcard_addgcal','Add to google calendar')."'>".eventon_get_custom_language($evoOPT2, 'evcal_evcard_calgcal','GoogleCal')."</a>";
		break;
	}

if( $addCaloptions != 'none'){
	echo "<div class='evo_metarow_ICS evorow evcal_evdata_row'>
			<span class='evcal_evdata_icons'><i class='fa ".get_eventON_icon('evcal__fai_008', 'fa-calendar',$evOPT )."'></i></span>
			<div class='evcal_evdata_cell'>
				<p>{$addCalContent}</p>	
			</div>
		</div>";
}