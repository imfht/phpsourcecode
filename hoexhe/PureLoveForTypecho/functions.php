<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Engine: typecho
 * Theme Name: PureLoveForTypecho
 * Time: 2018年11月12日11:51
 * Author: Hoe
 * Author URI: http://www.hoehub.com/
 */

/**
 * @param $form
 * 插件设置
 */
function themeConfig($form)
{
    $form->addItem((new Typecho_Widget_Helper_Layout())->html(_t('<h3 style="color:black; font-weight: bold;">站点设置</h3>')));

    // Logo
    $logoValue = '/usr/themes/PureLoveForTypecho/images/logo-160x60.png';
    $logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', null, $logoValue, _t('站点Logo地址'), _t('左上角的Logo 建议尺寸160px*60px'));
    $form->addInput($logoUrl);

    // Icon
    $iconUrlValue = '/usr/themes/PureLoveForTypecho/images/favicon.ico';
    $iconUrl = new Typecho_Widget_Helper_Form_Element_Text('iconUrl', null, $iconUrlValue, _t('站点Icon地址'), _t('网站Icon 建议尺寸32px*32px'));
    $form->addInput($iconUrl);

    // 轮播图
    $description = _t('图片建议尺寸700*250<br>Json格式 如 <pre class="description" style="font-family: Consolas; font-size: 12px;">
[
    {"imgUrl": "绝对路径", "url": "跳转地址", "desc": "描述"},
    {"imgUrl": "https://www.hoehub.com/banner1.png", "url": "https://www.hoehub.com", "desc": "描述1"},
    {"imgUrl": "https://www.hoehub.com/banner2.png", "url": "https://www.hoehub.com", "desc": "描述2"},
    {"imgUrl": "https://www.hoehub.com/banner3.png", "url": "https://www.hoehub.com", "desc": "描述3"}
] </pre>');
    $bannersValue = '[
        {"imgUrl": "/usr/themes/PureLoveForTypecho/images/banner1.jpg", "url": "https://www.hoehub.com", "desc": "For you, a thousand times over. 为你，千千万万遍。--《追风筝的人》"},
        {"imgUrl": "/usr/themes/PureLoveForTypecho/images/banner2.jpg", "url": "https://www.hoehub.com", "desc": "This path has been placed before you. The choice... 路就在你脚下，你自己决定。 —星球大战"},
        {"imgUrl": "/usr/themes/PureLoveForTypecho/images/banner3.jpg", "url": "https://www.hoehub.com", "desc": "However big the problem, tell your heart, All is well, pal. 无论问题有多大，告诉你的心，“一切皆好，朋友。”—《三傻大闹宝莱坞》"}
    ]';
    $banners = new Typecho_Widget_Helper_Form_Element_Textarea('banners', null, $bannersValue, _t('首页轮播图'), $description);
    $banners->input->setAttribute('style', 'height: 250px;');
    $form->addInput($banners);

    // 检验JSON格式
    $btn = new Typecho_Widget_Helper_Form_Element_Submit(null, null, _t('检验JSON格式'));
    $btn->input->setAttribute('class', 'btn notice');
    $form->addItem($btn);

    $attributeValue = <<<JS
    /**
     * 检验字符串是否为JSON格式
     * @param str
     */
    function testJSON(str) {
        if (typeof str == 'string') {
            try {
                let obj = JSON.parse(str);
                if (typeof obj == 'object' && obj) {
                    return true;
                } else {
                    return false;
                }
            } catch(e) {
                return false;
            }
        }
    }
    let string = $('textarea[name=banners]').val();
    let bool = testJSON(string);
    $('textarea[name=banners]').css('border', 'none');
    if (bool) {
        alert('JSON格式正确');
    } else {
        $('textarea[name=banners]').focus().css('border', '3px solid red');
        alert('JSON格式错误');
    }
    return false;
JS;
    $btn->input->setAttribute('onclick', $attributeValue);

    // 侧边栏显示
    $sidebarBlock = new Typecho_Widget_Helper_Form_Element_Checkbox('sidebarBlock',
        [
            'showSiteInfo' => _t('显示网站信息'),
            'showSiteStatistics' => _t('显示网站统计'),
            'showRecentPosts' => _t('显示最新文章, 条数: 设置->阅读->文章列表数目'),
            'showHotPosts' => _t('显示热门文章, 默认10条'),
            'showTagCloud' => _t('显示标签云, 默认30条'),
            'showRecentComments' => _t('显示最近回复, 条数: 设置->评论->评论列表数目'),
            'showArchive' => _t('显示归档, 默认按月归档, 显示6条'),
            'showOther' => _t('显示其它杂项')
        ],
        [
            'showSiteInfo',
            'showSiteStatistics',
            'showRecentPosts',
            'showHotPosts',
            'showTagCloud',
            'showRecentComments',
            'showArchive',
            'showOther',
        ], _t('侧边栏显示'));
    $form->addInput($sidebarBlock->multiMode());

    // 留言头像设置
    $options = ['qqFirst' => _t('优先使用QQ头像')];
    $commentSettings = new Typecho_Widget_Helper_Form_Element_Checkbox('commentSettings', $options, ['qqFirst'], _t('留言头像'), _t('如果使用QQ邮箱留言，留言（评论）列表则会优先显示QQ头像；不勾选则使用系统自带的gravatar全球通用头像。注：显示QQ头像可能会暴露QQ号码'));
    $form->addInput($commentSettings->multiMode());

    // 版权声明
    $copyrightValue = '<p>1.您可自由分发和演绎本站内容，只需保留本站署名且非商业使用<a href="https://creativecommons.org/licenses/by-nc-sa/4.0/deed.zh" target="_blank">(CC BY-NC-SA 4.0 CN)</a></p>
<p>2.本站引用资源会尽最大可能标明出处及著作权所有者，但不能保证对所有资源都可声明上述内容。侵权请联络作者。</p>';
    $copyright = new Typecho_Widget_Helper_Form_Element_Textarea('copyright', null, $copyrightValue, _t('版权声明'), _t('页脚的版权声明, 允许使用html标签'));
    $form->addInput($copyright);

    $form->addItem((new Typecho_Widget_Helper_Layout())->html(_t('<h3 style="color:black; font-weight: bold;">我的介绍</h3>')));

    $name = new Typecho_Widget_Helper_Form_Element_Text('name', null, 'Hoe', _t('昵称'), _t('我的介绍 昵称'));
    $form->addInput($name);

    $gender = new Typecho_Widget_Helper_Form_Element_Radio('gender', ['1' => '男', '0' => '女'], '1', _t('性别'), _t('我的介绍 性别'));
    $form->addInput($gender);

    $job = new Typecho_Widget_Helper_Form_Element_Text('job', null, '后端开发', _t('职业'), _t('我的介绍 职业'));
    $form->addInput($job);

    $github = new Typecho_Widget_Helper_Form_Element_Text('github', null, 'https://github.com/HoeXHe', _t('github主页'), _t('我的介绍 github主页'));
    $form->addInput($github);

    $gitee = new Typecho_Widget_Helper_Form_Element_Text('gitee', null, 'https://gitee.com/HoeXHe', _t('码云主页'), _t('我的介绍 码云主页'));
    $form->addInput($gitee);

    $email = new Typecho_Widget_Helper_Form_Element_Text('email', null, 'i@hoehub.com', _t('邮箱'), _t('我的介绍 邮箱'));
    $form->addInput($email);

    $qq = new Typecho_Widget_Helper_Form_Element_Text('qq', null, '', _t('QQ号'), _t('我的介绍 QQ号'));
    $form->addInput($qq);

    $wechat = new Typecho_Widget_Helper_Form_Element_Text('wechat', null, '', _t('微信号'), _t('我的介绍 微信号'));
    $form->addInput($wechat);

    $phone = new Typecho_Widget_Helper_Form_Element_Text('phone', null, '', _t('手机号'), _t('我的介绍 手机号'));
    $form->addInput($phone);

    $introductionValue = 'BUG制造者 爱打羽毛球
我每天都在思考如何把脑子里的钱存入银行
采得百花成蜜后，为谁辛苦为谁甜。—— 罗隐《蜂》';
    $introduction = new Typecho_Widget_Helper_Form_Element_Textarea('introduction', null, $introductionValue, _t('简介、公告或其他'), _t('可以填写网站简介或网站公告等，以段落形式显示 支持html标签'));
    $form->addInput($introduction);

    $form->addItem((new Typecho_Widget_Helper_Layout())->html(_t('<h3 style="color:black; font-weight: bold;">页脚设置</h3>')));

    $beiAnCode = new Typecho_Widget_Helper_Form_Element_Text('beiAnCode', null, '桂ICP备16007***号-1', _t('备案号'), _t('页脚备案号'));
    $form->addInput($beiAnCode);

    $startAt = new Typecho_Widget_Helper_Form_Element_Text('startAt', null, '10/01/2016 08:00:00', _t('建站时间'), _t('显示本站运行时间 格式: 10/01/2016 08:00:00 兼容Safari浏览器'));
    $form->addInput($startAt);

    $form->addItem((new Typecho_Widget_Helper_Layout())->html(_t('<h3 style="color:black; font-weight: bold;">其他设置</h3>')));

    $tongJiJs = new Typecho_Widget_Helper_Form_Element_Textarea('tongJiJs', null, '<script></script>', _t('网站统计Js代码'), _t('请填入包括script标签的统计代码即可'));
    $form->addInput($tongJiJs);

    $advertisingJs = new Typecho_Widget_Helper_Form_Element_Textarea('advertisingJs', null, '<script></script>', _t('广告JS代码'), _t('请填入包括script标签的代码'));
    $form->addInput($advertisingJs);

}

