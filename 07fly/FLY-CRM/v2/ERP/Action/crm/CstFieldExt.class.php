<?php
/*
 *
 * erp.CstFieldExt  客户信息表单字段扩展管理   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */
class CstFieldExt extends Action {
	private $cacheDir = ''; //缓存目录
	public function __construct() {
		_instance( 'Action/sysmanage/Auth' );
		$this->field = $this->L( 'sysmanage/FieldExt' );
	}

	public function cst_field_ext() {
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST( "pageNum" ); //第几页
		$pageSize = $this->_REQUEST( "pageSize" ); //每页多少条
		$pageNum = empty( $pageNum ) ? 1 : $pageNum;
		$pageSize = empty( $pageSize ) ? $GLOBALS[ "pageSize" ] : $pageSize;

		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name = $this->_REQUEST( "name" );
		$main_table = $this->_REQUEST( "main_table" );
		$ext_table = $this->_REQUEST( "ext_table" );
		$where_str = " d.field_ext_id>'0' ";
		if ( !empty( $name ) ) {
			$where_str .= " and d.show_name like '%$name%'";
		}
		if ( !empty( $main_table ) ) {
			$where_str .= " and d.main_table='$main_table'";
		}
		if ( !empty( $ext_table ) ) {
			$where_str .= " and d.ext_table='$ext_table'";
		}
		$countSql = "select * from cst_field_ext as d where $where_str";
		$totalCount = $this->C( $this->cacheDir )->countRecords( $countSql ); //计算记录数
		$beginRecord = ( $pageNum - 1 ) * $pageSize; //计算开始行数
		$sql = "select d.* from cst_field_ext as d where $where_str order by d.sort asc limit $beginRecord,$pageSize";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		foreach ( $list as $key => $row ) {
			$list[ $key ][ 'field_type_arr' ] = $this->cst_field_ext_type( $row[ 'field_type' ] );
		}
		$assignArray = array( 'list' => $list, "pageSize" => $pageSize, "totalCount" => $totalCount, "pageNum" => $pageNum );
		return $assignArray;
	}
	public function cst_field_ext_json() {
		$assArr = $this->cst_field_ext();
		echo json_encode( $assArr );
	}
	//返回一个二维数组
	public function cst_field_ext_list($ext_table=null) {
		$list 	= array();
		$where 	= ( !empty( $ext_table ) ) ? " where ext_table='$ext_table'" : "";
		$sql 	= "select * from cst_field_ext $where order by sort asc";
		$list 	= $this->C( $this->cacheDir )->findAll( $sql );
		return $list;
	}
	//浏览
	public function cst_field_ext_show() {
		$smarty = $this->setSmarty();
		$smarty->display( 'crm/cst_field_ext_show.html' );
	}
	
