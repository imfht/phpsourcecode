<?php

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit();
}

function themeConfig($form) {
    //设置头像
    $logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', NULL, NULL, _t('作者头像'), _t('在这里填入一个图片URL地址,作为作者的头像显示'));
    $form->addInput($logoUrl);

    //设置大图
    $imgUrl = new Typecho_Widget_Helper_Form_Element_Text('imgUrl', NULL, NULL, _t('首页大图'), _t('在这里填入一个图片URL地址,作为在首页显示的大图'));
    $form->addInput($imgUrl);

    //设置多说ID
    $duoshuoId = new Typecho_Widget_Helper_Form_Element_Text('duoshuoId', NULL, NULL, _t('你在多说的域名'), _t('只需要输入二级名称即可（比如：nyf.duoshuo.com,那么就输入nyf。）'));
    $form->addInput($duoshuoId);

    //多说评论
    $duoshuo = new Typecho_Widget_Helper_Form_Element_Checkbox('duoshuo', array('PostShowDuoshuo' => _t('文章页是否启用多说评论'),
        'PageShowDuoshuo' => _t('独立页面是否启用多说评论')), array('PostShowDuoshuo', 'PageShowDuoshuo'), _t('多说评论（注意，如果不开启多说评论，则页面不会有默认评论系统。）'));
    $form->addInput($duoshuo->multiMode());

    //设置查看更多
    $more = new Typecho_Widget_Helper_Form_Element_Checkbox('more', array('MoreStatus' => _t('首页是否开启【查看更多】')), array('MoreStatus'), _t('首页是否开启【显示更多】，如果不开启的话，则首页只会显示标题。'));
    $form->addInput($more->multiMode());

    //设置首页查看更多的文字
    $moretext = new Typecho_Widget_Helper_Form_Element_Text('moretext', NULL, NULL, _t('【查看全文】样式'), _t('设置首页【查看全文】的文字，为空则为默认的【查看全文】。'));
    $form->addInput($moretext);
    
    //设置侧边栏状态
    $siderbar = new Typecho_Widget_Helper_Form_Element_Radio('siderbar', array('显示', '隐藏'), array(1, 0), _t('设置侧边栏状态的默认状态'));
    $form->addInput($siderbar->multiMode());

    //联系方式开关
    $contentIfon = new Typecho_Widget_Helper_Form_Element_Checkbox('contentIfon', array('github' => _t('github是否开启'), 'qq' => _t('qq是否开启'), 'weibo' => _t('新浪微博是否开启'), 'skype' => _t('skype是否开启'), 'google_plus' => _t('Google+是否开启'), 'stack_overflow' => _t('stack-overflow是否开启'), 'tencent_weibo' => _t('腾讯微博是否开启'), 'slack' => _t('slack是否开启'), 'twitter' => _t('twitter是否开启'), 'pinterest' => _t('pinterest是否开启'), 'linkedin' => _t('linkedin是否开启')), array('github', 'qq', 'weibo', 'skype', 'email', 'google_plus', 'stack_overflow', 'tencent_weibo', 'slack', 'twitter', 'pinterest', 'linkedin'), _t('选中开启然后在下方的输入框中输入地址'));
    $form->addInput($contentIfon->multiMode());

    //侧边栏联系方式github
    $github = new Typecho_Widget_Helper_Form_Element_Text('github', NULL, NULL, _t('github地址'), _t(''));
    $form->addInput($github);

    //侧边栏联系方式qq
    $qq = new Typecho_Widget_Helper_Form_Element_Text('qq', NULL, NULL, _t('QQ会话'), _t('输入你的QQ号即可'));
    $form->addInput($qq);

    //侧边栏联系方式weibo
    $weibo = new Typecho_Widget_Helper_Form_Element_Text('weibo', NULL, NULL, _t('新浪微博地址'), _t(''));
    $form->addInput($weibo);

    //侧边栏联系方式skype
    $skype = new Typecho_Widget_Helper_Form_Element_Text('skype', NULL, NULL, _t('skype'), _t('输入你的Skype用户名/电话号码即可'));
    $form->addInput($skype);

    //侧边栏联系方式google-plus
    $google_plus = new Typecho_Widget_Helper_Form_Element_Text('google_plus', NULL, NULL, _t('google+ 地址'), _t());
    $form->addInput($google_plus);

    //侧边栏联系方式 stack-overflow
    $stack_overflow = new Typecho_Widget_Helper_Form_Element_Text('stack_overflow', NULL, NULL, _t('stack-overflow个人页'), _t());
    $form->addInput($stack_overflow);

    //侧边栏联系方式tencent-weibo
    $tencent_weibo = new Typecho_Widget_Helper_Form_Element_Text('tencent_weibo', NULL, NULL, _t('腾讯微博地址'), _t());
    $form->addInput($tencent_weibo);

    //侧边栏联系方式twitter
    $twitter = new Typecho_Widget_Helper_Form_Element_Text('twitter', NULL, NULL, _t('twitter'), _t());
    $form->addInput($twitter);

    //侧边栏联系方式pinterest
    $pinterest = new Typecho_Widget_Helper_Form_Element_Text('pinterest', NULL, NULL, _t('pinterest'), _t());
    $form->addInput($pinterest);

    //侧边栏联系方式linkedin
    $linkedin = new Typecho_Widget_Helper_Form_Element_Text('linkedin', NULL, NULL, _t('linkedin个人页'), _t());
    $form->addInput($linkedin);
	
	//设置CDN地址
	$CdnUrl = new Typecho_Widget_Helper_Form_Element_Text('CdnUrl', NULL, NULL, _t('CDN地址前缀（结尾加"/"）'), _t('将你的静态资源上传到CDN后，把cdn地址的前缀（不包含具体文件名）输入，建议所有静态资源都在同一个地址下。'));
    $form->addInput($CdnUrl);
}

?>