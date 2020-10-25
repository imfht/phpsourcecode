<?php

namespace Admin\Controller;


class BmobController extends AdminController {

    public function tools() {
        $this->meta_title = 'Bmob数据类型';
        $this->display();
    }

    public function lists($model_id = null, $p = 0) {
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //获取模型信息
        $model = M('Model')->find($model_id);
        $model || $this->error('模型不存在！');

        //解析列表规则
        $fields = array();
        $grids  = preg_split('/[;\r\n]+/s', $model['list_grid']);
        foreach ($grids as &$value) {
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
        foreach ($fields as $k=>$v) {
            if ($v == 'model_id') unset($fields[$k]);
        }
        // 关键字搜索
        $map	=	array();
        $key	=	$model['search_key']?$model['search_key']:'title';
        if(isset($_REQUEST[$key])){
            $map[$key]	=	array('$regex'=>'.*'.$_GET[$key].'.*');
            unset($_REQUEST[$key]);
        }
        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name]	=	array('$regex'=>'.*'.$val.'.*');
            }
        }
        $mapStr = '';
        if (count($map) > 0) {
            $mapStr = 'where='.json_encode($map);
        }

        $row    = empty($model['list_row']) ? 10 : $model['list_row'];
        in_array('id', $fields) || array_push($fields, 'id');
        $name = parse_name(get_table_name($model['id']), true);
        $data = array();
        $count = 0;
        $bmob = new \BmobObject($name);
        try {
            $data_result = $bmob->get("",array('limit='.$row,'skip='.($row*($page-1)),'order=-createdAt',$mapStr));
            $data = $data_result->results;
            /* 查询记录总数 */
            $count_result = $bmob->get("",array('limit=0','count=1'));
            $count = $count_result->count;
        } catch (\BmobException $e) {
            throw $e;
        }
        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $data =  json_decode( json_encode( $data),true);
        foreach ($data as $k=>$v) {
            $data[$k]['model_id'] = $model_id;
        }
        //dump($grids);die;

        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }

    public function add($model_id = null){
        //获取模型信息
        $model = M('Model')->find($model_id);
        $model || $this->error('模型不存在！');
        if(IS_POST){
            $name = parse_name(get_table_name($model['id']),1);
            $bmob = new \BmobObject($name);
            $data = $_POST;
            $fields     =   get_model_attribute($model_id,false);
            foreach($fields as $key=>$attr) {
                if ('Number' == $attr['type'] || 'String' == $attr['type']) {
                    $data[$attr['name']] = ($data[$attr['name']]);
                } else {
                    $data[$attr['name']] = json_decode($data[$attr['name']],true);
                }
            }
            $res = $bmob->create($data);
            if($res->objectId){
                $this->success('添加'.$model['title'].'成功！', U('lists?model_id='.$model_id));
            } else {
                $this->error('添加失败，请稍后再试!');
            }
        } else {
            $fields = get_model_attribute($model['id']);
            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->meta_title = '新增'.$model['title'];
            $this->display($model['template_add']?$model['template_add']:'');
        }
    }


    public function edit($model_id=null,$objectId = 0){
        //获取模型信息
        $model = M('Model')->find($model_id);
        $model || $this->error('模型不存在！');

        $name = parse_name(get_table_name($model['id']),1);
        $bmob = new \BmobObject($name);
        if(IS_POST){
            unset($_POST['objectId']);
            unset($_POST['createdAt']);
            unset($_POST['updatedAt']);
            $data = $_POST;
            $fields     = get_model_attribute($model['id'],false);
            foreach($fields as $key=>$attr) {
                if ('Number' == $attr['type'] || 'String' == $attr['type']) {
                    $data[$attr['name']] = ($data[$attr['name']]);
                } else {
                    $data[$attr['name']] = json_decode($data[$attr['name']],true);
                }
            }
            $res=$bmob->update($objectId, $data);
            if($res->updatedAt){
                $this->success('保存'.$model['title'].'成功！', U('edit', array('objectId'=>$objectId,'model_id'=>$model_id)));
            } else {
                $this->error('保存失败!');
            }
        } else {
            $fields     = get_model_attribute($model['id'],false);
            //获取数据
            $data = $bmob->get($objectId);
            $data || $this->error('数据不存在！');
            $data =  json_decode( json_encode( $data),true);
            foreach($fields as $key=>$attr) {
                if ('Number' == $attr['type'] || 'String' == $attr['type']) {
                    $data[$attr['name']] = ($data[$attr['name']]);
                } else {
                    $data[$attr['name']] = json_encode($data[$attr['name']]);
                }
            }
            $this->assign('model', $model);
            $this->assign('fields', get_model_attribute($model['id']));
            $this->assign('data', $data);
            $this->meta_title = '编辑'.$model['title'];
            $this->display($model['template_edit']?$model['template_edit']:'');
        }
    }

    public function del($ids=null,$model_id=null){
        $model = M('Model')->find($model_id);
        $model || $this->error('模型不存在！');
        $ids = array_unique((array)I('ids',0));
        if ( empty($ids) || count($ids) == 0 ) {
            $this->error('请至少选择1条数据删除!');
        }
        $name = get_table_name($model['id']);
        $bmob = new \BmobObject($name);
        foreach($ids as $id) {
            $res=$bmob->delete($id);
            if($res->msg != 'ok'){
                $this->error($id . ' 删除失败');
            }
        }
        $this->success('删除成功,条数:'.count($ids));
    }
}