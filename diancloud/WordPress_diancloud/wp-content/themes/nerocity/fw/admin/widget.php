<?php
    $wgd = '<div class="mythemes-widgets">';

    /* SLIDESHOW */
    $wgd .= '<div class="mythemes-widget mythemes-multi-adds">';
    $wgd .= '<div class="mythemes-adds-item"><a href="http://mythem.es/item/nerocity-premium-wordpress-theme/" title="myThem.es - Verbo Premium Responsive WordPress Theme"><img src="' . get_template_directory_uri() . '/media/admin/images/mythemes-nerocity-premium.png"/></a></div>';
    $wgd .= '<div class="mythemes-adds-item"><a href="http://mythem.es/item/my-lovely-premium-wordpress-theme/" title="myThem.es - general about customizations"><img src="' . get_template_directory_uri() . '/media/admin/images/mythemes-my-lovely-premium.png"/></a></div>';
    $wgd .= '<div class="mythemes-adds-item"><a href="mythem.es/item/verbo-premium-wordpress-theme/" title="myThem.es - contact for support or customizations"><img src="' . get_template_directory_uri() . '/media/admin/images/mythemes-verbo-premium.png"/></a></div>';
    $wgd .= '</div>';

    /* ADDS */
    $wgd .= '<div class="mythemes-widget my-presentation">';
    $wgd .= '<a href="http://codecanyon.net/item/my-presentation/6051397?ref=mythem_es"><img src="' . get_template_directory_uri() . '/media/admin/images/mythemes-my-presentation.png"/></a>';
    $wgd .= '</div>';

    /* PAYPAL DONATE */
    $wgd .= '<div class="mythemes-widget widget-paypal" target="popupwindow">';
    $wgd .= '<span class="paypal-title"><img src="' . get_template_directory_uri() . '/media/admin/images/paypal-title.png"/></span>';
    $wgd .= '<hr class="widget-delimiter">';
    $wgd .= '<p>If you find this theme useful then we would be grateful if you\'ve <b>DONATED</b> for a tea!</p>';
    $wgd .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">';
    $wgd .= '<input type="hidden" name="cmd" value="_s-xclick">';
    $wgd .= '<input type="hidden" name="hosted_button_id" value="AC6MJC7FL476W">';
    $wgd .= '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">';
    $wgd .= '<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';
    $wgd .= '</form>';
    $wgd .= '</div>';

    /* MYTHEM.ES SOCIAL LINK'S */
    $wgd .= '<div class="mythemes-widget">';
    $wgd .= '<h3 class="widget-title">Follow us</h3>';
    $wgd .= '<hr class="widget-delimiter">';
    $wgd .= '<div class="widget-social">';
    $wgd .= '<a href="https://www.facebook.com/myThemes" class="mythemes-facebook"></a>';
    $wgd .= '<a href="https://www.behance.net/mythemes" class="mythemes-behance"></a>';
    $wgd .= '<a href="https://twitter.com/my_themes" class="mythemes-twitter"></a>';
    $wgd .= '</div>';
    $wgd .= '</div>';

    $wgd .= '</div>';

    return $wgd;
?>