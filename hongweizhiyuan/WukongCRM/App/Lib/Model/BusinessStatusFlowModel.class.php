<?php 
	class BusinessStatusFlowModel extends Model{
		protected $_validate;
        public function _initialize(){
            $validate[0] = 'name';
            $validate[1] = 'require';
            $validate[2] = L('PLEASE FILL IN THE NAME OF THE STATE OF THE STREAM');
            $validate[3] = 0;
            $validate[4] = '';
            $validate[5] = 3;
            $this->_validate[] = $validate;
        }
	}