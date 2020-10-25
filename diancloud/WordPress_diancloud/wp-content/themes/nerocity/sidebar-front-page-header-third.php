<?php
    if ( dynamic_sidebar( 'front-page-header-third' ) ){
        /* IF NOT EMPTY */    
    }
    else{
        /* IF EMPTY */
        if( myThemes::get( 'default-content' ) ){
            echo '<div class="widget widget_text">';
            echo '<h3>Responsive Layout</h3>';
            echo '<div class="textwidget">We haven\'t forgotten about responsive layout. With Verbo free WordPress theme, you can create a website with full mobile support.</div>';
            echo '</div>';
        }
    }
?>