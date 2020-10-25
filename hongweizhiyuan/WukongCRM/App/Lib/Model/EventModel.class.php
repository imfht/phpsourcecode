<?php 
    class EventModel extends Model{
		protected $_auto = array(
			array('create_date', 'time', 1, 'function'),
		);
		protected $_validate;
        public function _initialize(){
            $validate[0] = 'subject';
            $validate[1] = '';
            $validate[2] = L('THE THEME ALREADY EXISTS');
            $validate[3] = 0;
            $validate[4] = 'unique';
            $validate[5] = 1;
            $this->_validate[] = $validate;
        }
		
    }