	//添加
	public function cst_field_ext_add() {
		if ( empty( $_POST ) ) {
			$ext_table = $this->_REQUEST( "ext_table" );
			$main_table = $this->_REQUEST( "main_table" );
			$type = $this->cst_field_ext_type();
			$smarty = $this->setSmarty();
			$smarty->assign( array( "type" => $type,"ext_table" => $ext_table, "main_table" => $main_table ) );
			$smarty->display( 'crm/cst_field_ext_add.html' );
		} else {
			$ext_table = $this->_REQUEST( "ext_table" );
			$show_name = $this->_REQUEST( "show_name" );
			$field_name = $this->_REQUEST( "field_name" );
			$field_type = $this->_REQUEST( "field_type" );
			$default = $this->_REQUEST( "default" );
			$maxlength = $this->_REQUEST( "maxlength" );
			//执行数据库操作
			$rtn = $this->field->add_field( $ext_table, $field_name, $field_type, $maxlength, $default, $show_name );
			if ( $rtn[ 'statusCode' ] == '200' ) {
				$into_data = array(
					'main_table' => $this->_REQUEST( "main_table" ),
					'ext_table' => $this->_REQUEST( "ext_table" ),
					'show_name' => $this->_REQUEST( "show_name" ),
					'field_name' => $this->_REQUEST( "field_name" ),
					'field_type' => $this->_REQUEST( "field_type" ),
					'default' => $this->_REQUEST( "default" ),
					'maxlength' => $this->_REQUEST( "maxlength" ),
					'sort' => $this->_REQUEST( "sort" ),
					'visible' => $this->_REQUEST( "visible" ),
					'is_must' => $this->_REQUEST( "is_must" ),
					'create_time' => NOWTIME,
					'create_user_id' => SYS_USER_ID,
				);
				$this->C( $this->cacheDir )->insert( 'cst_field_ext', $into_data );
				$this->L( "Common" )->ajax_json_success( "操作成功" );
			} else {
				$error = $rtn[ 'message' ];
				$this->L( "Common" )->ajax_json_error( $error );
			}

		}
	}
	//更新
	public function cst_field_ext_modify() {
		$field_ext_id = $this->_REQUEST( "field_ext_id" );
		if ( empty( $_POST ) ) {
			$sql = "select * from cst_field_ext where field_ext_id='$field_ext_id'";
			$one = $this->C( $this->cacheDir )->findOne( $sql );
			$type = $this->cst_field_ext_type();
			$smarty = $this->setSmarty();
			$smarty->assign( array( "one" => $one, 'type' => $type ) );
			$smarty->display( 'crm/cst_field_ext_modify.html' );
		} else {
			$ext_table = $this->_REQUEST( "ext_table" );
			$field_name = $this->_REQUEST( "field_name" );
			$show_name = $this->_REQUEST( "show_name" );
			$field_type = $this->_REQUEST( "field_type" );
			$default = $this->_REQUEST( "default" );
			$maxlength = $this->_REQUEST( "maxlength" );
			//执行数据库操作
			$rtn = $this->field->modify_field( $ext_table, $field_name, $field_type, $maxlength, $show_name );
			if ( $rtn[ 'statusCode' ] == '200' ) {
				$upt_data = array(
					'main_table' => $this->_REQUEST( "main_table" ),
					'ext_table' => $this->_REQUEST( "ext_table" ),
					'show_name' => $this->_REQUEST( "show_name" ),
					'field_name' => $this->_REQUEST( "field_name" ),
					'field_type' => $this->_REQUEST( "field_type" ),
					'default' => $this->_REQUEST( "default" ),
					'maxlength' => $this->_REQUEST( "maxlength" ),
					'sort' => $this->_REQUEST( "sort" ),
					'is_must' => $this->_REQUEST( "is_must" ),
					'visible' => $this->_REQUEST( "visible" )
				);
				$this->C( $this->cacheDir )->modify( 'cst_field_ext', $upt_data, "field_ext_id='$field_ext_id'" );
				$this->L( "Common" )->ajax_json_success( "操作成功" );
			} else {
				$error = $rtn[ 'message' ];
				$this->L( "Common" )->ajax_json_error( $error );
			}
		}
	}
	//删除
	public function cst_field_ext_del() {
		$field_ext_id = $this->_REQUEST( "field_ext_id" );
		//删除表中的字段
		$sql = "select field_name,ext_table from cst_field_ext where field_ext_id in ($field_ext_id)";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		foreach ( $list as $row ) {
			$this->field->del_field( $row[ 'ext_table' ], $row[ 'field_name' ] );
		}
		//删除管理字段
		$sql = "delete from cst_field_ext where field_ext_id in ($field_ext_id)";
		$this->C( $this->cacheDir )->update( $sql );
		$this->L( "Common" )->ajax_json_success( "操作成功" );
	}
	//返回jsone
	public function cst_field_ext_select() {
		$type = $this->_REQUEST( "type" );
		$sql = "select id,name from cst_field_ext where type='$type' order by sort asc;";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		echo json_encode( $list );
	}