/**
 * @param $agent
 * @return string
 * 获取浏览器信息
 */
function getBrowser($agent)
{
    if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
        $browserIcon = '<i class="fa fa-internet-explorer"></i>';
    } else if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
        $browserIcon = '<i class="fa fa-firefox"></i>';
    } else if (preg_match('/Chrome([\d]*)\/([^\s]+)/i', $agent, $regs)) {
        $browserIcon = '<i class="fa fa-chrome"></i>';
    } else if (preg_match('/QQBrowser\/([^\s]+)/i', $agent, $regs)) {
        $browserIcon = '<i class="fa fa-qq"></i>';
    } else if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
        $browserIcon = '<i class="fa fa-safari"></i>';
    } else if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
        $browserIcon = '<i class="fa fa-opera"></i>';
    } else {
        $browserIcon = '<i class="fa fa-question-circle"></i>';
    }
    return $browserIcon;
}

/**
 * 获取操作系统信息
 * @param $agent
 * @return string
 */
function getOS($agent)
{
    if (preg_match('/win/i', $agent)) {
        $osIcon = '<i class="fa fa-windows"></i>';
    } else if (preg_match('/android/i', $agent)) {
        $osIcon = '<i class="fa fa-android"></i>';
    } else if (preg_match('/linux/i', $agent)) {
        $osIcon = '<i class="fa fa-linux"></i>';
    } else if (preg_match('/mac/i', $agent)) {
        $osIcon = '<i class="fa fa-apple"></i>';
    } else if (preg_match('/iphone/i', $agent)) {
        $osIcon = '<i class="fa fa-apple"></i>';
    } else if (preg_match('/ipad/i', $agent)) {
        $osIcon = '<i class="fa fa-apple"></i>';
    } else {
        $osIcon = '<i class="fa fa-laptop"></i>';
    }
    return $osIcon;
}

