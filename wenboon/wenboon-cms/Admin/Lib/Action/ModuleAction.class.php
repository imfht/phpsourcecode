<?php
    class ModuleAction extends CommonAction {
        public function index(){
            $module=M('module')->order('orders asc')->select();
            $this->module=$module;
            $this->display();
        }
        public function orders(){
            $db=M('module');
            foreach($_POST as $tag=>$orders){
                $db->where(array('tag'=>$tag))->setField('orders',$orders);
            }
            $this->redirect('index');
        }  
        public function add(){
            $this->display();
        }
        public function addHandle(){
            if(!I("title")) $this->error('名称不能为空！');
            if(!I("tag")) $this->error('标识不能为空！');
            if(I("tag")=='access') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='attachment') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='category') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='discuss') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='module') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='node') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='role') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='role_user') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='session') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='site') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='user') $this->error('系统保留字段,请重新定义标识！');
            if(I("tag")=='fields') $this->error('系统保留字段,请重新定义标识！');
            $count=M('module')->where(array('tag'=>I('tag')))->count();
            if($count!=0)
            {
                $this->error('标识已存在，请重新定义标识!');
            }
            $table_tag=C('DB_PREFIX').I('tag');
            
            $Model = M();
            $Model->query("CREATE TABLE `".$table_tag."` (`id` int(4) NOT NULL AUTO_INCREMENT,`cid` int(4) NULL,`orders` int(4) NULL,`cdate` int(15) NULL,`udate` int(15) NULL,`redate` int(15) NULL,`user` varchar(15) NULL,`access` int(1) NULL,`review` int(1) NULL,`account` int(9) NULL,`recount` int(9) NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
            $db=M('module');
            $data=$db->create();
            $db->add($data);
            $this->success('创建完成！');
        }
        public function delete(){
            $mtag=I('mtag');
            $db=M('module');
            if(M($mtag)->count()) $this->error('请先删除内容!');
            
            M('fields')->where(array('mtag'=>$mtag))->delete();
            $db2 = M();
            $db2->query("DROP TABLE IF EXISTS `".C('DB_PREFIX').$mtag."`;");
            $db->where(array('tag'=>$mtag))->delete();
            $this->success('已删除！');
        }
        public function modify(){
            $mtag=I('mtag');
            $this->module=M('module')->find($mtag);
            $this->display();
        }
        public function mHandle(){
            $db=M('module');
            $data=$db->create();
            $db->where(array('tag'=>I('mtag')))->save($data);
            $this->success('已修改！');
        }
