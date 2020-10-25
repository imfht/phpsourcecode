                        <footer>
                            <?php
                                ob_start();
                                get_sidebar( 'footer-first' );
                                $first = ob_get_clean();

                                ob_start();
                                get_sidebar( 'footer-second' );
                                $second = ob_get_clean();

                                ob_start();
                                get_sidebar( 'footer-third' );
                                $third = ob_get_clean();

                                $sidebar_content = $first . $second . $third;

                                if( !empty( $sidebar_content ) ){
                            ?>
                                    <aside>
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <?php echo $first; ?>
                                                </div>
                                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                                                    <?php echo $second; ?>
                                                </div>
                                                <div class="col-xs-6 col-sm-3 col-md-3 col-lg-3">
                                                    <?php echo $third; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </aside>
                            <?php
                                }
                            ?>

                            <div class="mythemes-copyright">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <p><?php echo myThemes::get( 'footer-text', true ); ?></p>
                                        </div>
                                        <?php
                                            $github     = myThemes::get( 'github' );
                                            $vimeo      = myThemes::get( 'vimeo' );
                                            $twitter    = myThemes::get( 'twitter' );
                                            $renren     = myThemes::get( 'renren' );
                                            $skype      = myThemes::get( 'skype' );
                                            $linkedin   = myThemes::get( 'linkedin' );
                                            $behance    = myThemes::get( 'behance' );
                                            $dropbox    = myThemes::get( 'dropbox' );
                                            $flickr     = myThemes::get( 'flickr' );
                                            $tumblr     = myThemes::get( 'tumblr' );
                                            $instagram  = myThemes::get( 'instagram' );
                                            $vkontakte  = myThemes::get( 'vkontakte' );
                                            $facebook   = myThemes::get( 'facebook' );
                                            $evernote   = myThemes::get( 'evernote' );
                                            $flattr     = myThemes::get( 'flattr' );
                                            $picasa     = myThemes::get( 'picasa' );
                                            $dribbble   = myThemes::get( 'dribbble' );
                                            $soundcloud = myThemes::get( 'soundcloud' );
                                            $mixi       = myThemes::get( 'mixi' );
                                            $stumbl     = myThemes::get( 'stumbl' );
                                            $lastfm     = myThemes::get( 'lastfm' );
                                            $gplus      = myThemes::get( 'gplus' );
                                            $pinterest  = myThemes::get( 'pinterest' );
                                            $smashing   = myThemes::get( 'smashing' );
                                            $rdio       = myThemes::get( 'rdio' );
                                            $rss        = myThemes::get( 'rss' );
                                        ?>
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="mythemes-social">
                                                <?php
                                                    if( !empty( $github ) ){
                                                        echo '<a href="' . $github . '" class="icon-github" target="_blank"></a>';
                                                    }

                                                    if( !empty( $vimeo ) ){
                                                        echo '<a href="' . $vimeo . '" class="icon-vimeo" target="_blank"></a>';
                                                    }

                                                    if( !empty( $twitter ) ){
                                                        echo '<a href="' . $twitter . '" class="icon-twitter" target="_blank"></a>';
                                                    }

                                                    if( !empty( $renren ) ){
                                                        echo '<a href="' . $renren . '" class="icon-renren" target="_blank"></a>';
                                                    }

                                                    if( !empty( $skype ) ){
                                                        echo '<a href="' . $skype . '" class="icon-skype" target="_blank"></a>';
                                                    }

                                                    if( !empty( $linkedin ) ){
                                                        echo '<a href="' . $linkedin . '" class="icon-linkedin" target="_blank"></a>';
                                                    }

                                                    if( !empty( $behance ) ){
                                                        echo '<a href="' . $behance . '" class="icon-behance" target="_blank"></a>';
                                                    }

                                                    if( !empty( $dropbox ) ){
                                                        echo '<a href="' . $dropbox . '" class="icon-dropbox" target="_blank"></a>';
                                                    }

                                                    if( !empty( $flickr ) ){
                                                        echo '<a href="' . $flickr . '" class="icon-flickr" target="_blank"></a>';
                                                    }

                                                    if( !empty( $tumblr ) ){
                                                        echo '<a href="' . $tumblr . '" class="icon-tumblr" target="_blank"></a>';
                                                    }

                                                    if( !empty( $instagram ) ){
                                                        echo '<a href="' . $instagram . '" class="icon-instagram" target="_blank"></a>';
                                                    }

                                                    if( !empty( $vkontakte ) ){
                                                        echo '<a href="' . $vkontakte . '" class="icon-vkontakte" target="_blank"></a>';
                                                    }

                                                    if( !empty( $facebook ) ){
                                                        echo '<a href="' . $facebook . '" class="icon-facebook" target="_blank"></a>';
                                                    }

                                                    if( !empty( $evernote ) ){
                                                        echo '<a href="' . $evernote . '" class="icon-evernote" target="_blank"></a>';
                                                    }

                                                    if( !empty( $flattr ) ){
                                                        echo '<a href="' . $flattr . '" class="icon-flattr" target="_blank"></a>';
                                                    }

                                                    if( !empty( $picasa ) ){
                                                        echo '<a href="' . $picasa . '" class="icon-picasa" target="_blank"></a>';
                                                    }

                                                    if( !empty( $dribbble ) ){
                                                        echo '<a href="' . $dribbble . '" class="icon-dribbble" target="_blank"></a>';
                                                    }

                                                    if( !empty( $soundcloud ) ){
                                                        echo '<a href="' . $soundcloud . '" class="icon-soundcloud" target="_blank"></a>';
                                                    }

                                                    if( !empty( $mixi ) ){
                                                        echo '<a href="' . $mixi . '" class="icon-mixi" target="_blank"></a>';
                                                    }

                                                    if( !empty( $stumbl ) ){
                                                        echo '<a href="' . $stumbl . '" class="icon-stumbl" target="_blank"></a>';
                                                    }

                                                    if( !empty( $lastfm ) ){
                                                        echo '<a href="' . $lastfm . '" class="icon-lastfm" target="_blank"></a>';
                                                    }

                                                    if( !empty( $gplus ) ){
                                                        echo '<a href="' . $gplus . '" class="icon-gplus" target="_blank"></a>';
                                                    }

                                                    if( !empty( $pinterest ) ){
                                                        echo '<a href="' . $pinterest . '" class="icon-pinterest" target="_blank"></a>';
                                                    }

                                                    if( !empty( $smashing ) ){
                                                        echo '<a href="' . $smashing . '" class="icon-smashing" target="_blank"></a>';
                                                    }

                                                    if( !empty( $rdio ) ){
                                                        echo '<a href="' . $rdio . '" class="icon-rdio" target="_blank"></a>';
                                                    }

                                                    if( $rss ){
                                                        echo '<a href="'; bloginfo('rss2_url');  echo '" class="icon-rss" target="_blank"></a>';
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </footer>

                    </div>
                </div>
            </div>
        </div>

        <div class="mythemes-scroll-up">
            <a href="javascript:void(null);" class="icon-up-open"></a>
        </div>

        <?php wp_footer(); ?>

    </body>
</html>