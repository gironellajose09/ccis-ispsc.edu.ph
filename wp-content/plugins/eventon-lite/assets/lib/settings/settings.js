/**
 * EventON Settings scripts
 * @version  2.4
 */
jQuery(document).ready(function($){

	init();
	const BB = $('body');

	function init() {
	    var hash = window.location.hash;
	    if (!hash) return;

	    var hashId = hash.split('#')[1];
	    $('.nfer').hide();
	    $('#setting_' + hashId).show();
	    change_tab_position($('a[data-c_id=' + hashId + ']'));
	}


// header save changes button
	// Function to get query parameter by name
	function getQueryParam(name) {
	    let urlParams = new URLSearchParams(window.location.search);
	    return urlParams.get(name);
	}

	$('body').on('click','.evo_trig_form_save',function(event){
		event.preventDefault();
		//$('body').find('.evo_settings_form').submit();

		var el = $(this);
		
		var form = $('body').find('.evo_settings_box form');		

	    // Serialize form data with proper handling for checkbox arrays
	    var formData = form.serializeArray();

	    // Convert form data to JSON, handling checkboxes with multiple values
	    var dataObject = {};
	    
	    formData.forEach(function(item) {
	        var name = item.name;
	        var _is_item_arr = false;

	        // Strip [] from checkbox field names
	        if (name.endsWith('[]')) {

	        	if( item.value === undefined ) return;

	        	_is_item_arr = true;
	            name = name.slice(0, -2);
	        }

	        // Handle array fields
	        if ( _is_item_arr ) {
	            if (Array.isArray(dataObject[name])) {
	                dataObject[name].push(item.value);
	                //console.log(dataObject[name]);
	            } else {
	                dataObject[name] = [ item.value];
	                //console.log(dataObject[name]);
	            }
	        } else {
	            dataObject[name] = item.value;
	        }
	    });

	    var jsonData = JSON.stringify(dataObject);

		el.evo_admin_get_ajax({
			adata:{
				a:'eventon_general_settings_save',
				data: {
					formData: jsonData,
					lang: $('body').find('.evo_lang_selection').val(),	
					page: 	getQueryParam('page'),	
					evoajax: evoajax.nonce,			
				},
				loader_btn_el:true,
				show_snackbar: {duration:2000},
			},
			uid:'evo_save_lang_settings',
			onSuccess:function( OO, data, LB){
				// Check if the message div already exists
			    if ($('.evo_updated.updated.fade').length === 0) {
			        var messageDiv = $("<div class='evo_updated updated fade'><p>Settings Saved</p></div>").insertBefore(this);

			        setTimeout(function() {
			            // Remove message
			            messageDiv.remove();
			        }, 5000);
			    }
			}
		} );
	});

// Settings
	// webhooks
		$.fn.evo_webhooks = function (options){

			var init = function(){
				interaction();
			}

			var populate_wh_fields = function(){
				LB = BB.find('.evo_lightbox.evo_webhooks_config');

				const whdata = LB.find('.evo_elm_webhooks_data').data('whdata');
				var selected_key = LB.find('select').val();

				var new_content = 'n/a';
				if( whdata !== undefined && selected_key in whdata ) new_content = whdata[ selected_key ];

				LB.find('.evo_whdata_fields').html( new_content );
			}
			var interaction = function(){				

				BB.on('evo_ajax_success_evo_webhook_config',function(event, OO, data, el){
					populate_wh_fields();

					LB = BB.find('.evo_lightbox.evo_webhooks_config');

					LB.find('select.wh_trigger_point').on('change',function(){
						populate_wh_fields();
					});
				});

				// delete
				$('body').on('click','.evowh_del',function(){
					wh_id = $(this).closest('p').data('id');
					
					var dataajax = {};
					dataajax['id']= $(this).closest('p').data('id');
					dataajax['action']= 'evo_webhook_delete';
					const PAR = BB.find('#evowhs_container');

					$.ajax({
						beforeSend: function(){ PAR.addClass('evoloading');},
						url:	the_ajax_script.ajaxurl,
						data: 	dataajax,	dataType:'json', type: 	'POST',
						success:function(data){
							if( data.status == 'good'){
								$('body').find('#evowhs_container').html( data.html );
							}else{

							}
						},
						complete:function(){ PAR.removeClass('evoloading');	}
					});
				});

			}
			init();
		}
		$('#ajde_customization').evo_webhooks();


// Other
	// remove extra save changes button @since 4.2
		$('body').find('.evo_diag').each(function(){
			if(!($(this).hasClass('actual')) ) $(this).remove();
		});
	// colpase menu
		$('.ajde-collapse-menu').on('click', function(){
			if($(this).hasClass('close')){
				$(this).parent().removeClass('mini');
				$(this).closest('.ajde_settings').removeClass('mini');
				$('.evo_diag').removeClass('mini');
				$(this).removeClass('close');
			}else{
				$(this).closest('.ajde_settings').addClass('mini');
				$(this).parent().addClass('mini');
				$('.evo_diag').addClass('mini');
				$(this).addClass('close');
			}
		});

	// switching between tabs
		$('#acus_left').find('a').click(function(){

			var nfer_id = $(this).data('c_id');
			$('.nfer').hide();
			$('#setting_'+nfer_id).show();
			
			change_tab_position($(this));

			window.location.hash = nfer_id;

			if(nfer_id=='evcal_002'){
				$('#resetColor').show();
			}else{
				$('#resetColor').hide();
			}
			
			return false;
			
		});

		// position of the arrow
		function change_tab_position(obj){

			// class switch
			$('#acus_left').find('a').removeClass('focused');
			obj.addClass('focused');

			var menu_position = obj.position();
			$('#acus_arrow').css({'top':(menu_position.top+3)+'px'}).show();
		}

		// RESET colors
		$('#resetColor').on('click',function(){
			$('.colorselector ').each(function(){
				var item = $(this).siblings('input');
				item.attr({'value': item.attr('default') });
			});
			$('body').evo_snackbar({'message':'Default colors applied.'});
		});


	// hideable section
		$('body').on('click','.evo_hideable_show',function(){
			var O = $(this);
			var cv = O.html();
			O.html( O.data('t')).data('t', cv);
			
			const fc = O.parent().next('p.field_container');
			const I = fc.find('input');			
			I.attr('type') == 'password' ? I.attr('type','text') : I.attr('type','password');
		});
	
	// multicolor title/name display
		$('.row_multicolor').on('mouseover','em',function(){
			var name = $(this).data('name');
			$(this).closest('.row_multicolor').find('.multicolor_alt').html(name);
		});
		$('.row_multicolor').on('mouseout','em',function(){
			$(this).closest('.row_multicolor').find('.multicolor_alt').html(' ');
		});	
	
	//legend
		$('.legend_icon').hover(function(){
			$(this).siblings('.legend').show();
		},function(){
			$(this).siblings('.legend').hide();
		});
		
	// image
		if($('.ajt_choose_image').length>0){
			var _custom_media = true,
			_orig_send_attachment = wp.media.editor.send.attachment;
		}
		
		$('.ajt_choose_image').click(function() {
			var send_attachment_bkp = wp.media.editor.send.attachment;
			var button = $(this),
				imagesection = button.parent();

			//var id = button.attr('id').replace('_button', '');
			_custom_media = true;
			wp.media.editor.send.attachment = function(props, attachment){
				if ( _custom_media ) {
					//console.log(attachment);

					imagesection.find('.ajt_image_id').val(attachment.id);					
					imagesection.find('.ajt_image_holder img').attr('src',attachment.url);
					imagesection.find('.ajt_image_holder').fadeIn();
					button.fadeOut();

					//$("#"+id).val(attachment.url);
				} else {
					return _orig_send_attachment.apply( this, [props, attachment] );
				};
			}

			wp.media.editor.open(button);
			return false;
		});
		$('.add_media').on('click', function(){
			_custom_media = false;
		});

		// removre image
		$('.ajde_remove_image').click(function() {  
			imagesection = $(this).closest('p');
			imagesection.find('.ajt_image_id').val('');					
			imagesection.find('.ajt_image_holder').fadeOut();
			imagesection.find('.ajt_choose_image').fadeIn();
	        return false;  
	    });
	

	// hidden section
		$('.ajdeSET_hidden_open').click(function(){
			$(this).next('.ajdeSET_hidden_body').toggle();
			if( $(this).hasClass('open')){
				$(this).removeClass('open')
			}else{
				$(this).addClass('open');
			}
		});	

	// sortable		
		$('.ajderearrange_box').sortable({		
			update: function(e, ul){
				var sortedID = $(this).sortable('toArray',{attribute:'val'});
				BOX = $(this).closest('.ajderearrange_box');
				BOX.siblings('.ajderearrange_order').val(sortedID);
				update_card_hides( BOX );
			}
		});
		
		// hide sortables
			$('.ajderearrange_box').on('click','span',function(){
				$(this).toggleClass('hide');
				BOX = $(this).closest('.ajderearrange_box');
				update_card_hides( BOX );
			});

			function update_card_hides(BOX){
				hidethese = '';
				BOX.find('span').each(function(index){
					if(!$(this).hasClass('hide')){
						FIELDNAME = $(this).parent().attr('val');
						console.log(FIELDNAME);
						hidethese += FIELDNAME+',';
					}
				});

				BOX.siblings('.ajderearrange_selected').val(hidethese);
			}
		
	// at first run a check on list items against saved list -
		var items='';
		$('#ajdeEVC_arrange_box').find('p').each(function(){
			if($(this).attr('val')!='' && $(this).attr('val')!='undefined'){
				items += $(this).attr('val')+',';
			}
		});
		$('.ajderearrange_order').val(items);	
});