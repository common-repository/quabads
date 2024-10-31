( function( blocks, editor, i18n, element, components,  _ ) {
	var el = element.createElement;
	var RichText = editor.RichText;
	var BlockControls = components.SelectControl;
    var prefix = "quabads";
    var uniqueId = null;
    function getUniqueName() {
        if (!uniqueId) uniqueId = (new Date()).getTime();
        return (prefix || 'id') + (uniqueId++);
    }
    var image_link = null;
	const Icon = el('svg',{width:24, height:24, viewBox:'0 0 24 24'},
                 el('g',{},
                   el('path',{style:{fill:'#1395BA'}, d:'M23.5,21.6l-2.5-0.6h-5c1.2-0.1,2.3-0.6,3.3-1.2l1.5,0.3l1.4,0.3L21.7,19l-0.4-1.1c0.8-1.1,1.2-2.4,1.3-3.9v5.1L23.5,21.6z'}),
                   el('path',{style:{fill:'#082F41'}, d:'M22.6,3.1V14c-0.1,1.4-0.6,2.8-1.3,3.9l0.4,1.1l0.5,1.5l-1.4-0.3l-1.5-0.3c-1,0.7-2.1,1.1-3.3,1.2H2.2c-0.5,0-0.9-0.4-0.9-0.9v-17c0-0.5,0.4-0.9,0.9-0.9h19.5c0.1,0,0.2,0,0.3,0.1C22.4,2.4,22.6,2.8,22.6,3.1z'}),
                   el('path',{style:{fill:'#FFFFFF'}, d:'M22.6,14c-0.1,1.4-0.6,2.8-1.3,3.9l0.4,1.1l0.5,1.5l-1.4-0.3l-1.5-0.3c-1,0.7-2.1,1.1-3.3,1.2c-0.3,0-0.6,0.1-0.9,0.1c-0.3,0-0.6,0-0.9-0.1c-3.8-0.4-6.7-3.6-6.7-7.5c0-4.2,3.4-7.6,7.6-7.6c4,0,7.3,3.1,7.5,7.1c0,0.1,0,0.3,0,0.4C22.6,13.7,22.6,13.9,22.6,14z'}),
                   el('circle',{style:{fill:'#F16C20'}, cx:'15.1', cy:'13.6', r:'5'})
                   )
                 );
    
	blocks.registerBlockType( 'quabads/quabads-banner-block', {
		title: i18n.__( 'QuabAds', 'quabads' ),
		icon: Icon,
		category: 'shortcode',
        keywords: [ 'QuabAds', 'Ad', 'Banner' ],
		attributes: {
			shortcode: {
				type: 'string',
				source: 'text',
			},
		},
        
		edit: function( props ) {
			var attributes = props.attributes;
            
            function getImgLnk(short_code){
                var link;
                switch(short_code){
                    case '[quabads size="banner"]': 
                    image_link = 'https://asset.quabads.com/default/46860';
                    break;
                    case '[quabads size="billboard"]': 
                    image_link = 'https://asset.quabads.com/default/970250';
                    break;
                    case '[quabads size="half_page"]': 
                    image_link = 'https://asset.quabads.com/default/300600';
                    break;
                    case '[quabads size="inline_rectangle"]': 
                    image_link = 'https://asset.quabads.com/default/300250';
                    break;
                    case '[quabads size="large_leaderboard"]': 
                    image_link = 'https://asset.quabads.com/default/97090';
                    break;
                    case '[quabads size="large_rectangle"]': 
                    image_link = 'https://asset.quabads.com/default/336280';
                    break;
                    case '[quabads size="mobile_large_banner"]': 
                    image_link = 'https://asset.quabads.com/default/320100';
                    break;
                    case '[quabads size="leaderboard"]': 
                    image_link = 'https://asset.quabads.com/default/72890';
                    break;
                    case '[quabads size="mobile_leaderboard"]': 
                    image_link = 'https://asset.quabads.com/default/32050';
                    break;
                    case '[quabads size="small_square"]': 
                    image_link = 'https://asset.quabads.com/default/200200';
                    break;
                    case '[quabads size="skyscraper"]': 
                    image_link = 'https://asset.quabads.com/default/120600';
                    break;
                    case '[quabads size="square"]': 
                    image_link = 'https://asset.quabads.com/default/250250';
                    break;
                    case '[quabads size="wide_skyscraper"]': 
                    image_link = 'https://asset.quabads.com/default/160600';
                    break;
                    default : 
                    image_link = 'https://asset.quabads.com/default/46860';
                }
                return image_link;
            }
            
            var selected = attributes.shortcode;
            image_link = getImgLnk(selected);
            var selectionChange = function( event ) {
			var parent_wrap = event.target.parentElement.parentElement;	
                var selectInput = event.target.previousSibling;
                var myNode = parent_wrap.parentElement;
                image_link = getImgLnk(selectInput.options[selectInput.selectedIndex].value);
                props.setAttributes( {shortcode: selectInput.options[selectInput.selectedIndex].value,} ); 
               parent_wrap.nextSibling.firstChild.firstChild.firstChild.setAttribute('src',''); parent_wrap.nextSibling.firstChild.firstChild.firstChild.setAttribute('src',image_link);
                parent_wrap.nextSibling.setAttribute('style','display:block;');
                parent_wrap.setAttribute('style','display:none;');
                document.getElementById(select_id).value = selected;
              
            };
            var select_id = getUniqueName();         
            var formSubmit = function (event){
                event.preventDefault();
            };
            
            var viewModeClick = function(event){ 
               var parent_wrap = event.target.parentElement.parentElement.parentElement; parent_wrap.previousSibling.setAttribute('style','display:block;');
               parent_wrap.setAttribute('style','display:none;'); 
               document.getElementById(select_id).value = selected;
            };
            
            return (
                el('div', {className:props.className}, 
                    el( 'div', { className: 'quabads-edit-mode',},
                       el('div',{className: 'label-cont'},
                         Icon,
                         el('span',{className: 'label-value'},i18n.__( 'QuabAds', 'quabads' ))
                         ),
                       el('p',{},i18n.__( 'Select Banner size to embed', 'quabads')),
                       el('form',{ onSubmit: formSubmit },
                          el( "select", { className: 'main-editor-wrap', id: select_id},
                            el("option", {value: '[quabads size="banner"]' }, "Banner - 468 x 60"),
                            el("option", {value: '[quabads size="billboard"]' }, "Billboard - 970 x 250"),
                            el("option", {value: '[quabads size="half_page"]' }, "Half-Page Ad - 300 x 600"),
                            el("option", {value: '[quabads size="inline_rectangle"]' }, "Inline Rectangle - 300 x 250"),
                            el("option", {value: '[quabads size="large_leaderboard"]' }, "Large Leaderboard - 970 x 90"),
                            el("option", {value: '[quabads size="mobile_large_banner"]' }, "Large Mobile Banner - 320 x 100"),
                            el("option", {value: '[quabads size="large_rectangle"]' }, "Large Rectangle - 336 x 280"),
                            el("option", {value: '[quabads size="leaderboard"]' }, "Leaderboard - 728 x 90"),
                            el("option", {value: '[quabads size="mobile_leaderboard"]' }, "Mobile leaderboard - 320 x 50"),
                            el("option", {value: '[quabads size="small_square"]' }, "Small Square - 200 x 200"),
                            el("option", {value: '[quabads size="skyscraper"]' }, "Skyscraper - 120 x 600"),
                            el("option", {value: '[quabads size="square"]' }, "Square - 250 x 250"),
                            el("option", {value: '[quabads size="wide_skyscraper"]' }, "Wide Skyscraper - 160 x 600")
                          ),
                          el('button',{type:'submit', 
                                       className:'submit-btn',
                                       onClick: selectionChange},i18n.__( 'Embed', 'quabads' ))
                         )
                    ),
                    el('div',{ className: 'quabads-view-mode', onClick:viewModeClick,},
                      el('div',{className: 'view-window'},
                          el('a',{ id: 'quabads-ad-anchor', className: 'ad-link-frame'},
                             el('img',{src: image_link, alt:'banner image'}),
                            )
                         ),
                      ),   
                 )
            );
		},
		
        save: function( props ) {
			var attributes = props.attributes;

			return (
				attributes.shortcode
			);
		},
	} );

} )(
	window.wp.blocks,
	window.wp.editor,
	window.wp.i18n,
	window.wp.element,
	window.wp.components,
	window._
);
