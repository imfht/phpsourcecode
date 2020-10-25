<?php
    if ( dynamic_sidebar( 'footer-first' ) ){
        /* IF NOT EMPTY */    
    }
    else{
        /* IF EMPTY */
        if( myThemes::get( 'default-content' ) ){
            echo '<div class="widget website-description">';
            echo '<h1>';
            echo '<a href="" title="Nerocity - premium wordpress theme">Nerocity</a>';
            echo '</h1>';
            echo '<p>Vivamus imperdiet felis consectetur onec eget orci adipiscing nunc.<br>';
            echo 'Pellentesque fermentum, ante ac interdum ullamcorper.</p>';
            echo '</div>';
        }
    }
?>