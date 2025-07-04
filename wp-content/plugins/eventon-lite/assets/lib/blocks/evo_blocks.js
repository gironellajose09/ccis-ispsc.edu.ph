/**
EventON Gutenberg Blocks
@version 4.4
**/


jQuery(document).ready(function($){ 

// svg icon
    var svg_d = "M24.102 1.227h-16.578c-3.596 0-6.511 2.915-6.511 6.511v16.578c0 3.596 2.915 6.511 6.511 6.511h16.578c3.596 0 6.511-2.915 6.511-6.511v-16.578c0-3.596-2.915-6.511-6.511-6.511zM11.467 6.88h0.381l0.896 1.44h0.008c-0.002-0.053 0.002-0.095-0.004-0.216s-0.001-0.221-0.001-0.288v-0.936h0.267v1.813h-0.372l-0.899-1.44h-0.010l-0.005 0.070c0.012 0.155 0.006 0.285 0.006 0.413v0.957h-0.267v-1.813zM9.603 7.072c0.149-0.162 0.361-0.242 0.637-0.242 0.272 0 0.482 0.082 0.63 0.246s0.223 0.395 0.223 0.694c0 0.298-0.074 0.529-0.223 0.694s-0.359 0.248-0.632 0.248c-0.276 0-0.488-0.082-0.636-0.246s-0.222-0.396-0.222-0.698 0.074-0.533 0.223-0.695zM7.093 6.88h0.435l0.508 1.44h0.008l0.522-1.44h0.447v1.813h-0.32v-0.911c0-0.091 0.006-0.217 0.011-0.363s0.012-0.22 0.016-0.273h-0.010l-0.549 1.547h-0.264l-0.53-1.547h-0.010c0.014 0.267 0.003 0.452 0.003 0.651v0.896h-0.267v-1.813zM14.453 23.52h-6.773v-1.173h1.714c0.21 0 0.391-0.062 0.483-0.153 0.111-0.109 0.149-0.275 0.149-0.384v-8.112c0-0.054-0.027-0.127-0.12-0.208-0.077-0.067-0.202-0.102-0.341-0.102h-2.206v-1.127l0.243-0.032c0.917-0.117 1.613-0.261 2.132-0.44 0.48-0.166 0.957-0.399 1.419-0.707l0.070-0.041h0.776v10.993c0 0.070 0.012 0.135 0.054 0.166 0.122 0.089 0.264 0.148 0.437 0.148h1.962v1.173zM24.311 23.52h-8.258v-0.863l2.582-2.932c1.443-1.568 2.235-2.467 2.496-2.83 0.384-0.532 0.666-1.051 0.839-1.542 0.172-0.486 0.259-0.93 0.259-1.321 0-0.635-0.166-1.115-0.506-1.468-0.337-0.35-0.802-0.52-1.422-0.52-0.713 0-1.298 0.169-1.739 0.502-0.416 0.315-0.627 0.638-0.627 0.961 0 0.061 0.011 0.108 0.028 0.126l0.008 0.008c0.001 0.001 0.059 0.053 0.308 0.115 0.895 0.213 1.084 0.819 1.084 1.29 0 0.37-0.127 0.682-0.377 0.929-0.249 0.245-0.568 0.37-0.948 0.37-0.438 0-0.821-0.199-1.14-0.591-0.305-0.374-0.459-0.861-0.459-1.448 0-0.636 0.167-1.224 0.495-1.747 0.328-0.521 0.824-0.952 1.476-1.281 0.645-0.326 1.348-0.491 2.089-0.491 0.735 0 1.428 0.161 2.059 0.478 0.639 0.321 1.127 0.751 1.452 1.279s0.489 1.112 0.489 1.736c0 0.427-0.081 0.865-0.24 1.302-0.159 0.435-0.394 0.847-0.699 1.225-0.501 0.625-1.026 1.168-1.56 1.614l-2.267 1.91c-0.467 0.393-0.831 0.739-1.085 1.033 0.305 0.085 1.158 0.208 3.542 0.208 0.34 0 0.575-0.077 0.698-0.227 0.084-0.103 0.27-0.454 0.57-1.725l0.052-0.207h1.125l-0.324 4.107zM10.238 8.456c0.178 0 0.311-0.057 0.402-0.172s0.136-0.286 0.136-0.514c0-0.224-0.045-0.394-0.134-0.51s-0.223-0.174-0.401-0.174c-0.179 0-0.314 0.058-0.406 0.174s-0.137 0.286-0.137 0.51c0 0.225 0.045 0.396 0.136 0.512s0.225 0.174 0.404 0.174z";
    

var el = wp.element.createElement;
const evo_icon = el('svg',
    {width:32, height: 32, viewBox:'0 0 32 32'},
    el('path', {'d': svg_d})
);
var blockStyle = { backgroundColor: 'transparent', color: '#808080', padding: '20px', 'border-radius':'5px' };
var blockEditor = wp.blockEditor;
var components = wp.components;
var shortcode = wp.shortcode;

wp.blocks.registerBlockType( 'eventon-blocks/evo-eventon-main', {
    title: 'EventON',
    icon: evo_icon,
    category: 'eventon',  
    description: 'All EventON Calendar Block using EventON Shortcode Generator', 
    supports: {   html: false,  },
    example:{
        attributes: {
            isPreview: false,
        },
    },
    attributes: {
        blockId: {type:'string'},
        shortcode: { type: 'string',  default: '[add_eventon]'  },
        visible: {  type: 'boolean', default: true  },
    },
    edit: function( props ) {
        const {
            content,
            applyStyles,
            alignment                
        } = props.attributes;
        
        const {clientId , setAttributes } = props;
        const blockId = props.blockId;
        
        if(!blockId || blockId === undefined ) setAttributes( { blockId: clientId } );
        
        var ATTR = props.attributes;
                        
        // when shortcode gen values saved
            $('body').on('evo_shortcode_generator_saved',function(event, code, data){  
                if( data.type == 'block' && data.other_id == ATTR.blockId){                       
                    setAttributes( { shortcode : code});
                }  
            });
        
        return [
            // block button > open shortcode generator
            el(
                blockEditor.BlockControls, {key:'controls'},
                el('div', {className:'evotoolbar components-toolbar'},
                    el('p',
                        {
                            className:'evo_gb_shortcode_gen',
                            'data-sc': ATTR.shortcode,
                            sc: ATTR.shortcode,
                            style:{margin:'0',fontSize:'14px',lineHeight:'1',display:'flex',alignItems:'center',padding:'5px',cursor:'pointer',width:'190px',justifyContent:'center'},
                            onClick: function(item){
                                $('body').trigger('evo_trigger_shortcodegenerator', 
                                    [ATTR.shortcode, 'block', ATTR.blockId] );
                            }
                        },
                        '[ Shortcode Generator ]'
                    )
                )
            ),
            // inspector controls
            el(
                blockEditor.InspectorControls, {key:'controls'},
                el(components.PanelBody,
                    {   title:'Shortcode Controls',  initialOpen: true,   },
                    el(
                        components.TextareaControl,{
                            label:'Editable Calendar Shortcode',
                            help:'You may have to re-select eventON block to see updated shortcode.',
                            value: ATTR.shortcode,
                            onChange: (value) => {
                                setAttributes( { shortcode : value}); 
                            }
                        }
                    ),
                ),   
            ),
            // inside block
            el(
                'div',{
                    id:'evo_main',
                    className:'evo_main_cal_block',
                },
                el('span',{},'EventON Calendar'),
                el('span',{
                    className:'evogb_show_sc',
                    onClick: function(item){
                        //$(item.target).siblings('.evogb_sc_code').html( ATTR.shortcode ).toggle();
                        $(item.target).siblings('.evogb_sc_code').toggle();
                    }
                },'Show Shortcode'),
                el('span',{
                    style:{display:'none'},
                    className:'evogb_sc_code'
                },ATTR.shortcode),
            ),
        ];
        //return el( 'p', { style: blockStyle }, 'Basic EventON Calendar' );
    },   
    save: function({ attributes }) {
        return el('div', { className: 'wp-block-eventon-lite', style: blockStyle }, attributes.shortcode);
    }
} );



});


