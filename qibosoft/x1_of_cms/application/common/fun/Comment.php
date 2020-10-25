<?php
namespace app\common\fun;

use think\Db;

/**
 * 评论用到的相关函数
 */
class Comment{
    
    /**
     * 评论总数
     * 模板中使用方法 内容页中的使用方法 {:fun('comment@total',$id,'cms')}  列表页中的使用方法 {:fun('comment@total',$rs['id'],'cms')} 
     * 如果是在当前频道调用的话, 后面的cms可以不写 比如  {:fun('comment@total',$id)}  {:fun('comment@total',$rs['id'])} 
     * @param number $aid 内容ID
     * @param string $sys 指定系统比如cms shop
     * @return void|number|number[]|array|unknown[]|unknown
     */
    public function total($aid=0,$sys=''){
        if (is_array($aid)) {   //$aid为$rs的情况
            $sys = $aid['DIR'];
            $aid = $aid['id'];
        }
        if ($sys==''){
            $sys = config('system_dirname');
        }
        $sysid = modules_config($sys)['id'];
        if(empty($sysid)){
            return 0;
        }
        $map = [
                'where' =>['sysid'=>$sysid,
                        'aid'=>$aid,
                ],
                'count'=>'id'
        ];
        $num = query('comment_content',$map);
        return intval($num);
   }
   
   /**
    * 取得某条评论
    * @param number $id 评论ID
    * @return mixed|number
    */
   public function info($id = 0){
       $map = [
               'where'=>['id'=>$id],
               'type'=>'one',
       ];
       $rsdb = query('comment_content',$map);
       $rsdb && $rsdb['content'] = del_html($rsdb['content']);
       return $rsdb;
   }
   
   /**
    * 获取多条评论
    * @param number|array $aid 主题ID或者是查询数组
    * @param number $rows 取多少条记录
    * @param number|string $sys 频道目录名
    */
   public function more($aid=0,$rows=5,$sys=0){
       if (empty($sys)) {
           $sys = config('system_dirname');
       }
       if (!is_numeric($sys)) {
           $sys = modules_config($sys)['id'];
       }
       if (empty($sys)) {
           return ;
       }
       $map = [];
       if (is_array($aid)) {
           $map = $aid;
       }elseif($aid>0){
           $map = ['aid'=>$aid];
       }
       $map['sysid'] = $sys;
       $listdb = DB::name('comment_content')->where($map)->order('id desc')->limit($rows)->column(true);
       return $listdb;
   }
}