<!doctype html>
<html>
<head><?php include 'inc/head.php';?></head>
<body class="scroll-assist" data-reveal-selectors="section:not(.masonry):not(:first-of-type):not(.parallax)" data-reveal-timing="1000">
  <a id="top"></a>
  <div class="loader"></div>
  <?php include 'inc/nav.php';?>
  <div class="main-container transition--fade">
    <section class="blog-post">
      <div class="blog-post__title bg--secondary">
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
      <div class="container">
        <div class="row">
          <div class="col-sm-10 col-sm-offset-1 text-center">
            <?php
            if (!empty($detail['d_picture'])) echo '<img class="blog-post__hero box-shadow" alt="pic" src="'.img_always($detail['d_picture']).'" />';
            ?>
          </div>
          <div class="col-sm-8 col-sm-offset-2"><?php echo $detail['d_content'];?></div>
        </div>
        <!--end of row-->
      </div>
      <!--end of container-->
    </section>
    <?php include 'inc/footer.php';?>
  </div>
  <?php include 'inc/js.php';?>
</body>
</html>
