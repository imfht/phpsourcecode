
var tools = new Object();
tools.r = function( action , method , args ){
    jQuery(function(){
        jQuery.post( ajaxurl , {
            "action" : action,
            "method" : method,
            "args" : args
        } , function( result ){ tools.init( result ); } );
    });
}
tools.init = function( result ){
    return result;
}

/* hide show object */
tools.hs = new Object();
tools.hs.select = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(){
            jQuery( 'option' , jQuery( this ) ).each(function(){
                if( jQuery(this).is(':selected') ){
                    for (var key in args) {
                        if ( args.hasOwnProperty( key ) ) {
                            if( jQuery( this ).val().trim()  == key ){
                                jQuery( args[ key ] ).hide('slow');
                            }else{
                                jQuery( args[ key ] ).show('slow');
                            }
                        }
                    }
                }
            });
        });
    });
}

tools.hs.imageSelect = function( selector , args ){
    jQuery(function(){
        jQuery( selector + ' input[type="hidden"]' ).each(function(){
            jQuery( 'option' , jQuery( this ) ).each(function(){
                if( jQuery(this).is(':selected') ){
                    for (var key in args) {
                        if ( args.hasOwnProperty( key ) ) {
                            if( jQuery( this ).val().trim()  == key ){
                                jQuery( args[ key ] ).hide('slow');
                            }else{
                                jQuery( args[ key ] ).show('slow');
                            }
                        }
                    }
                }
            });
        });
    });
}

tools.hs.check = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(){
            if( jQuery( this ).is(':checked') ){
                for (var key in args) {
                    if ( args.hasOwnProperty( key ) ) {
                        if( jQuery( selector ).val().trim()  == key ){
                            jQuery( args[ key ] ).hide('slow');
                        }else{
                            jQuery( args[ key ] ).show('slow');
                        }
                    }
                }
            }
        });
         
    });
}

tools.hs.checkButton = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(){
            if( jQuery( selector ).parent().children('input[type="hidden"]').val() ){
                for (var key in args) {
                    if ( args.hasOwnProperty( key ) ) {
                        if( jQuery( selector ).parent().children('input[type="hidden"]').val().trim()  == key ){
                            jQuery( args[ key ] ).hide('slow');
                        }else{
                            jQuery( args[ key ] ).show('slow');
                        }
                    }
                }
            }
        });
    });
}

/* show hide object */
tools.sh = new Object();
tools.sh.select = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(){
            jQuery( 'option' , jQuery( this ) ).each(function(){
                if( jQuery(this).is(':selected') ){
                    for (var key in args) {
                        if ( args.hasOwnProperty( key ) ) {
                            if( jQuery( this ).val().trim()  == key ){
                                jQuery( args[ key ] ).show();
                                
                                if( args[ key ] == '.my-sc-map-settings'){
                                    jQuery( "div#map_canvas" ).gmap( map_args );
                                }
                            }else{
                                jQuery( args[ key ] ).hide();
                            }
                        }
                    }
                }
            });
        });
    });
}

tools.sh.check = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(){
            if( jQuery( this ).is(':checked') ){
                for (var key in args) {
                    if ( args.hasOwnProperty( key ) ) {
                        if( jQuery( selector ).val().trim()  == key ){
                            jQuery( args[ key ] ).show();
                        }else{
                            jQuery( args[ key ] ).hide();
                        }
                    }
                }
            }
        });
         
    });
}

