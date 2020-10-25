<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/2
 * Time: 15:01
 */
namespace app\first\controller;

use app\first\model\Column;
use think\response\Json;
use app\first\validate\Column as ColumnValidate;
use think\Request;

/**
 * Class Navbar
 * @title 栏目相关
 * @url /v1/navbar
 * @desc  获取相关栏目信息
 * @version 1.0
 * @readme
 */
class Navbar extends Base{
    //是否开启授权认证
    public $apiAuth = true;
    //附加方法
    protected $extraActionList = [];

    //跳过鉴权的方法
    protected $skipAuthActionList = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @title 获取无限级导航
     * @method get
     * @param int $id 父亲栏目id false
     * @param int $tree 无限级 false 默认:1 0|1
     * @return json data 返回数据
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     */
    public function get(){
        $id = input('id/d');
        $tree = empty(input('tree'))?0:1;
        $where=[];
        if($id>0){
            $where['fid'] = $id;
            $list = db('column')
                ->field('id,fid,title,banner,name,keywords,description,image,ico,frcolor,bgcolor')
                ->where('status','eq',0)
                ->where($where)
                ->whereOr('id','eq',$id)
                ->select();
        }else{
            $list = db('column')
                ->field('id,fid,title,banner,name,keywords,description,image,ico,frcolor,bgcolor')
                ->where('status','eq',0)
                ->where($where)
                ->select();
        }

        if($tree){
            $list = \service\Category::unlimitedForLevel($list);
        }
        return $this->sendSuccess([
            'status'=>1,
            'msg'=>lang('done',[lang('success',[lang('query')])]),
            'data'=>$list
        ]);
    }
    /**
     * @title 获取父栏目
     * @method parent
     * @param int $id 栏目id true
     * @param int $tree 栏目id true 默认:0 0|1
     *  @return json data 返回数据
     * @route('v1/column/parent','get')
     *  ->pattern(['*' => '\w+'])
     */
    public function parent($id='',$tree=''){
        $validate = new ColumnValidate;
        if(!$validate->scene('query')->check(['id'=>$id])){
            return $this->sendError(0,$validate->getError());
        }
        $column = new Column;
        $_column = $column::getColumn($id);
        if(!$_column){
            return $this->sendError(0,lang('unalready',[lang('column')]));
        }
        $data = $column::all()->toArray();
        $list = \service\Category::getparents($data,$_column->toArray());
        $list[] = $_column;
        if($tree){
            $list = \service\Category::unlimitedForLevel($list);
        }
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('success',[lang('query')]),
            'data'=>$list
        ]);
    }
    /**
     * @title 获取栏目
     * @method column
     * @param int $id 栏目id true
     * @return json data 栏目详情
     * @route('v1/column','get')
     *  ->pattern(['id' => '\d+'])
     */
    public function column($id=null){
        $validate = new ColumnValidate;
        if(!$validate->scene('query')->check(['id'=>$id])){
            return $this->sendError(0,$validate->getError());
        }
        $column = new Column;
        $_column =  $column::getColumn($id);
        if(!$_column){
            return $this->sendError(0,lang('error',[lang('query')]));
        }
        return $this->sendSuccess([
            'status'=>1,
            'msg'=>lang('done',[lang('success',[lang('query')])]),
            'data'=>$_column
        ]);
    }
    /**
     * @title 更新栏目
     * @method post
     * @param int $id 栏目id true
     * @param string $title 栏目名 true
     * @param string $name 栏目标识 true 字母且栏目标识唯一
     * @route('v1/navbar','post')
     * @return json data 返回更新结果
     */
    public function post(){
        $data = request()->post();

        $validate = new ColumnValidate;
        $column = new Column;
        if(!$validate->scene('edit')->check($data)){
            return $this->sendError(0,$validate->getError());
        }
        $_column = $column::get($data['id']);
        if(!$_column){
            return $this->sendError(0,lang('unalready',[lang('column')]));
        }
        $data = array_filter($data);
        $data['update_time']=time();
        if(!db('column')->update($data)){
            return $this->sendError(0,lang('error',[lang('upgrade')]));
        }
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('success',[lang('upgrade')])
        ]);
    }

    /**
     * @title 删除栏目
     * @method delete
     * @param int $id 栏目id true
     * @return json data 操作结果
     * @route('v1/column','delete')
     * @return \think\facade\Response|Json|\think\response\Jsonp|\think\response\Xml
     */
    public function delete($id=0){
        $validate = new ColumnValidate;
        if(!$validate->scene('delete')->check(['id'=>$id])){
            return $this->sendError(0,$validate->getError());
        }
        $column = new Column;
        if(!$column::getColumn($id)){
            return $this->sendError(0,lang('unalready',[lang('column')]));
        }
        if(!$column::destroy($id)){
            return $this->sendError(0,lang('error',[lang('delete')]));
        }
        return $this->sendSuccess([
            'status'=>1,
            'message'=>lang('success',[lang('delete')])
        ]);
    }
}