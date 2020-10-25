<?php
namespace Wpf\App\Admin\Controllers;
class AuthmanagerController extends \Wpf\App\Admin\Common\Controllers\CommonController{
    public $_model;
    
    public function initialize(){
        parent::initialize();
    }
    
    public function onConstruct(){
        parent::onConstruct();
        $this->_model = new \Wpf\App\Admin\Models\AdminAuthGroup();
        
    }
    
    public function indexAction(){

        
        $this->headercss
            ->addCss("theme/assets/global/plugins/select2/select2.css")
            ->addCss("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css");
        
        $this->footerjs
            ->addJs("theme/assets/global/plugins/select2/select2.min.js")
            ->addJs("theme/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js")
            ->addJs("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js");
            
        $list = $this->_model->find()->toArray();
        
        

        
        int_to_string($list);
        $this->view->setVar('list', $list);
        $this->view->setVar('_use_tip', true ); 
        $this->view->setVar('meta_title','权限管理');
    }
    
    public function changeStatusAction($method=null){
        $method = $this->request->getQuery("method","string","");
        $id = $this->request->get("id");
        
        if(is_numeric($id)){
            $id = array($id);
        }

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        
        $id = implode(",",array_unique($id));
        
        $where = "id in ({$id})";
        $list = $this->_model->find($where);
        
        
        switch ( strtolower($method) ){
            case 'forbidgroup':
                $list->update(array(
                    "status" => 0
                ));
                break;
            case 'resumegroup':
                $list->update(array(
                    "status" => 1
                ));
                break;
            case 'deletegroup':
                $list->delete();
                break;
            default:
                $this->error('参数非法');
        }
        
        $this->success("操作成功");
    }
    
    /**
     * 创建管理员用户组
     * @author 吴佳恒
     */
    public function createGroupAction(){
        if ( (! $this->view->getVar("auth_group")) ) {
            $this->view->setVar('auth_group',array('title'=>null,'id'=>null,'description'=>null,'rules'=>null,));//排除notice信息
        }
        $this->view->pick("Authmanager/editgroup");
    }
    
    public function editGroupAction(){
        
        $id = $this->request->getQuery("id","int",0);
        
        $auth_group = $this->_model->getInfo($id)->toArray();
        
        $this->view->setVar('auth_group',$auth_group);
        $this->view->pick("Authmanager/editgroup");
    }
    
    public function writeGroupAction(){
        
        $rules = $this->request->getPost("rules",null,"");
        
        
        if(is_array($rules)){
            sort($rules);

            $rules = array_unique($rules);
            
            $rules = implode(",",$rules);
            
            $rules = trim($rules,",");

        }
        
        
        
        $data['module'] = 'admin';
        
        $data['type'] = \Wpf\App\Admin\Models\AdminAuthGroup::TYPE_ADMIN;
        
        $data = array_merge($data,$this->request->getPost());
        
        if($rules){
            $data['rules'] = $rules;
        }
        
        
        if($data['id']){
            $info = $this->_model->getInfo($data['id']);
            if($info && $info->save($data)){
                $this->success('操作成功!',$this->url->get(CONTROLLER_NAME."/index/"));
            }else{
                $this->success('操作失败!');
            }
        }else{
            if($this->_model->save($data)){
                $this->success('操作成功!',$this->url->get(CONTROLLER_NAME."/index/"));
            }
        }
    }
    
