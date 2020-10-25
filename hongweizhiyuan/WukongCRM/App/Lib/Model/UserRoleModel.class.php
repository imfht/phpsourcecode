<?php 
	class UserRoleModel extends Model{
        protected $_validate;
        public function _initialize(){
            $validate[0] = 'name';
            $validate[1] = 'require';
            $validate[2] = L('PLEASE FILL IN THE NAME OF JOB');
            $validate[3] = 0;
            $validate[4] = '';
            $validate[5] = 3;
            $this->_validate[] = $validate;
            
            $validate[0] = 'department_id';
            $validate[1] = 'require';
            $validate[2] = L('PLEASE SELECT THE JOB SECTOR');
            $validate[3] = 0;
            $validate[4] = '';
            $validate[5] = 3;
            $this->_validate[] = $validate;
        }
	}