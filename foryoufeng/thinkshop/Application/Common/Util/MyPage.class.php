<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/30
 * Time: 11:32
 * 自定义分页类
 */
namespace Common\Util;
class MyPage{
    private static $page;//当前页
    private static $size;//每页条数
    private static $count;//总共条数
    private static $showpage;//显示出来的数据，多于需要显示的就以...代替

    /**
     * 根据传进来的数据来格式化分页数据
     * @param $page 当前页
     * @param $count 总共条数
     * @param $size 每页条数
     * @param int $showpage 每屏显示多少个页  默认是10
     * @return array 格式好的页
     */
    public static function pager($page,$count,$size,$showpage=0){
        $p=array();
        MyPage::$count=$count;
        MyPage::$size=$size;
        MyPage::$showpage=$showpage>0?$showpage:10;
        MyPage::$page=$page;
        $pagers=ceil( MyPage::$count/ MyPage::$size);
        $p['count']=MyPage::$count;//总条数
        $p['size']=MyPage::$size;//每页显示条数
        $p['pagers']=$pagers;//总页数
        $p['current']=MyPage::$page;//当前页
        $p['pre']=(MyPage::$page-1)>0? '?page='.(MyPage::$page-1):0;//显示前一页
        $p['next']=(MyPage::$page+1)<=$pagers? '?page='.(MyPage::$page+1):0;//显示后一页
        $p=MyPage::to_page($p);
        return $p;
    }

    /**
     *
     * @param $pager  出入分页数据
     * @return mixed 格式化分页数据
     */
    private static function to_page($pager){
        $pager["pprev"]=0;
        $pager["pnext"]=0;
       if($pager['pagers']>MyPage::$showpage){//总页数大于一屏显示的条数，则分屏显示
           if(MyPage::$page>5){//当前页大于5的时候的处理
                for($i=(MyPage::$page-5);$i<(MyPage::$page+1);$i++){//显示当前页的前5页
                    $pager['num'][$i]= '?page='.($i);
                }
               if((MyPage::$page+4)<=$pager['pagers']){//总页数比当前页的后4页还要大，则只显示后4条
                   for($i=MyPage::$page;$i<(MyPage::$page+4);$i++){
                       $pager['num'][$i]= '?page='.($i);
                   }
               }else{//总页数不比当前页的后4页大就全部显示
                   for($i=MyPage::$page;$i<=$pager['pagers'];$i++){
                       $pager['num'][$i]= '?page='.($i);
                   }
               }
           }else{//当前页不大于5的时候的处理，全部显示出来
               for($i=1;$i<=MyPage::$showpage;$i++){
                   $pager['num'][$i]= '?page='.($i);
               }
           }
           if(MyPage::$page<MyPage::$showpage-1){
               $pager["pprev"]=0;
               $pager["pnext"]=1;
           }else{
               $pager["pprev"]=1;
               if((MyPage::$page+4)<$pager['pagers']){
                   $pager["pnext"]=1;
               }else{
                   $pager["pnext"]=0;
               }
           }
       }else{//总页数小于一屏显示的条数就全部显示
           for($i=0;$i<$pager['pagers'];$i++){
               $pager['num'][$i+1]= '?page='.($i+1);
           }
       }
       return $pager;
    }

    /**
     * 设置每屏显示的页数，默认为10
     * @param mixed $showpage
     */
    public static function setShowpage($showpage)
    {
        self::$showpage = $showpage;
    }

}