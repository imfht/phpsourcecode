<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\common\controller;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Hook;
use think\Route;
use think\Loader;
use think\Db;
use think\Cache;
use think\Validate;
use app\common\builder\ZBuilder;


class Base extends controller
{
 protected $request;
 protected $getbase;
 protected function _initialize()
    {
    //请求
	  $this->request = Request::instance();	
    $this->getbase = model('Base');
    }
    /**
     * [validate 验证加密数据]
     * @Author   Jerry
     * @DateTime 2017-05-04T11:15:24+0800
     * @Example  eg:
     * @param    [type]                   $rule [description]
     * @param    [type]                   $msg  [description]
     * @param    [type]                   $data [description]
     * @return   [type]                         [description]
     */
  protected function validate($rule,$msg,$data,$returnType="api"){
    $returnType = strtolower($returnType);
    if(is_array($rule)){
      #传统的流程验证
      $validate = new Validate($rule, $msg);
      $result   = $validate->check($data);
    }else{
      #走ecode加密后的json数据验证
      $validateArr = json_decode(decode($rule),true);
      if(!$validateArr){
         switch ($returnType) {
          case 'api':
            returnJson(1,'',"验证数据异常");
            break;
           case 'ajax':
            $this->error("验证数据异常");
            break;
        }
      }
      $validate = new Validate($validateArr['rule'], $validateArr['message']);
      $result   = $validate->check($data);
    }
    if(!$result){
      switch ($returnType) {
        case 'api':
          returnJson(1,'',$validate->getError());
          break;
         case 'ajax':
          $this->error($validate->getError());
          break;
      }
    }
    // return $result;
  }
  /**
   * [auth 权限]
   * @param  [type] $var [description]
   * @return [type]      [description]
   */
  static public function Auth($var){
   return \app\common\auth\Auth::Auth($var);
  }

  /**
   * [getUid 获得UID]
   * @return [type] [description]
   */
  static public function getUid(){
    $uid = self::Auth('ucenter')->getUid();
    return $uid?$uid:0;
  }

    /**
     * [arrToStr 取数组里面的值]
     * @param  [type] $array [数组]
     * @param  [type] $att   [字符]
     * @return [type]        [description]
     */
  
  
  protected function arrTstr($array,$att){
  		return  $array[$att];
  }
  /**
   * [builder 表单构造器]
   * @Author   Jerry
   * @DateTime 2017-04-14T10:28:55+0800
   * @Example  eg:
   * @param    [type]                   $name [description]
   * @return   [type]                         [description]
   */
  protected function builder($name){
    return ZBuilder::make($name);
  }
 /**
     * 获取筛选条件
     * @author 
     * @return array
     */
     protected function getMap()
    {
        $search_field     = input('param.search_field/s', '');
        $keyword          = input('param.keyword/s', '');
        $filter           = input('param._filter/s', '');
        $filter_content   = input('param._filter_content/s', '');
        $filter_time      = input('param._filter_time/s', '');
        $filter_time_from = input('param._filter_time_from/s', '');
        $filter_time_to   = input('param._filter_time_to/s', '');

        $map = [];

        // 搜索框搜索
        if ($search_field != '' && $keyword !== '') {
            $map[$search_field] = ['like', "%$keyword%"];
        }

        // 时间段搜索
        if ($filter_time != '' && $filter_time_from != '' && $filter_time_to != '') {
            $map[$filter_time] = ['between time', [$filter_time_from, $filter_time_to]];
        }

        // 下拉筛选
        if ($filter != '') {
            $filter         = array_filter(explode('|', $filter), 'strlen');
            $filter_content = array_filter(explode('|', $filter_content), 'strlen');
            foreach ($filter as $key => $item) {
                $map[$item] = ['in', $filter_content[$key]];
            }
        }
        return $map;
    }

    /**
     * 设置分页参数
     * @author 
     */
    final protected function setPageParam()
    {
        if (input('?get.list_rows') && input('get.list_rows') != '') {
            $list_rows = input('get.list_rows');
        } elseif (input('?param.list_rows') && input('param.list_rows') != '') {
            $list_rows = input('param.list_rows');
        } else {
            $list_rows = config('list_rows');
        }

        config('paginate.list_rows', $list_rows);
        config('paginate.query', input('get.'));
    }

    /**
     * 获取字段排序
     * @param string $extra_order 额外的排序字段
     * @param bool $before 额外排序字段是否前置
     * @author 
     * @return string
     */
     protected function getOrder($extra_order = '', $before = false)
    {
        $order = input('param._order/s', '');
        $by    = input('param._by/s', '');
        if ($order == '' || $by == '') {
            return $extra_order;
        }
        if ($extra_order == '') {
            return $order. ' '. $by;
        }
        if ($before) {
            return $extra_order. ',' .$order. ' '. $by;
        } else {
            return $order. ' '. $by . ',' . $extra_order;
        }
    }

  
  /**
   * [is_admin 是否为后台管理员]
   * @return boolean [description]
   */
  protected function is_admin(){
    if(!$this->getuid()) $this->error('您还没有登陆');
    if($this->getuserinfo('group_id')!=1) $this->error('您没有后台管理权限');
  }
 
  
  /**
   * [error2 没有倒计时的确误页面]
   * @param  [type] $message [description]
   * @return [type]          [description]
   */
  protected function error2($message){
   $this->assign('error',$message);
   echo  $this->fetch('system/error2');
   die;
  }
  /**
   * [success2 没有倒计时的正确页面]
   * @param  [type] $message [description]
   * @return [type]          [description]
   */
  protected function success2($message){
     $this->assign('message',$message);
   echo  $this->fetch('system/error2');
   die;
  }

 

}
