document = window.document;
            
(function( $ , window ){

    var mythemes_sl = [];
    var mythemes_sl_deff = {

        itemClass       : '.mythemes-slideshow-item',
        pagination      : true,
        pause           : true,
        paginationClass : '.mythemes-slideshow-pagination' ,
        paginationType  : 'orizontal',
        delay           : 5000,
        spead           : 400,
        animation       : {
            pagination :{
                hide : {
                    opacity: 0.3
                },
                show : {
                    opacity: 0.8,
                }
            },
            items : {
                hide : {
                    opacity: 0
                },
                show : {
                    opacity: 1
                }
            }
        },
        before : function(){
            return;
        }
    };

    $.fn.mythemes_slideshow = function( opt ){
        var options = jQuery.extend( {} , mythemes_sl_deff , opt );

        return this.each(function(){
            
            var self = jQuery( this );
            var i = jQuery( this ).index();
            
            /* RUN SLIDESHOW */
            self.mythemes_sl_run( options, true );
            
            /* INIT PAUSE */
            if( options.pause ){
                self.find( options.itemClass + '.current ').mouseout(function() {
                    mythemes_sl[ i ] = setTimeout(function(){
                        self.mythemes_sl_run( options, false );
                    }, options.delay );
                }).mouseover(function() {
                    clearTimeout( mythemes_sl[ i ] );
                });
            }

        });
    }
    
    /* RUN */
    $.fn.mythemes_sl_run = function( options, first_run ){
    
        var self = jQuery( this );
        var i = jQuery( this ).index();
        
        if( !first_run ){

            var item = self.find( options.itemClass + '.current' ).next();
            var page = self.find( options.paginationClass + ' ul li.current' ).next().find( 'a' );

            self.mythemes_sl_get_item( options, item, page );
        }
        
        /* GENERATE PAGINATION */
        else if( options.pagination ) {
            /* GENERATE PAGE ITEMS BY SLIDES */
            var pagination = '';
            self.find( options.itemClass ).each(function(){
                var current = '';
                
                if( jQuery( this ).hasClass( 'current' ) ){
                    current = ' class="current"';
                }
                pagination += '<li' + current + '><a href="javascript:void(null);"></a></li>';
            });
                
            if( self.find( options.paginationClass ).length ){

                if( self.find( options.paginationClass ).find( 'nav' ).length ){
                    self.find( options.paginationClass ).find( 'nav' ).html('');
                    jQuery( '<ul>' + pagination +  '</ul>' ).appendTo( self.find( options.paginationClass ).find( 'nav' ) );
                }
                else{
                    self.find( options.paginationClass ).html( '' );
                    jQuery( '<ul>' + pagination +  '</ul>' ).appendTo( self.find( options.paginationClass ) );
                }
            }
            
            else{
                jQuery( '<nav class="' + options.paginationClass.substr( 1, options.paginationClass.length ) + '" ><ul>' + pagination + '</ul></nav>' ).appendTo( self );
            }
            
            /* INIT NAVIGATION BY CLICK ON PAGINATION */
            self.mythemes_sl_nav_click( self, options );
        }

        /* SET TIME OUT */
        mythemes_sl[ i ] = setTimeout(function(){
            self.mythemes_sl_run( options, false );
        }, options.delay );
    }
    
    /* SLIDE ITEMS */
    $.fn.mythemes_sl_get_item = function( options, item, page ){
        var self = jQuery( this );
        
        /* GET FIRST IF NOT EXISTS */
        if( !item.length )
            item = jQuery( self.find( options.itemClass )[ 0 ] );

        if( !page.length )
            page = jQuery( self.find( options.paginationClass + ' ul li' )[ 0 ] ).find( 'a' );
            
        /* ITEM */
        self.find( options.itemClass + '.current' ).animate( options.animation.items.hide, options.spead , function(){
            jQuery( this ).removeClass( 'current' );
        });

        jQuery( item ).animate( options.animation.items.show, options.spead, function(){
            jQuery( this ).addClass( 'current' );
        });

        /* NAVIGATION PAGE */
        self.find( options.paginationClass + ' ul li.current a' ).animate( options.animation.pagination.hide, options.spead, function(){
            jQuery( this ).parent().removeClass( 'current' );
        });

        jQuery( page ).animate( options.animation.pagination.show, options.spead, function(){
            jQuery( this ).parent().addClass( 'current' );
        });
    }
    
    /* NAVIGATION BY CLICK ON PAGINATION ITEM */
    $.fn.mythemes_sl_nav_click = function( parent, options ){
    
        var self = jQuery( this );
        var i = jQuery( this ).index();
        
        self.find( options.paginationClass + ' ul li a').each(function(){
            jQuery( this ).click(function(){
                if( !jQuery( this ).parent().hasClass( 'current' ) ){
                
                    /* STOP */
                    clearTimeout( mythemes_sl[ i ] );

                    var page = jQuery( this );

                    var items = parent.find( options.itemClass );
                    
                    var index = jQuery( this ).parent().index();
                    var item;

                    if( items.hasOwnProperty( index ) ){
                        item = jQuery( items[ index ] );
                    }
                    else{
                        item = jQuery( items[ 0 ] );
                    }

                    parent.mythemes_sl_get_item( options, item, page );

                    /* SET TIME OUT */    
                    mythemes_sl[ i ] = setTimeout(function(){
                        parent.mythemes_sl_run( options, false );
                    }, options.delay );
                }
            });
        });
    }
    
})( jQuery , window );

