<?php
/**
 * 自动表单相关处理
 *
 * @author		嬴益虎 <Yingyh@whoneed.com>
 * @copyright	Copyright 2012
 * @package		com.auto_form
 */
	class CF{

		/**
		 * 递归获取所有分类
		 * @author	嬴益虎(whoneed@yeah.net)
		 * @param	int		$fid	父id
		 * @param	boolean	$isAll	是否返回所有(true:返回所有 false:只返回有效数据)
		 * @time	2012-07-25
		 * @return	array
		 */
		static function funArrGetColumn($fid = 0, $isAll = false){
			$status = '';
			if(!$isAll){
				$status = ' and status = 1 ';		
			}

			$sql	= "select * from whoneed_rbac_column where fid= {$fid} ".$status.' order by c_order desc';
			$conn	= Yii::app()->db;
			$arr	= array();
			$result	= $conn->createCommand($sql)->queryAll();

			if(!empty($result)){
				foreach($result as $k=>$v){
					$v['child']	= CF::funArrGetColumn($v['id'], $isAll);
					$arr[]		= $v;				
				}
			}

			return $arr;
		}	

		/**
		 * 取得全局静态数组值, 只在需要的时候获取
		 * @author	嬴益虎(whoneed@yeah.net)
		 * @param	string	$strKey		需要操作的数据key
		 * @param	int		$intT		需要返回指定的值,否则返回所有 默认为-1,返回所有
		 * @time	2011-10-11
		 * @return	array or string
		 */
		static function funGetData($strKey='', $intT = -1){

			$arrTemp = array();

			// 定义常用form
			if($strKey == 'field_type'){
				$arrTemp = array(
					1	=> '单行文本框',
					2	=> '多行文本框',
					3	=> '单选框',
					4	=> '复选框',
                    5	=> '下拉框',
                    14  => '查找带回',
					6	=> '富媒体编辑器',
					7	=> '单张图片上传',
					12	=> '单张图片上传(单独域名)',
					8	=> '密码框',
					9	=> '文件上传',
                    13  => '文件上传(单独域名)',
					10	=> '时间控件',
					11	=> '行内数据处理',
				);
			}

			// 返回
			if(empty($arrTemp)){
				MyFunction::funAlert('静态数组出错!');
				return array();
			}else{
				if($intT > -1){					
					return $arrTemp[$intT];
				}else{
					return $arrTemp;
				}
			}
		}

		/**
		 * 取得配制表单的html
		 * @author	嬴益虎(whoneed@yeah.net)
		 * @param	int		$intType	需要操作的表单id
		 * @param	array	$arrParm	针对此种类型的表单的参数
		 * @param	string	$strParm	针对此字段的特殊处理流程
		 * @param	boolean	$isORead	是否只读 true:yew false:no
		 * @param	boolean	$isQuery	针对query,不需要有初始化的值
		 * @return	string
		 */
		static function getFormHtml($intType = 1, $arrParm = array(), $strParm = '', $isORead = false, $isQuery = false){

			$arrFieldValue	= array();
			$intType		= intval($intType);
			$strReturn		= '';

			// 转换配制的参数
			if($strParm){
				$str = "\$arrFieldValue = ".$strParm.";";
				eval($str);
			}
			
			// 针对query的请求,不需要有初始化的值
			if($isQuery){
				$arrFieldValue['default_value'] = '';
			}

			// 如果本身有值，不需要有初始化的值
			if($arrParm['value'] || $arrParm['value'] !== '' || $arrParm['value'] === 0){
				$arrFieldValue['default_value'] = '';
			}

			// 单行文本框
			if($intType == 1){
				$size = intval($arrFieldValue['size']) ? intval($arrFieldValue['size']) : 30;
				if(!$arrParm['value'] && ($arrFieldValue['default_value'] || $arrFieldValue['default_value'] !== '')) 
					$arrParm['value'] = $arrFieldValue['default_value'];

				$strReturn = "<input type='text' size='{$size}' name='table{$arrParm['table_id']}[{$arrParm['name']}]' value='{$arrParm['value']}' />";

				if($isORead){
					$strReturn = $arrParm['value'];
				}
			}

			// 多行文本框
			else if($intType == 2){
				$cols = intval($arrFieldValue['cols']) ? intval($arrFieldValue['cols']) : 40;
				$rows = intval($arrFieldValue['rows']) ? intval($arrFieldValue['rows']) : 3;

				$strReturn = "<textarea cols='{$cols}' rows='{$rows}' name='table{$arrParm['table_id']}[{$arrParm['name']}]'>{$arrParm['value']}</textarea>";

				if($isORead){
					$strReturn = $arrParm['value'];
				}
			}
			
			// 单选
			else if($intType == 3){
				if($arrFieldValue){
					// 自定义函数处理
					$self_function = $arrFieldValue['function'];
					if($self_function){
						$str = "\$arr = ".$self_function.";";
						eval($str);
						$arrFieldValue['value'] = $arr;	
					}

					// 默认值处理
					$default_value = intval($arrFieldValue['default_value']);

					foreach($arrFieldValue['value'] as $k=>$v){
						$isCheck = '';
						if($arrParm['value'] == $k || (!$arrParm['value'] && $arrParm['value'] !== 0 && $default_value === $k)){ $isCheck = 'checked=checked';}
						$strReturn .= "<input type='radio' name='table{$arrParm['table_id']}[{$arrParm['name']}]' value='{$k}' {$isCheck} />{$v}";

						if($isORead && $isCheck){
							$strReturn = $v;
							break;
						}
					}
				}
			}
			
			// 复选框
			else if($intType == 4){
				if($arrFieldValue){
					// 自定义函数处理
					$self_function = $arrFieldValue['function'];
					if($self_function){
						$str = "\$arr = ".$self_function.";";
						eval($str);
						$arrFieldValue['value'] = $arr;	
					}

					// 默认值处理
					$default_value = intval($arrFieldValue['default_value']);
					
					// 只读:记录选中的值，多用于列表
					$strSelect = '';

					// 对传过来的值作处理
					if($arrParm['value']){
						$arrParm['value'] = explode(',', $arrParm['value']);
					}else{
						$arrParm['value'] = array();
					}

					foreach($arrFieldValue['value'] as $k=>$v){
						$isCheck = '';
						if(array_search($k, $arrParm['value']) !== false || (!$arrParm['value'] && $arrParm['value'] !== 0 && $default_value === $k)){ $isCheck = 'checked=true';}
						$strReturn .= "<input type='checkbox' name='table{$arrParm['table_id']}[{$arrParm['name']}][]' value='{$k}' {$isCheck} />{$v}";

						if($isORead && $isCheck){
							$strSelect .= $strSelect == '' ? $v : '/'.$v ;
						}
					}

					// 只读赋值
					if($isORead && $strSelect){
						$strReturn = $strSelect;
					}
				}
			}

			// 下拉框
			else if($intType == 5){
				if($arrFieldValue){
					// 自定义函数处理
					$self_function = $arrFieldValue['function'];
					if($self_function){
						$str = "\$arr = ".$self_function.";";
						eval($str);
						$arrFieldValue['value'] = $arr;	
					}

					// 默认值处理
					$default_value = intval($arrFieldValue['default_value']);

					// 只读:记录选中的值，多用于列表
					$strSelect = '';

					$strReturn .= "<select name='table{$arrParm['table_id']}[{$arrParm['name']}]' >";
					foreach($arrFieldValue['value'] as $k=>$v){
						$isCheck = '';
						if($arrParm['value'] == $k || (!$arrParm['value'] && $arrParm['value'] !== 0 && $default_value === $k)){ $isCheck = 'selected=\"selected\"';}

						if(strpos($v, '###') === false){
							$strReturn .= "<option value='{$k}' {$isCheck}>{$v}</option>";
						}else{
							$v = str_replace('###', '', $v);
							$strReturn .= $v;
						}

						if($isORead && $isCheck){
							$strSelect = $v;
						}
					}
					$strReturn .= "</select>";

					// 只读赋值
					if($isORead && $strSelect){
						$strReturn = $strSelect;
					}
				}
			}

			// 富媒体编辑器
			else if($intType == 6){
				$cols = intval($arrFieldValue['cols']) ? intval($arrFieldValue['cols']) : 110;
				$rows = intval($arrFieldValue['rows']) ? intval($arrFieldValue['rows']) : 10;

				// 插入上传定制功能
				$strReturn = "
					<script type='text/javascript'>
					$(document).ready(function(){ 
						$('#table{$arrParm['table_id']}_{$arrParm['name']}').xheditor({upImgUrl:'/uploadFile.php?immediate=1',upImgExt:'jpg,jpeg,gif,png'});
					});
					</script>
				";

				$strReturn .= "<textarea class='editor' cols='{$cols}' rows='{$rows}' name='table{$arrParm['table_id']}[{$arrParm['name']}]' id='table{$arrParm['table_id']}_{$arrParm['name']}'>{$arrParm['value']}</textarea>";

				if($isORead){
					$strReturn = $arrParm['value'];
				}
			}

			// 图片上传控件
			else if($intType == 7 || $intType == 12){
				$strReturn = "<input type='file' name='table{$arrParm['table_id']}_{$arrParm['name']}' size='30' class='textInput' />";
				
				// 针对单独域名的图片,拼凑图片路径
				if($intType == 12 && $arrParm['value']){
					$arrParm['value'] = Yii::app()->params['img_domain'].$arrParm['value'];
				}

				// 针对已经上传的图片显示
				if($arrParm['value'] && $arrParm['value'] != '&nbsp;'){
					$strReturn .= "<dl class='nowrap'><dt>&nbsp;</dt><dd><img src='{$arrParm['value']}' width=100 height=100 onload='javascript:DrawImage(this, 100, 100)' /></dd>";
				}

				if($isORead){
					if(!empty($arrParm['value']) && $arrParm['value'] != '&nbsp;'){
						$strReturn = "<img src='{$arrParm['value']}' width=100 height=50 /> ";
					}else{
						$strReturn = '&nbsp;';
					}
				}
			}

			// 密码框
			else if($intType == 8){
				$size = intval($arrFieldValue['size']) ? intval($arrFieldValue['size']) : 30;
				if(!$arrParm['value'] && $arrFieldValue['default_value']) $arrParm['value'] = $arrFieldValue['default_value'];

				$strReturn = "<input type='text' size='{$size}' name='table{$arrParm['table_id']}[{$arrParm['name']}]' value='' />";
			}

			// 文件上传
			else if($intType == 9 || $intType === 13){
                if($isQuery){
                    $strReturn = "<input type='text' size='15' name='table{$arrParm['table_id']}[{$arrParm['name']}]' value='' />";
                }else{
                    $strReturn = "<input type='file' name='table{$arrParm['table_id']}_{$arrParm['name']}' size='30' class='textInput' />";

                    // 针对单独域名的文件,拼凑路径
                    if($intType == 13 && $arrParm['value']){
                        $arrParm['value'] = Yii::app()->params['img_domain'].$arrParm['value'];
                    }

                    // 针对已经上传的文件显示
                    if($arrParm['value'] && $arrParm['value'] != '&nbsp;'){
                        $strReturn .= "<dl class='nowrap'><dt>&nbsp;</dt><dd><a href='{$arrParm['value']}' target='_blank'><font color=red>下载</font></a> {$arrParm['value']}</dd>";
                    }

                    if($isORead){
                        if(!empty($arrParm['value']) && $arrParm['value'] != '&nbsp;'){
                            $strReturn = "<a href='{$arrParm['value']}' target='_blank'><font color=red>下载</font></a> {$arrParm['value']}";
                        }else{
                            $strReturn = '&nbsp;';
                        }
                    }
                }
			}

			// 时间控件
			else if($intType == 10){
				// 大小
				$size = intval($arrFieldValue['size']) ? intval($arrFieldValue['size']) : 30;
                $style = '';
                if(isset($arrFieldValue['style'])) $style = $arrFieldValue['style'];
				
				// 启用默认值
				if(!$arrParm['value'] && $arrFieldValue['default_value']) $arrParm['value'] = $arrFieldValue['default_value'];

				// 时间如果没有设置,置空
				if($arrParm['value'] == '0000-00-00 00:00:00') $arrParm['value'] = '';
				
				// 自定义时间格式
				$strDateFormat = '';
				if($arrFieldValue['date_format']){
					$strDateFormat = "format='{$arrFieldValue['date_format']}'";
				}

				$strReturn = "<input type='text' {$strDateFormat} name='table{$arrParm['table_id']}[{$arrParm['name']}]' class='date' size='{$size}' value='{$arrParm['value']}' {$style} /><a class='inputDateButton' href='javascript:;'>选择</a>";

				if($isORead){
					$strReturn = $arrParm['value'];
				}
			}

			// 行数据处理
			// 比如：有些字段需要本行数据的某几个字段运算而得
			else if($intType == 11){
				if($arrFieldValue['list_show_function']){					
					// GameAdserving::funShowPayStatus($objData);					
					$rowData = $arrParm['objData'];
					$strDeal = "\$strReturn = {$arrFieldValue['list_show_function']};";
					eval($strDeal);
				}
			}

            // 查找带回
            else if($intType == 14){
                $size       = intval($arrFieldValue['size']) ? intval($arrFieldValue['size']) : 30; // size
                $district   = trim($arrFieldValue['mc_prefix']) ? trim($arrFieldValue['mc_prefix']) : 'district';
                $back_url   = trim($arrFieldValue['mc_back_url']);
                $data_by    = trim($arrFieldValue['mc_data_by']);
                
                // 自定义，取数据源
                if($data_by && !$isQuery){
                    $arrT = array();
                    $arrT = explode(',', $data_by);
                    $where    = $arrT['1'];
                    $select   = $arrT['2'];

                    $obj    = CF::getModelName($arrT['0']);
                    $obj    = new $obj();
                    $objData = $obj->find("{$where} = '{$arrParm['value']}'");
                    if($objData){
                        $arrParm['value'] = $objData->$select;
                        unset($arrT);
                        unset($obj);
                        unset($objData);
                    }
                }

                $strReturn = "<input type='text' name='{$district}.{$arrParm['name']}' value='{$arrParm['value']}' readonly='readonly' size='{$size}' />"; 
                $strReturn.= "<a class='btnLook' href='{$back_url}' lookupGroup='{$district}'>查找带回</a>";

                if($isORead){
                    $strReturn = $arrParm['value'];
                }
            }

			// 针对没有选择默认字段类型的赋值
			if(!$intType){
				$strReturn = $arrParm['value'];
			}
			return $strReturn;
		}
		
		// 根据自动表单中whoneed_tables表中的id,取得相应的实际表名
		static function getTableName($strTid = 0, $isModel = true){
			$strModelName = '';

			if($strTid){
				// 取出系统定义的表名
				$objData	= Whoneed_tables::model()->find("id = '{$strTid}'");

				if($objData){
					if($isModel){
						$strModelName = ucfirst($objData->physics_name);
					}else{
						$strModelName = $objData->physics_name;
					}
				}
			}

			return $strModelName;
		}

		// 根据自动表单中whoneed_tables表中的id,取得相应的实际model名
		static function getModelName($strTid = 0, $isModel = true){
			$strModelName = '';
			$strModelName = CF::getTableName($strTid, $isModel);
			return $strModelName;
		}
		
		// 用户自定义流程处理
		// 取得需要自动处理的字段
		static function doUserFlow($tid = 0, $strType = ''){
			$strFName = CF::getFName($tid);
            $cdb = new CDbCriteria();
            $cdb->condition = "table_id = $tid and flow_deal != ''";
            $cdb->order		= 'field_order desc, id asc';
            $objField = Whoneed_fields::model()->findAll($cdb);
            if($objField){
                foreach($objField as $v){									
                    if($v->flow_deal){
                        $arrDeal = array();
                        $str = "\$arrDeal = ".$v->flow_deal.";";
                        eval($str);

                        foreach($arrDeal as $deal){
                            // Flow::addDealField
                            $strDeal = "{$deal}({$tid}, {$v->physics_name});";
                            eval($strDeal);
                        }
                    }
                }
            }
		}

		// 取得当前表的form数据
		static function getFName($tid = 0){
			$strFName = '';
			$tid	  = intval($tid);
			if($tid)  $strFName = 'table'.$tid;
			return $strFName;
		}

		// 自动根据表id入库
		static function doAutoSave($tid = 0, $rid = 0){
			$intDid = 0;

			if($tid){
				// 取出实际的Model名称
				$objModel = CF::getModelName($tid);
				$objModel = new $objModel();

				// 记录rid存在，则更新
				// 记录rid存在，但是表中没有数据，则添加
				if($rid){ 
					$objModel = $objModel->find("id = {$rid}");
					if(!$objModel){
						$objModel = CF::getModelName($tid);
						$objModel = new $objModel();
						$objModel->id = $rid;
					}
				}

				// 当前需要处理的form数组key
				$strFName = CF::getFName($tid);

				// 循环赋值
				if($_POST[$strFName]){
					foreach($_POST[$strFName] as $k=>$v){
						$objModel->$k = $v;
					}
				}

				// 入库
				if($objModel->save()){
					$intDid = $objModel->id;	
				}
			}
			
			return $intDid;
		}

		// 取得需要自动处理的字段
		static function getNeedDealFields($tid = 0){
			$objField = null;

			if($tid){
				$cdb = new CDbCriteria();
				$cdb->condition = 'table_id=:id and is_submit = 1';
				$cdb->params	= array(':id' => $tid);
				$cdb->order		= 'field_order desc, id asc';

				$objField = Whoneed_fields::model()->findAll($cdb);
			}

			return $objField;
		}

		// 取得当前表的从表信息
		static function getSlaveTableId($tid = 0){
			$intSlaveTid = 0;

			if($tid){
				$cdb = new CDbCriteria();
				$cdb->condition = 'id=:id';
				$cdb->params	= array(':id' => $tid);

				$objDB = Whoneed_tables::model()->find($cdb);
				if($objDB){
					$intSlaveTid = $objDB->slave_tid;
				}
			}

			return $intSlaveTid;
		}

		// 取得当前表的从表信息
		static function getSlaveTable($tid = 0){
			$objSlave = null;

			if($tid){
				$cdb = new CDbCriteria();
				$cdb->condition = 'id=:id';
				$cdb->params	= array(':id' => $tid);

				$objDB = Whoneed_tables::model()->find($cdb);
				if($objDB){
					$objSlave = CF::getNeedDealFields($objDB->slave_tid);
				}
			}

			return $objSlave;
		}

		// 取得需要自动处理的字段
		static function getNeedListFields($tid = 0){
			$objField = null;

			if($tid){
				$cdb = new CDbCriteria();
				$cdb->condition = 'table_id=:id and is_list = 1';
				$cdb->params	= array(':id' => $tid);
				$cdb->order		= 'field_order desc, id asc';

				$objField = Whoneed_fields::model()->findAll($cdb);
			}

			return $objField;
		}

		// 系统模型列表
		static function getSystemModelList(){
			$objDB = Whoneed_model::model()->findAll();
			return $objDB;
		}

		// 得到模型表的内容
		static function getSystemModel($id = 0){
			$id = intval($id);
			$objDB = Whoneed_model::model()->find("id = {$id}");
			return $objDB;			
		}

		// 得到指定id的栏目信息
		static function getColumnInfo($id, $select = '*'){
			$sql	= "select {$select} from whoneed_rbac_column where id = {$id}";
			$conn	= Yii::app()->db;
			$result	= $conn->createCommand($sql)->queryRow();
			return $result;		
		}

		// 取处自动表单的自定义操作
		static function getAutoFormOpe($tid = 0, $opeKey = 'list'){
			$tid   = intval($tid);
			$arrR  = array();

			$objDB = Whoneed_tables::model()->find("id = {$tid}");
			if($objDB && $objDB->operation){
				$arrOpeList = array();
				$str = "\$arrOpeList = ".$objDB->operation.";";
				eval($str);

				if($arrOpeList && $arrOpeList[$opeKey]){
					$arrR = $arrOpeList[$opeKey];	
				}
			}

			return $arrR;		
		}

		// 取得需要查询的字段
		static function getNeedQueryFields($tid = 0){
			$objField = null;

			if($tid){
				$cdb = new CDbCriteria();
				$cdb->condition = 'table_id=:id and is_query = 1';
				$cdb->params	= array(':id' => $tid);
				$cdb->order		= 'field_order desc, id asc';

				$objField = Whoneed_fields::model()->findAll($cdb);
			}

			return $objField;
		}

		// 获取角色所对应的相应栏目
		static function getRoleColumn($role_id = ''){
			$arrR = array();

			if($role_id){
				$sql	= "select column_id from whoneed_rbac_role_column c right join whoneed_rbac_role r on r.id in ({$role_id}) and r.status = 1 and r.id = c.role_id";
				$conn	= Yii::app()->db;
				$result	= $conn->createCommand($sql)->queryAll();
				if($result){
					foreach($result as $v){
						$arrR[] = $v['column_id'];
					}
				}
			}

			return $arrR;		
		}
		
		// 获取内容模型
		static function getContentModelList(){
			$arrR = array();
			$arrR[] = '请选择模型';

			$objModelList = CF::getSystemModelList();
			if($objModelList){
				foreach($objModelList as $model){
					$arrR[$model->id] = $model->model_name;
				}
			}
			
			return $arrR;
		}
		
		static function getTypeModelUrl($url = ''){
			$strR = '';
			
			if($url){
				$url = explode(':', $url);
				$url = explode(',', $url['1']);
				$tid = intval($url['0']);
				$id  = intval($url['1']);
				
				// 取出实际的Model名称
				if($tid && $id){
					$objModel = CF::getModelName($tid);
					$objModel = new $objModel();
					$objDB = $objModel->find("id = {$id}");
					if($objDB){
						if($objDB->url){	// 直接指定URL
							$strR = $objDB->url;
						}else if($objDB->model_id){	// 读取对应模型中的地址
							$strR = CF::getSystemModel($objDB->model_id)->model_url;
						}					
					}
				}
			}

			return $strR;
		}

		// 自动表单 -- 输出列表
		static function echoAutoFormList($tid, $objField, $data, $arrAutoFormOpe, $strView = '', $strSFlag = ''){
			echo "<tr align='center' target='did' rel='{$data->id}'>";
			echo "<td><input name='ids' value='{$data->id}' type='checkbox'></td>";
			
			// 控制字段显示风格
			$arrAFOpe	= array();
			$arrAFOpe	= CF::getAutoFormOpe($tid, 'show_style');

			foreach($objField as $v){
				$strT = '&nbsp;';
				$k = $v->physics_name;
				if($data->$k || $data->$k !== '' || $data->$k === 0) $strT = $data->$k;

				//自动替换相关表单数据
				// 查看字段是否需要按层级显示
				if($arrAFOpe && $arrAFOpe['sub_type_data'] && array_search($v->physics_name, $arrAFOpe['sub_type_data']) !== false){	
					$strT	= $strSFlag.$strT;	// 加层级显示前导字符
				}

				// 字段配制
				$arrParm = array(
					'name'		=> $v->physics_name,
					'value'		=> $strT,
					'table_id'	=> $v->table_id,
					'objData'	=> $data
				);

				// 显示style
				$strShowType = '';
				if($arrAFOpe && $arrAFOpe['style'] && $arrAFOpe['style'][$v->physics_name]){	
					$strShowType	= $arrAFOpe['style'][$v->physics_name];
				}
				echo "<td {$strShowType}>";
				echo CF::getFormHtml($v->field_type, $arrParm, $v->field_type_value, 1);
				echo '</td>';
			}
			
			// 无操作功能
			if($strView && $strView == 'no_ope'){
			}else{
				echo '<td>';
				if($strView && $strView == 'no_view'){
				}else{
					echo "<a href='/admin/auto_form/auto_edit/tid/{$tid}/id/{$data->id}' target='dialog' mask='true' width='800' height='500'>查看</a>";
				}

				// 自定义的列表操作
				if($arrAutoFormOpe){
					foreach($arrAutoFormOpe as $k => $v){
						$strT = $k;
						$strU = $v;
						
						//====================================================
						// 前置替换
						// 替换父类id
						$strU = str_replace('{$fid}', $tid, $strU);	// 历史原因，保留向前兼容
						$strU = str_replace('{$tid}', $tid, $strU);	// 对应tid,比较好理解

						// 替换子类id
						$strU = str_replace('{$id}', $data->id, $strU);
						
						// 特殊的子类数据url处理
						if(strpos($strU, 'URL:') !== false){
							$strU = CF::getTypeModelUrl($strU);
						}

						// 后置替换
						// 替换父类id
						$strU = str_replace('{$fid}', $tid, $strU);	// 历史原因，保留向前兼容
						$strU = str_replace('{$tid}', $tid, $strU);	// 对应tid,比较好理解

						// 替换子类id
						$strU = str_replace('{$id}', $data->id, $strU);

						// 穿透参数传递
						$arrAFOpe	= array();
						$arrAFOpe	= CF::getAutoFormOpe($tid, 'pass_param');
						if($arrAFOpe && is_array($arrAFOpe)){						
							foreach($arrAFOpe as $pass_param => $type){
								$paramValue = trim($_GET[$pass_param]);
								if($type == 'int'){
									$paramValue = intval($paramValue);
								}

								$strU = str_replace('{$'.$pass_param.'}', $paramValue, $strU);
							}
						}
						//====================================================
						if($strU)
                            if($strT === 'SELF'){
                                echo $strU;
                            }else if($strT === 'MC_SELF'){
                                // 查找带回
                                $key  = '';
                                $val  = '';
                                $arrT = array();
                                $arrT = explode(':', $strU);
                                $key  = $arrT['0'];

                                $arrV = array();
                                $arrV = explode(',', $arrT['1']);

                                if($key && $arrV){
                                    foreach($arrV as $vv){
                                        $val .= $val == '' ? $data->$vv : ','.$data->$vv ;
                                    }

                                    $strUU = "{{$key}:'{$val}'}";
                                    echo "&nbsp;/&nbsp;<a href=\"javascript:$.bringBack({$strUU})\" title='查找带回'>选择</a>";
                                }
                            }else{
							    echo "&nbsp;/&nbsp;<a href='{$strU}' target='navTab' rel='column_list_sub_{$tid}'>{$strT}</a>";
                            }
                        else
							echo '&nbsp;/&nbsp;'.$strT;
					}
				}
			}

			echo '</td></tr>';		
		}

		// 递归输出子类列表
		static function echoSubAutoFormList($tid, $objField, $objSubData, $arrAutoFormOpe, $strView = '', $strFSpace = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $strBSpace = '&nbsp;&nbsp;&nbsp;&nbsp;'){

			$isSubFlag  = true;		// 子类执行标志
			$strFlag	= '├';		// 子类分隔标志			

			while($isSubFlag){
				if($objSubData->sub_type_data){
					$objSubData = $objSubData->sub_type_data;
					foreach($objSubData as $subTypeData){
						// 输出数据
						CF::echoAutoFormList($tid, $objField, $subTypeData, $arrAutoFormOpe, $strView, $strFSpace.$strFlag.$strBSpace);
						
						// 递归子类
						CF::echoSubAutoFormList($tid, $objField, $subTypeData, $arrAutoFormOpe, $strView, $strFSpace.$strFSpace);
					}
				}else{
					$isSubFlag = false;
				}
			}		
		}

        // 解析扩展数据 string => array
        static function funGetExtraData($data, $strType = 'string2array')
        {
            $retData = array();

            if($data)
            {
                if($strType == 'string2array'){
				    $str = "\$retData = ".$data.";";
                    eval($str);
                }else if($strType == 'array2string'){
                    $retData = var_export($data, true);
                }
            }

            return $retData;
        }

        // 自动查询数据，反解析成对应存在的key
        static function funGetTheWhereKey($arrData)
        {
            $arrR = array();
    
            if($arrData){
                foreach($arrData as $data){
                    $arrT = array();
                    $arrT = explode(' ', $data);
                    $data = $arrT['0'];
                    $arrT = explode('=', $data);
                    $arrR[$arrT['0']] = 1;            
                }
            }
            
            return $arrR;
        }
	}
?>
