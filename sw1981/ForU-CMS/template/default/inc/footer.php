<footer class="bg--dark footer-4">
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-sm-4 visible-md visible-lg">
        <a href="./"><img alt="logo" class="logo" src="<?php echo $t_path;?>img/logo-light.png" /></a>
        <ul class="footer__navigation">
          <?php echo navigation_s(0);?>
        </ul>
      </div>
      <div class="col-md-4 col-sm-8">
        <h6>近期新闻</h6>
        <div class="footer__navigation">
          <ul class="slides">
            <?php
            $cid=2;
            $res = list_detail(get_channel($cid, 'c_sub'), '0,5');
            foreach ($res as $val) {
              $durl = !empty($val['d_link']) ? $val['d_link'] : d_url($val['id']);
              echo '<li><a href="'.$durl.'">'.$val['d_name'].'</a>&nbsp; &nbsp; '.local_date('Y-m-d',$val['d_date']).'</li>';
            }
            ?>
          </ul>
        </div>
      </div>
      <div class="col-md-4 col-md-offset-1 col-sm-12">
        <h6>邮件订阅</h6>
        <p>免费获取最新资讯</p>
        <form class="form--merge form--no-labels" action="ajax.php?act=subscribe" method="post" id="subForm" data-error="请核实您的电子邮箱" data-success="您已订阅成功">
            <input class="col-md-8 col-sm-6 validate-required validate-email" id="fieldEmail" name="sub_mail" type="email" required />
            <button type="submit">Go</button>
        </form>
        <h6>联系我们</h6>
        <ul class="social-list">
          <li>
            <a href="#">
              <i class="socicon-weibo"></i>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="socicon-blogger"></i>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="socicon-qq"></i>
            </a>
          </li>
          <li>
            <a href="#">
              <i class="socicon-mail"></i>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <!--end of row-->
  </div>
  <!--end of container-->
  <div class="footer__lower">
    <div class="container">
      <div class="row">
        <div class="col-sm-6 text-center-xs">
          <span class="type--fine-print"><?php echo $cms['s_copyright'];?></span>
        </div>
        <div class="col-sm-6 text-right text-center-xs">
          <a href="#top" class="inner-link top-link">
            <i class="interface-up-open-big"></i>
          </a>
        </div>
      </div>
      <?php echo $cms['s_code'];?>
      <!--end of row-->
    </div>
    <!--end of container-->
  </div>
</footer>