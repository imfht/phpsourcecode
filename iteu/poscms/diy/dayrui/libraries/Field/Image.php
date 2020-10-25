<?php



class F_Image extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = '图片上传'; // 字段名称
		$this->fieldtype = array('TEXT' => '', 'VARCHAR' => '255'); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'VARCHAR'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
	
		$option['count'] = isset($option['count']) ? $option['count'] : 2;
		$option['width'] = isset($option['width']) ? $option['width'] : '80%';
		$option['fieldtype'] = isset($option['fieldtype']) ? $option['fieldtype'] : '';
		$option['uploadpath'] = isset($option['uploadpath']) ? $option['uploadpath'] : '';
		$option['fieldlength'] = isset($option['fieldlength']) ? $option['fieldlength'] : '';
		
		return '
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
					<span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('文件大小').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" value="'.$option['size'].'" name="data[setting][option][size]"></label>
					<span class="help-block">'.fc_lang('单位MB').'</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('上传数量').'：</label>
				<div class="col-md-9">
					<label><input type="text" class="form-control" value="'.$option['count'].'" name="data[setting][option][count]"></label>
					<span class="help-block">'.fc_lang('每次最多上传的文件数量').'</span>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('远程图片模式').'：</label>
				<div class="col-md-9">
				<div class="radio-list">
					<label class="radio-inline"><input type="radio" value="2" name="data[setting][option][autodown]" '.($option['autodown'] == 2 ? 'checked' : '').' > '.fc_lang('自动（会影响发布速度）').'</label>
					<label class="radio-inline"><input type="radio" value="0" name="data[setting][option][autodown]" '.($option['autodown'] == 0 ? 'checked' : '').' > '.fc_lang('关闭').'</label>
				</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label">'.fc_lang('保存目录').'：</label>
				<div class="col-md-9">
				<input type="text" class="form-control" size="50" name="data[setting][option][uploadpath]" value="'.$option['uploadpath'].'">
				<span class="help-block">'.fc_lang('目录中不得包含中文 <br />标签介绍：站点id{siteid}、模块目录{module}、年{y}、月{m}、日{d} <br />例如：{siteid}/{module}/test/，将附件保存至：uploadfile/站点/模块目录/test目录/附件名称.扩展名').'</span>
				</div>
			</div>
            ';
	}
	
	/**
	 * 字段输出
	 */
	public function output($value) {
	
		return dr_string2array($value);
	}
	
	/**
	 * 获取附件id
	 */
	public function get_attach_id($value) {

		return dr_string2array($value);
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {

		$my = $this->ci->post[$field['fieldname']];
		$data = array();
		if ($my) {
			foreach ($my as $id) {
				if ($id) {
					// 下载远程图片
					if (strpos($id, 'http') === 0
						&& (strpos($id, 'jpg') !== false || strpos($id, 'png') !== false || strpos($id, 'gif') !== false || strpos($id, 'jpeg') !== false)
						&& isset($field['setting']['option']['autodown']) && $field['setting']['option']['autodown']) {
						// 当前作者
						$uid = isset($_POST['data']['uid']) ? (int)$_POST['data']['uid'] : $this->ci->uid;
						// 附件总大小判断
						if ($uid == $this->ci->uid
							&& ($this->ci->member['adminid'] || $this->ci->member_rule['attachsize'])) {
							$data = $this->ci->db->select_sum('filesize')->where('uid', $uid)->get('attachment')->row_array();
							if ($this->ci->member['adminid']
								|| $data['filesize'] <= $this->ci->member_rule['attachsize'] * 1024 * 1024) {
								// 可以下载
								$file = dr_catcher_data($id);
								if (!$file) {
									log_message('error', 'Image字段下载远程图片失败：获取远程数据失败('.$id.')');
								} else {
									$path = SYS_UPLOAD_PATH.'/'.date('Ym', SYS_TIME).'/';
									if (!is_dir($path)) {
										dr_mkdirs($path);
									}
									$fileext = strtolower(trim(substr(strrchr($id, '.'), 1, 10))); //扩展名
									$filename = substr(md5(time()), 0, 7).rand(100, 999);
									if (@file_put_contents($path.$filename.'.'.$fileext, $file)) {
										$info = array(
											'file_ext' => '.'.$fileext,
											'full_path' => $path.$filename.'.'.$fileext,
											'file_size' => filesize($path.$filename.'.'.$fileext)/1024,
											'client_name' => $id,
										);
										$this->ci->load->model('attachment_model');
										$result = $this->ci->attachment_model->upload($uid, $info);
										if (is_array($result)) {
											$id = $result[0];
										} else {
											@unlink($path.$filename.'.'.$fileext);
											log_message('error', '编辑器下载远程图片失败：'.$result);
										}
									} else {
										log_message('error', '编辑器下载远程图片失败：文件写入失败');
									}
								}
							} else {
								// 附件总空间不足
								$this->ci->member_model->add_notice($uid, 1, fc_lang('附件可用空间不足，无法下载远程图片'));
							}
						}
					}
                    $data[] = $id;
                }
			}

		}



		// 第一张作为缩略图
		if (isset($_POST['data']['thumb']) && !$_POST['data']['thumb'] && isset($data[0]) && $data[0]) {
            $this->ci->data[1]['thumb'] = $data[0];
		}

		$this->ci->data[$field['ismain']][$field['fieldname']] = dr_array2string($data);
	}
	
	/**
	 * 附件处理
	 */
	public function attach($data, $_data) {
		
		$data = dr_string2array($data);
		$_data = dr_string2array($_data);

        if (!isset($_data)) {
            $_data = array();
        }
        if (!isset($data)) {
            $data = array();
        }

		// 新旧数据都无附件就跳出
		if (!$data && !$_data) {
			return NULL;
		}
		
		// 新旧数据都一样时表示没做改变就跳出
		if ($data === $_data) {
			return NULL;
		}
		
		// 当无新数据且有旧数据表示删除旧附件
		if (!$data && $_data) {
			return array(
				array(),
				$_data
			);
		}
		
		// 当无旧数据且有新数据表示增加新附件
		if ($data && !$_data) {
			return array(
				$data,
				array()
			);
		}
		
		// 剩下的情况就是删除旧文件增加新文件
		
		// 新旧附件的交集，表示固定的
		$intersect = @array_intersect($data, $_data);
		
		return array(
			@array_diff($data, $intersect), // 固有的与新文件中的差集表示新增的附件
			@array_diff($_data, $intersect), // 固有的与旧文件中的差集表示待删除的附件
		);
	}
	
	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	array	$data	值
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';
		// 显示框宽度设置
		$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '80%';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 禁止修改
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : ''; 
		// 当字段必填时，加入html5验证标签
		if (isset($cfg['validate']['required'])
            && $cfg['validate']['required'] == 1) {
            $attr.= ' required="required"';
        }
		// 上传的URL
		$url = '/index.php?s=member&c=api&m=ajax_upload&name='.$name.'&siteid='.SITE_ID.'&code='.str_replace('=', '', dr_authcode($cfg['option']['size'].'|'.$this->get_upload_path($cfg['option']['uploadpath']), 'ENCODE'));
		// 字段默认值
		$file_value = '';
		$value && $value = dr_string2array($value);
		// 默认值输出
		$info = array();
		if ($value && is_array($value)) {
			foreach ($value as $i => $id) {
				$info[$i]['id'] = $id;
				$info[$i]['file'] = dr_get_file($id);
			}
		}
		$count = max(1, (int)$cfg['option']['count']);
		// 输出变量
		$str ='';
		$str.= '<script type="text/javascript" src="'.THEME_PATH.'js/ajax.upload.js"></script>';
        $str.= '<script type="text/javascript" src="'.THEME_PATH.'js/jquery-ui.min.js"></script>';
		$str.= '<div class="bk10"></div>';
		$str.= ' <ul class="cover imgreset" id="dr_upload_'.$name.'">';
        for($i=0; $i<$count; $i++) {
			$str.= '<li class="upload-container" style="width:90px;height:72px;">';
			$str.= '<div class="upload-trigger '.($info[$i]['id'] ? 'completed" style="display:none' : '').'" id="dr_'.$name.'_upload_'.$i.'"></div>';
			$str.= '<div class="upload-preview" style="display:'.($info[$i]['id'] ? 'block' : 'none').';">';
			$str.= '<input type="hidden" class="dr_'.$name.'_upload_value_'.$i.'" name="data['.$name.']['.$i.']" id="cover" value="'.$info[$i]['id'].'" />';
			$str.= '<div class="pic" style="width:90px; height:71px; overflow:hidden;background-image:none !important"><img src="'.($info[$i]['id'] ? $info[$i]['file'] : 'javascript:void(0);').'" style="height:auto;" /></div>';
			$str.= '<a href="javascript:dr_remove_'.$name.'('.$i.')" class="remove" style="display: none;"></a>';
			$str.= '<span class="rearrange-text" style="display: none;">拖动排序</span>';
			$str.= '</div>';
			$str.= '</li>';
			$str.= '<script type="text/javascript">
			function dr_remove_'.$name.'(i) {
				var cover = $("#dr_'.$name.'_upload_"+i);
				cover.removeClass("completed");
				cover.show();
				cover.html("");
				$(".dr_'.$name.'_upload_value_"+i).val("");
				var preview = cover.next(\'div\');
				$(\'img\', preview).attr({src:\'javascript:void(0);\'});
				preview.hide();
			}
			$(".upload-container").bind({
				mouseenter:function(){
				  $("a,span", $(this)).show();
				},
				mouseleave:function(){
				  $("a,span", $(this)).hide();
				}
			});
			// 拖动图片
			$("#dr_upload_'.$name.'").sortable({
				revert: true,
				axis:   "x"
			});
			$(function () {
				var $cover_'.$name.' = $("#dr_'.$name.'_upload_'.$i.'");
				var $preview_'.$name.' = $cover_'.$name.'.next(\'div\');
				var fileType = "pic",fileNum = "one"; 
				var button = $("#dr_'.$name.'_upload_'.$i.'"), interval;
				new AjaxUpload(button, {
					action: "'.$url.'",
					name: "Filedata",
					onSubmit: function (file, ext) {
						if(ext && /^(jpg|jpeg|png|gif)$/i.test(ext)){
							// 上传成功
							$cover_'.$name.'.html("<img src=\"'.THEME_PATH.'admin/images/loading-mini.gif\">");
						}else{
							dr_tips("请上传图片");
							return false;
						}
					},
					onComplete: function (file, response) {
						var json = $.parseJSON(response);
						if(json.code){
						  $("img", $preview_'.$name.').attr({src:json.url});
						  $("input", $preview_'.$name.').val(json.id);
						  $cover_'.$name.'.addClass("completed").hide();
						  $preview_'.$name.'.show();
						  $(\'div.upload-trigger:not(".completed")\').eq(0).html(\'\').show();
						}else{
						  $(\'img\', $preview_'.$name.').attr({src:\'javascript:void(0);\'});
						  $(\'input\', $preview_'.$name.').val(\'\');
						  dr_tips(json.msg);
						}
					}
				});
			});
			</script>';
		}
		$str.= '</ul>';
		$str.= '<div class="bk10"></div>';
		$str.= ''.$tips;
		// 输出最终表单显示
		return $this->input_format($name, $text, $str);
	}
	
}