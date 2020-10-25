<?php
/*
    字段表xxx_fields
    xxx为主表名
*/
    class FieldsAction extends CommonAction {
        public function index(){
            $table=I('tb');
            if($table){
                $this->table=$table;
                $this->lists=M($table)->order('orders asc')->select();
                $this->display();
            }
        }
        public function forders(){
            $table=I('tb');
            $db=M($table);
            foreach($_POST as $id=>$orders){
                $db->where(array('id'=>$id))->setField('orders',$orders);
            }
            $this->redirect('index',array('tb'=>$table));
        }
         public function delete(){
            $table=I('tb');
            $pd=explode('_',$table);
            $thispd=M($table)->find(I('id'));
            if($thispd[sys]==1) $this->error('系统保留字段，不可删除！');
            M($table)->where(array('id'=>I('id')))->delete();
            ////////
            $db1 = M();
            $db1->query("alter table `".C('DB_PREFIX').$pd[0]."` drop column `".$thispd[tag]."`;");
            //$this->success('已删除！');
            $this->redirect('index',array('tb'=>$table));
        }
        public function add(){
            $this->table=I('tb');
            $this->display();
        }
        public function addhandle(){
            $table=I('tb');
            $pd=explode('_',$table);
            if(!I("title")) $this->error('名称不能为空！');
            if(!I("tag")) $this->error('标识不能为空！');
            //if(I("tag")=='id') $this->error('系统保留标识，请重新定义标识！');
            //if(I("tag")=='username') $this->error('系统保留标识，请重新定义标识！');
            //if(I("tag")=='userpass') $this->error('系统保留标识，请重新定义标识！');
            $count=M($table)->where(array('tag'=>I('tag')))->count();
            if($count!=0)
            {
                $this->error('标识已存在，请重新定义标识!');
            }
            $db=M($table);
            $data=$db->create();
            $db->add($data);
            //创建表
            $db1 = M();
            switch($data['ftype'])
            {
                case '1':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` add `'.$data['tag'].'` varchar(225);');
                    break;
                case '2':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` add `'.$data['tag'].'` mediumtext;');
                    break;
                case '3':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` add `'.$data['tag'].'` int(10);');
                    break;
                case '4':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` add `'.$data['tag'].'` varchar(225);');
                    break;
                case '6':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` add `'.$data['tag'].'` varchar(225);');
                    break;
                case '7':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` add `'.$data['tag'].'` int(1);');
                    break;
                case '8':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` add `'.$data['tag'].'` text;');
                    break;
                default:
                    break;
            }
            $this->success('添加成功！');
        }
         public function modify(){
            $table=I('tb');
            $thispd=M($table)->find(I('id'));
            if($thispd[sys]==1) $this->error('系统保留字段，不可修改！');
            
            $this->table=$table;
            $this->field=$thispd;
            $this->display();
        }
        public function modifyHandle(){
            $table=I('tb');
            $pd=explode('_',$table);
            if(!I("title")) $this->error('名称不能为空！');
            if(!I("tag")) $this->error('标识不能为空！');
            
            $db=M($table);
            $data=$db->create();
            unset($data['tag']);
            $db->where(array('id'=>I('id')))->save($data);
            
            $db1 = M();
            switch($data['ftype'])
            {
                case '1':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` change  `'.I('tag').'` `'.I('tag').'` varchar(225);');
                    break;
                case '2':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` change  `'.I('tag').'` `'.I('tag').'` mediumtext;');
                    break;
                case '3':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` change  `'.I('tag').'` `'.I('tag').'` int(10);');
                    break;
                case '4':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` change  `'.I('tag').'` `'.I('tag').'` varchar(225);');
                    break;
                case '5':
                    break;
                case '6':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` change  `'.I('tag').'` `'.I('tag').'` varchar(225);');
                    break;
                case '7':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` change  `'.I('tag').'` `'.I('tag').'` int(1);');
                    break;
                case '8':
                    $db1->query('alter table `'.C('DB_PREFIX').$pd[0].'` change  `'.I('tag').'` `'.I('tag').'` text;');
                    break;
                default:
                    break;
            }
            $this->success('修改成功！');
        }
    }