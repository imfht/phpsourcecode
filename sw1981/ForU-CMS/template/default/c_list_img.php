<!doctype html>
<html>
<head><?php include 'inc/head.php';?></head>
<body class="scroll-assist" data-reveal-selectors="section:not(.masonry):not(:first-of-type):not(.parallax)" data-reveal-timing="1000">
  <a id="top"></a>
  <div class="loader"></div>
  <?php include 'inc/nav_index.php';?>
  <div class="main-container transition--fade">
    <?php include 'inc/banner.php';?>
    <section class="masonry-contained">
      <div class="container">
        <div class="row">
          <div class="masonry">
            <div class="masonry__filters" data-filter-all-text="Show All"></div>
            <div class="masonry__container masonry--animate">
              <?php
              if ($res = $list_pager) {
                foreach ($res as $val) {
                  echo '<div class="col-md-4 col-sm-6 masonry__item" data-masonry-filter="'.get_channel($val['d_parent'],'c_name').'"><a href="'.d_url($val['id'],$val['d_link']).'"><div class="portfolio-item portfolio-item-1 text-center"><img alt="'.$val['d_name'].'" src="'.img_always($val['d_picture']).'" /><h5>'.$val['d_name'].'</h5></div></a></div>';
                }
              }
              ?>
            </div>
            <!--end masonry container-->
          </div>
          <div class="pagination-container">
            <hr>
            <ul class="pagination">
              <?php echo $pager->show();?>
            </ul>
          </div>
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
