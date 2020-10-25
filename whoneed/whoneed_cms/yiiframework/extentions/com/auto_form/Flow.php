<?php
/**
 * 系统级
 * 输入输出流管理
 *
 * @author		嬴益虎 <Yingyh@whoneed.com>
 * @copyright	Copyright 2012
 * @package		com.auto_form
 */
	class Flow{

		// ============================= 输入流
		// 获取角色信息
		public static function getRoleInfo(){
			$arrR = array();

			$objDB = Whoneed_rbac_role::model()->findAll('status = 1');
			if($objDB){
				foreach($objDB as $role){
					$arrR[$role->id] = $role->role_name;
				}
			}

			return $arrR;
		}

		// ============================= 输出流
		// 通用checkbox处理
		public static function flowCheckbox($tid = 0, $field = ''){			
			$strFName = CF::getFName($tid);
			if($_POST[$strFName][$field]){
				$_POST[$strFName][$field] = implode(',', $_POST[$strFName][$field]);
			}else{
				$_POST[$strFName][$field] = '';
			}
		}

		// 添加处理范围之外的字段进入处理范围内
		public static function addDealField($tid = 0, $field = ''){
			$strFName = CF::getFName($tid);

			if($_POST[$field])
				$_POST[$strFName][$field] = $_POST[$field];		
		}

		// 添加处理范围之外的字段进入处理范围内
		// 此处特指 type
		public static function addDealFieldType($tid = 0, $field = ''){
			$strFName = CF::getFName($tid);

			if($_POST[$field])
				$_POST[$strFName][$field] = intval($_POST[$field]) > 0 ? intval($_POST[$field]) : 0;		
		}

		// 上传图片
		public static function uploadImg($tid = 0, $field = ''){
			$strFName		= CF::getFName($tid);
			$strUploadFile	= "{$strFName}_{$field}";

			$strFileName	= MyUploadFile::uploadImg($strUploadFile);
			if($strFileName){
				$_POST[$strFName][$field] = $strFileName;
			}
		}

		// 上传图片 有单独域名
		public static function uploadImgCdn($tid = 0, $field = ''){
			$strFName		= CF::getFName($tid);
			$strUploadFile	= "{$strFName}_{$field}";

			$strFileName	= MyUploadFile::uploadImgCdn($strUploadFile);
			if($strFileName){
				$_POST[$strFName][$field] = $strFileName;
			}
		}
		
		// 上传文件
		// return 文件名称,文件类型,文件大小
		public static function uploadFile($tid = 0, $field = ''){
			$strFName		= CF::getFName($tid);
			$strUploadFile	= "{$strFName}_{$field}";

			$arrFile	= MyUploadFile::uploadFile($strUploadFile);
			if($arrFile){
				$_POST[$strFName][$field] = $arrFile['file_name'];
			}
		}

        // 上传apk文件
        public static function uploadApkFile($tid = 0, $field = ''){
            $strFName		= CF::getFName($tid);
			$strUploadFile	= "{$strFName}_{$field}";

			$strFileName	= MyUploadFile::uploadApkFile($strUploadFile);
			if($strFileName){
				$_POST[$strFName][$field] = $strFileName;
			}
        }

		// 上传文件
		// return 文件名称,文件类型,文件大小
		public static function uploadFileByTypeSize($tid = 0, $field = ''){
			$strFName		= CF::getFName($tid);
			$strUploadFile	= "{$strFName}_{$field}";

			$arrFile	= MyUploadFile::uploadFile($strUploadFile);
			if($arrFile){
				$_POST[$strFName][$field] = $arrFile['file_name'];
				$_POST[$strFName]['file_type'] = $arrFile['file_type'];
				$_POST[$strFName]['file_size'] = $arrFile['file_size'];
			}
		}

		// 密码处理
		public static function dealPass($tid = 0, $field = ''){
			$strFName = CF::getFName($tid);
			if($_POST[$strFName][$field]){
				$_POST[$strFName][$field] = MyFunction::funHashPassword($_POST[$strFName][$field], true);
			}else{
				// 没有修改密码，unset
				// 否则密码会被置为空
				unset($_POST[$strFName][$field]);
			}
		}

		// 当前登录用户
		// 添加处理范围之外的字段进入处理范围内
		public static function addDealUserId($tid = 0, $field = ''){
			$strFName = CF::getFName($tid);

			$_POST[$strFName][$field] = Yii::app()->user->getState('admin_id');	
		}

		// 更新时间
		public static function updateTime($tid = 0, $field = ''){
			$strFName = CF::getFName($tid);

			if(!$_POST[$field])
				$_POST[$strFName][$field] = date('Y-m-d H:i:s', time());		
        }

        // 清除相应的前缀，恢复input name 命名
        public static function replaceMCName($tid = 0, $field = ''){
            $strFName = CF::getFName($tid);

            foreach($_POST as $k => $v){
                if(strpos($k, '_') !== false){
                    $arrR = array();
                    $arrR = explode('_', $k);

                    if($arrR['1'] === $field){
                        $_POST[$strFName][$field] = $v;
                        unset($_POST[$k]);
                        break;
                    }
                }
            }
        }

        public static function replaceMCNameGetFirst($tid = 0, $field = ''){
            $strFName = CF::getFName($tid);

            foreach($_POST as $k => $v){
                if(strpos($k, '_') !== false){
                    $arrR = array();
                    $arrR = explode('_', $k);

                    if($arrR['1'] === $field){
                        $arrT = array();
                        $arrT = explode(',', $v);
                        $_POST[$strFName][$field] = $arrT['0'];
                        unset($_POST[$k]);
                        unset($arrT);
                        break;
                    }
                }
            }
        }        
	}
?>
