<?php 
/**
 * Event Edit Meta box Organizer
 * @version 2.3
 * @fullversion 4.7.4
 */

?>
<div class='evcal_data_block_style1'>
	<p class='edb_icon evcal_edb_map'></p>
	<div class='evcal_db_data'>
		<div class='evcal_location_data_section'>
			<div class='evo_singular_tax_for_event event_organizer' >
			<?php
				echo EVO()->taxonomies->get_meta_box_content( 'event_organizer', esc_attr( $p_id ), esc_html__('organizer','eventon'));
			?>
			</div>										
        </div><!--.evcal_location_data_section-->

        <?php
        EVO()->elements->print_process_multiple_elements(
			array(
				array(
					'type'=>'yesno_btn',
					'label'=> esc_html__('Hide Organizer field from EventCard','eventon'),
					'id'=>'evo_evcrd_field_org',
					'value'=> esc_attr( $EVENT->get_prop('evo_evcrd_field_org') ),
				),
				array(
					'type'=>'yesno_btn',
					'label'=> esc_html__('SEO: Use organizer information to also populate performer schema data for this event.','eventon'),
					'id'=>'evo_event_org_as_perf',
					'value'=> esc_attr( $EVENT->get_prop('evo_event_org_as_perf') ),
				),
			)
		);
        ?>
	</div>
</div>
<?php