<?php 
/**
 * EventCard Related Events html content
 * @version 2.3
 * @version 4.8
 */


?>
<div class='evo_metarow_rel_events evorow evcal_evdata_row'>
	<span class='evcal_evdata_icons'><i class='fa <?php echo esc_html( get_eventON_icon('evcal__fai_relev', 'fa-calendar-plus',$evOPT ) );?>'></i></span>
	<div class='evcal_evdata_cell'>
		<h3 class='evo_h3'><?php echo esc_html( evo_lang('Related Events') );?></h3>
		<div class='evcal_cell_rel_events'>
		<?php

		$rel_events = array();

		// each related events
		foreach($events as $I=>$N){
			$id = explode('-', $I);
			$EE = new EVO_Event($id[0]);
			$x = isset($id[1])? $id[1]:'0';
			$time = $EE->get_formatted_smart_time($x);
			
			$__a_class = $img_content = '';

			$hex = $EE->get_hex();

			// if event image to be visible
			if( !$EVENT->check_yn('_evo_relevs_hide_img')){
				
				$imgs = $EE->get_image_urls();
				if($imgs){
					$__a_class = 'hasimg';
					$img_content = "<span class='img' style='background-image: url(". $imgs['full']."); '></span>";
				}
			}

			$index = $I .'.'. $EE->get_start_time();

			$rel_events[ $index ] =  
				"<a class='{$__a_class}' style=' ' href='". $EE->get_permalink($x). "' >
				{$img_content}												
				<span class='evomar15'>
					<h4 class='evo_h4'>{$N}</h4>
					<em><i class='fa fa-clock-o'></i> {$time}</em>
				</span>

				</a>";
		}

		//krsort($rel_events);
		foreach($rel_events as $html){
			echo wp_kses_post( $html );
		}
		?>
		</div>
	</div>
</div>
<?php