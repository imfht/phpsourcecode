<?php
class RbacAction extends CommonAction {
    //用户列表
    public function index(){
      import('ORG.Util.Page'); 
      $count= M('user')->count();
      $Page= new Page($count,15);
      $limit=$Page->firstRow .',' .$Page->listRows;
      
      $this->user=D('User')->field('userpass',true)->limit($limit)->relation(true)->select();
      $this->page=$Page->show();
      $this->display();
    }
     //添加用户
    public function addUser(){
        $this->role=M('role')->where(array('status'=>1))->select(); 
        $this->display();
    }
    public function addUserHandle(){
        if(!IS_POST) halt("页面不存在!");
        if(trim($_POST[username])=='') $this->error('用户名不能为空！'); 
        
        $user=array(
            'username'=>I('username'),
            'userpass'=>I('userpass','','md5'),
            'logintime'=>time(),
            'loginip'=>get_client_ip()
        );
        $role=array();
        if($uid=M('user')->add($user)){
            foreach($_POST['role_id'] as $v){
                $role[]=array(
                    'role_id'=>$v,
                    'user_id'=>$uid
                );
            }
            M('role_user')->addAll($role);
            $this->success('添加成功',U('Rbac/addUser'));
        }
        else
            $this->error('添加失败');
    }
    //删除用户
    public function deleteUser(){
        M('role_user')->where('user_id='.I('uid'))->delete();
        M('user')->where('id='.I('uid'))->delete();
        $this->success("删除成功!");
    }
    //用户锁定
    public function lockUser(){
        $uid=I('uid');
        $date=array('id'=>$uid,
                    'lock'=>I('val')
                    );
        M('user')->save($date);
        $this->success("操作成功!");
    }
    //用户修改
    public function modifyUser(){
        $date=D('User')->where(array('id'=>I('uid')))->field('userpass,loginip,logintime',true)->relation(true)->select();
        $this->user=$date[0];
        $this->role=M('role')->where(array('status'=>1))->select();
        $this->display();
    }
    public function modifyUserHandle(){
        $user_s=array('id'=>I('uid'),
                      'username'=>I('username'),
                      'rename'=>I('rename'),
                      'sex'=>I('sex'),
                      'birthday'=>I('birthday'),
                      'score'=>I('score'),
                      'email'=>I('email'),
                      'mobile'=>I('mobile'),
                      'mobile1'=>I('mobile1'),
                      'mobile2'=>I('mobile2'),
                      'address'=>I('address'),
                      'other'=>I('other'),
                      'lock'=>I('lock'),
                      'ye'=>I('ye')
            );
        if(I('userpass')!=null)
        {
            $user_s=array_merge($user_s,array('userpass'=>I('userpass','','md5')));
        }
        
        $role_s=array();
        foreach($_POST[role_id] as $v){
            $role_s[]=array(
                'role_id'=>$v,
                'user_id'=>I('uid')
            );
        }
        
        M('user')->save($user_s);
        M('role_user')->where(array('user_id'=>I('uid')))->delete();
        M('role_user')->addAll($role_s);
        $this->success("修改成功,下次登陆时有效!",U('index'));
    }
//---------------------------------------------------------------------------------------------
    //角色列表
    public function role(){
        $this->role=M('role')->select(); 
        $this->display();
    }
    //添加角色
    public function addRole(){
        $this->display();
    }
    public function addRoleHandle(){
        if(!IS_POST) halt("页面不存在!");
        if(trim($_POST[name])=='') $this->error('名称不能为空！'); 
        if(trim($_POST[remark])=='') $this->error('描述不能为空！'); 

        if(M('role')->add($_POST)){
            $this->success("添加成功!");
        }
        else{
            $this->error('添加失败!');
        }
    }
    //删除
    public function deleteRole(){
        $rid=I('rid');
        M('role')->where(array('id'=>$rid))->delete();
        M('role_user')->where(array('role_id'=>$rid))->delete();
        M('access')->where(array('role_id'=>$rid))->delete();
        $this->success("删除成功!");
    }
    //修改
    public function modifyRole(){
        $data=M('role')->field('pid',true)->where(array('id'=>I('rid')))->select();
        $this->roles=$data[0];
        //p($this->roles);
        $this->display();
    }
    public function modifyRoleHandle(){
        if(!IS_POST) halt("页面不存在!");
        $rid=I('rid');
        $data=array('id'=>$rid,
                    'name'=>I('name'),
                    'remark'=>I('remark'),
                    'status'=>I('status')
        );
        M('role')->where(array('id'=>$rid))->save($data);
        $this->success("修成功,下次登陆生效！");
    }
    //配置角色权限
    public function access(){
        $this->rid=I('rid',0,'intval');
        $node=M('node')->order('sort')->select();
        $access=M('access')->where(array('role_id'=>$this->rid))->getField('node_id',true);
        $this->node=node_merge($node,$access);
        $this->display();
    }
    public function accessHandle(){
        if(!IS_POST) halt("页面不存在!");
        $rid=I('rid',0,'intval');
        $data=array();
        foreach($_POST[access] as $v){
            $tmp=explode('_',$v);
            $data[]=array(
                'role_id'=>$rid,
                'node_id'=>$tmp[0],
                'level'=>$tmp[1]
            );
        }
        $db=M('access');
        $db->where(array('role_id'=>$rid))->delete();
        if($db->addAll($data))
        {
            $this->success("修改成功,下次登陆生效！",U('Rbac/role'));
        }
    }
//-----------------------------------------------------------------------------------------
    //节点列表
    public function node(){
        $field=array('id','name','title','pid');
        $node=M('node')->field($field)->order('sort')->select();
        $this->node=node_merge($node);
        $this->display();
    }
    
    //添加节点
    public function addNode(){
        $this->pid=I('pid',0,'intval');
        $this->level=I('level',1,'intval');
        switch($this->level){
            case 1:
                $this->type='应用';
                break;
            case 2:
                $this->type='控制器';
                break;
            case 3:
                $this->type='方法';
                break;
        }
        $this->display();
    }
    
    public function addNodeHandle(){
        if(!IS_POST) halt("页面不存在!");
        if(trim($_POST[name])=='') $this->error('名称不能为空！'); 
        if(trim($_POST[title])=='') $this->error('描述不能为空！'); 

        if(M('node')->add($_POST)){
            $this->success("添加成功!");
        }
        else
            $this->error('添加失败!');
    }
    //修改节点
    public function modifyNode(){
        $nid=I('nid');
        if(trim($nid)=='') $this->error('操作错误！');
        
        $data=M('node')->field('id,name,title')->where(array('id'=>I('nid')))->select();
        $this->node=$data[0];
        $this->display();
    }
    public function modifyNodeHandle(){
        if(!IS_POST) halt("页面不存在!");
        if(trim($_POST[name])=='') $this->error('名称不能为空！'); 
        if(trim($_POST[title])=='') $this->error('描述不能为空！'); 
        
        $nid=I('nid');
        $data=array('id'=>$nid,
                    'name'=>I('name'),
                    'title'=>I('title'),
        );
        M('node')->where(array('id'=>$nid))->save($data);
        $this->success("修成功,下次登陆生效！");
    }
    //删除节点
    public function deleteNode(){
        $nid=I('nid');
        if(trim($nid)=='') $this->error('操作错误！');
        $count=M('node')->where('pid='.$nid)->count();
        if($count)  $this->error('请先删除子节点！');
        M('node')->where('id='.$nid)->delete();
        M('access')->where(array('node_id'=>$nid))->delete();
        //$this->success("删除成功!");
        $this->redirect('node');
    }
}
?>