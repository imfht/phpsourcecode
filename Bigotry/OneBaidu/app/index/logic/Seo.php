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

namespace app\index\logic;

/**
 * SEO逻辑
 */
class Seo extends IndexBase
{

    public function getSeoInfo()
    {
        
        $cache_key = 'cache_' . serialize(URL_MODULE);
        
        $data = cache($cache_key);
        
        if (empty($data)) {
            
            $info = $this->modelSeo->getInfo(['url' => URL_MODULE]);

            if (empty($info)) :

                $info['seo_title']          = config('seo_title');
                $info['seo_keywords']       = config('seo_keywords');
                $info['seo_description']    = config('seo_description');

            endif;

            $data = '<title>' . $info['seo_title'] . '</title><meta name="keywords" content="' . $info['seo_keywords'] . '"/><meta name="description" content="' . $info['seo_description'] . '"/>';
            
            cache($cache_key, $data);
        }
        
        return $data;
    }
}