/**
 * @return int
 * @throws Typecho_Db_Exception
 * 查询标签总数
 */
function getTagCount()
{
    $db = Typecho_Db::get();
    $widget = new Widget_Metas_Tag_Cloud(new Typecho_Request(), new Typecho_Response());
    // 查询
    $select = $widget->select()->where('type = ?', 'tag');
    $tags = $db->fetchAll($select, [$widget, 'push']); // 获取上级评论对象
    return count($tags);
}

/**
 * @param $archive
 * 关闭反垃圾机制 否则Pjax无法提交评论
 */
function themeInit($archive)
{
    Helper::options()->commentsAntiSpam = false;
}

/**
 * @param $article
 * @return string
 * 文章无图时, 随机输出缩略图
 */
function articleThumb($article)
{
    // 当文章无图片时的默认缩略图
    $pattern = '/<img[\s\S]*?src\s*=\s*[\"|\'](.*?)[\"|\'][\s\S]*?>/';
    preg_match_all($pattern, $article->content, $matches);
    if (isset($matches[1][0])) {
        $thumb = $matches[1][0];
    } else {
        $ran = mt_rand(1, 8);
        $thumb = $article->widget('Widget_Options')->themeUrl . '/thumb/' . $ran . '.jpg'; // 随机图片
    }
    return $thumb;
}

/**
 * 调取金山每日一句
 * @return array 接口数据
 */
function ICIB_API()
{
    $date = date('Y-m-d');
    $content = file_get_contents('http://open.iciba.com/dsapi/?date=' . $date);
    $result = json_decode($content);
    return $result;
}

/**
 * @param array $result
 * @param int $num
 * @throws Typecho_Db_Exception
 * @throws Typecho_Exception
 * 热门文章
 */
function hotPosts(&$result, $num = 5)
{
    $db = Typecho_Db::get();
    $sql = $db->select()->from('table.contents')
        ->where('type = ?', 'post')
        ->where('status = ?', 'publish') // 2018年11月29日9:34:49 感谢jiffei反馈
        ->limit($num)
        ->order('commentsNum', Typecho_Db::SORT_DESC);
    $result = $db->fetchAll($sql);
    foreach ($result as &$item) {
        $item = Typecho_Widget::widget('Widget_Abstract_Contents')->filter($item);
    }
}

/**
 * @return bool
 * 是否为移动设备
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 * 获取评论头像
 * @param $comments
 * @return string
 */
function getCommentAvatarUrl($comments)
{
    $commentSettings = Helper::options()->commentSettings;
    $size = '40';// 头像大小
    // 优先使用QQ头像
    $qqFirst = array_key_exists(0, $commentSettings) ? $commentSettings[0] : '';
    if ($qqFirst == 'qqFirst' && strrpos($comments->mail, '@qq.com')) {
        $qq = str_replace("@qq.com", '', $comments->mail);
        return '//q.qlogo.cn/g?b=qq&nk=' . $qq . '&refer=web1n&s=' . $size;
    }

    // 默认只使用gravatar全球通用头像
    $gravatar = defined('__TYPECHO_GRAVATAR_PREFIX__') ? __TYPECHO_GRAVATAR_PREFIX__ : '//cdn.v2ex.com/gravatar';
    $gravatar = rtrim($gravatar, '/') . '/';
    $rating = Helper::options()->commentsAvatarRating;
    $hash = md5(strtolower($comments->mail));
    return $gravatar . $hash . '?s=' . $size . '&r=' . $rating . '&d=';

}
