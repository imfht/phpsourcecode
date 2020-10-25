<?php
// 权限控制器       
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\controller;


use think\db\Query;
use tpvue\admin\builder\AdminFormBuilder;
use tpvue\admin\library\util\Category;
use tpvue\admin\model\AdminAuthGroupModel;
use tpvue\admin\model\AdminAuthRuleModel;
use tpvue\admin\model\AdminAuthGroupAccessModel;
use tpvue\admin\model\AdminModel;
use tpvue\admin\validate\AuthGroupValidate;
use tpvue\admin\validate\AuthRuleValidate;


class AccessController extends BaseController
{

    protected $middleware = ['MemberLogin','Auth'];


    /**
     * [index 空操作提醒]
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * [nodeList 节点管理]
     * @return [array] [节点树]
     */
    public function nodeList() 
    {
        $this->assign('meta_title', '节点列表');
        $list = array();

        $condition = function (Query $query) {
            $keyword = $this->request->get('keyword', '');
            if ($keyword) {
                $query->whereLike('name|title', $keyword);
            }
        };
        $cat = new Category(AdminAuthRuleModel::class, array('id', 'pid', 'title', 'fullname','sort asc , id desc'));
        $temp = $cat->getList($condition, '', 'sort asc, id asc');
        $level = array("1" => "项目（GROUP_NAME）", "2" => "模块（MODEL_NAME）", "3" => "操作（ACTION_NAME）", "4" => "操作（ACTION_NAME）");
        foreach ($temp as $k => $v) {
            $temp[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
            $temp[$k]['chStatusTxt'] = $v['status'] == 0 ? "启用" : "禁用";
            $temp[$k]['is_displayTxt'] = $v['is_display'] == 0 ? "隐藏" : "显示";
            $temp[$k]['level'] = $level[$v['level']];
            $list[$v['id']] = $temp[$k];
        }
        $this->assign('list', $list);
        return $this->fetch('access/nodeList');
    }

    /**
     * [add 新增节点]
     * @return [string] [新增节点单个处理]
     */
    public function add()
    {
        $ruleData = array();
        $title = '新增';
        if ($this->request->isPost()) {
            $form = $this->request->only('title,name,level,status,sort', 'post');
            $valid = new AuthRuleValidate();
            if (!$valid->check($form)) {
                $this->error($valid->getError());
            }
            $ruleData = AdminAuthRuleModel::create($form);
            if (!$ruleData) {
                $this->error('新增失败');
            }
            $this->success('操作成功！', 'admin/access/nodeList');
        }

        $infolist = $this->getPid($ruleData, '', 'sort asc, id asc');
        $builder = new AdminFormBuilder();
        // 设置页面标题
        return $builder->setMetaTitle($title . '节点')
            ->addFormItem('id', '', '', 'hidden')
            ->addFormItem('title', '节点名称', '请填写节点名称，例如：会员编辑', 'text', '', 'required')
            ->addFormItem('name', '节点地址', '请填写节点地址，例如：Admin/User/userEdit', 'text', '', 'required')
            ->addFormItem('level', '节点类型', '设置“节点”类型', 'self', $infolist['levelOption'], 'required')
            ->addFormItem('pid', '节点类型', '设置“节点”类型', 'self', $infolist['pidOption'], 'required')
            ->addFormItem('status', '节点状态', '设置“节点”状态', 'radio', array(1 => '启用', 0 => '禁用'), 'required')
            ->addFormItem('is_display', '是否显示', '用于导航是否显示', 'radio', array(1 => '显示', 0 => '隐藏'), 'required')
            ->addFormItem('node_icon', '节点图标', '请填写节点地址，例如：icon-drds')
            ->addFormItem('class_icon', '分类图标', '请填写节点地址，例如：icon-drds')
            ->addFormItem('sort', '节点排序', '请填写节点序号，例如：2', 'text', '', 'required')
            ->setFormData($ruleData)
            ->addButton('submit')
            ->addButton('back')// 设置表单按钮
            ->fetch();
    }

    /**
     * [edit 修改节点]
     * @return [string] [修改节点单个处理]
     */
    public function edit($id)
    {
        $title = '编辑';
        $ruleData = AdminAuthRuleModel::where('id', $id)->find();

        if ($this->request->isPost()) {
            $form = $this->request->only('title,name,level,status,sort', 'post');
            $valid = new AuthRuleValidate();
            if (!$valid->check($form)) {
                $this->error($valid->getError());
            }
            $ruleData->save($form);
            $this->success('操作成功！', 'admin/access/nodeList');
        }

        $infolist = $this->getPid($ruleData);
        $builder = new AdminFormBuilder();
        return $builder->setMetaTitle($title . '节点')// 设置页面标题
            ->addFormItem('id', '', '', 'hidden')
            ->addFormItem('title', '节点名称', '请填写节点名称，例如：会员编辑', 'text', '', 'required')
            ->addFormItem('name', '节点地址', '请填写节点地址，例如：Admin/User/userEdit', 'text', '', 'required')
            ->addFormItem('level', '节点类型', '设置“节点”类型', 'self', $infolist['levelOption'], 'required')
            ->addFormItem('pid', '节点类型', '设置“节点”类型', 'self', $infolist['pidOption'], 'required')
            ->addFormItem('status', '节点状态', '设置“节点”状态', 'radio', array(1 => '启用', 0 => '禁用'), 'required')
            ->addFormItem('is_display', '是否显示', '用于导航是否显示', 'radio', array(1 => '显示', 0 => '隐藏'), 'required')
            ->addFormItem('node_icon', '节点图标', '请填写节点地址，例如：icon-drds')
            ->addFormItem('class_icon', '分类图标', '请填写节点地址，例如：icon-drds')
            ->addFormItem('sort', '节点排序', '请填写节点序号，例如：2', 'text', '', 'required')
            ->setFormData($ruleData)
            ->addButton('submit')->addButton('back')// 设置表单按钮
            ->fetch();
    }

    /**
     * [setStatus 禁用启动 节点]
     */
    public function setStatus()
    {
        $id = $this->request->get('ids', '');
        if (empty($id)) {
            $this->error('参数错误', 'admin/access/nodeList');
        }
        $ids = explode(',', $id);
        $dataStauts = $this->request->get('status', 0);

        if (AdminAuthRuleModel::where('id', 'in', $ids)->setField('status', $dataStauts)) {
            $this->success('操作成功！','admin/access/nodeList');
        } else {
            $this->error('操作失败！','admin/access/nodeList');
        }
    }

    /**
     * [delNode 删除节点]
     */
    public function delNode()
    {
        if ($this->request->isPost()) {
                $dataUid = $this->param['ids'];
            if(empty($dataUid)) return json( ['msg' => '参数错误！', 'code' => '0', 'url' =>  url("Access/nodeList")] );
                $arrData = str2arr($dataUid);
            foreach ($arrData as $key => $value) {
                //如果有子节点则不能删除
                $result = db::name('AuthRule')->where(array('pid'=>$value))->count();
                if ($result) return json( ['msg' => '当前节点下还拥有子节点，不能删除！', 'code' => '0', 'url' =>  url("Access/nodeList")] );
            }
            if (AdminAuthRuleModel::where('id','in',$dataUid)->delete()) {
                return json( ['msg' => '删除成功！', 'code' => '1', 'url' =>  url("Access/nodeList")] );
            } else {
                return json( ['msg' => '删除失败！', 'code' => '0', 'url' =>  url("Access/nodeList")] );
            }
        } else {
            $this->fuck();
        }
    }

    /**
     * [roleList 角色列表]
     */
    public function roleList()
    {       
        $this->assign('meta_title','角色管理');
        $keyword =isset($this->param['keyword']) ? $this->param['keyword']:false;
        
        $condition = [];
        if ($keyword) {
            $condition['id|title'] = ['like','%'.$keyword.'%'];
        }

        
        $AuthGroupData = AdminAuthGroupModel::where($condition)->order('create_time desc , update_time desc')->paginate(15,false,['query' => request()->param()]);
        $this->assign('authgroup', $AuthGroupData);
        return $this->fetch('access/roleList');
    }

    /**
    * [addRole 添加角色]
    */
    public function addRole()
    {
        if ($this->request->isPost()) {

            $form = $this->request->only('title,status,rules,description', 'post');
            $validate = new AuthGroupValidate;

            if (!$validate->check($form)) {
                $this->error($validate->getError());
            }

            $res = AdminAuthGroupModel::create($form);
            if (!$res) {
                $this->error('角色添加失败');
            }
            $this->success('操作成功！', 'admin/access/roleList', ['lastId'=>$res->id]);
        }

        $data = list_to_tree2(AdminAuthRuleModel::select()->toArray(),'id','pid');
        $this->assign('nodeList', $data);
        return $this->fetch('access/addRole');
    }
    
     /**
     * [authRole 角色授权]
     */
    public function authRole()
    {
        $id = $this->request->get('id', '', 'decode');
        if (empty($id)) {
            $this->error('参数错误', 'admin/access/roleList');
        }

        $group = AdminAuthGroupModel::where('id', $id)->find();
        if (!$group) {
            $this->error('不存在该用户组', 'admin/access/roleList');
        }

        if ($this->request->isPost()) {
            $form = $this->request->only('title,status,rules,description', 'post');
            $validate = new AuthGroupValidate;

            if (!$validate->check($form)) {
                $this->error($validate->getError());
            }

            $group->save($form);

            $this->success('操作成功！', 'admin/access/roleList');
        }

        $this->assign('id', $group->id);
        $this->assign('info', $group);

        $data = AdminAuthRuleModel::order('sort asc, id asc')->select()->toArray();
        $data = list_to_tree2($data);
        $this->assign('nodeList', $data);
        return $this->fetch('access/authRole');
    }

    /**
     * [disabledRole 角色禁用]
     */
    public function disabledRole()
    {
        $id = $this->request->get('id', '', 'decode');
        if (empty($id)) {
            $this->error('参数错误', 'admin/access/roleList');
        }
        $dataStauts = $this->request->get('status', '', 'decode');

        if (AdminAuthGroupModel::where('id', 'in', $id)->setField('status', $dataStauts ? 0 : 1)) {
            $this->success('操作成功！','admin/access/roleList');
        } else {
            $this->error('操作失败！','admin/access/roleList');
        }
    }

    /**
     * [delRole 删除角色分组]
     */
    public function delRole()
    {
        if ($this->request->isGet()) {
            $this->fuck();
        }

        $dataUid = $this->request->post('ids', '');
        if (empty($dataUid)) {
            $this->error('参数错误', 'admin/access/roleList');
        }

        // 单行删除
        if (strpos($dataUid, ',') === false) {
            if (AdminAuthGroupAccessModel::where('group_id', $dataUid)->value('id')) {
                $this->error('当前分组有授权用户、禁止删除！', 'admin/access/roleList');
            }

            if (AdminAuthGroupModel::where('id', intval($dataUid))->delete()) {
                $this->error('删除成功', 'admin/access/roleList');
            } else {
                $this->error('删除失败', 'admin/access/roleList');
            }
        }

        // 批量删除
        $arrData = explode(',', $dataUid);
        if (AdminAuthGroupAccessModel::where('group_id', 'in', $arrData)->value('id')) {
            $this->error('当前分组有授权用户、禁止删除！', 'admin/access/roleList');
        }

        if (AdminAuthGroupModel::where('id', 'in', $arrData)->delete()) {
            $this->error('删除成功', 'admin/access/roleList');
        } else {
            $this->error('删除失败', 'admin/access/roleList');
        }
    }

    /**
     * [delUser 批量删除/单删除]
     */
    public function delUser()
    {
        if (IS_POST) {
            if (!$this->param['ids']) return json( ['msg' => '参数错误!', 'code' => '0', 'url' => url('admin/access/authlist')] );
            if ($this->authgroupaccess_model->where('uid','in',$this->param['ids'])->delete()) {
                return json( ['msg' => '删除成功！', 'code' => '1', 'url' => url('Access/authlist')] );
            } else {
                return json( ['msg' => '删除失败！', 'code' => '0', 'url' => url('Access/authlist')] );
            }
        } else {
            $this->fuck();
        }
    }

    /**
     * [disabledUser 修改用户登陆权限]
     */
    public function disabledUser()
    {
        $dataUid = $this->param['id'];
        $status  = $this->param['status'];
        //p($status,1);
        if (empty($dataUid)) $this->error('参数错误！', U('Access/authlist'));
        $sqlmap = array();
        $sqlmap['id'] = array("IN", $dataUid);
        $status = $status ? 0 : 1;
        $data = array('status'=>$status);
        if ($this->member_model->where($sqlmap)->setField($data)) {
            $this->success('操作成功！', url('Access/authlist'));
        }else{
            $this->error('操作失败！', url('Access/authlist'));
        }
    }

    /**
     * [getPid 获取节点]
     */
    private function getPid($info) 
    {
        if(empty($info['level'])){
            $info['level']='';
        }

        if(empty($info['pid'])){
            $info['pid']='';
        }

        $info['levelOption'] = '';
        $arr = array("请选择", "项目", "模块", "操作", '分类');

        $info['levelOption'].='<div class="rule-single-select single-select">';
        $info['levelOption'].='<select name="level" id="level" datatype="*" errormsg="请选择管理员角色" sucmsg=" ">';

        for ($i = 0; $i < 5; $i++) {
            $selected = $info['level'] == $i ? " selected='selected'" : "";
            $info['levelOption'].='<option value="' . $i . '" ' . $selected . '>' . $arr[$i] . '</option>';
        }

        $info['levelOption'].='</select>';
        $info['levelOption'].='</div>';
        $info['levelOption'].='<script type="text/javascript">
                                    $(function(){
                                        $("select[name=\'level\']").change(function(){
                                            var level=$(this).val();
                                            $("select[name=\'pid\']>option").attr("disabled","disabled");
                                            if(level==1){
                                                $("select[name=\'pid\']>option[value=\'0\']").removeAttr("disabled").attr("selected","selected");
                                            }else if(level==2){
                                                $("select[name=\'pid\']>option[level=\'1\']").removeAttr("disabled").attr("selected","selected");
                                            }else if(level==3){
                                                $("select[name=\'pid\']>option[level=\'2\']").removeAttr("disabled").attr("selected","selected");
                                            }else{
                                                $("select[name=\'pid\']>option[level=\'3\']").removeAttr("disabled").attr("selected","selected");
                                            }
                                        });
                                    });
                                </script>';

        $level    = intval($info['level']);
        $cat      = new Category(AdminAuthRuleModel::class, array('id', 'pid', 'title', 'fullname'));
        $list     = $cat->getList('','','sort asc ,id asc');               //获取分类结构
        #         =======================此处有一个BUG,如果加上div有样式了,但是无法进行关联操作.==========================
        //$option ='<div class="rule-single-select single-select">';
        $option   ='<select name="pid" id="pid" datatype="*" errormsg="请选择管理员角色" sucmsg=" ">';
        $option   .= $level == 0 ? '<option value="0" level="0">根节点</option>' : '<option value="0" disabled="disabled">根节点</option>';

        foreach ($list as $k => $v) {
            $disabled = $v['level'] == ($level-1) ? "" : ' disabled="disabled"';
            $selected = $v['id'] != $info['pid'] ? "" : ' selected="selected"';
            $option.='<option value="' . $v['id'] . '"' . $disabled . $selected . '  level="' . $v['level'] . '">' . $v['fullname'] . '</option>';
        }

        $option.='</select>';
        //$option.='</div>';
        $info['pidOption'] = $option;
        return $info;
    }
}
