<?php
use yii\helpers\Url;
use yii\widgets\LinkPager;
// use common\widgets\Carousel;

$this->title = '主页';
?>
<!--main-->
<div class="main">
  <div class="box">
    <div class="box_top">
      <!-- banner-->
      <div class="FocusPic">
          <div class="content" id="main-slide">
              <div class="changeDiv">
                  <?php foreach ( $carousel as $item ) { ?>
                      <a href="<?= $item['link'] ?>" title="<?= $item['title'] ?>"><img src="<?= $item['img_url'] ?>" width="100%" height="100%"/></a>
                  <?php } ?>
              </div>
          </div>
      </div>
      <!---banner end-->
      <!--选项卡-->
      <div class="choose">
        <div id="menubox">
          <ul>
            <li id="two1" onmousemove="setTab('two',1,3)" class="hover" onclick="gourl(1)">工作动态</li>
            <li id="two2" onmousemove="setTab('two',2,3)">文化动态</li>
            <li id="two3" onmousemove="setTab('two',3,3)" onclick="gourl(2)">通知公告</li>
          </ul>
        </div>
        <div id="conten">
          <div class="city_ser_show" id="con_two_1">
            <div class="news" id="whdt">
            	<?php
	            	foreach ( $posts_gzdt as $post ) {
	                    if ( mb_strlen($post['title']) >19 )
	                        $title = mb_substr($post['title'], 0, 19, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>">
		                    <div class="title" style='<?php if ( $post['important'] >= 100 ) echo 'font-weight: bolder; color: #111 !important;'; ?>'><?= $title ?></div>
		                    <font style='<?php if ( $post['important'] >= 100 ) echo 'font-weight: bolder; color: #111 !important;'; ?>'><?= date('Y-m-d', $post['published_at']) ?></font>
	                    </a>
	                </li>
            	<?php } ?>
            </div>
            <div class="morelink"><a href="<?= Url::to(['category/gzdt']) ?>" class="more">更多>></a></div>
          </div>
          <div class="city_ser_show" id="con_two_2">
            <div class="news">
				<?php
	            	foreach ( $posts_whdt as $post ) {
	                    if ( mb_strlen($post['title']) >19 )
	                        $title = mb_substr($post['title'], 0, 19, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>">
		                    <div class="title"><?= $title ?></div>
		                    <font><?= date('Y-m-d', $post['published_at']) ?></font>
	                    </a>
	                </li>
            	<?php } ?>
            </div>
            <div class="morelink"><a href="<?= Url::to(['category/whdt']) ?>" class="more">更多>></a></div>
          </div>
          <div class="city_ser_show" id="con_two_3">
            <div class="news">
                <?php
	            	foreach ( $posts_tzgg as $post ) {
	                    if ( mb_strlen($post['title']) >19 )
	                        $title = mb_substr($post['title'], 0, 19, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>">
		                    <div class="title"><?= $title ?></div>
		                    <font><?= date('Y-m-d', $post['published_at']) ?></font>
	                    </a>
	                </li>
            	<?php } ?>
            </div>
            <div class="morelink"><a href="<?= Url::to(['category/tzgg']) ?>" class="more">更多>></a></div>
          </div>
        </div>
      </div>

      <!--选项卡 end-->
    </div>
    <div id="imgADPlayer"  class="advertisement"></div>
    <div class="box_bot">
      <div class="box_bot_l">
        <div class="newlist">
          <div class="newlist_top"><span>专业艺术</span><a href="<?= Url::to(['category/zyys']) ?>" class="more">更多>></a></div>
          <div class="news">
            	<?php
	            	foreach ( $posts_zyys as $post ) {
	                    if ( mb_strlen($post['title']) >16 )
	                        $title = mb_substr($post['title'], 0, 16, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>"><?= $post['title'] ?></a>
	                </li>
            	<?php } ?>
          </div>
        </div>
        <div class="newlist jg">
          <div class="newlist_top"><span>社会文化</span><a href="<?= Url::to(['category/shwh']) ?>" class="more">更多>></a></div>
          <div class="news">
              <?php
	            	foreach ( $posts_shwh as $post ) {
	                    if ( mb_strlen($post['title']) >16 )
	                        $title = mb_substr($post['title'], 0, 16, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>"><?= $post['title'] ?></a>
	                </li>
            	<?php } ?>
          </div>
        </div>
        <div class="newlist">
          <div class="newlist_top"><span>文化遗产</span><a href="<?= Url::to(['category/whyc']) ?>" class="more">更多>></a></div>
          <div class="news">
              <?php
	            	foreach ( $posts_whyc as $post ) {
	                    if ( mb_strlen($post['title']) >16 )
	                        $title = mb_substr($post['title'], 0, 16, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>"><?= $post['title'] ?></a>
	                </li>
            	<?php } ?>
          </div>
        </div>
        <div class="newlist jg">
          <div class="newlist_top"><span>文化市场</span><a href="<?= Url::to(['category/whsc']) ?>" class="more">更多>></a></div>
          <div class="news">
                <?php
	            	foreach ( $posts_whsc as $post ) {
	                    if ( mb_strlen($post['title']) >16 )
	                        $title = mb_substr($post['title'], 0, 16, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>"><?= $post['title'] ?></a>
	                </li>
            	<?php } ?>
          </div>
        </div>
      </div>
      <div class="box_bot_r">
        <div class="newlist">
          <div class="newlist_top"><span>视频新闻</span><a href="<?= Url::to(['category/spxw']) ?>" class="more">更多>></a></div>
          <div class="video"><a href="<?= Url::to('post/'.$posts_spxw[0]['id'], true) ?>"><img src="<?= $posts_spxw[0]['thumbnail'] ?>"></a></div>
          <div class="news">
                <?php
	            	foreach ( $posts_spxw as $post ) {
	                    if ( mb_strlen($post['title']) >16 )
	                        $title = mb_substr($post['title'], 0, 16, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li style="background: url(<?= Url::to('@web/hss_whj/images/t-video.png') ?>) left center no-repeat;background-size:14px 14px;">
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>"><?= $post['title'] ?></a>
	                </li>
            	<?php } ?>
          </div>
        </div>
        <div class="yqlj">
            <a href="http://www.ccm.gov.cn/swordcms/publish/default/static/bsguide/index.htm"><img src="<?= Url::to('@web/hss_whj/images/index_03.jpg') ?>"></a>

            <a href="http://www.ccm.gov.cn/swordcms/publish/default/static/bsguide/index.htm"><img src="<?= Url::to('@web/hss_whj/images/index_04.jpg') ?>" class="jg"></a>
        </div>
      </div>
    </div>

    <!--精彩图集-->
    <div class="sypic">
      <div class="sypic_left"></div>
      <div class="sypic_right">
        <!--滚动图片-->
        <div class="index_Roll">
          <div id="demo">
            <div id="indemo">
              <div id="demo1">
                <li><a href=""><img src="<?= Url::to('@web/hss_whj/images/banner/1.jpg') ?>"/>
                  <p>图片标题</p>
                  </a> </li>
                <li><a href=""><img src="<?= Url::to('@web/hss_whj/images/banner/2.jpg') ?>"/>
                  <p>图片标题</p>
                  </a> </li>
                <li><a href=""><img src="<?= Url::to('@web/hss_whj/images/banner/3.jpg') ?>"/>
                  <p>图片标题</p>
                  </a> </li>
                <li><a href=""><img src="<?= Url::to('@web/hss_whj/images/banner/4.jpg') ?>"/>
                  <p>图片标题</p>
                  </a> </li>
                <li><a href=""><img src="<?= Url::to('@web/hss_whj/images/banner/5.jpg') ?>"/>
                  <p>图片标题</p>
                  </a> </li>
              </div>
              <div id="demo2"></div>
            </div>
          </div>
        </div>
        <!--滚动图片-->
      </div>
    </div>
    <!--精彩图集 end-->

    <!--下边新闻-->
    <div class="sy_bot">
      <div class="newlist">
        <div class="newlist_top"><span>新闻出版</span><a href="<?= Url::to(['category/xwcb']) ?>" class="more">更多>></a></div>
        <div class="news">
            <?php
	            	foreach ( $posts_xwcb as $post ) {
	                    if ( mb_strlen($post['title']) >16 )
	                        $title = mb_substr($post['title'], 0, 16, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>"><?= $post['title'] ?></a>
	                </li>
            	<?php } ?>
        </div>
      </div>
      <div class="newlist jg">
        <div class="newlist_top"><span>文化产业</span><a href="<?= Url::to(['category/whcy']) ?>" class="more">更多>></a></div>
        <div class="news">
            <?php
	            	foreach ( $posts_whcy as $post ) {
	                    if ( mb_strlen($post['title']) >16 )
	                        $title = mb_substr($post['title'], 0, 16, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>"><?= $post['title'] ?></a>
	                </li>
            	<?php } ?>
        </div>
      </div>
      <div class="newlist jg">
        <div class="newlist_top"><span>政策法规</span><a href="<?= Url::to(['category/zcfg']) ?>" class="more">更多>></a></div>
        <div class="news">
            <?php
	            	foreach ( $posts_zcfg as $post ) {
	                    if ( mb_strlen($post['title']) >16 )
	                        $title = mb_substr($post['title'], 0, 16, 'utf-8') . '...';
	                    else
	                        $title = $post['title'];
                ?>
	                <li>
	                    <a href="<?= Url::to('post/'.$post['id'], true) ?>"><?= $post['title'] ?></a>
	                </li>
            	<?php } ?>
        </div>
      </div>

    </div>
    <iframe width="100%" id="tushuo" scrolling="no" height="268" frameborder="0" target="_blank" hspace="0" vspace="0" marginheight="0" marginwidth="0" src="http://www.wenming.cn/gxym/sygygg/index_10867.shtml">
      </iframe>
    <!--下边新闻 end-->
    <script type="text/javascript">

        console.log($('#tushu').contents().find('#tab_1_1').attr('class'));
    </script>

    <!--下属单位-->
    <div class="xsdw">
        <div class="xsdw_top">
          <div class="lft" style="margin-left:0;"><a href="<?= Url::to('contact') ?>">局长邮箱</a></div>
          <div class="lft"><a href="<?= Url::to('contact') ?>">政务咨询</a></div>
          <div class="lft"><a href="http://www.huangshi.gov.cn/hdjl/dcyzj/">调查征集</a></div>
          <div class="rgt">
            <li><a href="http://cms.bookgo.com.cn/post/25116">直属单位</a></li>
            <li class="jg"><a href="http://cms.bookgo.com.cn/post/25115">县市区局</a></li>
          </div>
        </div>
      <div class="xsdw_bot">
        <li><a href="http://www.mcprc.gov.cn"><img src="<?= Url::to('@web/hss_whj/images/link/01.png') ?>"></a></li>
        <li class="jg"><a href="http://www.ccdy.cn"><img src="<?= Url::to('@web/hss_whj/images/link/02.png') ?>"></a></li>
        <li class="jg"><a href="http://www.cflac.org.cn"><img src="<?= Url::to('@web/hss_whj/images/link/03.png') ?>"></a></li>
        <li class="jg"><a href="http://www.wenwuchina.com"><img src="<?= Url::to('@web/hss_whj/images/link/04.png') ?>"></a></li>
        <li class="jg"><a href="http://www.hbwh.gov.cn"><img src="<?= Url::to('@web/hss_whj/images/link/05.png') ?>"></a></li>
      </div>
      <div class="xsdw_bot">
        <li><a href="http://www.hbnp.gov.cn"><img src="<?= Url::to('@web/hss_whj/images/link/06.png') ?>"></a></li>
        <li class="jg"><a href="http://www.huangshi.gov.cn"><img src="<?= Url::to('@web/hss_whj/images/link/07.png') ?>"></a></li>
        <li class="jg"><a href="http://www.hszzb.gov.cn"><img src="<?= Url::to('@web/hss_whj/images/link/08.png') ?>"></a></li>
        <li class="jg"><a href="http://huangshifb.cjyun.org"><img src="<?= Url::to('@web/hss_whj/images/link/09.png') ?>"></a></li>
        <li class="jg"><a href="http://www.hsdcw.com"><img src="<?= Url::to('@web/hss_whj/images/link/10.png') ?>"></a></li>
      </div>
    </div>
    <!--下属单位 end-->
  </div>
</div>
<script type="text/javascript" src='../hss_whj/js/ad.js'></script>
<script src="../hss_whj/js/wfgd1.js"></script>
<!----main end---->