//字段管理
        public function fields(){
            $mtag=I('mtag');
            $this->fields=M('fields')->where(array('mtag'=>$mtag))->order('orders asc')->select();
            $this->mtag=$mtag;
            $this->display();
        }
        public function forders(){
            $db=M('fields');
            foreach($_POST as $v=>$orders){
                $db->where(array('mtag'=>I('mtag'),'tag'=>$v))->setField('orders',$orders);
            }
            $this->redirect('fields',array('mtag'=>I('mtag')));
        }
        public function addfield(){
            $this->mtag=I('mtag');
            $this->category=M('category')->where(array('module'=>I('mtag')))->select();
            $this->display();
        }
        public function addfieldHandle(){
            if(!I("title")) $this->error('名称不能为空！');
            if(!I("tag")) $this->error('标识不能为空！');
            $count=M('fields')->where(array('tag'=>I('tag'),'mtag'=>I('mtag')))->count();
            if($count!=0) $this->error('标识已存在，请重新定义标识!');
            if(I('ftype')==5)
            {
                $count=M('fields')->where(array('mtag'=>I('mtag'),'ftype'=>I('ftype')))->count();
                if($count!=0) $this->error('该类型只能有一个!');
            }
            
            if(strtoupper(I('tag'))=='ID') $this->error('标识已存在，请重新定义标识!');
            if(strtoupper(I('tag'))=='CID') $this->error('标识已存在，请重新定义标识!');
            if(strtoupper(I('tag'))=='ORDERS') $this->error('标识已存在，请重新定义标识!');
            if(strtoupper(I('tag'))=='CDATE') $this->error('标识已存在，请重新定义标识!');
            if(strtoupper(I('tag'))=='UDATE') $this->error('标识已存在，请重新定义标识!');
            if(strtoupper(I('tag'))=='review') $this->error('标识已存在，请重新定义标识!');
            if(strtoupper(I('tag'))=='access') $this->error('标识已存在，请重新定义标识!');
            if(strtoupper(I('tag'))=='account') $this->error('标识已存在，请重新定义标识!');
            if(strtoupper(I('tag'))=='recount') $this->error('标识已存在，请重新定义标识!');
            if(strtoupper(I('tag'))=='user') $this->error('标识已存在，请重新定义标识!');
             
            $db=M("fields");
            //
            $data=$db->create();
                if($_POST['cid']){
                foreach($_POST['cid'] as $v){
                    $cid.=$v;
                }
                $data['cid']=$cid;
            }
            $db->add($data);
            
            $db1 = M();
            switch($data['ftype'])
            {
                case '1':
                    $db1->query('alter table `'.C('DB_PREFIX').$data['mtag'].'` add `'.$data['tag'].'` varchar(225);');
                    break;
                case '2':
                    $db1->query('alter table `'.C('DB_PREFIX').$data['mtag'].'` add `'.$data['tag'].'` mediumtext;');
                    break;
                case '3':
                    $db1->query('alter table `'.C('DB_PREFIX').$data['mtag'].'` add `'.$data['tag'].'` int(10);');
                    break;
                case '4':
                    $db1->query('alter table `'.C('DB_PREFIX').$data['mtag'].'` add `'.$data['tag'].'` varchar(225);');
                    break;
                case '5':
                    break;
                case '6':
                    $db1->query('alter table `'.C('DB_PREFIX').$data['mtag'].'` add `'.$data['tag'].'` varchar(225);');
                    break;
                case '7':
                    $db1->query('alter table `'.C('DB_PREFIX').$data['mtag'].'` add `'.$data['tag'].'` int(1);');
                    break;
                case '8':
                    $db1->query('alter table `'.C('DB_PREFIX').$data['mtag'].'` add `'.$data['tag'].'` text;');
                    break;
                case '9':
                    $db1->query('alter table `'.C('DB_PREFIX').$data['mtag'].'` add `'.$data['tag'].'` varchar(225);');
                    break;
                default:
                    break;
            }
            $this->success('添加成功！');
        }
        public function deletefieldHandle(){
            M('fields')->where(array('tag'=>I('tag'),'mtag'=>I('mtag')))->delete();
            $db1 = M();
            $db1->query("alter table `".C('DB_PREFIX').I('mtag')."` drop column `".I('tag')."`;");
            $this->success('已删除！');
        }
        public function modifyfield(){
            $mtag=I('mtag');
            $tag=I('tag');
            $this->field=M('fields')->where(array('tag'=>$tag,'mtag'=>$mtag))->find();
            $this->category=M('category')->where(array('module'=>I('mtag')))->select();
            $this->display();
        }
        public function modifyfieldHandle(){
            if(!I("title")) $this->error('名称不能为空！');
            if(!I("tag")) $this->error('标识不能为空！');
            
            $db=M("fields");
            $data=$db->create();
            unset($data['mtag']);
            unset($data['tag']);
            foreach($_POST['cid'] as $v){
                $cid.=$v;
            }
            $data['cid']=$cid;
            $db->where(array('tag'=>I('tag'),'mtag'=>I('mtag')))->save($data);

            $db1 = M();
            switch($data['ftype'])
            {
                case '1':
                    $db1->query('alter table `'.C('DB_PREFIX').I('mtag').'` change  `'.I('tag').'` `'.I('tag').'` varchar(225);');
                    break;
                case '2':
                    $db1->query('alter table `'.C('DB_PREFIX').I('mtag').'` change  `'.I('tag').'` `'.I('tag').'` mediumtext;');
                    break;
                case '3':
                    $db1->query('alter table `'.C('DB_PREFIX').I('mtag').'` change  `'.I('tag').'` `'.I('tag').'` int(10);');
                    break;
                case '4':
                    $db1->query('alter table `'.C('DB_PREFIX').I('mtag').'` change  `'.I('tag').'` `'.I('tag').'` varchar(225);');
                    break;
                case '5':
                    break;
                case '6':
                    $db1->query('alter table `'.C('DB_PREFIX').I('mtag').'` change  `'.I('tag').'` `'.I('tag').'` varchar(225);');
                    break;
                case '7':
                    $db1->query('alter table `'.C('DB_PREFIX').I('mtag').'` change  `'.I('tag').'` `'.I('tag').'` int(1);');
                    break;
                case '8':
                    $db1->query('alter table `'.C('DB_PREFIX').I('mtag').'` change  `'.I('tag').'` `'.I('tag').'` text;');
                    break;
                case '9':
                    $db1->query('alter table `'.C('DB_PREFIX').I('mtag').'` change `'.I('tag').'` varchar(225);');
                    break;
                default:
                    break;
            }
            $this->success('修改成功！');
        }
    }
?>