<?php
    if ( dynamic_sidebar( 'footer-second' ) ){
        /* IF NOT EMPTY */    
    }
    else{
        /* IF EMPTY */
        if( myThemes::get( 'default-content' ) ){
            echo '<div class="widget widget_text">';
            echo '<h5>Address</h5>';
            echo '<div class="textwidget">';
            echo '1 Infinite Loop</br>';
            echo 'Cupertino, CA 95014</br>';
            echo 'United States</div>';
            echo '</div>';
        }
    }
?>