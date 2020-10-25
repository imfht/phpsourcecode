<?php
    /* GENERAL OPTIONS */
    
    $sett = & myThemes_acfg::$pages[ 'mythemes-general' ][ 'content' ];

    $icon = pathinfo( myThemes::pget( 'favicon' ) );
    if( strlen( myThemes::pget( 'favicon' ) ) && $icon[ 'extension' ] != 'ico' ){
        $icon_hint = '<span style="color:#cc0000;">' . __( 'Error, please select "ico" type media file' , 'myThemes' ) . '</span>';
    }else{
        $icon_hint = __( "Please select 'ico' type media file. Make sure you allow uploading 'ico' type in General Settings -> Upload file types." , 'myThemes' );
    }

    $sett[ 'favicon' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'upload'
        ),
        'label' => __( 'Upload your custom favicon' , 'myThemes' ),
        'hint' => $icon_hint
    );

    $sett[ 'logo' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'upload'
        ),
        'label' => __( 'Upload your custom logo' , 'myThemes' )
    );

    $sett[ 'logo-height' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'digit'
        ),
        'label' => __( 'Logo height (px)' , 'myThemes' ),
        'hint' => __( 'wrapper height for logo, title and description.' , 'myThemes' )
    );
    
    $sett[ 'first-color' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'pickColor'
        ),
        'label' => __( 'First Color' , 'myThemes' )
    );
    
    $sett[ 'second-color' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'pickColor'
        ),
        'label' => __( 'Second Color' , 'myThemes' )
    );

    $sett[ 'default-content' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'label' => __( 'Show default content ( sidebars )' , 'myThemes' )
    );

    $sett[ 'show-top-meta' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'label' => __( 'Top meta from single' , 'myThemes' ),
        'hint' => __( 'Show Author, Date and Comments cound from single' , 'myThemes' )
    );

    $sett[ 'show-bottom-meta' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'label' => __( 'Bottom meta from single' , 'myThemes' ),
        'hint' => __( 'Show Categories and Tags from single' , 'myThemes' )
    );

    $sett[ 'html-suggestions' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'label' => __( 'HTML suggestions' , 'myThemes' ),
        'hint' => __( 'Hide HTML suggestions after comments form for single posts and portfolios' , 'myThemes' )
    );

    /////////////////////////////////////////
    /* HEADER SETTINGS */
    $sett[ 'header-settings-title' ] = array(
        'type' => array( 
            'template' => 'none',
        ),
        'content' => '<div style="padding-top: 80px;" class="title"><h2>' . __( 'Header' , 'myThemes' ) . '</h2></div>' .
        '<p>' . __( 'These options use default WordPress option "Header". Explore option ' , 'myThemes' ) . '<a href="' . admin_url( 'themes.php?page=custom-header' ) . '">' . __( 'header' , 'myThemes' ) . '</a></p>'
    );

    $sett[ 'show-header' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'action' => "{'t' : '.mythemes-show-header' , 'f' : '-' }",
        'label' => __( 'Show header' , 'myThemes' )
    );

    $showHeaderClass = 'mythemes-show-header hidden';
    if( myThemes::pget( 'show-header') ){
        $showHeaderClass = 'mythemes-show-header';
    }

    /* OPTIONS WRAPPER */
    $sett[ 'options-wrapper-start' ] = array(
        'type' => array( 
            'template' => 'none',
        ),
        'content' => '<div class="' . $showHeaderClass . '">'
    );

    if( get_option( 'show_on_front' ) == 'page' ){
        $sett[ 'show-header-blog' ] = array(
            'type' => array(
                'template' => 'inline',
                'input' => 'logic'
            ),
            'action' => "{'t' : '.mythemes-show-header' , 'f' : '-' }",
            'label' => __( 'Show header on Blog Page' , 'myThemes' )
        );
    }

    $sett[ 'show-header-all' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'action' => "{'t' : '.mythemes-show-header' , 'f' : '-' }",
        'label' => __( 'Show header on All pages' , 'myThemes' ),
        'hint' => __( 'Exclude single post and single page. Header will not be displayed on single posts and single pages'  , 'myThemes' )
    );

    $sett[ 'show-header-post' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'action' => "{'t' : '.mythemes-show-header' , 'f' : '-' }",
        'label' => __( 'Show header on Single Post' , 'myThemes' )
    );

    $sett[ 'show-header-page' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'action' => "{'t' : '.mythemes-show-header' , 'f' : '-' }",
        'label' => __( 'Show header on Single Page' , 'myThemes' )
    );

    $sett[ 'header-height' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        
        'label' => __( 'Header Height ( in pixels )' , 'myThemes' )
    );

    $sett[ 'mask-color' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'pickColor'
        ),
        'label' => __( 'Mask color' , 'myThemes' ),
        'hint' => __( 'By default this is a dark semi transparent foil over background image.' , 'myThemes' )
    );

    $sett[ 'mask-opacity' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Mask Opacity ( % )' , 'myThemes' ),
        'hint' => __( 'use one number between 1 and 100' , 'myThemes' )
    );

    /* FIRST BUTTON */
    $sett[ 'first-button-headline' ] = array(
        'type' => array( 
            'template' => 'code'
        ),
        'title' => __( 'First Button' , 'myThemes' )
    );
    $sett[ 'show-first-button' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'action' => "{'t' : '.mythemes-show-first-button' , 'f' : '-' }",
        'label' => __( 'Show first button in header' , 'myThemes' )
    );

    $showFirstButtonClass = 'mythemes-show-first-button hidden';
    if( myThemes::pget( 'show-first-button') ){
        $showFirstButtonClass = 'mythemes-show-first-button';
    }

    $sett[ 'first-button-label' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'templateClass' => $showFirstButtonClass,
        'label' => __( 'First button label' , 'myThemes' )
    );
    $sett[ 'first-button-desc' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'textarea'
        ),
        'templateClass' => $showFirstButtonClass,
        'label' => __( 'First button description' , 'myThemes' ),
        'hint' => __( 'This is link description used for attribute title' , 'myThemes' )
    );
    $sett[ 'first-button-url' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'templateClass' => $showFirstButtonClass,
        'label' => __( 'First button URL' , 'myThemes' )
    );

    /* SECOND BUTTON */
    $sett[ 'second-button-headline' ] = array(
        'type' => array( 
            'template' => 'code'
        ),
        'title' => __( 'Second Button' , 'myThemes' )
    );
    $sett[ 'show-second-button' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'action' => "{'t' : '.mythemes-show-second-button' , 'f' : '-' }",
        'label' => __( 'Show second button in header' , 'myThemes' )
    );

    $showSecondButtonClass = 'mythemes-show-second-button hidden';
    if( myThemes::pget( 'show-second-button') ){
        $showSecondButtonClass = 'mythemes-show-second-button';
    }

    $sett[ 'second-button-label' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'templateClass' => $showSecondButtonClass,
        'label' => __( 'Second button label' , 'myThemes' )
    );
    $sett[ 'second-button-desc' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'textarea'
        ),
        'templateClass' => $showSecondButtonClass,
        'label' => __( 'Second button description' , 'myThemes' ),
        'hint' => __( 'This is link description used for attribute title' , 'myThemes' )
    );
    $sett[ 'second-button-url' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'templateClass' => $showSecondButtonClass,
        'label' => __( 'Second button URL' , 'myThemes' )
    );

    /* END OPTIONS WRAPPER */
    $sett[ 'options-wrapper-end' ] = array(
        'type' => array( 
            'template' => 'none',
        ),
        'content' => '</div>'
    );


    /////////////////////////////////////////
    /* LAYOUT SETTINGS */
    $sett[ 'front-page-settings-title' ] = array(
        'type' => array( 
            'template' => 'none',
        ),
        'content' => '<div style="padding-top: 80px;" class="title"><h2>' . __( 'Layout and Templates' , 'myThemes' ) . '</h2></div>'
    );

    $layouts = array(
        'right'  => get_template_directory_uri() . '/media/admin/images/left.layout.png',
        'left' => get_template_directory_uri() . '/media/admin/images/right.layout.png',
        'full'  => get_template_directory_uri() . '/media/admin/images/full.layout.png'
    );
    
    /* DEFAULT */
    $values = myThemes::cfg( 'sidebars-list' );

    $sett[ 'layout' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'imageSelect'
        ),
        'values' => $layouts,
        'coll' => 3,
        'label' => __( 'Default layout' , 'myThemes' ),
        'hint' => __( 'If not is set custom layout, will be used default layout.' , 'myThemes' ),
        'action' => "[ 'hs' , { 'full' : '.sidebar' } ]"
    );

    if( myThemes::pget( 'layout' ) == 'full' ){
        $sidebarClass = 'sidebar hidden';
    }else{
        $sidebarClass = 'sidebar';
    }

    $sett[ 'sidebar' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'select'
        ),
        'templateClass' => $sidebarClass,
        'values' => $values,
        'label' => __( 'Default sidebar' , 'myThemes' ),
    );

    /* FRONT PAGE */
    $sett[ 'front-page-title' ] = array(
        'type' => array( 
            'template' => 'code',
        ),
        'title' => __( 'Front Page Layout' , 'myThemes' )
    );
    $sett[ 'front-page-layout' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'imageSelect'
        ),
        'values' => $layouts,
        'coll' => 3,
        'label' => __( 'Front page layout' , 'myThemes' ),
        'hint' => __( 'If not is set custom layout, will be used default layout.' , 'myThemes' ),
        'action' => "[ 'hs' , { 'full' : '.front-page-sidebar' } ]"
    );

    if( myThemes::pget( 'front-page-layout' ) == 'full' ){
        $sidebarClass = 'front-page-sidebar hidden';
    }else{
        $sidebarClass = 'front-page-sidebar';
    }

    $sett[ 'front-page-sidebar' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'select'
        ),
        'templateClass' => $sidebarClass,
        'values' => $values,
        'label' => __( 'Front page sidebar' , 'myThemes' ),
    );

    /* PAGES LAYOUT */
    $sett[ 'page-title' ] = array(
        'type' => array( 
            'template' => 'code',
        ),
        'title' => __( 'Page Layout' , 'myThemes' )
    );
    $sett[ 'page-layout' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'imageSelect'
        ),
        'values' => $layouts,
        'coll' => 3,
        'label' => __( 'Pages layout' , 'myThemes' ),
        'hint' => __( 'If not is set custom layout, will be used default layout.' , 'myThemes' ),
        'action' => "[ 'hs' , { 'full' : '.page-sidebar' } ]"
    );

    if( myThemes::pget( 'page-layout' ) == 'full' ){
        $sidebarClass = 'page-sidebar hidden';
    }else{
        $sidebarClass = 'page-sidebar';
    }

    $sett[ 'page-sidebar' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'select'
        ),
        'templateClass' => $sidebarClass,
        'values' => $values,
        'label' => __( 'Pages sidebar' , 'myThemes' ),
    );
    
    /* SINGLE POSTS */
    $sett[ 'single-title' ] = array(
        'type' => array( 
            'template' => 'code',
        ),
        'title' => __( 'Single Posts Layout' , 'myThemes' )
    );
    $sett[ 'single-layout' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'imageSelect'
        ),
        'values' => $layouts,
        'coll' => 3,
        'label' => __( 'Single posts layout' , 'myThemes' ),
        'hint' => __( 'If not is set single post layout, will be used default layout.' , 'myThemes' ),
        'action' => "[ 'hs' , { 'full' : '.single-sidebar' } ]"
    );

    if( myThemes::pget( 'single-layout' ) == 'full' ){
        $sidebarClass = 'single-sidebar hidden';
    }else{
        $sidebarClass = 'single-sidebar';
    }

    $sett[ 'single-sidebar' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'select'
        ),
        'templateClass' => $sidebarClass,
        'values' => $values,
        'label' => __( 'Single posts sidebar' , 'myThemes' ),
    );


    /////////////////////////////////////////
    /* SOCIAL SETTINGS */
    $sett[ 'social-settings-title' ] = array(
        'type' => array( 
            'template' => 'none',
        ),
        'content' => '<div style="padding-top: 80px;" class="title"><h2>' . __( 'Social' , 'myThemes' ) . '</h2></div>'
    );

    $sett[ 'github' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Github URL profile' , 'myThemes' )
    );
    $sett[ 'vimeo' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Vimeo URL profile' , 'myThemes' )
    );
    $sett[ 'twitter' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Twitter URL profile' , 'myThemes' )
    );
    $sett[ 'renren' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Renren URL profile' , 'myThemes' )
    );
    $sett[ 'skype' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Skype URL profile' , 'myThemes' )
    );
    $sett[ 'linkedin' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Linkedin URL profile' , 'myThemes' )
    );
    $sett[ 'behance' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Behance URL profile' , 'myThemes' )
    );
    $sett[ 'dropbox' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Dropbox URL profile' , 'myThemes' )
    );
    $sett[ 'flickr' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Flickr URL profile' , 'myThemes' )
    );
    $sett[ 'tumblr' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Tumblr URL profile' , 'myThemes' )
    );
    $sett[ 'instagram' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Instagram URL profile' , 'myThemes' )
    );
    $sett[ 'vkontakte' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Vkontakte URL profile' , 'myThemes' )
    );
    $sett[ 'facebook' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Facebook URL profile' , 'myThemes' )
    );
    $sett[ 'evernote' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Evernote URL profile' , 'myThemes' )
    );
    $sett[ 'flattr' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Flattr URL profile' , 'myThemes' )
    );
    $sett[ 'picasa' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Picasa URL profile' , 'myThemes' )
    );
    $sett[ 'dribbble' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Dribbble URL profile' , 'myThemes' )
    );
    $sett[ 'soundcloud' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Soundcloud URL profile' , 'myThemes' )
    );
    $sett[ 'mixi' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Mixi URL profile' , 'myThemes' )
    );
    $sett[ 'stumbl' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Stumbl URL profile' , 'myThemes' )
    );
    $sett[ 'lastfm' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Lastfm URL profile' , 'myThemes' )
    );
    $sett[ 'gplus' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Google plus URL profile' , 'myThemes' )
    );
    $sett[ 'pinterest' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Pinterest URL profile' , 'myThemes' )
    );
    $sett[ 'smashing' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Smashing URL profile' , 'myThemes' )
    );
    $sett[ 'rdio' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'text'
        ),
        'label' => __( 'Rdio URL profile' , 'myThemes' )
    );
    $sett[ 'rss' ] = array(
        'type' => array(
            'template' => 'inline',
            'input' => 'logic'
        ),
        'label' => __( 'Use RSS' , 'myThemes' )
    );


    /////////////////////////////////////////
    /* OTHERS SETTINGS */
    $sett[ 'others-settings-title' ] = array(
        'type' => array( 
            'template' => 'none',
        ),
        'content' => '<div style="padding-top: 80px;" class="title"><h2>' . __( 'Others' , 'myThemes' ) . '</h2></div>'
    );

    $sett[ 'css' ] = array(
        'type' => array(
            'template' => 'inlist',
            'input' => 'textarea',
            'validator' => 'noesc'
        ),
        'label' => __( 'Add custom css' , 'myThemes' ),
        'hint' => __( 'overwrite styles for some elements using custom css' , 'myThemes' )
    );
?>