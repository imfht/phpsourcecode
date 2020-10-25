<?php
use yii\helpers\Url;
use frontend\components\LinkPage;

$this->title = $category->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .page>.active {
        background: #970101;
        color: #fff;
    }
    a.share-a{
        cursor: pointer;
    }
</style>
<div class="main">
  <div class="box">
    <!---left---->
    <div class="ny-left">
      <div class="newlist">
        <div class="newlist_top"><span>公示公告</span></div>
        <div class="news">
            <?php
                foreach ( $posts_tzgg as $post ) {
                    if ( mb_strlen($post['title']) >16 )
                        $title = mb_substr($post['title'], 0, 10, 'utf-8') . '...';
                    else
                        $title = $post['title'];
            ?>
                <li>
                    <a href="<?= Url::to('post/'.$post['id'], true) ?>">
                        <div class="title"><?= $post['title'] ?></div>
                        <font><?= date('Y-m-d', $post['published_at']) ?></font>
                    </a>
                </li>
            <?php } ?>
        </div>
      </div>
      <div class="newlist">
        <div class="newlist_top"><span>联系我们</span></div>
        <div class="contact">·地址：黄石市杭州西路58号<br>
          ·电话：0714-6359550<br>
          ·邮编：435003<br>
          ·E-mail：hswhjbgs@sina.com </div>
      </div>
    </div>
    <!-- left end -->
    <!-- right -->
    <div class="ny-right">
      <div class="newlist">
        <div class="newlist_top">
            <span><?= $category->title ?></span>
            <!-- <a href="" class="more">更多>></a> -->
        </div>
        <div class="news">
            <?php foreach ( $posts as $post ): ?>
                <li>
                    <div class="title">
                        <a href="<?= Url::to('post/'.$post->id, true) ?>"><?= $post->title ?></a>
                    </div>
                    <dl>
                        <dd>发布时间: <?= date('Y-m-d', $post->published_at) ?></dd>
                        <!-- <dd>信息来源:市群艺馆</dd> -->


                        <dt>
                        <a class="share-a" data-url="<?= Url::to('post/' . $post->id, true) ?>" data-title="<?= $post->title ?>">分享</a>
                        </dt>

                    </dl>
                </li>
            <?php endforeach; ?>
        </div>
      </div>
      <?= LinkPage::widget(['pagination' => $pagination]); ?>
      <!-- 分页代码 end -->
    </div>
    <!-- right end -->
    <div class="clear"></div>
  </div>
</div>

<style>
    .share-alert{
        width: 210px;
        height: 60px;
        position: fixed;
        top: 50vh;
        left: 40vw;
        border: 1px #ccc solid;
        background: #fff;
        z-index: 999999;
        line-height: 100px;
        padding: 60px;
    }
    .share-box{
        float: left;
        margin-left: 10px;
    }
    .share-box img{
        width: 40px;
        height: 40px;
        cursor: pointer;
    }
</style>
<div class="share-alert" style="display:none;">
    <div class="share-box">
        <img src="<?= Url::to('@web/hss_whj/images/qzone.png') ?>" alt="">
    </div>
    <div class="share-box">
        <img src="<?= Url::to('@web/hss_whj/images/sina.png') ?>" alt="">
    </div>
    <div class="share-box">
        <img src="<?= Url::to('@web/hss_whj/images/douban.png') ?>" alt="">
    </div>
    <div class="share-box">
        <img src="<?= Url::to('@web/hss_whj/images/weixin.png') ?>" alt="">
    </div>
    <img class="t-close close-3" src="<?= Url::to('@web/hss_whj/images/t-close.png') ?>" alt="">
</div>

<script src="../hss_whj/js/share.js"></script>

<script type="text/javascript">
var _href;
var _title;
$(function() {
    $('#hold_bottom').hide();
    // 分享按钮被点击
    $('.share-a').click(function(){
        $('.share-alert').show();
        _href = $(this).attr('data-url');
        _title = $(this).attr('data-title');
    });
    // 关闭按钮被点击
    $('.close-3').click(function(){
        $('.share-alert').hide();
    });
    $('.share-box').click(function(){
        var index = $(this).index();
        switch (index) {
            case 0:
                socialShare('qzone',_href,_title);
                break;
            case 1:
                socialShare('sina',_href,_title);
                break;
            // case 2:
            //     socialShare('qq',_href,_title);
            //     break;
            case 2:
                socialShare('douban',_href,_title);
                break;
            case 3:
                socialShare('weixin',_href,_title);
                break;
            default:
                socialShare('qzone',_href,_title);
        }
    });

});
</script>
