<?php
    $bkg        = '';
    $header_img = get_header_image();

    if( is_admin() ){
        $bkg = 'background-image: url(' . $header_img . '); background-position: top center; background-repeat: no-repeat;';
    }
?>
<div class="mythemes-header mythemes-bkg-image" style="<?php echo $bkg; ?> height: <?php echo myThemes::get( 'header-height' ); ?>px;" data-bkg-image="<?php echo $header_img; ?>" data-bkg-color="<?php echo myThemes::get( 'mask-color' ); ?>">
    <div style="background: rgba( <?php echo mythemes_tools::hex2rgb( myThemes::get( 'mask-color' ) ); ?>, <?php echo (float)myThemes::get( 'mask-opacity' ) / 100; ?> ); height: <?php echo myThemes::get( 'header-height' ); ?>px;">

        <div class="overflow-wrapper" style="height: <?php echo (int)myThemes::get( 'header-height' ); ?>px;">
            <div class="valign-cell-wrapper">

                <div class="valign-cell">
                    
                        <div class="row">
                            <div class="col-lg-12">
                                <div style="text-align: center;">
                                
                                    <div class="mythemes-header-animation">

                                        <?php if( myThemes::get( 'logo' ) ) { ?>
                                            <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); echo ' '; bloginfo( 'description' ); ?>">
                                                <img src="<?php echo myThemes::get( 'logo' ); ?>" alt="<?php bloginfo( 'name' ); echo ' '; bloginfo( 'description' ); ?>"/>
                                            </a>
                                        <?php } ?>

                                        <?php if( is_home() || is_front_page() ) { ?>
                                            <h1 class="brand"><a class="mythemes-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); echo ' '; bloginfo( 'description' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
                                            <a class="mythemes-description" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); echo ' '; bloginfo( 'description' ); ?>"><?php bloginfo( 'description' ); ?></a>
                                        <?php }else{ ?>
                                            <a class="mythemes-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); echo ' '; bloginfo( 'description' ); ?>"><?php bloginfo( 'name' ); ?></a>
                                            <a class="mythemes-description" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); echo ' '; bloginfo( 'description' ); ?>"><?php bloginfo( 'description' ); ?></a>
                                        <?php } ?>
                                    </div>

                                    <?php
                                        /* HEADER BUTTONS */
                                        if( myThemes::get( 'show-first-button' ) || myThemes::get( 'show-second-button' ) ){
                                    ?>

                                            <p class="buttons">
                                                <?php
                                                    /* FIRST BUTTON */
                                                    if( myThemes::get( 'show-first-button' ) ){
                                                ?>
                                                        <a href="<?php echo myThemes::get( 'first-button-url' ) ?>" class="btn first-button" title="<?php echo myThemes::get( 'first-button-desc' ) ?>"><?php echo myThemes::get( 'first-button-label' ) ?></a> 
                                                <?php
                                                    }

                                                    /* SECOND BUTTON */
                                                    if( myThemes::get( 'show-second-button' ) ){
                                                ?>
                                                        <a href="<?php echo myThemes::get( 'second-button-url' ) ?>" class="btn second-button" title="<?php echo myThemes::get( 'second-button-desc' ) ?>"><?php echo myThemes::get( 'second-button-label' ) ?></a> 
                                                <?php
                                                    }
                                                ?>
                                            </p>
                                    <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                </div>
            </div>
        </div>
    </div>
</div>