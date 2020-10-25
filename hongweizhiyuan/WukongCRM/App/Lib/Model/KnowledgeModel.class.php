<?php 
    class KnowledgeModel extends Model{
        protected $_validate;
        public function _initialize(){
            $validate[0] = 'title';
            $validate[1] = '';
            $validate[2] = L('THIS TITLE NAME ALREADY EXISTS');
            $validate[3] = 0;
            $validate[4] = 'unique';
            $validate[5] = 1;
            $this->_validate[] = $validate;
            
            $validate[0] = 'title';
            $validate[1] = 'require';
            $validate[2] = L('TITLE CAN NOT NULL');
            $validate[3] = 0;
            $validate[4] = '';
            $validate[5] = 3;
            $this->_validate[] = $validate;
            
            $validate[0] = 'category_id';
            $validate[1] = 'require';
            $validate[2] = L('PLEASE SELECT A CATEGORY');
            $validate[3] = 0;
            $validate[4] = '';
            $validate[5] = 3;
            $this->_validate[] = $validate;
        }
	}