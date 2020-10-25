fields = new Object();

document = window.document;

fields.initInputDigit = function(){
    jQuery(function(){
        jQuery('input[type="text"].my-field-digit').bind( 'keyup' , function(){
            var value = jQuery( this ).val()
            jQuery( this ).val( value.replace( /[^\d|\.|\,]/g , '' ) );
        });    
    });
}

fields.initInputPickColor = function(){
    
    jQuery(document).ready(function(){
        jQuery('input.my-field.my-field-pickColor').each(function( index ) {
            var frb;
            var self = this;
            (function( $ , window ){
                var pickColor = function( a ) {
                    frb.setColor( a );
                    jQuery( '#my-field-' + jQuery( self ).attr( 'op_name' ) ).val( a );
                    jQuery( '#link-pick-' + jQuery( self ).attr( 'op_name' ) ).css( 'background-color' , a );
                    if( jQuery( self ).attr( 'rel' ) !== undefined ){
                        var attr = jQuery( self ).attr( 'rel' );
                        (function( attr ){
                            var fn_attr = function(){
                                try{
                                    eval( attr );
                                }catch ( e ){
                                    if (e instanceof SyntaxError) {
                                        console.log( e.message );
                                    }
                                }
                            };
                            fn_attr( attr );
                        })( attr );
                    }
                };

                frb = jQuery.farbtastic( '#color-panel-'  + jQuery( self ).attr( 'op_name' ) , pickColor );

                pickColor( jQuery( '#my-field-' + jQuery( self ).attr( 'op_name' ) ).val() );

                jQuery( '#link-pick-' + jQuery( self ).attr( 'op_name' ) ).click( function( e ) {
                    jQuery( '#color-panel-'  + jQuery( self ).attr( 'op_name' ) ).show();
                    e.preventDefault();
                });

                jQuery( '#my-field-' + jQuery( self ).attr( 'op_name' ) ).keyup( function() {
                    var a = jQuery( '#my-field-' + jQuery( self ).attr( 'op_name' ) ).val();
                    var b = a;
                    
                    a = a.replace( /[^a-fA-F0-9]/ , '');
                    if ( '#' + a !== b )
                        jQuery( '#' + jQuery( self ).attr( 'op_name' ) ).val( a );
                    if ( a.length === 3 || a.length === 6 )
                        pickColor( '#' + a );
                });

                jQuery(document).mousedown( function() {
                    jQuery('#color-panel-'  + jQuery( self ).attr( 'op_name' ) ).hide();
                });
                
            })( jQuery , window );
        });
    });
}

fields.clean = function( selector ){
    jQuery(function(){
        jQuery( selector + ' input[type="text"]' ).each(function(){
            jQuery( this ).val('');
        });
        jQuery( selector + ' input[type="hidden"]' ).each(function(){
            jQuery( this ).val('');
        });
        jQuery( selector + ' input[type="checkbox"]' ).each(function(){
            jQuery( this ).removeAttr('checked');
        });
        jQuery( selector + ' select' ).each(function(){
            jQuery( this ).removeAttr('selected');
        });
        jQuery( selector + ' textarea' ).each(function(){
            jQuery( this ).val('');
        });
    });
}

fields.limitString = function( obj , nr ){
    jQuery(function(){
        jQuery( obj ).val( jQuery( obj ).val().substr( 0 , nr ) );
    });
}

fields.limitWords = function( obj , nr ){
    jQuery(function(){
        jQuery( obj ).val( jQuery( obj ).val().split( ',' , nr ) );
    });
}

fields.check = function( obj , params ){
    jQuery(function(){
        if( jQuery( obj ).is(':checked') ){
            jQuery( obj ).parent().children('input[type="hidden"]').val( 1 );
            jQuery( params.t ).show('slow');
            jQuery( params.f ).hide('fast');
        }else{
            jQuery( obj ).parent().children('input[type="hidden"]').val( 0 );
            jQuery( params.t ).hide('fast');
            jQuery( params.f ).show('slow');
        }
    });
}

