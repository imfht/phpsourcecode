<?php

namespace Admin\Controller;

class AppsController extends AdminController {

    protected $_model_id = 7;

    public function models($models=null,$app_id=0) {
        if (IS_POST) {
            M('appmodels')->where(array('app_id'=>$app_id))->delete();
            foreach($models as $model_id) {
                $model_apps[] = array(
                    'app_id'=>$app_id,
                    'model_id'=>$model_id
                );
            }
            M('appmodels')->addAll($model_apps);
            $this->success('操作成功!');
        } else {
            $app = M('apps')->find($app_id);
            session('bmob_appid',$app['app_id']);
            session('bmob_restkey',$app['rest_api_key']);
            session('bmob_masterkey',$app['master_key']);
            $uds = M('appmodels')->where(array('app_id'=>$app_id))->getField('model_id',true);
            $model_ids = implode(",", $uds);
            $models = M('model')->where(array('status'=>1,'extend'=>2))->select();
            $this->assign('models', $models);
            $this->assign('model_ids', $model_ids);
            $this->assign('app_id', $app_id);
            $this->meta_title = 'Models';
            $this->display();
        }
    }

    public function pushModels($models=null,$app_id=0) {
        if (IS_POST) {
            $result = '';
            foreach($models as $model_id) {
                $result = $result . $this->pushModel($model_id);
            }
            $this->success($result);
        } else {
            redirect(U('models'));
        }
    }

    private function pushModel($model_id=0) {
        $name = parse_name(get_table_name($model_id),1);
        $fields     = get_model_attribute($model_id,false);
        $data = array();
        foreach($fields as $key=>$attr) {
            if ('Number' == $attr['type']) {
                $data[$attr['name']] = doubleval($attr['field']);
            } else if ('String' == $attr['type']) {
                $data[$attr['name']] = $attr['field'];
            } else {
                $data[$attr['name']] = json_decode($attr['field']);
            }
        }
        $bmob = new \BmobObject($name);
        $res = $bmob->create($data);
        if($res->objectId){
            $delres=$bmob->delete($res->objectId);
            if($delres->msg == 'ok'){
                return 'id:'.$model_id.',name:'.$name.',推送成功!';
            } else {
                return 'id:'.$model_id.',name:'.$name.',创建成功删除失败!';
            }
        } else {
            return 'id:'.$model_id.',name:'.$name.',推送失败!';
        }
    }

    public function datas($app_id=0) {
        $models = M()->table('t_appmodels am,t_model m')->where('am.model_id=m.id and am.app_id='.$app_id)->field('m.*')->select();
        $app = M('apps')->find($app_id);
        session('bmob_appid',$app['app_id']);
        session('bmob_restkey',$app['rest_api_key']);
        session('bmob_masterkey',$app['master_key']);
        $this->assign('models', $models);
        $this->assign('app', $app);
        $this->meta_title = 'Models';
        $this->display();
    }

    public function lists($p = 0) {
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //获取模型信息
        $model = M('Model')->find($this->_model_id);
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
            $fix    = C("DB_PREFIX");

            $key = array_search('id', $fields);
            if(false === $key){
                array_push($fields, "{$fix}{$parent}.id as id");
            } else {
                $fields[$key] = "{$fix}{$parent}.id as id";
            }

            /* 查询记录数 */
            $count = M($parent)->join("INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id")->where($map)->count();

            // 查询数据
            $data   = M($parent)
                ->join("INNER JOIN {$fix}{$name} ON {$fix}{$parent}.id = {$fix}{$name}.id")
                /* 查询指定字段，不指定则查询所有字段 */
                ->field(empty($fields) ? true : $fields)
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                ->order("{$fix}{$parent}.id DESC")
                /* 数据分页 */
                ->page($page, $row)
                /* 执行查询 */
                ->select();

        } else {
            in_array('id', $fields) || array_push($fields, 'id');
            $uds = M('userapps')->where(array('uid'=>UID))->getField('app_id',true);
            $map['id'] = array('in',$uds);
            $name = parse_name(get_table_name($model['id']), true);
            $data = M($name)
                /* 查询指定字段，不指定则查询所有字段 */
                ->field(empty($fields) ? true : $fields)
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                ->order('id DESC')
                /* 数据分页 */
                ->page($page, $row)
                /* 执行查询 */
                ->select();

            /* 查询记录总数 */
            $count = M($name)->where($map)->count();
        }

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);

    }

    public function add($model = null){
        //获取模型信息
        $model = M('Model')->find($this->_model_id);
        $model || $this->error('模型不存在！');
        if(IS_POST){
            $Model  =   D(parse_name(get_table_name($model['id']),1));
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            if($Model->create() && $Model->add()){
                $this->success('添加'.$model['title'].'成功！', U('lists?model='.$model['name'].'&product_id='.I('product_id')));
            } else {
                $this->error($Model->getError());
            }
        } else {

            $fields = get_model_attribute($model['id']);

            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->meta_title = '新增'.$model['title'];
            $this->display($model['template_add']?$model['template_add']:'');
        }
    }


    public function edit($id = 0){
        //获取模型信息
        $model = M('Model')->find($this->_model_id);
        $model || $this->error('模型不存在！');

        if(IS_POST){
            $Model  =   D(parse_name(get_table_name($model['id']),1));
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            if($Model->create() && $Model->save()){
                $this->success('保存'.$model['title'].'成功！', U('edit', array('id'=>$id)));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields     = get_model_attribute($model['id']);

            //获取数据
            $data       = M(get_table_name($model['id']))->find($id);
            $data || $this->error('数据不存在！');

            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑'.$model['title'];
            $this->display($model['template_edit']?$model['template_edit']:'');
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
            }elseif('datetime' == $attr['type']){ // 日期型
                $auto[] =   array($attr['name'],'strtotime',3,'function');
            }
        }
        return $Model->validate($validate)->auto($auto);
    }

    public function del($ids=null){
        $model = M('Model')->find($this->_model_id);
        $model || $this->error('模型不存在！');

        $ids = array_unique((array)I('ids',0));

        if ( empty($ids) ) {
            $this->error('请选择要操作的数据!');
        }

        $Model = M(get_table_name($model['id']));
        $map = array('id' => array('in', $ids) );
        if($Model->where($map)->setField('status','-1')){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}