    /**
     * 用户组授权用户列表
     * @author 吴佳恒
     */
    public function userAction(){
                
        if(! $group_id = $this->request->getQuery("group_id","int",0)){
            $this->error('参数错误');
        }
        
        $this->headercss
            ->addCss("theme/assets/global/plugins/select2/select2.css")
            ->addCss("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css");
        
        $this->footerjs
            ->addJs("theme/assets/global/plugins/select2/select2.min.js")
            ->addJs("theme/assets/global/plugins/datatables/media/js/jquery.dataTables.min.js")
            ->addJs("theme/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js");
        
        
        $where = array(
            "status <> 0",
            "type = ".\Wpf\App\Admin\Models\AdminAuthGroup::TYPE_ADMIN,
            "module = 'admin'",
        );
        $where = implode(" and ",$where);
        $list = $this->_model->find(array(
            "conditions" => $where,
            "columns" => "id,title,rules"
        ))->toArray();        
        foreach($list as $value){
            $auth_group[$value['id']] = $value;
        }
        
        //$auth_group = M(C("BASE_DB_NAME").".".'AuthGroup')->where( array('status'=>array('egt','0'),'module'=>'admin','type'=>AuthGroupModel::TYPE_ADMIN) )
        //    ->getfield('id,id,title,rules');
        
        
        

        
        $AdminAuthGroupAccess = "Wpf\App\Admin\Models\AdminAuthGroupAccess";
        $AdminAuthGroup = "Wpf\App\Admin\Models\AdminAuthGroup";
        $AdminMember = "Wpf\App\Admin\Models\AdminMember";
        $list = $this->_model->find();
        $memberlist = array();
        foreach($list as $value){            
            foreach($value->$AdminAuthGroupAccess as $member){
                if($member->$AdminMember){
                    $info = $member->$AdminMember->toArray();
                    $memberlist[$info['id']] = $info;
                }
            }
        }
        
        $list = $memberlist;
        
        
        //$prefix   = C('DB_PREFIX');
//        $l_table  = C("BASE_DB_NAME").".".$prefix.(AuthGroupModel::ADMIN_MEMBER);
//        $r_table  = C("BASE_DB_NAME").".".$prefix.(AuthGroupModel::AUTH_GROUP_ACCESS);
//        $list     = M()->field( 'm.id,m.username,m.last_login_time,m.last_login_ip,m.status' )
//                       ->table( $l_table.' m' )
//                       ->where( array('a.group_id'=>$group_id,'m.status'=>array('egt',0)) )
//                       ->order( 'm.id asc')
//                       ->join ( $r_table.' a ON m.id=a.uid' )
//                       ->select();
        //dump($list);exit;
        //$_REQUEST = array();
        //$list = $this->lists($list,null,null,null);
        
        int_to_string($list);
        $this->view->setVar( '_list',     $list );
        $this->view->setVar('auth_group', $auth_group);
        $this->view->setVar('this_group', $auth_group[$group_id]);
        $this->view->setVar('meta_title','成员授权');

    }
    
    /**
     * 将用户添加到用户组,入参uid,group_id
     * @author 吴佳恒
     */
    public function addToGroupAction(){
        if(!$uid = $this->request->getPost("uid","string","")){
            $this->error('参数有误');
        }
        if(!$gid = $this->request->getPost("group_id","int",0)){
            $this->error('参数有误');
        }
        
        
        if(is_numeric($uid)){
            if ( $this->isAdministrator($uid) ) {
                $this->error('该用户为超级管理员');
            }
            
            $AdminMemberModel = new \Wpf\App\Admin\Models\AdminMember();
            if(! $AdminMemberModel->getInfo($uid)){
                $this->error('用户不存在');
            }
            
        }
  
        if(!$this->_model->getInfo($gid)){
            $this->error("用户组不存在");
        }
        
        $return  = $this->_model->addToGroup($uid,$gid);
        
        if ( $return === true ){
            $this->success('操作成功');
        }else{
            $this->error($return);
        }
    }
    
