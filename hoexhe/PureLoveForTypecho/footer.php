<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
</section><!-- #content -->
<?php $this->need('sidebar.php'); ?>
</section><!-- #container -->
<span class="clearfix"></span>
<div id="bak_top"></div>
<footer class="footer">
    <div class="inner clearfix footer-top">
        <div class="fotbox">
            <h3 title="金山词霸 每日一句">每日一句</h3>
            <div id="daily-sentence">
                <?php $ICIB = ICIB_API(); ?>
                <p><?= property_exists($ICIB, 'content') ? $ICIB->content : ''; ?></p>
                <p><?= property_exists($ICIB, 'note') ? $ICIB->note : ''; ?></p>
            </div>
            <span id="typed"></span>
        </div>
        <div class="fotbox">
            <h3>版权声明</h3>
            <?= $this->options->copyright ? $this->options->copyright() : ''; ?>
        </div>
        <div class="fotbox2">
            <h3>我的介绍</h3>
            <p>
                <span>· <?php $this->options->name();?> <?= $this->options->gender == 1 ? '男' : '女';?> <?php $this->options->job();?></span>
                <span class='myinfo_pic'>
                    <?php if($this->options->github): ?>
                        <a href="<?php $this->options->github(); ?>" target='_blank'><i class='fa fa-github' title='github'></i></a>
                    <?php endif;?>
                    <?php if($this->options->gitee): ?>
                        <a href="<?php $this->options->gitee(); ?>" target='_blank'><i class='fa fa-git' title='gitee'></i></a>
                    <?php endif;?>
                    <?php if($this->options->email): ?>
                        <a href='mailto:<?php $this->options->email(); ?>'><i class='fa fa-envelope-o' title='<?php $this->options->email(); ?>'></i></a>
                    <?php endif;?>

                    <?php if($this->options->qq): ?>
                        <?php if(isMobile()): ?>
                            <a href='mqqwpa://im/chat?chat_type=wpa&uin=<?php $this->options->qq(); ?>&version=1&src_type=web&web_src=oicqzone.com'>
                                <i class='fa fa-qq' title='<?php $this->options->qq(); ?>'></i>
                            </a>
                        <?php else: ?>
                            <a href='tencent://message/?uin=<?php $this->options->qq(); ?>&Site=http://vps.shuidazhe.com&Menu=yes'>
                                <i class='fa fa-qq' title='<?php $this->options->qq(); ?>'></i>
                            </a>
                        <?php endif;?>
                    <?php endif;?>

                    <?php if($this->options->wechat): ?>
                        <a href='javascript:;'><i class='fa fa-wechat' title='<?php $this->options->wechat(); ?>'></i></a>
                    <?php endif;?>

                    <?php if($this->options->phone): ?>
                        <a href='tel::<?php $this->options->phone(); ?>'><i class='fa fa-phone-square' title='<?php $this->options->phone(); ?>'></i></a>
                    <?php endif;?>

                </span>
            </p>
            <?php $introduction = explode(PHP_EOL, $this->options->introduction); ?>
            <?php foreach ($introduction as $item):?>
                <?= $item ? '<p>· ' . $item . '</p>' : ''; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="copry clearfix footer-bottom">
        <div id="copyright">
            <i class="fa fa-copyright"></i>
            <?= $this->options->startAt ? date('Y', strtotime($this->options->startAt)) . ' -' : ''; ?>
            <?= date('Y'); ?>
            All rights reserved.
            <a href="http://beian.miit.gov.cn" target="_blank">
                <?= $this->options->beiAnCode ? $this->options->beiAnCode() : '桂ICP备xxx号-1' ?>
            </a>
        </div>
        <span id="mt">
            <span id="duration"></span>
            <a href="//creativecommons.org/licenses/by-nc-sa/3.0/cn/legalcode" target="_blank">
                <i class="fa fa-cc"></i>
            </a>
            <a href="http://typecho.org/" rel="external" target="_blank"><i class="fa fa-tumblr-square" aria-hidden="true"></i>Powered by Typecho</a>
            <a href="http://www.hoehub.com">Theme By Hoe</a>
        </span>
    </div>
</footer>
<!--JQ-->
<script src="//cdn.staticfile.org/jquery/1.12.2/jquery.min.js"></script>
<script src="//cdn.staticfile.org/ResponsiveSlides.js/1.55/responsiveslides.min.js"></script>
<script src="//cdn.staticfile.org/highlight.js/9.13.1/highlight.min.js"></script>
<script src="//cdn.staticfile.org/jquery.pjax/2.0.1/jquery.pjax.min.js"></script>
<script src="//cdn.staticfile.org/nprogress/0.2.0/nprogress.min.js"></script>
<script src="//cdn.staticfile.org/typed.js/2.0.9/typed.min.js"></script>
<script src="//cdn.staticfile.org/jquery.textcomplete/1.8.4/jquery.textcomplete.min.js"></script>
<script src="//cdn.staticfile.org/emojionearea/3.4.1/emojionearea.min.js"></script>
<script src="//cdn.staticfile.org/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script src="<?php $this->options->themeUrl('js/purelove.js'); ?>"></script>
<?php if ($this->is('index')): ?>
    <!--首页才会显示幻灯片-->
    <script>
        $(function () {
            banner();
        });
    </script>
<?php endif; ?>
<script>
    function openNew() { // 从新窗口打开不是本站的链接
        var selector = 'a[href]:not(a[href^="#"], a[href^="javascript"], a[href^="mailto"], a[href^="<?php Helper::options()->siteUrl()?>"])';
        $("#article " + selector).each(function (key, item) {
            $(item).attr('target', '_blank');
        });
        $("footer " + selector).each(function (key, item) {
            $(item).attr('target', '_blank');
        });
    }

    $(function () {
        var options = {
            container: '#content',
            fragment: '#content',
            timeout: 8000
        };
        // Pjax
        $(document).pjax('a[href^="<?php Helper::options()->siteUrl()?>"]:not(a[target="_blank"], a[no-pjax])', options);
        durationTime("<?= $this->options->startAt ?: '10/01/2016 08:00:00'; ?>");
        openNew();
    });
</script>
<?= $this->options->tongJiJs ? $this->options->tongJiJs() : ''; ?>
<?php $this->footer(); ?>
</body>
</html>