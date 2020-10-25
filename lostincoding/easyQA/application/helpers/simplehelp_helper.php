<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 上些小方法
 */

if (!function_exists('get_current_url')) {
    //获取当前访问的完整url地址
    function get_current_url()
    {
        $url = 'http://';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $url = 'https://';
        }
        $url .= $_SERVER['SERVER_NAME'];
        if ($_SERVER['SERVER_PORT'] != '80') {
            $url .= ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        } else {
            $url .= $_SERVER['REQUEST_URI'];
        }
        return $url;
    }
}

if (!function_exists('create_avatar_url')) {
    //生成用户头像url
    function create_avatar_url($user_id, $avatar_ext)
    {
        $CI = &get_instance();
        $avatar_url = $CI->config->item('avatar_base_url');
        if (!empty($avatar_ext)) {
            //如果不存在问号字符,则为系统提供的头像
            if (strpos($avatar_ext, '?') !== false) {
                $arr = explode('?', $avatar_ext);
                $avatar_url .= $user_id . '.' . $arr[0] . '!avatar?' . $arr[1];
            } else {
                $avatar_url .= 's/' . $avatar_ext . '!avatar';
            }
        } else {
            //$avatar_url .= 's/0.png!avatar';
            $avatar_url = '/static/default/img/0.png';
        }
        return $avatar_url;
    }
}

if (!function_exists('create_verify_icon')) {
    //生成用户认证信息icon
    function create_verify_icon($user)
    {
        if ($user['verify_type'] == 1) {
            return '';
        }

        $src = '';
        $title = '';
        //站长认证
        if ($user['verify_type'] == 2) {
            $src = '/static/default/img/icon_v1.png';
            $title = '站长认证，' . $user['verify_details'];
        }
        //微博黄V认证
        else if ($user['verify_type'] == 3) {
            $src = '/static/default/img/icon_v1.png';
            $title = '微博认证，' . $user['verify_details'];
        }
        //微博蓝V认证
        else if ($user['verify_type'] == 4) {
            $src = '/static/default/img/icon_v2.png';
            $title = '微博认证，' . $user['verify_details'];
        }
        $html = '<img class="icon" src="' . $src . '" title="' . $title . '" alt="' . $title . '">';
        return $html;
    }
}

if (!function_exists('create_verify_info')) {
    //生成用户认证信息
    function create_verify_info($user)
    {
        if ($user['verify_type'] == 1) {
            return '';
        }

        $src = '';
        $title = '';
        //站长认证
        if ($user['verify_type'] == 2) {
            $src = '/static/default/img/icon_v1.png';
            $title = '站长认证，' . $user['verify_details'];
        }
        //微博黄V认证
        else if ($user['verify_type'] == 3) {
            $src = '/static/default/img/icon_v1.png';
            $title = '微博认证，' . $user['verify_details'];
        }
        //微博蓝V认证
        else if ($user['verify_type'] == 4) {
            $src = '/static/default/img/icon_v2.png';
            $title = '微博认证，' . $user['verify_details'];
        }
        $html = '<p class="verify_info">';
        $html .= '<img class="icon" src="' . $src . '" title="' . $title . '" alt="' . $title . '">';
        $html .= '<a href="http://' . $user['verify_details'] . '" target="_blank">' . $title . '</a></p>';
        return $html;
    }
}