	//返回一个一维数组
	public function cst_field_ext_arr( $typetag = null ) {
		$rtArr = array();
		$where = ( !empty( $typetag ) ) ? " where typetag='$typetag'" : "";
		$sql = "select field_ext_id,name from cst_field_ext {$where}";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		if ( is_array( $list ) ) {
			foreach ( $list as $key => $row ) {
				$rtArr[ $row[ "field_ext_id" ] ] = $row[ "name" ];
			}
		}
		return $rtArr;
	}
	
	//返回字典名称
	public function cst_field_ext_get_name( $id ) {
		$sql = "select id,name from cst_field_ext where id='$id'";
		$one = $this->C( $this->cacheDir )->findOne( $sql );
		return $one['name'];
	}

	//扩展数据类型
	public function cst_field_ext_type( $key = null ) {
		$data = array(
			"varchar" => array( 'name' => '单行文本(varchar)', 'type' => 'varchar' ),
			"textarea" => array( 'name' => '多行文本', 'type' => 'varchar' ),
			"htmltext" => array( 'name' => 'HTML文本', 'type' => 'varchar' ),
			"int" => array( 'name' => '整数类型', 'type' => 'int' ),
			"float" => array( 'name' => '小数类型', 'type' => 'float' ),
			"datetime" => array( 'name' => '时间类型', 'type' => 'datetime' ),
			"date" => array( 'name' => '日期类型', 'type' => 'date' ),
			"imgurl" => array( 'name' => '图片(仅网址))', 'type' => 'varchar' ),
			"option" => array( 'name' => '使用option下拉框', 'type' => 'varchar' ),
			"radio" => array( 'name' => '使用radio选项卡', 'type' => 'varchar' ),
			"checkbox" => array( 'name' => 'Checkbox多选框', 'type' => 'varchar' ),
			"linkage" => array( 'name' => '系统内部关联', 'type' => 'varchar' )
		);
		return ( $key ) ? $data[ $key ] : $data;
	}

