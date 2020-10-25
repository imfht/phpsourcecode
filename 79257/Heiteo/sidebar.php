
<button data-action="open-drawer" id="drawer-button" class="drawer-button"><i class="fa fa-bars"></i></button>
<nav tabindex="-1" class="drawer">
    <div class="drawer-container"> 
        <!--.drawer-search(role="search")-->
        <ul role="navigation" class="drawer-list">
            <li class="drawer-list-item"> <a href="<?php $this->options->siteUrl(); ?>" data-pjax> <i class="fa fa-home"></i>首页 </a> </li>
            <li class="drawer-list-item"> <a href="/me.html" target="_blank"> <i class="fa fa-info"></i>关于 </a> </li>
            <li class="drawer-list-item"> <a href="/theme.html" target="_blank"> <i class="fa fa-file-code-o"></i>主题 </a> </li>
            <li class="drawer-list-item"> <a href="/project.html" target="_blank"> <i class="fa fa-inbox"></i>项目 </a> </li>
            <li class="drawer-list-item"> <a href="mailto:79257@163.com" target="_blank"> <i class="fa fa-envelope"></i>联系 </a> </li>
            <li class="drawer-list-divider"></li>
            <li class="drawer-list-item drawer-list-title"> Follow me </li>
            <?php if (!empty($this->options->github) && in_array('github', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="<?php echo $this->options->github; ?>"><i class="fa fa-github"></i>Github </a> </li>
            <?php endif; ?>
            <?php if (!empty($this->options->qq) && in_array('qq', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $this->options->qq; ?>"><i class="fa fa-qq"></i>QQ</a> </li>
            <?php endif; ?>
            <?php if (!empty($this->options->weibo) && in_array('weibo', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="<?php echo $this->options->weibo; ?>"><i class="fa fa-weibo"></i>新浪微博 </a> </li>
            <?php endif; ?>
            <?php if (!empty($this->options->skype) && in_array('skype', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="skype:<?php echo $this->options->skype;?>?add"><i class="fa fa-skype"></i>Skype </a> </li>
            <?php endif; ?>
            <?php if (!empty($this->options->google_plus) && in_array('google_plus', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="<?php echo $this->options->google_plus; ?>"><i class="fa fa-google-plus"></i>Google+ </a> </li>
            <?php endif; ?>
            <?php if (!empty($this->options->stack_overflow) && in_array('stack_overflow', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="<?php echo $this->options->stack_overflow;?>"><i class="fa fa-stack-overflow"></i>Stack-Overflow </a> </li>
            <?php endif; ?>
            <?php if (!empty($this->options->tencent_weibo) && in_array('tencent_weibo', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="<?php echo $this->options->tencent_weibo;?>"><i class="fa fa-tencent-weibo"></i>腾讯微博 </a> </li>
            <?php endif; ?>
            <?php if (!empty($this->options->twitter) && in_array('twitter', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="<?php echo $this->options->twitter; ?>"><i class="fa fa-twitter"></i>Twitter </a> </li>
            <?php endif; ?>
            <?php if (!empty($this->options->pinterest) && in_array('pinterest', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="<?php echo $this->options->pinterest; ?>"><i class="fa fa-pinterest"></i>Pinterest </a> </li>
            <?php endif; ?>
            <?php if (!empty($this->options->linkedin) && in_array('linkedin', $this->options->contentIfon)): ?>
            <li class="drawer-list-item"> <a href="<?php echo $this->options->linkedin; ?>"><i class="fa fa-linkedin"></i>LinkedIn </a> </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>