    /**
     * 将用户从用户组中移除  入参:uid,group_id
     * @author 吴佳恒
     */
    public function removeFromGroupAction(){
        if(!$uid = $this->request->getQuery("uid","string","")){
            $this->error('参数有误');
        }
        if(!$gid = $this->request->getQuery("group_id","int",0)){
            $this->error('参数有误');
        }
        if( $uid==ADMIN_UID ){
            $this->error('不允许解除自身授权');
        }
        
        //$AuthGroup = D('AuthGroup');
        if( !$this->_model->getInfo($gid)){
            $this->error('用户组不存在');
        }
        

        
        $where =
            "uid={$uid} and group_id={$gid}";
        
        $model = new \Wpf\App\Admin\Models\AdminAuthGroupAccess();
        if($del = $model->findFirst($where)){
            $del->delete();       
            $this->success('操作成功');
        }else{
            $this->error("操作失败");
        }
        
    }
    
    
    /**
     * 访问授权页面
     * @author 吴佳恒
     */
    public function accessAction(){
        $this->updateRules();
        //$auth_group = M(C("BASE_DB_NAME").".".'AuthGroup')->where( array('status'=>array('egt','0'),'module'=>'admin','type'=>AuthGroupModel::TYPE_ADMIN) )
        //    ->getfield('id,id,title,rules');
        
        $where = array(
            "status <> 0",
            "type = ".\Wpf\App\Admin\Models\AdminAuthGroup::TYPE_ADMIN,
            "module = 'admin'" 
        );
        $where = implode(" and ",$where);
        $list = $this->_model->find(array(
            "conditions" => $where,
            "columns" => "id,title,rules"
        ))->toArray();
        
        foreach($list as $value){
            $auth_group[$value['id']] = $value;
        }
        
        $node_list   = $this->returnNodes();
        
        //$map         = array('module'=>'admin','type'=>AuthRuleModel::RULE_MAIN,'status'=>1);
        //$main_rules  = M(C("BASE_DB_NAME").".".'AuthRule')->where($map)->getField('name,id');
        
        $AuthRuleModel = new \Wpf\App\Admin\Models\AdminAuthRule();
        $where = array(
            "type = ".\Wpf\App\Admin\Models\AdminAuthRule::RULE_MAIN,
            "status = 1",
            "module = 'admin'" 
        );
        $where = implode(" and ",$where);
        $list = $AuthRuleModel->find(array(
            "columns" => 'name,id',
            "conditions" => $where
        ))->toArray();
        
        foreach($list as $value){
            $main_rules[$value['name']] = $value['id'];
        }
        
        
        $where = array(
            "type = ".\Wpf\App\Admin\Models\AdminAuthRule::RULE_URL,
            "status = 1",
            "module = 'admin'" 
        );
        $where = implode(" and ",$where);
        $list = $AuthRuleModel->find(array(
            "columns" => 'name,id',
            "conditions" => $where
        ))->toArray();
        
        foreach($list as $value){
            $child_rules[$value['name']] = $value['id'];
        }
        
        //$map         = array('module'=>'admin','type'=>AuthRuleModel::RULE_URL,'status'=>1);
        //$child_rules = M(C("BASE_DB_NAME").".".'AuthRule')->where($map)->getField('name,id');
        
        $group_id = $this->request->getQuery("group_id","int",0);
        
        
        
        
        $this->view->setVar('main_rules', $main_rules);
        $this->view->setVar('auth_rules', $child_rules);
        $this->view->setVar('node_list',  $node_list);
        $this->view->setVar('auth_group', $auth_group);
        $this->view->setVar('this_group', $auth_group[$group_id]);
        
        $this->view->setVar('meta_title','访问授权');
        
        $this->view->pick("Authmanager/managergroup");
        
        //$this->meta_title = '访问授权'; 
        
        //dump($main_rules);
        //dump($node_list);
        //exit;       
        //$this->display('managergroup');
    }
    
