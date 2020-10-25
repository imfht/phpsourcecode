<?php
namespace ebcms;

class Tags
{

    public static function set()
    {
        if (!$apps = \think\Cache::get('eb_apps')) {
            $apps = \think\Db::name('app') -> where('status',1) -> column(true,'name');
            \think\Cache::set('eb_apps',$apps);
        }

        if (!$tags = \think\Cache::get('eb_tags')) {
            $tags = [
                'ebcms_init' => [
                    'app\\ebcms\\behavior\\Auth',
                ],
            ];
            foreach ($apps as $key => $app) {
                $tagfile = APP_PATH.$app['name'].'/install/tags.php';
                if (is_file($tagfile)) {
                    $temp = include $tagfile;
                    if ($temp && is_array($temp)) {
                        $tags = array_merge_recursive($tags,$temp);
                    }
                }
            }
            \think\Cache::set('eb_tags',$tags);
        }
        return $tags;
    }
}