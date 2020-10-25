<!doctype html>
<html>
<head><?php include 'inc/head.php';?></head>
<body class="scroll-assist" data-reveal-selectors="section:not(.masonry):not(:first-of-type):not(.parallax)" data-reveal-timing="1000">
  <a id="top"></a>
  <div class="loader"></div>
  <?php include 'inc/nav.php';?>
  <!--end of modal-container-->
  <div class="main-container transition--fade">
    <!-- 结果简述 -->
    <section class="height-40" data-overlay="3">
      <div class="container pos-vertical-center">
        <div class="row">
          <div class="col-sm-12 text-center">
            <h3>关键词:
              <span>&ldquo;<?php echo $keyword;?>&rdquo;</span>
            </h3>
            <span>
              <?php
              if (!empty($_POST['tag'])) {
                $res_count = $db->getOne("SELECT COUNT(*) FROM detail WHERE INSTR(d_tag, '$keyword')");
              } else {
                $res_count = $db->getOne("SELECT COUNT(*) FROM detail WHERE INSTR(d_name, '$keyword')");
              }
              echo '<em>检索得到 '.$res_count.' 个结果</em>';
              ?>
            </span>
          </div>
        </div>
      </div>
    </section>
    <!-- 结果列表 -->
    <section class="bg--secondary">
      <div class="container">
        <div class="row">
          <div class="masonry">
            <div class="masonry__container">
              <?php
              $pager = new Page(18);
              $pager->handle($res_count);
              if (!empty($_POST['tag'])) {
                $res = $db->getAll("SELECT * FROM detail WHERE INSTR(d_tag, '$keyword') ORDER BY d_order ASC,id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
              } else {
                $res = $db->getAll("SELECT * FROM detail WHERE INSTR(d_name, '$keyword') ORDER BY d_order ASC,id DESC LIMIT " . $pager->page_start . "," . $pager->page_size);
              }
              foreach ($res as $val) {
                echo '<div class="col-md-4 col-sm-6 masonry__item" data-masonry-filter="'.get_channel($val['d_parent'], 'c_name').'"><a href="'.d_url($val['id'], $val['d_link']).'"><div class="boxed bg--white box-shadow"><span>'.get_channel($val['d_parent'], 'c_name').'</span><h5>'.$val['d_name'].'</h5><hr><p>'.str_cut(str_text($val['d_content']), 50).'</p></div></a></div>';
              }
              ?>
            </div>
            <!--end of masonry container-->
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
    <!-- 重新检索 -->
    <section class="space--even bg--white">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 text-center">
            <h4>没有找到您想要找的东西吗?</h4>
            <a class="btn btn--primary modal-trigger" href="#" data-modal-id="search-form">
              <span class="btn__text">重新检索</span>
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
