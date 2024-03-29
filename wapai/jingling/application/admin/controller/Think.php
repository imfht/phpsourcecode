<?php
// +----------------------------------------------------------------------
// |   精灵后台系统 [ 基于TP5，快速开发web系统后台的解决方案 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 - 2017 http://www.apijingling.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wapai 邮箱:wapai@foxmail.com
// +---------------------------------------------------------------------- 

namespace app\admin\controller;

/**
 * 模型数据管理控制器
 * @author wapai   邮箱:wapai@foxmail.com
 */
class Think  extends Admin{

    /**
     * 显示指定模型列表数据
     * @param  String $model 模型标识
     * @author wapai   邮箱:wapai@foxmail.com
     */
    public function listnew($model = null, $p = 0){
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //获取模型信息
        $model = \think\Db::name('Model')->getByName($model);
        $model || $this->error('模型不存在！');   
        if(empty($model['list_grid']))
        	$this->error('未定义:列表定义');
        //解析列表规则
        $fields = array();
        $grids  = preg_split('/[;\r\n]+/s', trim($model['list_grid']));
        foreach ($grids as &$value) {
        	if(trim($value) === ''){
        		continue;
        	}
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']	=	$val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array	=	explode('|',$val);
                $fields[] = $array[0];
            }
        }
        // 过滤重复字段信息
        $fields =   array_unique($fields);
        // 关键字搜索
        $map	=	array();
        $key	=	$model['search_key']?$model['search_key']:'title';
        if(isset($_REQUEST[$key])){
            $map[$key]	=	array('like','%'.$_GET[$key].'%');
            unset($_REQUEST[$key]);
        }
         
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name]	=	$val;
            }
        }
        $row    = empty($model['list_row']) ? 10 : $model['list_row'];

        //读取模型数据列表
        if($model['extend']){
            $name   = get_table_name($model['id']);
            $parent = get_table_name($model['extend']);
            $fix    = config("database.prefix");

            $key = array_search('id', $fields);
            if(false === $key){
                array_push($fields, "{$fix}{$parent}.id as id");
            } else {
                $fields[$key] = "{$fix}{$parent}.id as id";
            } 
            /* 查询记录数 */
           $count = \think\Db::name($parent)->alias('a')
                 ->join("{$fix}{$name} b",'a.id = b.id')
                 ->where($map)->count(); 
//             $count = \think\Db::name($parent)->join("INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id")->where($map)->count();
        
            // 查询数据
            $data   =\think\Db::name($parent)->alias('a')
            ->join("{$fix}{$name} b",'a.id = b.id')
            /* 查询指定字段，不指定则查询所有字段 */
            //                 ->field(empty($fields) ? true : $fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order("b.id DESC")
            ->paginate($row); 
            /* 数据分页 */ 
            $page = $data->render();
            $data = $data->toArray(); 
        } else {
            if($model['need_pk']){
                in_array('id', $fields) || array_push($fields, 'id');
            }
            $name = parse_name(get_table_name($model['id']), true);
            $data = \think\Db::name($name)
                /* 查询指定字段，不指定则查询所有字段 */
                ->field(empty($fields) ? true : $fields)
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                ->order($model['need_pk']?'id DESC':'') 
                /* 执行查询 */
                ->paginate($row);  
               /* 数据分页 */
               $page = $data->render();
               $data = $data->toArray(); 
            /* 查询记录总数 */
            $count = \think\Db::name($name)->where($map)->count();
        } 
        //分页
        if($count > $row){ 
            $this->assign('_page', $page);
        }
        
        $data = $data['data'];
        $data   =   $this->parseDocumentList($data,$model['id']); 
        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->assign('meta_title' , $model['title'].'列表'); 
        return $this->fetch($model['template_list']);
    }

    public function del($model = null, $ids=null){
        $model = \think\Db::name('Model')->find($model);
        $model || $this->error('模型不存在！');

        $ids = array_unique((array)I('ids',0));

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        $Model = \think\Db::name(get_table_name($model['id']));
        $map = array('id' => array('in', $ids) );
        if($Model->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author wapai   邮箱:wapai@foxmail.com
     */
    public function setStatus($model='Document'){
        return parent::setStatus($model);
    }
    
    public function edit($model = null, $id = 0){
        //获取模型信息
        $model = \think\Db::name('Model')->find($model);
        $model || $this->error('模型不存在！');

        if($this->request->isPost()){
            //继承模型先写入基础模型数据
            $logic = \think\Loader::model('Document');
            $res = $logic->updates(); 
            if(!$res){
                $this->error($logic->getError());
            }else{
                $this->success('更新成功', Cookie('__forward__'));
            }
            // $tableName = parse_name(get_table_name($model['id']));
            // if(!class_exists('app\admin\model\\'.$tableName)){
            //     $class = 'app\common\logic\Base';
            //     $class = new $class('abc');
            //     //halt($class);
                
            // }
            // $Model  =   \think\Loader::model('Document');

            // // 获取模型的字段信息
            // //$Model  =   $this->checkAttr($Model,$model['id']);
            // $data = $this->request->post(); 
            // $id = $data['id']; 
            // $class->updates($id,false);
            // if($Model->allowField(true)->isUpdate(true)->save($data)){
            //     $this->success('保存'.$model['title'].'成功！', url('listnew?model='.$model['name']));
            // } else {
            //     $this->error($Model->getError());
            // }

        } else {
            $fields     = get_model_attribute($model['id']);

            //获取数据

            //$data = \think\Db::name(get_table_name($model['id']))->where(['id'=>$id])->find();
            $Document = \think\Loader::model('Document');
            // 获取详细数据
            $data = $Document->detail($id);
            
            $data || $this->error('数据不存在！'); 
             
            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->assign('meta_title','编辑'.$model['title']); 
            return $this->fetch($model['template_edit']?$model['template_edit']:'');
        }
    }

    public function add($model = null){
        $info['model_id']       =   input('model',0);
        //获取模型信息
        $model = \think\Db::name('Model')->where(array('status' => 1))->find($model);
        $model || $this->error('模型不存在！');

        if($this->request->isPost()){
            //继承模型先写入基础模型数据
            $logic = \think\Loader::model('Document');
            $res = $logic->updates(); 
            if(!$res){
                $this->error($logic->getError());
            }else{
                $this->success('新增成功', Cookie('__forward__'));
            }



            // $Model  =   model(parse_name(get_table_name($model['id']),1));
            // // 获取模型的字段信息
            // $Model  =   $this->checkAttr($Model,$model['id']);
            // if($Model->create() && $Model->add()){
            //     $this->success('添加'.$model['title'].'成功！', url('lists?model='.$model['name']));
            // } else {
            //     $this->error($Model->getError());
            // }

        } else {

            $fields = get_model_attribute($model['id']);
            $this->assign('info',       $info);
            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->assign('meta_title','新增'.$model['title']); 
            return $this->fetch($model['template_add']?$model['template_add']:'');
        }
    }

    protected function checkAttr($Model,$model_id){
        $fields     =   get_model_attribute($model_id,false);
        $validate   =   $auto   =   array();
        foreach($fields as $key=>$attr){
            if($attr['is_must']){// 必填字段
                $validate[]  =  array($attr['name'],'require',$attr['title'].'必须!');
            }
            // 自动验证规则
            if(!empty($attr['validate_rule'])) {
                $validate[]  =  array($attr['name'],$attr['validate_rule'],$attr['error_info']?$attr['error_info']:$attr['title'].'验证错误',0,$attr['validate_type'],$attr['validate_time']);
            }
            // 自动完成规则
            if(!empty($attr['auto_rule'])) {
                $auto[]  =  array($attr['name'],$attr['auto_rule'],$attr['auto_time'],$attr['auto_type']);
            }elseif('checkbox'==$attr['type']){ // 多选型
                $auto[] =   array($attr['name'],'arr2str',3,'function');
            }elseif('date' == $attr['type']){ // 日期型
                $auto[] =   array($attr['name'],'strtotime',3,'function');
            }elseif('datetime' == $attr['type']){ // 时间型
                $auto[] =   array($attr['name'],'strtotime',3,'function');
            }
        }
        return $Model->validate($validate)->auto($auto);
    }
}