tools.sh.checkButton = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(){
            if( jQuery( selector ).parent().children('input[type="hidden"]').val() ){
                for (var key in args) {
                    if ( args.hasOwnProperty( key ) ) {
                        if( jQuery( selector ).parent().children('input[type="hidden"]').val().trim()  == key ){
                            jQuery( args[ key ] ).show();
                        }else{
                            jQuery( args[ key ] ).hide();
                        }
                    }
                }
            }
        });
         
    });
}
tools.sh.check22 = function( s1 , s2 , c2 , c3 ){
    jQuery(function( ){
        jQuery( s1 ).each(function(){
            if( jQuery( this ).is(':checked') ){
                if( jQuery( this ).val().trim() == 'yes' ){
                    jQuery( c2 ).show();
                    jQuery( s2 ).each(function(){
                        if( jQuery( this ).is(':checked') ){
                            if( jQuery( this ).val().trim() == 'yes' ){
                                jQuery( c3 ).show();
                            }else{
                                jQuery( c3 ).hide();
                            }
                        }
                    });
                }else{
                    jQuery( c2 ).hide();
                    jQuery( c3 ).hide();
                }
            }
        });
    });
}
/* special show hide object */
tools.sh_ = new Object();
tools.sh_.select = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(i){
            jQuery( 'option' , jQuery( this ) ).each(function(i){
                var show = '';
                var show_ = '';
                if( jQuery( this ).is( ':selected' ) ){
                    for( var key in args ) {
                        if ( args.hasOwnProperty( key ) ) {

                            if( jQuery( this ).val().trim()  == key ){
                                show = args[ key ];
                            }else{
                                if( key == 'else' ){
                                    show_ = args[ key ];
                                }
                                jQuery( args[ key ] ).hide();
                            }
                        }
                    }

                    if( show == '' ){
                        jQuery( show_ ).show();
                    }else{
                        jQuery( show ).show();
                    }
                }
            });
        });
    });
}
tools.sh_.check = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(){
            var show = '';
            var show_ = '';
            if( jQuery( this ).is(':checked') ){
                
                for (var key in args) {
                    if ( args.hasOwnProperty( key ) ) {

                        if( jQuery( this ).val().trim()  == key ){
                            show = args[ key ];
                        }else{
                            if( key == 'else' ){
                                show_ = args[ key ];
                            }
                            jQuery( args[ key ] ).hide();
                        }
                    }
                }
                if( show == '' ){
                    jQuery( show_ ).show();
                }else{
                    jQuery( show ).show();
                }
            }
        });
    });
}
/* special hide show object */
tools.hs_ = new Object();
tools.hs_.select = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(){
            jQuery( 'option' , jQuery( this ) ).each(function(){
                var hide = '';
                if( jQuery(this).is(':selected') ){
                    for (var key in args) {
                        if ( args.hasOwnProperty( key ) ) {
                            if( jQuery( this ).val().trim()  == key ){
                                hide = args[ key ];
                            }else{
                                jQuery( args[ key ] ).show();
                            }
                        }
                    }	
					jQuery( hide ).hide();
                }
            });
        });
    });
}
tools.hs_.check = function( selector , args ){
    jQuery(function(){
        jQuery( selector ).each(function(){
            var hide = '';
            if( jQuery( this ).is(':checked') ){
                for (var key in args) {
                    if ( args.hasOwnProperty( key ) ) {

                        if( jQuery( this ).val().trim()  == key ){
                            hide = args[ key ];
                        }else{
                            jQuery( args[ key ] ).show();
                        }
                    }
                }

                jQuery( hide ).hide();
            }
        });
    });
}

/* additional method */
tools.v = function( selector ){
    
    var result;
    jQuery(document).ready(function(){
        if( jQuery( selector ).attr('type') == 'checkbox' || jQuery( selector ).attr('type') == 'radio' ){
            jQuery( selector ).each(function(){
                if( jQuery( this ).is(':checked') ){
                    result = jQuery( this ).val();
                }
            });
        }else{
            result = jQuery( selector ).val();
        }
    });
    
    return result;
}

tools.val = function( selector ){
    
    var result;
    if( jQuery( selector ).attr('type') == 'checkbox' || jQuery( selector ).attr('type') == 'radio' ){
        jQuery( selector ).each(function(){
            if( jQuery( this ).is(':checked') ){
                result = jQuery( this ).val();
            }
        });
    }else{
        result = jQuery( selector ).val();
    }
    
    return result;
}

tools.s = function( selector ){
    jQuery(document).ready(function( ){
        jQuery( selector ).show();
    });
}
tools.h = function( selector ){
    jQuery(document).ready(function( ){
        jQuery( selector ).hide();
    });
}
tools.ah = function( selector ){
    jQuery(function(){
        jQuery( selector ).show();
        jQuery( selector ).fadeTo( 2000 , 0 , function(){
            jQuery( selector ).css( 'opacity' , 1 );
            jQuery( selector ).hide();
        });
    });
}
tools.as = function( selector ){
    jQuery(function(){
        jQuery( selector ).hide();
        jQuery( selector ).fadeTo( 'slow' , 1.0 );
    });
}

tools.searchUsers = function( obj , selector ){
    jQuery(function(){
        jQuery.post( ajaxurl , {
            'action' : 'search_users',
            's' : jQuery( obj ).val()
            
        } , function( result ){ 
            jQuery( selector ).html( result );
        } );
    });
}

tools.confirmSubmit = function( text ){
    if( confirm( text ) )
        return true;
    else
        return false;
}

tools.popBox2 = function( selector ){
    jQuery(function(){

        var width       = parseInt( jQuery( window ).width() ) - 22;
        var height      = parseInt( jQuery( window ).height() ) - 72;
        var iHeight     = height - 39;
        var dh = jQuery( document ).height();

        jQuery( 'div.popup-box' + selector ).css({ 'width' : width + 'px', 'height' : height + 'px' });
        jQuery( 'div.popup-box' + selector ).find( 'div.mytheme_sc_settings').css({ 'height' : iHeight + 'px' });
        
        jQuery( 'div.popup-box-shadow' + selector + '-shadow' ).css( { 'height' : dh + "px" } );
        jQuery( 'div.popup-box-shadow' + selector + '-shadow' ).show();
        jQuery( 'div.popup-box' + selector ).show();
    });
}

tools.popBox2Hide = function( selector ){
    jQuery(function(){
        jQuery( 'div.popup-box-shadow' + selector + '-shadow' ).hide();
        jQuery( 'div.popup-box' + selector ).hide();
    });
}