fields.checkButton = function( obj , data ){
    jQuery(function(){
        if( jQuery( obj ).parent().children('input[type="hidden"]').val() == 1 ){
            jQuery( obj ).parent().children('input[type="hidden"]').val( 0 );
            jQuery( obj ).val( data.labels[ 0 ] );
        }else{
            jQuery( obj ).parent().children('input[type="hidden"]').val( '1' );
            jQuery( obj ).val( data.labels[ 1 ] );
        }
    });
}

fields.logicButton = function( params, fn , obj ){
    jQuery(function(){
        jQuery.post( ajaxurl , params , function( result ){
            var input = eval("(" + result + ")");
            jQuery( obj ).parent().children( 'span.message.logic-button' ).html( input.message );
             
            if( jQuery( obj ).parent().children( 'span.message.logic-button' ).hasClass( 'success' ) ){
                jQuery( obj ).parent().children( 'span.message.logic-button' ).removeClass( 'success' );
            }
            
            if( jQuery( obj ).parent().children( 'span.message.logic-button' ).hasClass( 'error' ) ){
                jQuery( obj ).parent().children( 'span.message.logic-button' ).removeClass( 'error' );
            }
            
            if( input.value ){
                jQuery( obj ).parent().children( 'span.message.logic-button' ).addClass( 'success' );
            }else{
                jQuery( obj ).parent().children( 'span.message.logic-button' ).addClass( 'error' );
            }
            
            jQuery( obj ).parent().children( 'span.message.logic-button' ).show();
            jQuery( obj ).parent().children( 'span.message.logic-button' ).fadeTo( 300 , 1 );
            
            if( !input.nofade ){
                jQuery( obj ).parent().children( 'span.message.logic-button' ).fadeTo( 3500 , 0 );
            }
            
            
            if( input.value == 2 ){
                input.value = 0
            }
            
            jQuery( obj ).parent().children( 'input[type="hidden"]' ).val( input.value );
            jQuery( obj ).val( input.label );
            
            fn( obj );
        });
    });
}

fields.ilogicButton = function( params, fn , obj ){
    jQuery(function(){
        jQuery.post( ajaxurl , params , function( result ){
            var input = eval("(" + result + ")");
            jQuery( obj ).parent().children( 'span.message.logic-button' ).html( input.message );
            
            if( input.value ){
                jQuery( obj ).parent().children( 'span.message.logic-button' ).addClass( 'success' );
            }else{
                jQuery( obj ).parent().children( 'span.message.logic-button' ).addClass( 'error' );
            }
            
            jQuery( obj ).parent().children( 'span.message.logic-button' ).show();
            
            if( input.value == 2 ){
                input.value = 0
            }
            
            jQuery( obj ).parent().children( 'input[type="hidden"]' ).val( input.value );
            jQuery( obj ).val( input.label );
            
            fn( obj );
        });
    });
}

fields.drop = function( key , option , obj ){
    jQuery(function(){
        var names = jQuery( obj ).parent().parent( 'form' ).serializeArray();
        jQuery.post( ajaxurl,
            {
                'action' : 'drop_settings',
                'key' : key,
                'option' : option,
                'names' : names
            },
            function(){
                jQuery( obj ).parent().parent().parent().parent().hide("slow");
            }
        );
            
    });
}


fields.box = function( selector ){
    jQuery(function(){
        if( jQuery( 'div#my-popup-container' ).length ){
            if( selector.length == 0 ){
                jQuery( 'body div#my-popup-container' ).toggle( 'fast' );
                return;
            }else{
                jQuery( 'div#my-popup-container' ).html( jQuery( selector ).html() );
            }
        }else{
            jQuery( 'body' ).append( '<div id="my-popup-container" class="hidden">' + jQuery( selector ).html() + '</div>' ); 
            jQuery( 'body div#my-popup-container div.popup-box-shadow' ).css( 'height' , jQuery( document ).height() + 'px' );
        }
        
        var bwidth = jQuery( 'body' ).width();
        var width = jQuery( 'body div#my-popup-container div.popup-box' ).width();
        var left = parseInt( ( bwidth - width ) / 2 );
        
        jQuery( 'body div#my-popup-container div.popup-box' ).css( {'left' : left + 'px'} );
        
        jQuery( 'body div#my-popup-container div.popup-box-shadow' ).click(function(){
            jQuery( 'body div#my-popup-container' ).toggle( 'fast' );
        });
        
        jQuery( 'body div#my-popup-container' ).toggle( 'slow' );
    });
}

