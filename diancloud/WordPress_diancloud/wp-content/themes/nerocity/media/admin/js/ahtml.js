jQuery(document).ready(function(){

    /* SAMPLE SLIDESHOW */

    var sl;
    function mythemes_slideshow_next( item, page ){

        if( !item.length )
            item = jQuery( jQuery( 'div.mythemes-slideshow .mythemes-slideshow-item' )[ 0 ] );

        if( !page.length )
            page = jQuery( jQuery( 'div.mythemes-slideshow .mythemes-slideshow-pagination ul li' )[ 0 ] ).find( 'a' );

        /* TESTIMONIALS AND BLOCKQUOTE */
        jQuery( 'div.mythemes-slideshow .mythemes-slideshow-item.current' ).animate({ 
            opacity: 0 
        }, 400, function(){
            jQuery( this ).removeClass( 'current' );
        });

        jQuery( item ).animate({ 
            opacity: 1 
        }, 400, function(){
            jQuery( this ).addClass( 'current' );
        });

        /* NAVIGATION PAGE */
        jQuery( 'div.mythemes-slideshow .mythemes-slideshow-pagination ul li.current a' ).animate({ 
            opacity: 0.3,
        }, 400, function(){
            jQuery( this ).parent().removeClass( 'current' );
        });

        jQuery( page ).animate({ 
            opacity: 0.8,
        }, 400, function(){
            jQuery( this ).parent().addClass( 'current' );
        });
    }
    function mythemes_slideshow_items( index ){

        if( index > 0 ){

            var item = jQuery( 'div.mythemes-slideshow .mythemes-slideshow-item.current' ).next();
            var page = jQuery( 'div.mythemes-slideshow .mythemes-slideshow-pagination ul li.current' ).next().find( 'a' );

            mythemes_slideshow_next( item, page );

        }

        sl = setTimeout(function(){
            mythemes_slideshow_items( 1 );
        }, 5000 );
    }

    jQuery( 'div.mythemes-slideshow .mythemes-slideshow-item.current' ).mouseout(function() {
        sl = setTimeout(function(){
            mythemes_slideshow_items( 1 );
        }, 5000 );
    }).mouseover(function() {
        clearTimeout( sl );
    });

    jQuery( 'div.mythemes-slideshow .mythemes-slideshow-pagination ul li a' ).each(function(){
        jQuery( this ).click(function(){
            if( !jQuery( this ).parent().hasClass( 'current' ) ){
                clearTimeout( sl );

                var page = jQuery( this );

                var items = jQuery( 'div.mythemes-slideshow .mythemes-slideshow-item' );
                var index = jQuery( this ).parent().index();
                var item;

                if( items.hasOwnProperty( index ) ){
                    item = jQuery( items[ index ] );
                }
                else{
                    item = jQuery( items[ 0 ] );
                }

                mythemes_slideshow_next( item, page );

                sl = setTimeout(function(){
                    mythemes_slideshow_items( 1 );
                }, 5000 );
                
            }
        });
    });

    mythemes_slideshow_items( 0 );

});


