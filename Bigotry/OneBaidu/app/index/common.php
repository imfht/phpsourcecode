<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

// 解析字符串中的{$变量}
function parse_string_val($string, $vars)
{
    
    $taglib_begin   = '{';
    $taglib_end     = '}';
    
    $pattern = '/('.$taglib_begin.').*?('.$taglib_end.')/is';
    
    $results = [];
    
    preg_match_all($pattern, $string, $results);
    
    foreach ($results[0] as $v)
    {
        
        $del_start = substr($v, 2);
        
        $del_end = substr($del_start, 0, strlen($del_start) - 1); 
        
        $string = isset($vars[$del_end]) ? str_replace($v, $vars[$del_end], $string) : sr($string, $v);
    }
    
    return $string;
}

// 获取CURL数据
function get_curl_data($url)
{
    
    $curl = curl_init();
    // 设置你需要抓取的URL 

    curl_setopt($curl, CURLOPT_URL, $url); 
    // 设置header 
    curl_setopt($curl, CURLOPT_HEADER, 1); 
    // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    // 运行cURL，请求网页 
    $data = curl_exec($curl); 

    curl_close($curl);
    
    return $data;
}

// 静态资源域名替换
function cdomain($str){
 
    $domain =   '{"http://graph.baidu.com": "https://sp0.baidu.com/-aYHfD0a2gU2pMbgoY3K","http://p.qiao.baidu.com":"https://sp0.baidu.com/5PoXdTebKgQFm2e88IuM_a","http://vse.baidu.com":"https://sp3.baidu.com/6qUDsjip0QIZ8tyhnq",
                "http://hdpreload.baidu.com":"https://sp3.baidu.com/7LAWfjuc_wUI8t7jm9iCKT-xh_","http://lcr.open.baidu.com":"https://sp2.baidu.com/8LUYsjW91Qh3otqbppnN2DJv","http://kankan.baidu.com":"https://sp3.baidu.com/7bM1dzeaKgQFm2e88IuM_a",
                "http://xapp.baidu.com":"https://sp2.baidu.com/yLMWfHSm2Q5IlBGlnYG","http://dr.dh.baidu.com":"https://sp0.baidu.com/-KZ1aD0a2gU2pMbgoY3K","http://xiaodu.baidu.com":"https://sp0.baidu.com/yLsHczq6KgQFm2e88IuM_a",
                "http://sensearch.baidu.com":"https://sp1.baidu.com/5b11fzupBgM18t7jm9iCKT-xh_","http://s1.bdstatic.com":"https://ss1.bdstatic.com/5eN1bjq8AAUYm2zgoY3K","http://olime.baidu.com":"https://sp0.baidu.com/8bg4cTva2gU2pMbgoY3K",
                "http://app.baidu.com":"https://sp2.baidu.com/9_QWsjip0QIZ8tyhnq","http://i.baidu.com":"https://sp0.baidu.com/74oIbT3kAMgDnd_","http://c.baidu.com":"https://sp0.baidu.com/9foIbT3kAMgDnd_",
                "http://sclick.baidu.com":"https://sp0.baidu.com/5bU_dTmfKgQFm2e88IuM_a","http://nsclick.baidu.com":"https://sp1.baidu.com/8qUJcD3n0sgCo2Kml5_Y_D3","http://sestat.baidu.com":"https://sp1.baidu.com/5b1ZeDe5KgQFm2e88IuM_a",
                "http://eclick.baidu.com":"https://sp3.baidu.com/-0U_dTmfKgQFm2e88IuM_a","http://api.map.baidu.com":"https://sp2.baidu.com/9_Q4sjOpB1gCo2Kml5_Y_D3","http://ecma.bdimg.com":"https://ss1.bdstatic.com/-0U0bXSm1A5BphGlnYG",
                "http://ecmb.bdimg.com":"https://ss0.bdstatic.com/-0U0bnSm1A5BphGlnYG","http://t1.baidu.com":"https://ss0.baidu.com/6ON1bjeh1BF3odCf","http://t2.baidu.com":"https://ss1.baidu.com/6OZ1bjeh1BF3odCf",
                "http://t3.baidu.com":"https://ss2.baidu.com/6OV1bjeh1BF3odCf","http://t10.baidu.com":"https://ss0.baidu.com/6ONWsjip0QIZ8tyhnq","http://t11.baidu.com":"https://ss1.baidu.com/6ONXsjip0QIZ8tyhnq",
                "http://t12.baidu.com":"https://ss2.baidu.com/6ONYsjip0QIZ8tyhnq","http://i7.baidu.com":"https://ss0.baidu.com/73F1bjeh1BF3odCf","http://i8.baidu.com":"https://ss0.baidu.com/73x1bjeh1BF3odCf",
                "http://i9.baidu.com":"https://ss0.baidu.com/73t1bjeh1BF3odCf","http://b1.bdstatic.com":"https://ss0.bdstatic.com/9uN1bjq8AAUYm2zgoY3K","http://ss.bdimg.com":"https://ss1.bdstatic.com/5aV1bjqh_Q23odCf",
                "http://opendata.baidu.com":"https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv","http://api.open.baidu.com":"https://sp0.baidu.com/9_Q4sjW91Qh3otqbppnN2DJv","http://tag.baidu.com":"https://sp1.baidu.com/6LMFsjip0QIZ8tyhnq","http://f3.baidu.com":"https://sp2.baidu.com/-uV1bjeh1BF3odCf",
                "http://s.share.baidu.com":"https://sp0.baidu.com/5foZdDe71MgCo2Kml5_Y_D3","http://bdimg.share.baidu.com":"https://ss1.baidu.com/9rA4cT8aBw9FktbgoI7O1ygwehsv",
                "http://1.su.bdimg.com":"https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG","http://2.su.bdimg.com":"https://ss1.bdstatic.com/kvoZeXSm1A5BphGlnYG",
                "http://3.su.bdimg.com":"https://ss2.bdstatic.com/kfoZeXSm1A5BphGlnYG","http://4.su.bdimg.com":"https://ss3.bdstatic.com/lPoZeXSm1A5BphGlnYG",
                "http://5.su.bdimg.com":"https://ss0.bdstatic.com/l4oZeXSm1A5BphGlnYG","http://6.su.bdimg.com":"https://ss1.bdstatic.com/lvoZeXSm1A5BphGlnYG",
                "http://7.su.bdimg.com":"https://ss2.bdstatic.com/lfoZeXSm1A5BphGlnYG","http://8.su.bdimg.com":"https://ss3.bdstatic.com/iPoZeXSm1A5BphGlnYG"}';
    
    $data = json_decode($domain, true);
 
    foreach($data as $k => $v){ $str = str_replace($k, $v, $str); }
 
    return $str;
}