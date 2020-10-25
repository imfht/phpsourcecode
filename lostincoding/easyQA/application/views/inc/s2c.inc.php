<script type="text/javascript">
<?php

//将一些$config信息转化为js config以方便前端操作
//1.不输出敏感信息
//2.不作为操作依据,只作显示用

$js_config = array(
    'theme_id' => $theme_id,
    'base_url' => base_url(),
    'user' => isset($user) ? $user : null,
    'qiniu' => array(
        'static_bucket_name' => $config['qiniu']['static_bucket_name'],
        'static_bucket_domain' => $config['qiniu']['static_bucket_domain'],
    ),
    'avatar_base_url' => $config['avatar_base_url'],
    'enum_show' => $config['enum_show'],
    'site_info' => array(
        'id' => $config['site_info']['id'],
        'domain' => $config['site_info']['domain'],
        'home_url' => $config['site_info']['home_url'],
    ),
    'weibo' => array('WB_AKEY' => $config['weibo']['WB_AKEY']),
);
echo 'var CONFIG = ' . json_encode($js_config, JSON_UNESCAPED_UNICODE) . ';';

?>
</script>
