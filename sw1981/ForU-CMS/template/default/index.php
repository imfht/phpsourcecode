<!doctype html>
<html>
<head><?php include 'inc/head.php';?></head>
<body class="scroll-assist" data-reveal-selectors="section:not(.masonry):not(:first-of-type):not(.parallax)" data-reveal-timing="1000">
  <a id="top"></a>
  <div class="loader"></div>
  <?php include 'inc/nav_index.php';?>
  <div class="main-container transition--fade">
    <?php include 'inc/banner.php';?>
    <!-- 产品介绍 -->
    <section class="space-bottom--xs">
      <div class="container">
        <div class="row">
          <div class="col-sm-8 col-sm-offset-2 text-center">
            <?php
            $cid = 3;
            $csub = get_channel($cid,'c_sub');
            ?>
            <h3><?php echo get_channel($cid,'c_name');?></h3>
            <p class="lead"><?php echo get_channel($cid,'c_scontent');?></p>
          </div>
        </div>
        <!--end of row-->
      </div>
    </section>
    <!-- 产品推荐 -->
    <section class="wide-grid masonry">
      <div class="masonry__container">
        <?php
        $res = list_detail($csub, '0,6');
        if (!empty($res)) {
          foreach ($res as $val) {
            echo '<div class="col-md-4 col-sm-6 masonry__item" data-masonry-filter="'.get_channel($val['d_parent'],'c_name').'"><a href="'.d_url($val['id'],$val['d_link']).'"><div class="hover-element hover-element-1" data-title-position="top,right"><div class="hover-element__initial"><img alt="Pic" src="'.img_always($val['d_picture']).'" /></div><div class="hover-element__reveal" data-overlay="9"><div class="boxed"><h5>'.$val['d_name'].'</h5><span><em>'.$val['d_scontent'].'</em></span></div></div></div></a></div>';
          }
        }else{
          echo '<div class="col-md-4 col-sm-6 masonry__item" data-masonry-filter="digital"><a href="#"><div class="hover-element hover-element-1" data-title-position="top,right"><div class="hover-element__initial"><img alt="Pic" src="'.$t_path.'img/work1.jpg" /></div><div class="hover-element__reveal" data-overlay="9"><div class="boxed"><h5>Freehance</h5><span><em>iOS Application</em></span></div></div></div></a></div><div class="col-md-4 col-sm-6 masonry__item" data-masonry-filter="branding"><a href="#"><div class="hover-element hover-element-1" data-title-position="top,right"><div class="hover-element__initial"><img alt="Pic" src="'.$t_path.'img/work2.jpg" /></div><div class="hover-element__reveal" data-overlay="9"><div class="boxed"><h5>Michael Andrews</h5><span><em>Branding & Identity</em></span></div></div></div></a></div><div class="col-md-4 col-sm-6 masonry__item" data-masonry-filter="branding"><a href="#"><div class="hover-element hover-element-1" data-title-position="top,right"><div class="hover-element__initial"><img alt="Pic" src="'.$t_path.'img/work3.jpg" /></div><div class="hover-element__reveal" data-overlay="9"><div class="boxed"><h5>Pillar Stationary</h5><span><em>Branding Collateral</em></span></div></div></div></a></div><div class="col-md-4 col-sm-6 masonry__item" data-masonry-filter="packaging"><a href="#"><div class="hover-element hover-element-1" data-title-position="top,right"><div class="hover-element__initial"><img alt="Pic" src="'.$t_path.'img/work4.jpg" /></div><div class="hover-element__reveal" data-overlay="9"><div class="boxed"><h5>Authentic Apparel</h5><span><em>Packaging Design</em></span></div></div></div></a></div><div class="col-md-4 col-sm-6 masonry__item" data-masonry-filter="branding"><a href="#"><div class="hover-element hover-element-1" data-title-position="top,right"><div class="hover-element__initial"><img alt="Pic" src="'.$t_path.'img/work5.jpg" /></div><div class="hover-element__reveal" data-overlay="9"><div class="boxed"><h5>Wave Poster</h5><span><em>Logo Design</em></span></div></div></div></a></div><div class="col-md-4 col-sm-6 masonry__item" data-masonry-filter="digital"><a href="#"><div class="hover-element hover-element-1" data-title-position="top,right"><div class="hover-element__initial"><img alt="Pic" src="'.$t_path.'img/work6.jpg" /></div><div class="hover-element__reveal" data-overlay="9"><div class="boxed"><h5>Tesla Controller</h5><span><em>Apple Watch Application</em></span></div></div></div></a></div>'; }
        ?>
      </div>
      <!--end masonry container-->
    </section>
    <!-- 企业介绍 -->
    <section class="space-bottom--sm">
      <div class="container">
        <div class="row">
          <div class="col-sm-8 col-sm-offset-2 text-center">
            <?php $cid = 1; ?>
            <h3><?php echo $cms['s_name'];?></h3>
            <p class="lead"><?php echo get_channel($cid,'c_scontent');?></p>
          </div>
        </div>
        <!--end of row-->
      </div>
      <!--end of container-->
    </section>
    <!-- 企业信息 -->
    <section class="imagebg section--even stats-1 parallax" data-overlay="7">
      <div class="background-image-holder">
        <img alt="image" src="<?php echo $t_path;?>img/hero2.jpg" />
      </div>
      <div class="row wide-grid"><?php include 'inc/corp_info.php';?></div>
      <!--end of row-->
    </section>
    <!-- 新闻介绍 -->
    <section>
      <div class="container">
        <div class="row">
          <div class="col-sm-8 col-sm-offset-2 text-center">
            <?php
            $cid = 2;
            $csub = get_channel($cid,'c_sub');
            ?>
            <h3><?php echo get_channel($cid,'c_name');?></h3>
            <p class="lead"><?php echo get_channel($cid,'c_scontent');?></p>
          </div>
        </div>
        <!--end of row-->
      </div>
      <!--end of container-->
    </section>
    <!-- 新闻列表 -->
    <section class="blog-snippet-1">
      <div class="container">
        <div class="row">
          <?php
          $res = list_detail($csub, '0,3');
          if (!empty($res)) {
            foreach ($res as $val) {
              echo '<div class="col-md-4 col-sm-6"><a href="'.d_url($val['id'],$val['d_link']).'"><div class="card card-4"><div class="card__image"><img alt="Pic" src="'.img_always($val['d_picture']).'" /></div><div class="card__body boxed boxed--sm bg--white"><h6>'.$val['d_name'].'</h6><div class="card__title"><h5>'.str_cut(str_text($val['d_content']),40).'</h5></div><hr><div class="card__lower"><span>'.local_date('Y-m-d',$val['d_date']).'</span></div></div></div></a></div>';
            }
          } else {
            echo '<div class="col-md-4 col-sm-6"><a href="#"><div class="card card-4"><div class="card__image"><img alt="Pic" src="'.$t_path.'img/small9.jpg" /></div><div class="card__body boxed boxed--sm bg--white"><h6>Lifestyle</h6><div class="card__title"><h5>Living large in a small apartment: One man\'s quest to downsize</h5></div><hr><div class="card__lower"><span>2015-01-01</span></div></div></div></a></div><div class="col-md-4 col-sm-6"><a href="#"><div class="card card-4"><div class="card__image"><img alt="Pic" src="'.$t_path.'img/small6.jpg" /></div><div class="card__body boxed boxed--sm bg--white"><h6>Technology</h6><div class="card__title"><h5>Good photography is the key to your online presence</h5></div><hr><div class="card__lower"><span>2015-01-01</span></div></div></div></a></div><div class="col-md-4 col-sm-6"><a href="#"><div class="card card-4"><div class="card__image"><img alt="Pic" src="'.$t_path.'img/small1.jpg" /></div><div class="card__body boxed boxed--sm bg--white"><h6>Travel</h6><div class="card__title"><h5>Meet the couple who ran their online business from a tent</h5></div><hr><div class="card__lower"><span>2015-01-01</span></div></div></div></a></div>';
          }
          ?>
        </div>
        <!--end of row-->
      </div>
      <!--end of container-->
    </section>
    <!-- 合作伙伴 -->
    <?php include 'inc/partner.php';?>
    <!-- 合作意向 -->
    <section class="bg--primary space--sm cta cta-5">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 text-center">
            <h4>您有合作意向了吗?</h4>
            <?php
            $cid = 6;
            $clink=get_channel($cid,'c_link');
            ?>
            <a class="btn btn--sm" href="<?php echo !empty($clink) ? $clink : c_url($cid);?>">
              <span class="btn__text">
                让我们聊聊吧
              </span>
            </a>
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
