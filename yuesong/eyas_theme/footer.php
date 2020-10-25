<!-- /.container -->
    <footer class="blog-footer">
      <div class="container">
        <p>Designer and built by <a href="mailto:liuyuesongde@163.com">Eyas（liuyuesongde@163.com）</a>.</p>
        <p>Power by <a href="http://w.org/">wordpress</a> 、 <a href="http://getbootstrap.com/"> Bootstrap </a> and <a href="#"> Flat UI </a>.</p>
      </div>
    </footer>


    <!-- Load JS here for greater good =============================-->
    <script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/jquery.ui.touch-punch.min.js"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/respond.min.js"></script>
    <script src="<?php bloginfo('template_directory'); ?>/js/jquery.qrcode.min.js"></script>
   <script>
    $(function(){
      $('.current-menu-item').addClass('active');
      $('.post-container').css('min-height',$('.sidebar').height());
      $('#page-erweima').qrcode({
        width:260,
        height:260,
        text:'<?php echo home_url(add_query_arg(array(),$wp->request)); ?>'
      });
    });
    </script>
    <?php wp_footer(); ?>
  </body>
</html>