if (!function_exists('is_wx')) {
    //判断是否在微信中打开的
    function is_wx($user_agent = null)
    {
        if (empty($user_agent)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        return strpos($user_agent, 'MicroMessenger');
    }
}

if (!function_exists('xss_filter')) {
    //xss过滤
    function xss_filter($str, $highlight = false)
    {
        //$str = htmlspecialchars($str, ENT_QUOTES);
        //替换尖括号
        $str = preg_replace('/</', '&lt;', $str);
        $str = preg_replace('/>/', '&gt;', $str);
        if ($highlight) {
            //<em></em>
            $str = preg_replace('/&lt;em&gt;(.*?)&lt;\/em&gt;/is', '<em>$1</em>', $str);
        }
        return $str;
    }
}

if (!function_exists('content_xss_filter')) {
    //xss过滤
    function content_xss_filter($str, $highlight = false)
    {
        $str = xss_filter($str, $highlight);

        $CI = &get_instance();
        $qiniu_config = $CI->config->item('qiniu');

        //[code][/code]
        $str = preg_replace_callback(
            '/\[code\](.*?)\[\/code\]\s*/is',
            function ($matches) {
                //替换换行符，防止代码高亮的时候多出空行
                return preg_replace('/[\n\r]+/', "\n", $matches[0]);
            },
            $str
        );
        $str = preg_replace('/\[code\](.*?)\[\/code\]\s*/is', "<pre><code>$1</code></pre>\n", $str);

        //替换url
        $str = preg_replace('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/isu', '<a href="$1" target="_blank">$1</a>', $str);

        //[img][/img]上传的图片
        $str = preg_replace('/\[img\](.*?)\[\/img\]\s*/is', '<div class="photo"><img src="http://' . $qiniu_config['static_bucket_domain'] . '/$1"></div>', $str);

        //替换#话题#
        $str = preg_replace_callback(
            '/#[\x{4e00}-\x{9fa5}A-Za-z0-9-\+\.,]+?#/isu',
            function ($matches) {
                //长度超过了则不替换
                if (mix_strlen($matches[0]) <= 20) {
                    $topic = trim($matches[0], '#');
                    return preg_replace('/(#[\x{4e00}-\x{9fa5}A-Za-z0-9-\+\.,]+?#)/isu', '<a href="/topic/articles?topic=' . urlencode($topic) . '" target="_blank">$1</a>', $matches[0]);
                } else {
                    return $matches[0];
                }
            },
            $str
        );

        //替换@用户昵称
        $str = preg_replace_callback(
            '/@[\x{4e00}-\x{9fa5}A-Za-z0-9-]+/isu',
            function ($matches) {
                //长度超过了则不替换
                if (mix_strlen($matches[0]) <= 16) {
                    $nickname = trim($matches[0], '@');
                    return preg_replace('/(@[\x{4e00}-\x{9fa5}A-Za-z0-9-]+)/isu', '<a href="/u/home?nickname=' . urlencode($nickname) . '" target="_blank">$1</a>', $matches[0]);
                } else {
                    return $matches[0];
                }
            },
            $str
        );

        //替换表情
        $face_arr = array(
            '微笑' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/5c/huanglianwx_thumb.gif',
            '嘻嘻' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0b/tootha_thumb.gif',
            '哈哈' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6a/laugh.gif',
            '可爱' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/14/tza_thumb.gif',
            '可怜' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/af/kl_thumb.gif',
            '挖鼻' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0b/wabi_thumb.gif',
            '吃惊' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f4/cj_thumb.gif',
            '害羞' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6e/shamea_thumb.gif',
            '挤眼' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c3/zy_thumb.gif',
            '闭嘴' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/29/bz_thumb.gif',
            '鄙视' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/71/bs2_thumb.gif',
            '爱你' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/lovea_thumb.gif',
            '泪' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9d/sada_thumb.gif',
            '偷笑' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/19/heia_thumb.gif',
            '亲亲' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/8f/qq_thumb.gif',
            '生病' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/b6/sb_thumb.gif',
            '太开心' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/58/mb_thumb.gif',
            '白眼' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/landeln_thumb.gif',
            '右哼哼' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/98/yhh_thumb.gif',
            '左哼哼' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/zhh_thumb.gif',
            '嘘' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a6/x_thumb.gif',
            '衰' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/af/cry.gif',
            '委屈' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/73/wq_thumb.gif',
            '吐' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9e/t_thumb.gif',
            '哈欠' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/cc/haqianv2_thumb.gif',
            '抱抱' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/27/bba_thumb.gif',
            '怒' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7c/angrya_thumb.gif',
            '疑问' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/5c/yw_thumb.gif',
            '馋嘴' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/a5/cza_thumb.gif',
            '拜拜' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/70/88_thumb.gif',
            '思考' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/e9/sk_thumb.gif',
            '汗' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/24/sweata_thumb.gif',
            '困' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/kunv2_thumb.gif',
            '睡' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/96/huangliansj_thumb.gif',
            '钱' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/90/money_thumb.gif',
            '失望' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/0c/sw_thumb.gif',
            '酷' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/cool_thumb.gif',
            '色' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/20/huanglianse_thumb.gif',
            '哼' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/49/hatea_thumb.gif',
            '鼓掌' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/36/gza_thumb.gif',
            '晕' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/dizzya_thumb.gif',
            '悲伤' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/1a/bs_thumb.gif',
            '抓狂' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/62/crazya_thumb.gif',
            '黑线' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/91/h_thumb.gif',
            '阴险' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6d/yx_thumb.gif',
            '怒骂' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/60/numav2_thumb.gif',
            '互粉' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/89/hufen_thumb.gif',
            '心' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/hearta_thumb.gif',
            '伤心' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/ea/unheart.gif',
            '猪头' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/58/pig.gif',
            '熊猫' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/6e/panda_thumb.gif',
            '兔子' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/81/rabbit_thumb.gif',
            'ok' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d6/ok_thumb.gif',
            '耶' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/ye_thumb.gif',
            'good' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d8/good_thumb.gif',
            'NO' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/ae/buyao_org.gif',
            '赞' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d0/z2_thumb.gif',
            '来' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/40/come_thumb.gif',
            '弱' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d8/sad_thumb.gif',
            '草泥马' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/7a/shenshou_thumb.gif',
            '神马' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/60/horse2_thumb.gif',
            '囧' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/15/j_thumb.gif',
            '浮云' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/bc/fuyun_thumb.gif',
            '给力' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/1e/geiliv2_thumb.gif',
            '围观' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/f2/wg_thumb.gif',
            '威武' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/70/vw_thumb.gif',
            '奥特曼' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/bc/otm_thumb.gif',
            '礼物' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/c4/liwu_thumb.gif',
            '钟' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d3/clock_thumb.gif',
            '话筒' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/9f/huatongv2_thumb.gif',
            '蜡烛' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/d9/lazhuv2_thumb.gif',
            '蛋糕' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/3a/cakev2_thumb.gif',
            'doge' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/b6/doge_thumb.gif',
            '喵喵' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/4a/mm_thumb.gif',
            '二哈' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/74/moren_hashiqi_thumb.png',
            '笑cry' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/34/xiaoku_thumb.gif',
            '摊手' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/09/pcmoren_tanshou_thumb.png',
            '抱抱' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/70/pcmoren_baobao_thumb.png',
            '坏笑' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/50/pcmoren_huaixiao_thumb.png',
            '污' => 'http://img.t.sinajs.cn/t4/appstyle/expression/ext/normal/3c/pcmoren_wu_thumb.png',
        );
        foreach ($face_arr as $_k => $_v) {
            $face_html = '<img class="face" src="' . $_v . '" title="' . $_k . '">';
            $str = str_replace('[' . $_k . ']', $face_html, $str);
        }
        return $str;
    }
}

if (!function_exists('html_newline')) {
    //xss过滤
    function html_newline($str)
    {
        return preg_replace('/\n/', '<br>', $str);
    }
}

if (!function_exists('mix_strlen')) {
    //中英文混合字符串长度
    function mix_strlen($str)
    {
        return (strlen($str) + mb_strlen($str, 'utf8')) / 2;
    }
}

if (!function_exists('time_tran')) {
    //时间翻译成可读的
    function time_tran($the_time)
    {
        $now_time = time();
        $show_time = strtotime($the_time);
        $dur = $now_time - $show_time;
        if ($dur < 0) {
            return $the_time;
        } else {
            if ($dur < 60) {
                return $dur . '秒前';
            } else {
                if ($dur < 3600) {
                    return floor($dur / 60) . '分钟前';
                } else {
                    if ($dur < 86400) {
                        return floor($dur / 3600) . '小时前';
                    } else {
                        //3天内
                        if ($dur < 259200) {
                            return floor($dur / 86400) . '天前';
                        } else {
                            return $the_time;
                        }
                    }
                }
            }
        }
    }
}

if (!function_exists('fetch_topic_lists')) {
    //抽取#话题#
    function fetch_topic_lists($str)
    {
        $topic_lists = array();
        //匹配出#话题#
        preg_match_all('/#[\x{4e00}-\x{9fa5}A-Za-z0-9-\+\.,]+?#/isu', $str, $matches);
        if (is_array($matches)) {
            foreach ($matches[0] as $match) {
                //清除符号#
                $topic = trim($match, '#');
                //话题允许最大长度为20
                if (!empty($topic) && mix_strlen($topic) <= 20) {
                    //统一转化为小写方便去重
                    $topic_lists[] = strtolower($topic);
                }
            }
        }
        //去重
        $topic_lists = array_unique($topic_lists);
        return !empty($topic_lists) ? $topic_lists : 0;
    }
}

if (!function_exists('fetch_description')) {
    //抽取description
    function fetch_description($str)
    {
        //去杂
        //去除空字符
        $str = preg_replace('/\s/is', '', $str);
        //去除代码[code][/code]
        $str = preg_replace('/\[code\](.*?)\[\/code\]\s*/is', '', $str);
        //去除图片
        $str = preg_replace('/\[img\](.*?)\[\/img\]\s*/is', '', $str);
        //去除url
        $str = preg_replace('/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/is', '', $str);

        //抽取前100个字符
        $description = mb_substr($str, 0, 100);
        return $description;
    }
}

if (!function_exists('sign_html')) {
    //获取签到html
    function sign_html()
    {
        $html = '';
        //用户未登录
        if (!isset($_SESSION['user'])) {
            $html = '<a class="t_red" href="javascript:;" onclick="sign(this);">领取今日登录奖励</a>';
            return $html;
        }

        //初始化签到信息
        if (!isset($_SESSION['sign_info'])) {
            sign_init();
        }

        $sign_info = $_SESSION['sign_info'];

        //从未签到
        if (empty($sign_info)) {
            $html = '<a class="t_red" href="javascript:;" onclick="sign(this);">领取今日登录奖励</a>';
            return $html;
        }

        //如果今天还未签到
        if (date('Y-m-d', strtotime($sign_info['sign_time'])) < date('Y-m-d')) {
            $html = '<a class="t_red" href="javascript:;" onclick="sign(this);">领取今日登录奖励</a>';
        } else {
            $html = "<span class=\"t_green\">已连续签到{$sign_info['con_sign_times']}天</span>";
        }
        return $html;
    }
}

if (!function_exists('sign_init')) {
    //登录后初始化签到信息
    function sign_init()
    {
        $user = $_SESSION['user'];
        $CI = &get_instance();
        $CI->load->model('sign_model');

        $sign_info = $CI->sign_model->get($user['id']);
        if (is_array($sign_info)) {
            $_SESSION['sign_info'] = $sign_info;
        } else {
            $_SESSION['sign_info'] = null;
        }
    }
}

if (!function_exists('sign')) {
    //签到
    function sign()
    {
        $user = $_SESSION['user'];
        $CI = &get_instance();
        $CI->load->model('sign_model');
        $sign_config = $CI->config->item('sign');

        $sign_points = $sign_config['points'];
        $con_sign_times = 0;
        $sign_info = $CI->sign_model->get($user['id']);
        if (is_array($sign_info)) {
            //今天还未签到
            if (date('Y-m-d', strtotime($sign_info['sign_time'])) < date('Y-m-d')) {
                //如果昨天已签到，则是连续签到
                if (date('Y-m-d', strtotime($sign_info['sign_time'])) == date('Y-m-d', strtotime('-1 day'))) {
                    $sign_points += $sign_info['con_sign_times'] * $sign_config['increment'];
                    //不能超过最大允许积分
                    $sign_points = $sign_points > $sign_config['max_points'] ? $sign_config['max_points'] : $sign_points;
                    $con_sign_times = $sign_info['con_sign_times'] + 1;
                } else {
                    $sign_points = $sign_config['points'];
                    $con_sign_times = 1;
                }
            }
            //今天已签到
            else {
                $_SESSION['sign_info'] = $sign_info;
                return -200032;
            }
        }
        //首次签到
        else {
            $sign_points = $sign_config['first_sign_points'];
            $con_sign_times = 1;
        }

        //更新签到信息
        $sign_info = $CI->sign_model->sign($user['id'], $con_sign_times);
        $_SESSION['sign_info'] = $sign_info;
        //增加积分
        $CI->load->model('user_model');
        $CI->user_model->add_points($user['id'], $sign_points);
        //可能还有已登录用户的session里没有points，所以先判断一下
        if (isset($user['points'])) {
            $_SESSION['user']['points'] += $sign_points;
        }
        return $sign_points;
    }
}

if (!function_exists('relationship_html')) {
    //获取关注html
    function relationship_html($ruser_id)
    {
        $html = '';
        //用户未登录
        if (!isset($_SESSION['user'])) {
            $html = '<button type="button" class="layui-btn layui-btn-small" ruser_id="' . $ruser_id . '" onclick="follow(this);" title="关注"><i class="iconfont">&#xe68d;</i><i class="iconfont">&#xe68e;</i>关注</button>';
            return $html;
        }

        $user = $_SESSION['user'];
        $CI = &get_instance();
        $CI->load->model('relationship_model');

        $relationship = $CI->relationship_model->get($user['id'], $ruser_id);
        if (is_array($relationship)) {
            //已关注
            if ($relationship['rtype'] == 1) {
                $html = '<button type="button" class="layui-btn layui-btn-small layui-btn-primary" ruser_id="' . $ruser_id . '" onclick="unfollow(this);" title="取消关注"><i class="iconfont">&#xe68d;</i>已关注</button>';
            }
            //已双向关注
            else if ($relationship['rtype'] == 2) {
                $html = '<button type="button" class="layui-btn layui-btn-small layui-btn-primary" ruser_id="' . $ruser_id . '" onclick="unfollow(this);" title="取消关注"><i class="iconfont">&#xe61a;</i>互相关注</button>';
            }
        }
        //未关注
        else {
            //查询对方是否已关注自己
            $r_relationship = $CI->relationship_model->get($ruser_id, $user['id']);
            if (is_array($r_relationship)) {
                //已被关注
                if ($r_relationship['rtype'] == 1) {
                    $html = '<button type="button" class="layui-btn layui-btn-small" ruser_id="' . $ruser_id . '" onclick="follow(this);" title="关注"><i class="iconfont">&#xe68d;</i><i class="iconfont">&#xe68e;</i>关注</button>';
                }
                //未被关注
                else {
                    $html = '<button type="button" class="layui-btn layui-btn-small" ruser_id="' . $ruser_id . '" onclick="follow(this);" title="关注"><i class="iconfont">&#xe68e;</i>关注</button>';
                }
            }
            //未被关注
            else {
                $html = '<button type="button" class="layui-btn layui-btn-small" ruser_id="' . $ruser_id . '" onclick="follow(this);" title="关注"><i class="iconfont">&#xe68e;</i>关注</button>';
            }
        }

        return $html;
    }
}
