<?php
    class UserAction extends CommonAction {
        public function index(){
            $where=array();
            if(I('select')=='select'){
                $where[I('select_tag')]=I('select_val');
            }
            import('ORG.Util.Page'); 
            $count= M('member')->where($where)->count();
            $Page= new Page($count,15);
            $limit=$Page->firstRow .',' .$Page->listRows;
      
            $this->fields=M('member_fields')->where('ls=0')->order('orders asc')->select();
            $this->select=M('member_fields')->order('orders asc')->select();
            $this->lists=M('member')->limit($limit)->where($where)->select();
            $this->page=$Page->show();
            $this->display();
        }
        public function add(){
            $this->fields=M('member_fields')->where('sys!=1')->order('orders asc')->select();
            $this->display();
        }
        public function addhandle(){
            $username=str_replace(' ','',I('username'));
            $userpass=str_replace(' ','',I('userpass'));
            if($username=='') $this->error('用户名不能为空！');
            if($userpass=='') $this->error('密码不能为空！');
            $count=M('member')->where(array('username'=>$username))->count();
            if($count) $this->error('用户名已存在！');
            
            $userpass=md5($userpass);
            $db=M('member');
            $data=$db->create();
            $data['username']=$username;
            $data['userpass']=$userpass;
            $db->add($data);
            $this->success('添加会员成功！');
        }
        public function modify(){
            $this->fields=M('member_fields')->where('sys!=1')->order('orders asc')->select();
            $this->body=M('member')->find(I('id'));
            $this->display();
        }
        public function modifyhandle(){
            $username=str_replace(' ','',I('username'));
            $userpass=str_replace(' ','',I('userpass'));
            if($username=='') $this->error('用户名不能为空！');
            $where=array();
            $where['username']=array('eq',$username);
            $where['id']=array('neq',I('id'));
            $count=M('member')->where($where)->count();
            if($count) $this->error('用户名已存在！');
            
            $db=M('member');
            $data=$db->create();
            $data['username']=$username;
            if($userpass==''){
                unset($data['userpass']);
            }
            else{
                $data['userpass']=md5($userpass);
            }
            
            $db->where(array('id'=>I('id')))->save($data);
            $this->success('修改会员成功！');
        }
        public function delete(){
            $id=I('id');
            M('member')->where(array('id'=>$id))->delete();
            $this->success('删除成功！');
        }
        
        //fields管理
        public function fields(){
            $this->lists=M('member_fields')->order('orders asc')->select();
            $this->display();
        }
        public function forders(){
            $db=M('member_fields');
            foreach($_POST as $id=>$orders){
                $db->where(array('id'=>$id))->setField('orders',$orders);
            }
            $this->redirect('fields');
        }
        public function addfield(){
            $this->display();
        }
        public function addfieldHandle(){
            if(!I("title")) $this->error('名称不能为空！');
            if(!I("tag")) $this->error('标识不能为空！');
            if(I("tag")=='id') $this->error('系统保留标识，请重新定义标识！');
            if(I("tag")=='username') $this->error('系统保留标识，请重新定义标识！');
            if(I("tag")=='userpass') $this->error('系统保留标识，请重新定义标识！');
            $count=M('member_fields')->where(array('tag'=>I('tag')))->count();
            if($count!=0)
            {
                $this->error('标识已存在，请重新定义标识!');
            }
            $db=M('member_fields');
            $data=$db->create();
            $db->add($data);
            //创建表
            $db1 = M();
            switch($data['ftype'])
            {
                case '1':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` add `'.$data['tag'].'` varchar(225);');
                    break;
                case '2':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` add `'.$data['tag'].'` mediumtext;');
                    break;
                case '3':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` add `'.$data['tag'].'` int(10);');
                    break;
                case '4':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` add `'.$data['tag'].'` varchar(225);');
                    break;
                case '6':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` add `'.$data['tag'].'` varchar(225);');
                    break;
                case '7':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` add `'.$data['tag'].'` int(1);');
                    break;
                case '8':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` add `'.$data['tag'].'` text;');
                    break;
                default:
                    break;
            }
            $this->success('添加成功！');
        }
        public function deletefieldHandle(){
            $tag=I('tag');
            if($tag=='username'||$tag=='userpass'||$tag=='id'||$tag=='sex')  $this->error('系统保留字段，不可删除！');
            M('member_fields')->where(array('id'=>I('id')))->delete();
            $db1 = M();
            $db1->query("alter table `".C('DB_PREFIX')."member` drop column `".I('tag')."`;");
            $this->success('已删除！');
        }
        public function modifyfield(){
            $tag=I('tag');
            if($tag=='username'||$tag=='userpass'||$tag=='id'||$tag=='sex')  $this->error('系统保留字段，不可修改！');
            $this->field=M('member_fields')->find(I('id'));
            $this->display();
        }
        public function modifyfieldHandle(){
            if(!I("title")) $this->error('名称不能为空！');
            if(!I("tag")) $this->error('标识不能为空！');
            
            $db=M("member_fields");
            $data=$db->create();
            unset($data['tag']);
            $db->where(array('id'=>I('id')))->save($data);
            
            $db1 = M();
            switch($data['ftype'])
            {
                case '1':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` change  `'.I('tag').'` `'.I('tag').'` varchar(225);');
                    break;
                case '2':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` change  `'.I('tag').'` `'.I('tag').'` mediumtext;');
                    break;
                case '3':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` change  `'.I('tag').'` `'.I('tag').'` int(10);');
                    break;
                case '4':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` change  `'.I('tag').'` `'.I('tag').'` varchar(225);');
                    break;
                case '5':
                    break;
                case '6':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` change  `'.I('tag').'` `'.I('tag').'` varchar(225);');
                    break;
                case '7':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` change  `'.I('tag').'` `'.I('tag').'` int(1);');
                    break;
                case '8':
                    $db1->query('alter table `'.C('DB_PREFIX').'member` change  `'.I('tag').'` `'.I('tag').'` text;');
                    break;
                default:
                    break;
            }
            $this->success('修改成功！');
        }
    }