<?php
namespace app\common\Model;

use think\Model;

class SeoRule extends Model
{
    public function getMetaOfCurrentPage()
    {
        $result = $this->getMeta(request()->module(), request()->controller(), request()->action());
        return $result;
    }

    private function getMeta($module, $controller, $action)
    {
        //查询缓存，如果已有，则直接返回
        $cacheKey = "seo_meta_{$module}_{$controller}_{$action}";
           $cache = cache($cacheKey);
           if($cache !== false) {
               return $cache;
           }

        //获取相关的规则
        $rules = $this->getRelatedRules($module, $controller, $action);
        
        //按照排序计算最终结果
        $title = '';
        $keywords = '';
        $description = '';

        foreach ($rules as $e) {
            //如果存在完全匹配的seo配置，则不用程序设置的seo资料
            if ($e['app'] && $e['controller'] && $e['action']) {
                $need_seo = 0;
            }
            if (!$title && $e['seo_title']) {
                $title = $e['seo_title'];
            }
            if (!$keywords && $e['seo_keywords']) {
                $keywords = $e['seo_keywords'];
            }
            if (!$description && $e['seo_description']) {
                $description = $e['seo_description'];
            }
        }
        
        //生成结果
        $result = ['title' => $title, 'keywords' => $keywords, 'description' => $description];
        //写入缓存
        cache($cacheKey, $result);

        //返回结果
        return $result;
    }

    private function getRelatedRules($module, $controller, $action)
    {
        
        //查询与当前页面相关的SEO规则
        $map['app'] = [['=',''],['=',$module],'or'];
        $map['controller'] = [['=',''],['=',$controller],'or']; 
        $map['action'] = [['=',''],['=',$action],'or'];
        $map['status'] = 1;

        $rules = $this->where($map)->order('sort asc')->select();

        //返回规则列表
        return $rules;
    }
}