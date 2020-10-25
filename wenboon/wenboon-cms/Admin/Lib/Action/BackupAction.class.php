<?php
    class BackupAction extends CommonAction {
        Public function index(){
            $this->display();
        }
        public function handle(){ 
           $filename='../Uploads/Backup/'.date('Y-m-d-H-i-s',time());
           mkdir($filename);
           $dbname=C('DB_NAME');
           $db=M();
           $dd=$db->query('show table status from '.$dbname);
           foreach($dd as $vs){
                dump_table($vs[Name],$filename);
            }
          
           $this->success('备份完成！',U('index'));
        }
}