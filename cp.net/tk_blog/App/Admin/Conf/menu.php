<?php
return array(
    //OPENED 为1 并且控制和方法都相等 才会给目标Li标签添加 该 class="active opened"
    //triangle 为1 可下拉 显示小三角形 为0没有
    'adminMenu' => array(
        array('name'=>'首页','url'=>'/Admin','icon'=>'fa fa-laptop','controller'=>'Index','action'=>'index','opened'=>'1','triangle'=>'0',
            'chlidmenu'=>array()
        ),
        //用户管理
        array('name'=>'用户管理','url'=>'#','icon'=>'fa fa-group','controller'=>'Users','action'=>'','opened'=>'0','triangle'=>'1',
            'chlidmenu'=> array(
                array('name'=>'本站用户','url'=>'/Admin/Users/originalUser','icon'=>'fa fa-user','controller'=>'Users','action'=>'originalUser','opened'=>'1'),
                array('name'=>'第三方用户','url'=>'/Admin/Users/foreignUser','icon'=>'fa fa-male','controller'=>'Users','action'=>'foreignUser','opened'=>'1'),
                array('name'=>'修改密码','url'=>'/Admin/Users/editPassword','icon'=>'fa fa-key','controller'=>'Users','action'=>'editPassword','opened'=>'1'),
                array('name'=>'修改头像','url'=>'/Admin/Users/editFace','icon'=>'fa fa-github-alt','controller'=>'Users','action'=>'editFace','opened'=>'1'),
            )
        ),


        //内容管理
        array('name'=>'内容管理','url'=>'#','icon'=>'fa fa-bar-chart-o','controller'=>'Contents','action'=>'','opened'=>'0','triangle'=>'1',
            'chlidmenu'=> array(
                array('name'=>'评论管理','url'=>'/Admin/Contents/comment','icon'=>'fa fa-comment','controller'=>'Contents','action'=>'comment','opened'=>'1'),
                array('name'=>'文章管理','url'=>'/Admin/Contents/article','icon'=>'fa fa-book','controller'=>'Contents','action'=>'article','opened'=>'1'),
                array('name'=>'分类管理','url'=>'/Admin/Contents/category','icon'=>'fa fa-calendar','controller'=>'Contents','action'=>'category','opened'=>'1'),
                array('name'=>'标签管理','url'=>'/Admin/Contents/tags','icon'=>'fa fa-tags','controller'=>'Contents','action'=>'tags','opened'=>'1'),
//                array('name'=>'留言管理','url'=>'/Admin/Contents/message','icon'=>'fa fa-tasks','controller'=>'Contents','action'=>'message','opened'=>'1'),
            )
        ),


        //菜单管理
       /* array('name'=>'菜单管理','url'=>'#','icon'=>'fa fa-bars','controller'=>'Menu','action'=>'','opened'=>'0','triangle'=>'1',
            'chlidmenu'=> array(
                array('name'=>'前台菜单','url'=>'/Admin/Menu/home_menu','icon'=>'fa fa-maxcdn','controller'=>'Menu','action'=>'home_menu','opened'=>'1'),
                array('name'=>'后台菜单','url'=>'/Admin/Menu/Admin_menu','icon'=>'fa fa-windows','controller'=>'Menu','action'=>'admin_menu','opened'=>'1'),
            )
        ),*/

        //网站设置
        array('name'=>'网站设置','url'=>'#','icon'=>'fa fa-cogs','controller'=>'Web','action'=>'','opened'=>'0','triangle'=>'1',
            'chlidmenu'=> array(
                array('name'=>'邮箱设置','url'=>'/Admin/Web/mailbox','icon'=>'fa fa-envelope','controller'=>'Web','action'=>'mailbox','opened'=>'1'),
                array('name'=>'邮箱模板','url'=>'/Admin/Web/mailbox_tmp','icon'=>'fa fa-credit-card','controller'=>'Web','action'=>'mailbox_tmp','opened'=>'1'),
                array('name'=>'网站信息','url'=>'/Admin/Web/web_info','icon'=>'fa fa-indent','controller'=>'Web','action'=>'web_info','opened'=>'1'),
                array('name'=>'第三方登录','url'=>'/Admin/Web/future','icon'=>'fa fa-github','controller'=>'Web','action'=>'future','opened'=>'1'),

            )
        ),

        //权限管理
        array('name'=>'AUTH权限管理','url'=>'#','icon'=>'fa fa-sun-o','controller'=>'Auth','action'=>'','opened'=>'0','triangle'=>'1',
            'chlidmenu'=> array(
                array('name'=>'权限管理','url'=>'/Admin/Auth/rule','icon'=>'fa fa-sitemap','controller'=>'Auth','action'=>'rule','opened'=>'1'),
                array('name'=>'用户组管理','url'=>'/Admin/Auth/group','icon'=>'fa fa-user','controller'=>'Auth','action'=>'group','opened'=>'1'),
                array('name'=>'管理员列表','url'=>'/Admin/Auth/admin_user','icon'=>'fa fa-github-alt','controller'=>'Auth','action'=>'admin_user','opened'=>'1'),

            )
        ),

        /*array('name'=>'消息管理','url'=>'/Admin/news','icon'=>'fa fa-bell','controller'=>'News','action'=>'index','opened'=>'1','triangle'=>'0',
            'chlidmenu'=>array()
        ),*/
        array('name'=>'回收站','url'=>'/Admin/Trash','icon'=>'fa fa-trash-o','controller'=>'Trash','action'=>'index','opened'=>'1','triangle'=>'0',
            'chlidmenu'=>array()
        ),
    ),
);