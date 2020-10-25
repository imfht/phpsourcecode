<!doctype html>
<html>
<head><?php include 'inc/head.php';?></head>
<body class="scroll-assist" data-reveal-selectors="section:not(.masonry):not(:first-of-type):not(.parallax)" data-reveal-timing="1000">
  <a id="top"></a>
  <div class="loader"></div>
  <?php include 'inc/nav_index.php';?>
  <div class="main-container transition--fade">
    <?php include 'inc/banner.php';?>
    <section class="features features-10">
      <div class="feature bg--white col-md-4 text-center">
        <i class="icon icon--lg icon-Map-Marker2"></i>
        <h4>所在地址</h4>
        <p><?php echo get_chip('contact-addr');?></p>
      </div>
      <div class="feature bg--secondary col-md-4 text-center">
        <i class="icon icon--lg icon-Phone-2"></i>
        <h4>致电我们</h4>
        <p><?php echo get_chip('contact-hotline');?></p>
      </div>
      <div class="feature bg--dark col-md-4 text-center">
        <i class="icon icon--lg icon-Computer"></i>
        <h4>在线联系</h4>
        <p><?php echo get_chip('contact-qq');?></p>
      </div>
    </section>
    <section>
      <div class="container">
        <div class="row">
          <div class="col-sm-8 col-sm-offset-2">
            <form action="ajax.php" method="post" class="form--square" data-success="谢谢您的关注与支持" data-error="请填写所有必填项">
              <h4 class="text-center">您也可以通过这里给我们留言</h4>
              <div class="input-with-icon col-sm-12">
                <i class="icon-MaleFemale"></i>
                <input class="validate-required" type="text" name="name" placeholder="您的姓名" />
              </div>
              <div class="input-with-icon col-sm-6">
                <i class="icon-Email"></i>
                <input class="validate-required validate-email" type="email" name="email" placeholder="电子邮箱地址" />
              </div>
              <div class="input-with-icon col-sm-6">
                <i class="icon-Phone-2"></i>
                <input type="tel" name="tel" placeholder="电话号码" />
              </div>
              <div class="col-sm-12">
                <textarea class="validate-required" name="message" placeholder="您的信息" rows="8"></textarea>
              </div>
              <div class="col-sm-12">
                <button type="submit" class="btn btn--primary">提交表单</button>
              </div>
              <input type="hidden" name="act" value="feedback_post_mail">
              <!--
              上面的是发送邮件
              下面的是写入数据库
              <input type="hidden" name="act" value="feedback_post">
               -->
            </form>
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