fields.imageSelect = function(){
    jQuery(function(){
        jQuery( 'div.my-field.my-field-imageSelect' ).each(function(){
            var $container = this;
            jQuery( 'div.image-select-options span' , this ).click(function(){
                
                jQuery( 'div.image-select-options span' , $container ).removeClass( 'current' );
                if( !jQuery( this ).hasClass( 'current' ) )
                    jQuery( this ).addClass( 'current' );

                var $span = this;
                jQuery( 'input' , $container ).val( jQuery( $span ).attr( 'ref' ) );
                jQuery( 'span.preview-value img' , $container ).attr( "src" , jQuery( 'img' , $span ).attr( 'src' ) );
                
                if( jQuery( this ).parent().parent().attr( 'rel' ) !== undefined ){
                    
                    var data = eval( '(' + jQuery( this ).parent().parent().attr( 'rel' ) + ')' );
                    var args = data[ 1 ];
                    
                    if( data[ 0 ] == 'sh' ){
                        for (var key in args) {
                            if ( args.hasOwnProperty( key ) ) {
                                if( jQuery( this ).parent().parent().children('input').val().trim()  == key ){
                                    jQuery( args[ key ] ).show();
                                }else{
                                    jQuery( args[ key ] ).hide();
                                }
                            }
                        }
                    }else{
                        for (var key in args) {
                            if ( args.hasOwnProperty( key ) ) {
                                if( jQuery( this ).parent().parent().children('input').val().trim()  == key ){
                                    jQuery( args[ key ] ).hide();
                                }else{
                                    jQuery( args[ key ] ).show();
                                }
                            }
                        }
                    }
                }
            });
        });
    });
}


function my_uploader( selector ){
    jQuery(document).ready(function($){
        
        var custom_uploader;

        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media({
        //custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        custom_uploader.on( 'select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            
            var post_id = parseInt( jQuery('#post_ID' ).val() );
            
            if( post_id > 0 ){
                jQuery.post( ajaxurl, {
                        action: 'attach_to_post',
                        attachment_id: attachment.id,
                        post_id: post_id
                }).done( function( result ) {
                        console.log( result );
                });
            }
                            
            jQuery( selector ).val(attachment.url);
            
            
        });

        custom_uploader.open();

    });
}

function mythemes_callback( callback , params ){
    (function( c , p ) {
        try{
            c( p );
        }catch ( e ){
            if (e instanceof SyntaxError) {
                console.log( (e.message) );
            }
        }
    })( callback , params );
}

function my_uploader2( callback ){
    jQuery(document).ready(function($){
        
        var custom_uploader;

        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        custom_uploader = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        custom_uploader.on( 'select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            
            var post_id = parseInt( jQuery('#post_ID' ).val() );
            
            if( post_id > 0 ){
                jQuery.post( ajaxurl, {
                        action: 'attach_to_post',
                        attachment_id: attachment.id,
                        post_id: post_id
                }).done( function( result ) {
                });
            }

            mythemes_callback( callback , attachment );
        });

        custom_uploader.open();
    });
}

function params( sc , param ){
    var result;
    jQuery(function(){
        var obj = jQuery( 'div.my-sc-' + sc + '-settings' );
        result = jQuery( param , obj ).val();
    });
    
    return result;
}

/* INIT ALL FIELDS */ 
/* INIT INPUT TYPE DIGIT */
fields.initInputDigit();
fields.initInputPickColor();
fields.imageSelect();
