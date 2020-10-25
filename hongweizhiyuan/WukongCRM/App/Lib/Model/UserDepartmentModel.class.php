<?php 
	class UserDepartmentModel extends Model{
        protected $_validate;
        public function _initialize(){
            $validate[0] = 'name';
            $validate[1] = '';
            $validate[2] = L('THE DEPARTMENT ALREADY EXISTS');
            $validate[3] = 0;
            $validate[4] = 'unique';
            $validate[5] = 1;
            $this->_validate[] = $validate;
            
            $validate[0] = 'name';
            $validate[1] = 'require';
            $validate[2] = L('PLEASE FILL IN THE DEPARTMENT NAME');
            $validate[3] = 0;
            $validate[4] = '';
            $validate[5] = 3;
            $this->_validate[] = $validate;
            
            $validate[0] = 'parent_id';
            $validate[1] = 'require';
            $validate[2] = L('PLEASE SELECT THE HIGHER AUTHORITIES');
            $validate[3] = 0;
            $validate[4] = '';
            $validate[5] = 3;
            $this->_validate[] = $validate;
        }
	}