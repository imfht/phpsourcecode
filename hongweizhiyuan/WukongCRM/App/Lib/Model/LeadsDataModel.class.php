<?php 
    class LeadsDataModel extends Model{
        protected $_validate = array();
        public function _initialize(){
            $fields = M('fields')->where('(model = \'\' or model = \'leads\') and is_validate=1 and is_main=0')->select();
			foreach($fields as $field){
				$validate = array();
				if($field['is_null']){
					$validate[0] = $field['field'];
					$validate[1] = 'require';
					$validate[2] = L('NOT NULL',array($field['name']));
					$validate[3] = 0;
					$validate[4] = '';
					$validate[5] = 3;
					$this->_validate[] = $validate;
				}
				
				
				$validate[0] = $field['field'];
				$validate[1] = '';
				$validate[2] = L('FORMAT ERROR',array($field['name']));
				$validate[3] = 0;
				$validate[4] = 'regex';
				$validate[5] = 3;
				switch ($field['form_type']){
					case 'email';
						$validate[1] = '/|^(\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)?$/';
						$this->_validate[] = $validate;
						break;
					case 'mobile';
						$validate[1] = '/|^1[358][0-9]{9}$/';
						$this->_validate[] = $validate;
						break;
					case 'phone';
						$validate[1] = '/|^((([0+]\d{2,3}-)?(0\d{2,3})-)?(\d{7,8})(-(\d{3,}))?)?$/';
						$this->_validate[] = $validate;
						break;
					case 'number';
						$validate[1] = '/|^\d+$/';
						$this->_validate[] = $validate;
						break;
				}
				
				if($field['is_unique']){
					$validate[0] = $field['field'];
					$validate[1] = '';
					$validate[2] = L('ALREADY EXISTS',array($field['name']));
					$validate[3] = 0;
					$validate[4] = 'unique';
					$validate[5] = 3;
					$this->_validate[] = $validate;
				}
				
			}
        }
    }