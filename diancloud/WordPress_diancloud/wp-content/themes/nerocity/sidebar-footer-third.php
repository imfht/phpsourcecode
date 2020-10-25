<?php
    if ( dynamic_sidebar( 'footer-third' ) ){
        /* IF NOT EMPTY */    
    }
    else{
        /* IF EMPTY */
        if( myThemes::get( 'default-content' ) ){
            echo '<div class="widget widget_text">';
            echo '<h5>Contact</h5>';
            echo '<div class="textwidget">';
            echo 'facebook: <a href="">http://facebook.com/#</a></br>';
            echo 'direct: <a href="">http://mythem.es/#</a></br>';
            echo 'e-mail: support at mythem.es</div>';
            echo '</div>';
        }
    }
?>