(function( $ , window ){

    var mythemes_hover_sl = [];
    var mythemes_hover_sl_deff = {

        itemClass       : '.mythemes-hover-sl-item',
        pagination      : true,
        paginationClass : '.mythemes-hover-sl-pagination',
        delay           : 500,
        spead           : 200,
        animation       : {
            pagination :{
                hide : {
                    opacity: 0.3
                },
                show : {
                    opacity: 0.8,
                }
            },
            items : {
                hide : {
                    opacity: 0
                },
                show : {
                    opacity: 1
                }
            }
        },
        before : function(){
            return;
        }
    };

    $.fn.mythemes_hover_sl = function( opt ){

        var options = jQuery.extend( {} , mythemes_hover_sl_deff , opt );

        return this.each(function(i){

            var self = jQuery( this );
            var index = jQuery( this ).index();

            /* GENERATE PAGINATION */
            if( options.pagination ){
                var pagination = '';

                self.find( options.itemClass ).each(function(){

                    var current = '';
                    
                    if( jQuery( this ).hasClass( 'current' ) ){
                        current = ' class="current"';
                    }

                    pagination += '<li' + current + '><a href="javascript:void(null);"></a></li>';
                });

                if( pagination.length ){
                    jQuery( '<nav class="valign-cell"><ul>' + pagination +  '</ul></nav>' ).appendTo( self.find( options.paginationClass ) );
                }
            }

            /* RUN / STOP SLIDESHOW */
            self.find( options.itemClass ).mouseover(function(){

                mythemes_hover_sl[ i ] = setTimeout( function(){

                    var item = self.find( options.itemClass + '.current' );

                    if( item.length ){

                        item.fadeOut( 500, function(){
                            jQuery( this ).removeClass( 'current' );

                            var index = jQuery( this ).index();
                            var paginationItem = jQuery( self.find( options.paginationClass + ' li' )[ index ] );
                            paginationItem.removeClass( 'current' );

                            var next = jQuery( this ).next();

                            if( !next.length ){
                                next = jQuery( self.find( options.itemClass )[ 0 ] );
                            }

                            next.fadeIn( 500, function(){
                                jQuery( this ).addClass( 'current' );
                            });

                            paginationItem =  jQuery( self.find( options.paginationClass + ' li' )[ next.index() ] )
                            paginationItem.addClass( 'current' );
                        });
                    }

                }, options.delay );

            }).mouseleave(function(){
                clearTimeout( mythemes_hover_sl[ i ] );
            });

            self.find( options.paginationClass + ' li a' ).click(function(){

                if( !jQuery( this ).parent().hasClass( 'current' ) ){

                    clearTimeout( mythemes_hover_sl[ i ] );

                    self.find( options.paginationClass + ' li' ).removeClass( 'current' );
                    jQuery( this ).parent().addClass( 'current' );

                    var index = jQuery( this ).parent().index();
                    var item = jQuery( self.find( options.itemClass )[ index ] );

                    self.find( options.itemClass + '.current' ).fadeOut( 500 , function(){
                        jQuery( this ).removeClass( 'current' );

                        item.fadeIn( 500, function(){
                            jQuery( this ).addClass( 'current' );
                        });
                    });

                    
                }
            });

        });
    }
    
})( jQuery , window );