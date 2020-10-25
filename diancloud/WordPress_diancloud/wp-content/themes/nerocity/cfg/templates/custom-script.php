<?php
    $first_color    = myThemes::get( 'first-color' );
    $second_color   = myThemes::get( 'second-color' );
    $header_color   = '#' . get_header_textcolor();
    $h_color        = mythemes_tools::hex2rgb( $header_color );
?>
<style type="text/css" id="mythemes-custom-style">

    body > div.mythemes-topper{
        background-color: rgba( 0,0,0, 0.9 );
    }

    div.mythemes-content-inner div.content{
        background-color: #090909;    
    }

    div.mythemes-content-inner footer{
        background-color: rgba( 0,0,0, 0.9 );
    }

    .mythemes-header-animation .mythemes-logo,
    div.mythemes-menu div.mythemes-nav-header h2,
    div.mythemes-menu div.mythemes-nav-header h2 a{
        color: <?php echo $header_color; ?>
    }

    .mythemes-header-animation .mythemes-description{
        color: rgba( <?php echo $h_color; ?>, 0.65 );
    }

    /* NAVIGATION */
    div.mythemes-menu ul li.current-menu-item,
    div.mythemes-menu ul li.current-menu-item > a{
        color: <?php echo $second_color; ?>   
    }
    
    /* BUTTONS */
    .btn,
    .button,
    .mythemes-button,
    button,
    input[type="submit"],
    input[type="button"],
    nav.user-nav div > ul,
    div#comments  p.form-submit input[type="submit"],
    div.widget_newsletter form button[type="submit"],
    .hentry input[type="button"],
    .hentry input[type="submit"],
    .hentry button,
    div.widget_post_meta ul li span.post-tag,
    div.widget_calendar table th,
    div.comment-respond h3.comment-reply-title small a,
    .content-border article div.post-meta-tags a:hover,
    .content-border article div.post-meta-categories a{
        background-color: <?php echo $first_color; ?>;
    }
    .btn.second-button,
    .button.second-button,
    div.widget_post_tags div.tagcloud a,
    div.widget_tag_cloud div.tagcloud a,
    .content-border article div.post-meta-tags a,
    .content-border article div.post-meta-categories a:hover,
    div.comment-respond h3.comment-reply-title small a:hover{
        background-color: <?php echo $second_color; ?>;   
    }

    nav.user-nav div > ul > li:hover,
    nav.user-nav > ul > li:hover,
    nav.user-nav div > ul > li ul,
    nav.user-nav > ul > li ul,
    nav.user-nav div > ul > li:hover > span.menu-delimiter,
    nav.user-nav > ul > li:hover > span.menu-delimiter{
        background-color: <?php echo mythemes_tools::brightness( $first_color , 10 ); ?>;
    }

    nav.user-nav div > ul > li ul li a,
    nav.user-nav > ul > li ul li a,
    nav.user-nav > ul > li ul,
    nav.user-nav div > ul > li ul{
        border-top: 1px solid <?php echo mythemes_tools::brightness( $second_color , 20 ); ?>;
    }

    /* LINK */
    a,
    div.widget ul li a:hover,
    div.widget_calendar table td a:hover,
    div.widget_categories ul li a:hover,
    .single-portfolio div.widget-collections a:hover,
    div.comments-list > ol li.pingback header cite a:hover,
    div.comments-list > ol li.comment header cite a:hover,
    div.widget_recent_comments_with_avatar ul li h5 a:hover,
    .mythemes-portfolio .portfolio-item h4 a:hover{
        color:  <?php echo $first_color; ?>;
    }
    
    a:hover,
    .content-border .hentry h2 a:hover,
    .content-border article h2 a:hover,
    nav.base-nav ul li.current-menu-item > a,
    .mythemes-portfolio .portfolio-antet h3 a:hover{
        color:  <?php echo $second_color; ?>;
    }

    /* DARK BORDER BOTTOM */
    .btn,
    button,
    .button,
    .mythemes-button,
    input[type="submit"],
    input[type="button"],
    article.hentry button,
    nav.user-nav div > ul,
    nav.user-nav div > ul > li ul,
    nav.user-nav > ul > li ul,
    .hentry input[type="button"],
    .hentry input[type="submit"],
    div.widget_post_meta ul li span.post-tag,
    div#comments  p.form-submit input[type="submit"],
    div.widget_newsletter form button[type="submit"],
    div.comment-respond h3.comment-reply-title small a,
    .content-border article div.post-meta-tags a:hover,
    .content-border article div.post-meta-categories a{
        border-bottom: 2px solid <?php echo mythemes_tools::brightness( $first_color , -40 ); ?>;
    }

    nav.user-nav div > ul > li,
    nav.user-nav > ul > li{
        border-right: 1px solid <?php echo mythemes_tools::brightness( $first_color , -30 ); ?>;
    }

    .btn.second-button,
    .button.second-button,
    div.widget_post_tags div.tagcloud a,
    div.widget_tag_cloud div.tagcloud a,
    .content-border article div.post-meta-tags a,
    .content-border article div.post-meta-categories a:hover,
    div.comment-respond h3.comment-reply-title small a:hover{
        border-bottom: 2px solid <?php echo mythemes_tools::brightness( $second_color , -40 ); ?>;
    }

    nav.user-nav div > ul > li,
    nav.user-nav > ul > li{
        border-left: 1px solid <?php echo mythemes_tools::brightness( $first_color , 30 ); ?>;
    }

    .single-portfolio div.widget-collections a:hover{
        border-color: rgba( <?php echo mythemes_tools::hex2rgb( $first_color ); ?> , 0.5 );
    }
</style>
<style type="text/css">
    <?php echo myThemes::get( 'css' ); ?>
</style>