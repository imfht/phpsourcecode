<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<div id="header">
    <div class="header-warp">
        <div id="header-right">
                <?php if(IS_LOGIN){ ?>
                <div class="dropdown">
                    <div id="dropdownMenu1" data-toggle="dropdown">
                        <img src="<?php echo $hd['session']['user']['icon'];?>" class="user" /> <?php echo $hd['session']['user']['username'];?>
                        <span class="caret"></span>
                    </div>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://localhost/PHPUnion/index.php?m=Member&c=Content&a=content&mid=1">文章管理</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://localhost/PHPUnion/index.php?m=Member&c=Account&a=personal">个人资料</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://localhost/PHPUnion/index.php?m=Member&c=Login&a=out">退出</a></li>
                    </ul>
                </div>
            <?php }else{ ?>
                <a href="<?php echo U('Member/Login/login');?>" class="bt-primary">登录</a>
                <a href="<?php echo U('Member/Login/reg');?>" class="bt-default">注册</a>
            <?php } ?>
        </div>
        <a id="logo" href="http://localhost/PHPUnion/index.php" title="后盾网开源"></a>
        <ul id="header-nav">
            <li     <?php if(!isset($_GET['cid'])){ ?>class="nav-current"<?php } ?>>
                <a href="http://localhost/PHPUnion/index.php">首页</a>
            </li>
            <channel type="top">
                <li     <?php if($_GET['cid']==$field['cid'] || Data::isChild(S(category),$_GET['cid'],$field['cid'])){ ?>class="nav-current"<?php } ?>>
                <?php echo $field['catlink'];?>
                </li>
            </channel>
        </ul>
    </div>
</div>