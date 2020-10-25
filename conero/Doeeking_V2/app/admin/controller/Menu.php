<?php
/* 2017年2月13日 星期一 系统菜单管理
 *
 */
namespace app\admin\controller;
use think\Controller;
class Menu extends Controller{
    // 首页
    public function index()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统菜单','bootstrap'=>true
        ]);  
        $bstp = $this->bootstrap($this->view);
        $wh = $bstp->getSearchWhere();
        $count = $this->croDb('sys_menu')->where($wh)->count();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['descrip'=>'描述','url'=>'路径','groupid'=>'分组标识','editor'=>'维护者','edittm'=>'编辑时间']]);
        $bstp->tableGrid(['__viewTr__'=>'trs'],['table'=>'sys_menu','cols'=>[
                function($record){return '<a href="'.url('edit','groupid='.$record['groupid']).'">'.$record['groupid'].'</a>';},
                'order',
                function($record){return '<a href="'.$record['url'].'" target="_blank">'.$record['descrip'].'</a>';},
                'url','edittm','editor']
            ],
            function($db) use ($wh,$bstp){
                $page = $bstp->page_decode();
                return $db->page($page,30)->where($wh)->order('groupid,`order`,edittm desc')->select();
        });
        $this->bootstrap($this->view)->pageBar($count);
        return $this->fetch();
    }    
    // 编辑
    public function edit()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统菜单','js'=>['Menu/edit'],'bootstrap'=>true
        ]);  
        $groupid = request()->param('groupid');
        if($groupid){
            $data = model('Menu')->where(['groupid'=>$groupid])->order('`order` asc')->select();
            $recordList = '';$ctt = 1;
            foreach($data as $v){
                $v = $v->toArray();                
                $recordList .= '
                    <tr dataid="'.$ctt.'"><td class="rowno">
                        <input type="checkbox" class="rowselecter">
                        '.$ctt.'
                        </td>
                        <td>
                            <input type="text" name="groupid" class="form-control" value="'.$v['groupid'].'" required>
                            <input type="hidden" name="listno" value="'.$v['listno'].'" required></td>
                        <td><input type="text" name="descrip" class="form-control" value="'.$v['descrip'].'" required></td>
                        <td><input type="text" name="url" class="form-control" value="'.$v['url'].'" required></td>
                        <td><input type="text" name="code_mk" class="form-control" value="'.$v['code_mk'].'"></td>
                        <td><input type="text" name="remark" value="'.$v['remark'].'" class="form-control"></td>
                        <td><input type="text" name="param" value="'.htmlspecialchars($v['param']).'" class="form-control"></td>
                    </tr>
                ';
                //  
                $ctt++;
                // println($v->toArray());
                // println($v['param']);
                // println($recordList);
                // var_dump($recordList);break;
            }
            $page = [];
            $page['newlink'] = ' <a href="'.url('edit').'">新增</a>';            
            if($recordList) $page['recordList'] = $recordList;
            // println($data);
            $this->assign('page',$page);
        }
        return $this->fetch();
    }
    public function save()
    {
        list($data,$mode,$map) = $this->_getSaveData();
        $ret = '';
        if(empty($mode)){
            $order = null;
            $menu = model('Menu');
            $aCtt = 0;$mCtt = 0;$dCtt = 0; $lastOrder = 1;
            foreach($data as $v){
                $menuData = json_decode($v,true);
                if(empty($order)) $order = $menu->where(['groupid'=>$menuData['groupid']])->max('`order`');       
                $type = (isset($menuData['type']) && 'D' == $menuData['type'])? 'D':null;
                if(empty($type)) $type = isset($menuData['listno'])? 'M':'A';
                $map = isset($menuData['listno'])? ['listno'=>$menuData['listno']]:null;
                if($map) unset($menuData['listno']);
                if($type == 'A'){
                    $menuData['order'] = $order == 0? 1:$order;
                    $order += $order == 0? 2:1;
                    if($menu->insert($menuData)) $aCtt += 1;
                }
                elseif($type == 'M'){
                    $menuData['order'] = $lastOrder;    // 自动调整序号
                    if($menu->where($map)->update($menuData)) $mCtt += 1;
                    $lastOrder++;
                }
                elseif($type == 'D'){
                    if($menu->where($map)->delete()) $dCtt += 1;
                }
                //println($menuData);//break;
            }
            $retArry = [];
            if($aCtt > 0) $retArry[] = '新增数据共【'.$aCtt.'】条';
            if($mCtt > 0) $retArry[] = '修改数据共【'.$mCtt.'】条';
            if($dCtt > 0) $retArry[] = '删除数据共【'.$dCtt.'】条';
            $preTips = implode(',',$retArry);
            $ret = ($preTips? $preTips:'数据没有做任何更变').',提交数据总数【'.count($data).'】。';
        }
        if($ret) $this->success($ret);
        println($data,$mode);
    }
    public function ajax(){
        list($item,$data) = $this->_getAjaxData();
        $ret = '';
        switch($item){
            case 'get_menu_relymd':
                $lgAdmin = uLogic('Admin');
                $mds = explode(',',$lgAdmin->getLisaVar('module'));
                // return [$lgAdmin->getLisaVar(['pro_code'=>'code','parents_node'=>'parents_node']),$mds];                
                if(in_array($data['name'],$mds)){
                    $map = $lgAdmin->getLisaVar(['pro_code'=>'code','parents_node'=>'parents_node']);
                    // $map['node_code'] = $data['name'];
                    $pNo = $this->croDb('project_tree')->where(array_merge($map,['node_code'=>$data['name']]))->value('no');
                    $map['parents_node'] = $pNo;
                    $ret = $this->croDb('project_tree')->where($map)->field('node_name,url')->select();
                    // $ret = $this->croDb('project_tree')->where($lgAdmin->getLisaVar(['pro_code'=>'code','parents_node'=>'parents_node']))->select();
                    // debugOut($ret);
                    // print_r($ret);die;
                    // echo print_r($ret,true);die;
                    // return $ret;
                }
                else $ret = json(['error'=>1,'desc'=>'模块名非法']);
                break;
        }
        return $ret;
    }
}