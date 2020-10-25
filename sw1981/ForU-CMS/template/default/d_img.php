<!doctype html>
<html>
<head><?php include 'inc/head.php';?></head>
<body class="scroll-assist" data-reveal-selectors="section:not(.masonry):not(:first-of-type):not(.parallax)" data-reveal-timing="1000">
  <a id="top"></a>
  <div class="loader"></div>
  <?php include 'inc/nav.php';?>
  <div class="main-container transition--fade">
    <section>
      <div class="blog-post__title">
        <div class="container">
          <div class="row">
            <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 text-center">
              <h1><?php echo $detail['d_name'];?></h1>
              <div class="blog-post__author">
                <span class="h6"><?php echo local_date('Y-m-d',$detail['d_date']);?></span>
              </div>
            </div>
          </div>
          <!--end of row-->
        </div>
        <!--end of container-->
      </div>
    </section>
    <?php
    if (!empty($detail['d_slideshow'])) {
      echo '<section><div class="container"><div class="row"><div class="col-sm-10 col-sm-offset-1 text-center"><div class="slider" data-arrows="false" data-paging="true" data-timing="5000"><ul class="slides">';
      $arr=explode('|',$detail['d_slideshow']);
      foreach ($arr as $val) {
        echo '<li><img src="'.img_always($val).'" alt="'.$detail['d_name'].'"></li>';
      }
      echo '</ul></div></div></div></div></section>';
    }
    ?>
    <section>
      <div class="container">
        <div class="row">
          <div class="col-sm-8 col-sm-offset-2"><?php echo $detail['d_content'];?></div>
        </div>
        <!--end of row-->
      </div>
    </section>
    <?php include 'inc/footer.php';?>
  </div>
  <?php include 'inc/js.php';?>
</body>
</html>