tools.my_sl_post = function( obj , fields ){
    jQuery(function(){
        if( jQuery( obj ).val() == 0 ) {
            return null;
        }
        jQuery.post( ajaxurl , {
            'action' : 'my_sl_manager_post',
            'postID' : jQuery( obj ).val() } ,
            function( rett ){
                rett = eval('(' + rett + ')');
                
                jQuery( fields.title.toString() ).val( rett.title );
                jQuery( fields.image.toString() ).val( rett.image );
                jQuery( fields.url.toString() ).val( rett.url );
                jQuery( fields.description.toString() ).val( rett.description );
            }
        );
    });
}

tools.colorIcons = function( colorSelector , selector ){
    jQuery(function(){
        //alert( jQuery( colorSelector ).val() );
        jQuery( selector ).css( { 'background-color' : jQuery( colorSelector ).val() } );
    });
}


Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
}

function is_selected( selector , args ){
    jQuery(function(){
        jQuery( 'option' , jQuery( selector ) ).each(function(){
            if( jQuery( this ).is(':selected') ){
                var val = jQuery( this ).val().trim();
                for ( var key in args ) {
                    if( key == 'show' ){
                        for( var i = 0; i < args[ key ].length; i++ ){
                            if( !(args[ val ] && typeof args[ val ].hide == 'array' &&
                                args[ val ].hide.contains( args[ key ][ i ] )) ){
                                jQuery( args[ key ][ i ] ).show('slow');
                            }
                        }
                        continue;
                    }

                    if( key == 'hide' ){
                        for( var i = 0; i < args[ key ].length; i++ ){

                            console.log( args[ val ].show );
                            console.log( args[ key ][ i ] );

                            if( !(args[ val ] && typeof args[ val ].show == 'object' &&
                                args[ val ].show.contains( args[ key ][ i ] )) ){
                                jQuery( args[ key ][ i ] ).hide('slow');
                            }
                        }
                        continue;
                    }
                    if( key == val ){
                        if( args[ key ].hasOwnProperty( 'show' ) ){
                            for( var i = 0; i < args[ key ].show.length; i++ ){
                                jQuery( args[ key ].show[ i ] ).show('slow');
                            }
                        }
                        if( args[ key ].hasOwnProperty( 'hide' ) ){
                            for( var i = 0; i < args[ key ].hide.length; i++ ){
                                jQuery( args[ key ].hide[ i ] ).hide('slow');
                            }
                        }
                    }
                    else{
                        if( args[ key ].hasOwnProperty( 'hide' ) ){
                            for( var i = 0; i < args[ key ].hide.length; i++ ){
                                jQuery( args[ key ].hide[ i ] ).hide('slow');
                            }
                        }
                    }
                }
            }
        });
    });
}

function mythemes_slideshow_delete_item( id ){
    if( confirm( "Are you sure you want to delete this item from slideshow!" ) ){
        if( jQuery( 'div.mythemes-slideshow-container' ).find( 'div#mythemes-slideshow-item-' + id ).length ){
            jQuery( 'div.mythemes-slideshow-container' ).find( 'div#mythemes-slideshow-item-' + id ).remove();    
        }
        
    }
}
function mythemes_slideshow_add_item( item ){

    var uploader = false;

    if( typeof item !== 'object' ){

        var d = new Date();

        item = { 
            id          : d.getTime(),
            media_id    : 0,
            media_url   : ''
        };

        uploader = true;
    }

    var obj = jQuery( '<div class="mythemes-slideshow-item" id="mythemes-slideshow-item-' + item.id + '">' +
        '<div class="mythemes-slideshow-item-header">' +
        '<a href="javascript:mythemes_slideshow_delete_item(' + item.id + ')"><i class="icon-trash"></i></a>' +
        '</div>' +
        '<img src="' + item.media_url + '" />' +
        '<input type="hidden" class="mythemes-slideshow-item-id" name="mythemes-slideshow-items[' + item.id + '][id]" value="' + item.id + '"/>' +
        '<input type="hidden" class="mythemes-slideshow-item-media-id" name="mythemes-slideshow-items[' + item.id + '][media_id]" value="' + item.media_id + '"/>' +
        '<input type="hidden" class="mythemes-slideshow-item-media-url" name="mythemes-slideshow-items[' + item.id + '][media_url]" value="' + item.media_url + '"/>' +
        '</div>'
    );

    if( uploader ){
        my_uploader2(function( params ){
            obj.find( 'input.mythemes-slideshow-item-media-id' ).val( params.id );
            obj.find( 'input.mythemes-slideshow-item-media-url' ).val( params.sizes.thumbnail.url );
            obj.find( 'img' ).attr( 'src' , params.sizes.thumbnail.url );

            obj.appendTo( 'div.mythemes-slideshow-container div.mythemes-slideshow-items' );    
        });
    }
    else{
        obj.appendTo( 'div.mythemes-slideshow-container div.mythemes-slideshow-items' );    
    }
}

function mythemes_slideshow_add_items( items ){
    if( typeof items == 'object' ){
        for ( var key in items ) {
            mythemes_slideshow_add_item( items[ key ] );
        }    
    }
}