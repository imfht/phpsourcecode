<?php
namespace app\finance\controller;
use think\Controller;
use app\Server\Finance;
use hyang\Bootstrap;
class Fevent extends Controller
{
    // 初始化
    public function _initialize(){
        if(request()->isAjax()) return;
        $action = request()->action();
        $this->loadScript([
            'auth'=>'','title'=>'Conero-财务纪事','js'=>['Fevent/'.$action],'css'=>['Fevent/'.$action],'bootstrap'=>true
        ]);
    }
    public function index()
    {
        (new Bootstrap())->linkApp($this->view)
            ->tableGrid(['__viewTr__'=>'fevent'],[
                'table' => 'fevent',
                'cols'  => [
                    ['key'=>'name','link'=>'/Conero/finance/fevthome?listno={:list_no}'],
                    'open_dt','sider','close_dt','edittm','editor'],
                'edit'  => [
                    'link' => [
                        ['label'=>'修改','url'=>'/Conero/finance/fevent/edit?listno={:list_no}','attr'=>['class="edit_btn"']],
                        ['label'=>'删除','url'=>'/Conero/finance/fevent/del?listno={:list_no}','attr'=>['class="del_btn"']]
                    ]
                ],
            ],
            function($db){
                return $db->where('center_id=\''.uInfo('cid').'\'')->field('name,open_dt,sider,close_dt,edittm,editor,list_no')->select();
            }
        );
        return $this->fetch();
    }
    public function edit()
    {
        if(isset($_GET['listno'])){
            $data = $this->croDb('fevent')->where('list_no',$_GET['listno'])->find();
            $data['listno'] = '<input type="hidden" name="list_no" value="'.$data['list_no'].'">';
            $this->assign('fevet',$data);
        }
        return $this->fetch();
    }
    public function del()
    {
        if(isset($_GET['listno'])){
            $this->pushRptBack('fevent',['list_no'=>$_GET['listno']],true);
            $ret = $this->croDb('fevent')->where('list_no',$_GET['listno'])->delete();
            if($ret){$this->success('数据删除成功！');}
            else{$this->error('数据删除时出错！');}
        }
        $this->error('非法访问路径');
    }
    public function save()
    {
        $data = $_POST;
        if(isset($data['source'])){
            switch($data['source']){
                case 'fevent':
                    unset($data['source']);
                    $this->_feventSave($data);
                    break;
            }
        }
    }
    private function _feventSave($data){
        if(isset($data['list_no'])){
            $map = ['list_no'=>$data['list_no']];unset($data['list_no']);
            $old = $this->croDb('fevent')->where($map)->field('loginfo')->find();

            $loginfo = $old['loginfo'];
            $loginfo .= uInfo('name').' 于 '.sysdate().'修改了该财务纪事。<br>';
            $data['edittm'] = sysdate();$data['loginfo'] = $loginfo;$data['editor'] = uInfo('name');

            if($data['close_dt'] == '0000-00-00') unset($data['close_dt']);
            $fev = $this->croDb('fevent')->where($map)->update($data);
            if($fev){$this->success('数据修改成功！');}
            else{$this->error('[:_:] 数据修改失败');}
            die;
        }
        else{
            $data['center_id'] = uInfo('cid');
            $data['user_code'] = uInfo('code');
            $data['editor'] = uInfo('name');
            if(empty($data['close_dt'])) unset($data['close_dt']);
            $data['loginfo'] = uInfo('cname').' 于 '.sysdate().' 新建【财务纪事-'.$data['name'].'】<br>';
            $fev = $this->croDb('fevent')->insert($data);
            if($fev){$this->success('数据新增成功！');}
            else{$this->error('[:_:] 数据新增失败');}
            die;
        }
    }
}