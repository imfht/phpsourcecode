<?php

use yii\helpers\Url;

?>
<!--菜单开始-->
<aside id="sidebar">
    <div class="sidebar-inner">
        <div class="si-inner">
            <div class="profile-menu">
                <a href="">
                    <div class="profile-pic">
                        <img src="<?php echo Url::to('@web/public/img/profile-pics/1.jpg') ?>" alt="">
                    </div>

                    <div class="profile-info">
                        Hello,<?=$user->username;?>
                        <i class="md md-arrow-drop-down"></i>
                    </div>
                </a>

                <ul class="main-menu">
                    <li>
                        <a href="/Admin/user/logout"><i class="md md-history"></i>退出登录</a>
                    </li>
                </ul>
            </div>

            <ul class="main-menu">
                <!--遍历菜单-->
                <?php foreach ($menus as $item): ?>

                    <?php
                    $str =  Url::current();
                    $arr = explode('/',$str);
                    if(in_array($item['url'],$arr)){
                        $flag = 0;
                    }else{
                        $flag = 1;
                    }
                    ?>
                    <li <?php if($flag == 0){ echo 'class="sub-menu toggled"';} else{echo 'class="sub-menu"';} ?>>
                        <a href=""><i class="md md-now-widgets"></i><?=$item['name']?></a>
                        <ul <?php if($flag == 0) echo 'style="display: block;"'; ?>>

                            <?php if(!empty($item['son'])) :?>
                            <?php foreach ($item['son'] as $son): ?>

                                <li><a <?php if(Url::current() == $son['url']) echo 'class="active"'; ?>  href="<?= Url::toRoute($son['url']); ?>"><i class="md md-arrow-forward"></i><?=$son['name']?></a></li>

                            <?php endforeach; ?>
                            <?php endif;?>

                        </ul>
                    </li>

                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</aside>
<!--菜单结束-->