	//是否启用
	public function cst_field_ext_modify_visible() {
		$field_ext_id = $this->_REQUEST( 'field_ext_id' );
		$upt_data = array(
			'visible' => $this->_REQUEST( "visible" )
		);
		$this->C( $this->cacheDir )->modify( 'cst_field_ext', $upt_data, "field_ext_id='$field_ext_id'", true );
		$this->L( "Common" )->ajax_json_success( "操作成功" );
	}
	//更排序
	public function cst_field_ext_modify_sort() {
		$field_ext_id = $this->_REQUEST( 'field_ext_id' );
		$upt_data = array(
			'sort' => $this->_REQUEST( "sort" )
		);
		$this->C( $this->cacheDir )->modify( 'cst_field_ext', $upt_data, "field_ext_id='$field_ext_id'", true );
		$this->L( "Common" )->ajax_json_success( "操作成功" );
	}

	
	//扩展字段表单显示
	//pararm $ext_table 		[description] 扩展表名
	//pararm field_val_arr 		[description] 需要展示的字段html
	//return html string
	public function cst_field_ext_html( $ext_table, $field_val_arr = array() ) {
		$sql = "select * from cst_field_ext where ext_table='$ext_table' and visible='1' order by sort asc;";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		$htmltxt = "";
		foreach ( $list as $key => $row ) {
			//是否存在字段值
			$field_value = array_key_exists( $row[ "field_name" ], $field_val_arr ) ? $field_val_arr[ $row[ "field_name" ] ] : "";
			switch ( $row[ 'field_type' ] ) {
				case "varchar":
					$htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
					break;
				case "textarea":
					$htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<textarea name="' . $row[ "field_name" ] . '" class="form-control" >' . $field_value . '</textarea>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
					break;
				case "int":
					$htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
					break;
				case "float":
					$htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
					break;
				case "datetime":
					$htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control datetimepicker" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
					break;
				case "date":
					$htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<input name="' . $row[ "field_name" ] . '" class="form-control datepicker" type="text" value="' . $field_value . '"/>
										<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
									</div>
								</div>';
					break;
				case "option":
					$htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
									  <select data-placeholder="选择' . $row[ "show_name" ] . '..." name="' . $row[ "field_name" ] . '" class="chosen-select ' . $row[ "field_name" ] . '-chosen-select" style="width: 200px;" tabindex="2">
								';
					$option_arr = explode( ',', $row[ 'default' ] );
					foreach ( $option_arr as $va ) {
						$htmltxt .= '<option value="' . $va . '" hassubinfo="true">' . $va . '</option>';
					}
					$htmltxt .= '
									  </select>
									</div>
								</div>';
					break;
				case "linkage":
					$htmltxt .= '<div class="form-group">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
									  <select data-placeholder="选择' . $row[ "show_name" ] . '..." name="' . $row[ "field_name" ] . '" class="chosen-select ' . $row[ "field_name" ] . '-chosen-select" style="width: 200px;" tabindex="2">
								';
					$option_arr = explode( ',', $row[ 'default' ] );
					$option_arr = $this->cst_field_ext_linkage( $row[ 'default' ] );
					foreach ( $option_arr as $row ) {
						$htmltxt .= '<option value="' . $row[ 'id' ] . '" hassubinfo="true">' . $row[ 'name' ] . '</option>';
					}
					$htmltxt .= '
									  </select>
									</div>
								</div>';
					break;
				case "radio":
					$htmltxt .= '<div class="form-group text-left">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<div class="radio i-checks">
								';
					$option_arr = explode( ',', $row[ 'default' ] );
					foreach ( $option_arr as $va ) {
						$checked = ( $va == $field_value ) ? "checked" : "";
						$htmltxt .= '<input type="radio" name="' . $row[ "field_name" ] . '" value="' . $va . '" ' . $checked . ' /> ' . $va . ' ';
					}
					$htmltxt .= '
									  </div>
									</div>
								</div>';
					break;
				case "checkbox":
					$htmltxt .= '<div class="form-group text-left">
									<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
									<div class="col-sm-10">
										<div class="checkbox i-checks">
								';
					$option_arr = explode( ',', $row[ 'default' ] );
					foreach ( $option_arr as $va ) {
						$htmltxt .= '<input type="checkbox" name="' . $row[ "field_name" ] . '" value="' . $va . '" ' . $checked . '/> ' . $va . ' ';
					}
					$htmltxt .= '
									  </div>
									</div>
								</div>';
					break;
				default:
					$htmltxt .= '';
			}
		}
		return $htmltxt;
	}


