<?php
namespace app\index;
use think\template\TagLib;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯撒 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class MyTags extends TagLib{
    /**
     * 定义标签列表
     */
    protected $tags   =  [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'category'  => ['attr' => 'cid,row', 'close' => 1], // 文章分类
        'data'      => ['attr' => 'cid,row,order', 'close' => 1], // 1热门，2最新
        'hits'      => ['attr' => 'cid,row', 'close' => 1], // 推荐文章
        'artlist'   => ['attr' => 'cateid,row', 'close' => 1], 
    ];

    /**
    *网址栏目标签
    *@param string $tag category
    */
    public function tagCategory($tag, $content)
    {
        $row = isset($tag['row']) ? $tag['row'] : 24; //显示条数
        $php = <<<str
<?php
    \$db = db('category');
    \$where = '';
    \$where['type']=array('EQ',1);
    \$result = \$db->where(\$where)->order("sort ASC")->limit($row)->select();
    foreach (\$result as \$index=>\$field){
        \$field['caturl'] = Url('/category/'.\$field['cid']);
?>
str;
        $php .= $content;
        $php .= '<?php } unset($where);unset($result);unset($field); ?>';
        return $php;
    }

    /**
    *热门、最新列表标签
    *@param string $tag Weblist
    */
    public function tagData($tag, $content)
    {
        $cid = isset($tag['cid']) ? trim($tag['cid']) : 0;
        $row    = isset($tag['row']) ? trim($tag['row']) : 7;
        $order    = isset($tag['order']) ? trim($tag['order']) : ''; 
        $php = <<<str
<?php
    //获取cate值
    \$cid =$cid;
    \$order =$order;
    \$db =db('article');
    //---查询条件---
    \$where='';
    if(\$cid){
        //查询条件
        \$where['cid']=array('EQ',\$cid);
    }
    if(\$order == 1){
        \$order='click DESC,sort ASC';
    }else{
        \$order='time DESC,sort ASC';
    }
    //---获取数据---
        \$results = \$db->where(\$where)->limit($row)->order(\$order)->select();
        foreach(\$results as \$index=>\$v){
            \$v['url'] = Url('/article/'.\$v['aid']);
?>
str;
        $php .= $content;
        $php .= '<?php } unset($where);unset($results);unset($v); ?>';
        return $php;
    }

    /**
    *推荐列表标签
    *@param string $tag Weblist
    */
    public function tagHits($tag, $content)
    {
        $cid = isset($tag['cid']) ? trim($tag['cid']) : 0;
        $row    = isset($tag['row']) ? trim($tag['row']) : 7;
        $php = <<<str
<?php
    //获取cate值
    \$cid =$cid;
    \$db =db('article');
    //---查询条件---
    \$where['type']=array('EQ',1);
    if(\$cid){
        //查询条件
        \$where['cid']=array('EQ',\$cid);
        \$where['type']=array('EQ',1);
    }
    //---获取数据---
        \$results = \$db->where(\$where)->limit($row)->order('time DESC,sort ASC')->select();
        foreach(\$results as \$index=>\$v){
            \$v['_index']=\$index;
            \$v['url'] = Url('/article/'.\$v['aid']);
?>
str;
        $php .= $content;
        $php .= '<?php } unset($where);unset($results);unset($v); ?>';
        return $php;
    }

    /**
    *网址列表标签
    *@param string $tag Weblist
    */
    public function tagArtlist($tag, $content)
    {
        $cateid = isset($tag['cateid']) ? trim($tag['cateid']) : 0;
        $row    = isset($tag['row']) ? trim($tag['row']) : 35;
        $php = <<<str
<?php
    //获取cate值
    \$cateid =$cateid;
    \$db =db('Webs');
    //---查询条件---
        \$where='';
        if(\$cateid){
            //查询条件
            \$where['cateid']=array('EQ',\$cateid);
        }
    //---已经审核的网址---
        \$where['look']=array('EQ',0);
    //---获取数据---
        \$results = \$db->where(\$where)->limit($row)->order('click DESC,sort ASC,addtime ASC')->select();
        foreach(\$results as \$index=>\$datas){
            \$datas['url']=\$datas['weburl'];
?>
str;
        $php .= $content;
        $php .= '<?php } unset($where);unset($results);unset($datas); ?>';
        return $php;
    }

}