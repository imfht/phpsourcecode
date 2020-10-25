<?php 
	class ControlModuleModel extends Model{
        protected $_validate;
        public function _initialize(){
            $validate[0] = 'name';
            $validate[1] = '';
            $validate[2] = L('OPERATION ALREADY EXISTS');
            $validate[3] = 0;
            $validate[4] = 'unique';
            $validate[5] = 1;
            $this->_validate[] = $validate;
            
            $validate[0] = 'name';
            $validate[1] = 'require';
            $validate[2] = L('PLEASE FILL IN THE NAME OF THE OPERATION');
            $validate[3] = 0;
            $validate[4] = '';
            $validate[5] = 3;
            $this->_validate[] = $validate;
        }
	}