	//推展字段表单显示
	public function cst_field_ext_html_one( $ext_table, $field_name = '', $field_value = '' ) {
		$sql = "select * from cst_field_ext where ext_table='$ext_table' and field_name='$field_name' order by sort asc;";
		$row = $this->C( $this->cacheDir )->findOne( $sql );
		$htmltxt = "";
		switch ( $row[ 'field_type' ] ) {
			case "varchar":
				$htmltxt .= '<div class="form-group">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
									<input name="' . $row[ "field_name" ] . '" class="form-control" type="text" value="' . $field_value . '"/>
									<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
								</div>
							</div>';
				break;
			case "textarea":
				$htmltxt .= '<div class="form-group">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
									<textarea name="' . $row[ "field_name" ] . '" class="form-control" >' . $field_value . '</textarea>
									<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
								</div>
							</div>';
				break;
			case "int":
				$htmltxt .= '<div class="form-group">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
									<input name="' . $row[ "field_name" ] . '" class="form-control" type="text" value="' . $field_value . '"/>
									<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
								</div>
							</div>';
				break;
			case "float":
				$htmltxt .= '<div class="form-group">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
									<input name="' . $row[ "field_name" ] . '" class="form-control" type="text" value="' . $field_value . '"/>
									<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
								</div>
							</div>';
				break;
			case "datetime":
				$htmltxt .= '<div class="form-group">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
									<input name="' . $row[ "field_name" ] . '" class="form-control datetimepicker" type="text" value="' . $field_value . '"/>
									<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
								</div>
							</div>';
				break;
			case "date":
				$htmltxt .= '<div class="form-group">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
									<input name="' . $row[ "field_name" ] . '" class="form-control datepicker" type="text" value="' . $field_value . '"/>
									<span class="help-block m-b-none">' . $row[ 'desc' ] . '</span> 
								</div>
							</div>';
				break;
			case "option":
				$htmltxt .= '<div class="form-group text-left pd-b-5" >

								  <select data-placeholder="选择' . $row[ "show_name" ] . '..." name="' . $row[ "field_name" ] . '" class="chosen-select ' . $row[ "field_name" ] . '-chosen-select" style="width: 200px;" tabindex="2">
								  <option value="" hassubinfo="true">选择' . $row[ "show_name" ] . '...</option>
							';
				$option_arr = explode( ',', $row[ 'default' ] );
				foreach ( $option_arr as $va ) {
					$htmltxt .= '<option value="' . $va . '" hassubinfo="true">' . $va . '</option>';
				}
				$htmltxt .= '
								  </select>
							</div>';
				break;
			case "linkage":
				$htmltxt .= '<div class="form-group">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
								  <select data-placeholder="选择' . $row[ "show_name" ] . '..." name="' . $row[ "field_name" ] . '" class="chosen-select ' . $row[ "field_name" ] . '-chosen-select" style="width: 200px;" tabindex="2">
							';
				$option_arr = explode( ',', $row[ 'default' ] );
				$option_arr = $this->cst_field_ext_linkage( $row[ 'default' ] );
				foreach ( $option_arr as $row ) {
					$htmltxt .= '<option value="' . $row[ 'id' ] . '" hassubinfo="true">' . $row[ 'name' ] . '</option>';
				}
				$htmltxt .= '
								  </select>
								</div>
							</div>';
				break;
			case "radio":
				$htmltxt .= '<div class="form-group text-left">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
									<div class="radio i-checks">
							';
				$option_arr = explode( ',', $row[ 'default' ] );
				foreach ( $option_arr as $va ) {
					$checked = ( $va == $field_value ) ? "checked" : "";
					$htmltxt .= '<input type="radio" name="' . $row[ "field_name" ] . '" value="' . $va . '" ' . $checked . ' /> ' . $va . ' ';
				}
				$htmltxt .= '
								  </div>
								</div>
							</div>';
				break;
			case "checkbox":
				$htmltxt .= '<div class="form-group text-left">
								<label class="col-sm-2 control-label">' . $row[ "show_name" ] . '</label>
								<div class="col-sm-10">
									<div class="checkbox i-checks">
							';
				$option_arr = explode( ',', $row[ 'default' ] );
				foreach ( $option_arr as $va ) {
					$htmltxt .= '<input type="checkbox" name="' . $row[ "field_name" ] . '" value="' . $va . '" ' . $checked . '/> ' . $va . ' ';
				}
				$htmltxt .= '
								  </div>
								</div>
							</div>';
				break;
			default:
				$htmltxt .= '';
		}
		return $htmltxt;
	}


	//获得关联联动数据,主要为关联内部数据
	//param  $type 			[description]传入内部关联数据标识
	//return array() 		[description]返回一个二维数组
	public function cst_field_ext_linkage( $type ) {
		$data = array();
		switch ( $type ) {
			case "sys_user":
				$sql = "select id,name from fly_sys_user";
				$data = $this->C( $this->cacheDir )->findAll( $sql );
				break;
            case "sys_area":
                $sql = "select id,name from fly_sys_area";
                $data = $this->C( $this->cacheDir )->findAll( $sql );
                break;
			default:
				echo "Your favorite fruit is neither apple, banana, or orange!";
		}
		return $data;
	}

	//获得为下拉选项的字段，
	public function cst_field_ext_option( $ext_table, $field_type ) {
		$sql = "select * from cst_field_ext where ext_table='$ext_table' order by sort asc;";
		$list = $this->C( $this->cacheDir )->findAll( $sql );
		$rtnArr = array();
		foreach ( $list as $row ) {
			if ( $row[ 'field_type' ] == $field_type ) {
				$rtnArr[] = $row[ 'field_name' ];
			}
		}
		return $rtnArr;
	}
} //end class
?>