    /**
     * 返回后台节点数据
     * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function returnNodes($tree = true){
        static $tree_nodes = array();
        if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
            return $tree_nodes[$tree];
        }
        $Menumodel = new \Wpf\App\Admin\Models\AdminMenu();
        if((int)$tree){
            
            
            $list = $Menumodel->cleanCache()->find(array(
                "columns" => 'id,pid,title,url,tip,hide',
                "order" => 'sort asc'
            ))->toArray();
            
            //$list = M(C("BASE_DB_NAME").".".'Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if( stripos($value['url'],MODULE_NAME)!==0 ){
                    $list[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
            $nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
            foreach ($nodes as $key => $value) {
                if(!empty($value['operator'])){
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        }else{
            //$nodes = M(C("BASE_DB_NAME").".".'Menu')->field('title,url,tip,pid')->order('sort asc')->select();
            
            $nodes = $Menumodel->cleanCache()->find(array(
                "columns" => 'title,url,tip,pid',
                "order" => 'sort asc'
            ))->toArray();
            
            foreach ($nodes as $key => $value) {
                if( stripos($value['url'],MODULE_NAME)!==0 ){
                    $nodes[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;
        return $nodes;
    }
    
    /**
     * 后台节点配置的url作为规则存入auth_rule
     * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
     * @author 吴佳恒
     */
    public function updateRules(){
        //需要新增的节点必然位于$nodes
        $nodes    = $this->returnNodes(false);
        
        $AuthRuleModel = new \Wpf\App\Admin\Models\AdminAuthRule();
        $where = array(
            "module='admin'",
            "type in (1,2)"
        );
        $where = implode(" and ",$where);
        $rules = $AuthRuleModel->cleanCache()->find(array(
            "order" => 'name',
            "conditions" => $where
        ))->toArray();
        
        //$AuthRule = M(C("BASE_DB_NAME").".".'AuthRule');
//        $map      = array('module'=>'admin','type'=>array('in','1,2'));//status全部取出,以进行更新
//        //需要更新和删除的节点必然位于$rules
//        $rules    = $AuthRule->where($map)->order('name')->select();

        //构建insert数据
        $data     = array();//保存需要插入和更新的新节点
        foreach ($nodes as $value){
            $temp['name']   = $value['url'];
            $temp['title']  = $value['title'];
            $temp['module'] = 'admin';
            if($value['pid'] >0){
                $temp['type'] = \Wpf\App\Admin\Models\AdminAuthRule::RULE_URL;
            }else{
                $temp['type'] = \Wpf\App\Admin\Models\AdminAuthRule::RULE_MAIN;
            }
            $temp['status']   = 1;
            $data[strtolower($temp['name'].$temp['module'].$temp['type'])] = $temp;//去除重复项
        }

        $update = array();//保存需要更新的节点
        $ids    = array();//保存需要删除的节点的id
        foreach ($rules as $index=>$rule){
            $key = strtolower($rule['name'].$rule['module'].$rule['type']);
            if ( isset($data[$key]) ) {//如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
                $data[$key]['id'] = $rule['id'];//为需要更新的节点补充id值
                $update[] = $data[$key];
                unset($data[$key]);
                unset($rules[$index]);
                unset($rule['condition']);
                $diff[$rule['id']]=$rule;
            }elseif($rule['status']==1){
                $ids[] = $rule['id'];
            }
        }
        if ( count($update) ) {
            foreach ($update as $k=>$row){
                if ( $row!=$diff[$row['id']] ) {
                    $upinfo = $AuthRuleModel->cleanCache()->getInfo($row['id']);
                    $upinfo->save($row);
                    //$AuthRule->where(array('id'=>$row['id']))->save($row);
                }
            }
        }
        if ( count($ids) ) {
            $dellist = $AuthRuleModel->cleanCache()->find("id in (".implode(',',$ids).")");
            $dellist->update(array('status'=>-1));
            
            //$AuthRule->where( array( 'id'=>array('IN',implode(',',$ids)) ) )->save(array('status'=>-1));
            //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        }
        
        
        if( count($data) ){
            foreach($data as $value){ 
                $AuthRuleModel = new \Wpf\App\Admin\Models\AdminAuthRule();
                $AuthRuleModel->save($value);
            }
            //$AuthRule->addAll(array_values($data));
        }
        return true;
        
    }
}
