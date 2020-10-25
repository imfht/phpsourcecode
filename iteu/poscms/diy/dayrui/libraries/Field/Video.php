<?php

/* v3.1.0  */

class F_Video extends A_Field {
	
	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		$this->name = IS_ADMIN ? fc_lang('视频文件') : ''; // 字段名称
		$this->fieldtype = array(
			'TEXT' => ''
		); // TRUE表全部可用字段类型,自定义格式为 array('可用字段类型名称' => '默认长度', ... )
		$this->defaulttype = 'TEXT'; // 当用户没有选择字段类型时的缺省值
    }
	
	/**
	 * 字段相关属性参数
	 *
	 * @param	array	$value	值
	 * @return  string
	 */
	public function option($option) {
	
		$option['width'] = isset($option['width']) ? $option['width'] : '90%';
		$option['uploadpath'] = isset($option['uploadpath']) ? $option['uploadpath'] : '';
		$option['is_swfupload'] = isset($option['is_swfupload']) ? $option['is_swfupload'] : 0;
		
		
		return '<div class="form-group">
					<label class="col-md-2 control-label">'.fc_lang('宽度').'：</label>
					<div class="col-md-9">
						<label><input type="text" class="form-control" size="10" name="data[setting][option][width]" value="'.$option['width'].'"></label>
						<span class="help-block">'.fc_lang('[整数]表示固定宽带；[整数%]表示百分比').'</span>
					</div>
				</div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('文件大小').'：</label>
                    <div class="col-md-9">
						<label><input id="field_default_value" type="text" class="form-control" value="'.$option['size'].'" name="data[setting][option][size]"></label>
						<span class="help-block">'.fc_lang('单位MB').'</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('扩展名').'：</label>
                    <div class="col-md-9">
                    	<label><input type="text" class="form-control" size="40" name="data[setting][option][ext]" value="'.$option['ext'].'"></label>
						<span class="help-block">'.fc_lang('格式：jpg,gif,png,exe,html,php,rar,zip').'</span>
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('前端SWF上传').'：</label>
                    <div class="col-md-9">
                    	<input type="checkbox" name="data[setting][option][is_swfupload]" '.($option['is_swfupload'] ? 'checked' : '').' value="1"  data-on-text="'.fc_lang('开启').'" data-off-text="'.fc_lang('关闭').'" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
					</div>
                </div>
				<div class="form-group">
                    <label class="col-md-2 control-label">'.fc_lang('保存目录').'：</label>
                    <div class="col-md-9">
                    <input type="text" class="form-control" size="50" name="data[setting][option][uploadpath]" value="'.$option['uploadpath'].'">
					<span class="help-block">'.fc_lang('目录中不得包含中文 <br />标签介绍：站点id{siteid}、模块目录{module}、年{y}、月{m}、日{d} <br />例如：{siteid}/{module}/test/，将附件保存至：uploadfile/站点/模块目录/test目录/附件名称.扩展名').'</span>
                    </div>
                </div>';
	}
	
	/**
	 * 字段输出
	 */
	public function output($value) {
		return dr_string2array($value);
	}
	
	/**
	 * 字段入库值
	 */
	public function insert_value($field) {
		$data = $this->ci->post[$field['fieldname']];
        if ($data && $data['file']) {
            $value = array();
            if ($data['time']) {
                foreach ($data['time'] as $i => $t) {
                    if ($data['title'][$i]) {
                        $value['point'][$t] = $data['title'][$i];
                    }
                }
            }
            $value['file'] = $data['file'];
            $this->ci->data[$field['ismain']][$field['fieldname']] = dr_array2string($value);
        } else {
            $this->ci->data[$field['ismain']][$field['fieldname']] = '';
        }
	}
	
	/**
	 * 附件处理
	 */
	public function attach($data, $_data) {

		$data = dr_string2array($data);
		$_data = dr_string2array($_data);

        if (!isset($_data['file'])) {
            $_data = array('file' => NULL);
        }
        if (!isset($data['file'])) {
            $data = array('file' => NULL);
        }
		
		// 新旧数据都无附件就跳出
		if (!$data['file'] && !$_data['file']) {
			return NULL;
		}
		
		// 新旧数据都一样时表示没做改变就跳出
		if ($data && $data['file'] === $_data['file']) {
			return NULL;
		}
		
		// 当无新数据且有旧数据表示删除旧附件
		if (!$data['file'] && $_data['file']) {
			return array(
				array(),
				array($_data['file'])
			);
		}
		
		// 当无旧数据且有新数据表示增加新附件
		if ($data && $data['file'] && !$_data['file']) {
			return array(
				array($data['file']),
				array()
			);
		}
		
		// 剩下的情况就是删除旧文件增加新文件
		return array(
			array($data['file']),
			array($_data['file'])
		);
	}
	
	/**
	 * 字段表单输入
	 *
	 * @param	string	$cname	字段别名
	 * @param	string	$name	字段名称
	 * @param	array	$cfg	字段配置
	 * @param	string	$value	值
	 * @return  string
	 */
	public function input($cname, $name, $cfg, $value = NULL, $id = 0) {
		// 字段显示名称
		$text = (isset($cfg['validate']['required']) && $cfg['validate']['required'] == 1 ? '<font color="red">*</font>' : '').''.$cname.'：';
		// 表单附加参数
		$attr = isset($cfg['validate']['formattr']) && $cfg['validate']['formattr'] ? $cfg['validate']['formattr'] : '';
		// 字段提示信息
		$tips = isset($cfg['validate']['tips']) && $cfg['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$cfg['validate']['tips'].'</span>' : '';
		// 当字段必填时，加入html5验证标签
		if (isset($cfg['validate']['required'])
            && $cfg['validate']['required'] == 1) {
            $attr.= ' required="required"';
        }
		// 表单选项
		$disabled = !IS_ADMIN && $id && $value && isset($cfg['validate']['isedit']) && $cfg['validate']['isedit'] ? 'disabled' : '';

		if ((IS_ADMIN && IS_PC) || (isset($cfg['option']['is_swfupload']) && $cfg['option']['is_swfupload'])) {
			// 上传的URL
			$url = '/index.php?s=member&c=api&m=upload&name='.$name.'&count=1&code='.str_replace('=', '', dr_authcode($cfg['option']['size'].'|'.$cfg['option']['ext'].'|'.$this->get_upload_path($cfg['option']['uploadpath']), 'ENCODE'));
			// 文件值
			$file = $info = '';
			$value = dr_string2array($value);
			if ($value['file']) {
				$file = $value['file'];
				$data = dr_file_info($file);
				if ($data) {
					$info = '<button type="button" style="cursor:pointer;" class="btn green btn-sm file_info_'.$name.'" onclick="dr_show_file_info(\''.$file.'\')"> <i class="fa fa-search"></i> ' . dr_strcut($data['filename'] ? $data['filename'] : $file, 20).'.'.$data['fileext'] . '</button>';
				} elseif (is_numeric($file) && !get_attachment($file)) {
					$info = '<button type="button" style="cursor:pointer;" class="btn red btn-sm file_info_'.$name.'"> <i class="fa fa-close"></i> ' . fc_lang('文件信息不存在') . '</button>';
				}
				$info.= '
				<button type="button" style="cursor:pointer;"  class="btn red btn-sm file_info_'.$name.'" onclick="dr_delete_file(\''.$name.'\')"> <i class="fa fa-trash"></i> ' . fc_lang('删除文件') . '</button>';
				unset($data);
				$default = '';
				if ($value['point']) {
					$i = 0;
					foreach ($value['point'] as $time => $title) {
						$default.= '
						<li id="dr_items_'.$name.'_'.$i.'">
						时间(秒)：<input type="text" class="input-text" style="width:70px;" value="'.$time.'" name="data['.$name.'][time][]">&nbsp;&nbsp;提示文字：<input type="text" class="input-text" style="width:200px;" value="'.$title.'" name="data['.$name.'][title][]\">&nbsp;&nbsp;<a href="javascript:;" onclick="$(\'#dr_items_'.$name.'_'.$i.'\').remove()">'.fc_lang('删除').'</a>
						</li>';
						$i++;
					}
				}
			}
			// 显示框宽度设置
			$width = isset($cfg['option']['width']) && $cfg['option']['width'] ? $cfg['option']['width'] : '100%';
			$str = '<fieldset class="blue pad-10" style="width:'.$width.(is_numeric($width) ? 'px' : '').';">
						<legend>'.$cname.'</legend>
						<div class="picList">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td style="text-align:left;padding-left:0;">
								<span>'.fc_lang('文件格式：%s', str_replace('|', '、', $cfg['option']['ext'])).'</span>&nbsp;&nbsp;
								<input type="hidden" id="fileid_'.$name.'" name="data['.$name.'][file]" value="'.$file.'" '.$attr.' />
								<span id="file_html_'.$name.'">
								<button type="button" style="cursor:pointer;" '.$disabled.' class="btn blue btn-sm" onclick="dr_upload_file(\''.$name.'\', \''.$url.'\')"> <i class="fa fa-video-camera"></i> ' . fc_lang('上传文件') . '</button>
								</span>
								<span id="dr_my_'.$name.'_list">'.$info.'</span>'.$tips.'
								</td>
							</tr>
							</table>
							<ul id="'.$name.'-sort-items" style="margin-top:8px;">
							'.$default.'
							</ul>
						</div>
					<div class="">
						<a href="javascript:;" class="btn blue btn-sm" onClick="dr_add_video_'.$name.'()"> <i class="fa fa-flag"></i> 添加提示点 </a>
					</div>
					<div class="onShow" style="margin-top:2px;">鼠标经过进度栏N秒时，N秒会提示相应的文字</div>
					<script type="text/javascript">
					$("#'.$name.'-sort-items").sortable();
					var id=$("#'.$name.'-sort-items li").size();
					function dr_add_video_'.$name.'() {
						id ++;
						var html = "<li id=\"dr_items_'.$name.'_"+id+"\">";
						html+= "时间(秒)：<input type=\"text\" class=\"input-text\" style=\"width:70px;\" value=\"\" name=\"data['.$name.'][time][]\">&nbsp;&nbsp;";
						html+= "提示文字：<input type=\"text\" class=\"input-text\" style=\"width:200px;\" value=\"\" name=\"data['.$name.'][title][]\">&nbsp;&nbsp;";
						html+= "<a href=\"javascript:;\" onclick=\"$(\'#dr_items_'.$name.'_"+id+"\').remove()\">'.fc_lang('删除').'</a>";
						html+= "</li>";
						$("#'.$name.'-sort-items").append(html);
					}
					</script>
					</fieldset>
			';
		} else {
			// 会员中心时
			$url = '/index.php?s=member&c=api&m=new_ajax_upload&name='.$name.'&siteid='.SITE_ID.'&count=1&code='.str_replace('=', '', dr_authcode($cfg['option']['size'].'|'.$cfg['option']['ext'].'|'.$this->get_upload_path($cfg['option']['uploadpath']), 'ENCODE'));
			// 文件值
			$my = $file = $info = '';
			$value = dr_string2array($value);
			if ($value['file']) {
				$file = $value['file'];
				$data = dr_file_info($file);
				if ($data) {
					$size = $data['size'] ? ' ('.$data['size'].')' : '';
					$my = '
					<a href="javascript:;" onclick="dr_show_file_info(\''.$data['id'].'\')"><img align="absmiddle" src="'.$data['icon'].'"><span class="badge badge-info badge-roundless">'.$data['filename'].$size.'</span></a>
					<a href="javascript:;" title="'.fc_lang('删除').'" onclick="dr_delete_file2(\''.$name.'\')">
					<img align="absmiddle" src="'.THEME_PATH.'admin/images/b_drop.png"></a>
					';
				} elseif (is_numeric($file) && !get_attachment($file)) {
					$my = '<span class="badge badge-danger">文件信息不存在</span>';
				}
				unset($data);
				if ($value['point']) {
					foreach ($value['point'] as $time => $title) {
						$my.= '<input type="hidden" value="'.$time.'" name="data['.$name.'][time][]">
						<input type="hidden" value="'.$title.'" name="data['.$name.'][title][]\">';
					}
				}
			}

			$str = '<div class="row" style="margin:0">
		    <input type="hidden" value="'.$file.'" name="data['.$name.'][file]" id="fileid_'.$name.'" />';
			// 加载js
			if (!defined('FINECMS_FILES_MOBILE')) {
				$str.= '<script type="text/javascript" src="'.THEME_PATH.'js/dmuploader.min.js"></script>
				<style>
				div.uploader {
					width:auto!important;
					cursor: pointer;padding-right:20px;
				}
				.my_list {
					margin-top: 5px;padding-left:0
				}
				.uploader input {
					position: absolute;
					top: 0;
					right: 0;
					margin: 0;
					border: solid transparent;
					border-width: 0 0 100px 200px;
					opacity: .0;
					filter: alpha(opacity= 0);
					-o-transform: translate(250px,-50px) scale(1);
					-moz-transform: translate(-300px,0) scale(4);
					direction: ltr;
					cursor: pointer;
				}</style>';
				define('FINECMS_FILES_MOBILE', 1);//防止重复加载JS
			}

			if (!$disabled) {
				$str.= '
				<div id="drag-and-drop-zone-'.$name.'" class="col-md-6 btn blue uploader">
					<i class="fa fa-cloud-upload"></i> 单击上传（'.$cfg['option']['ext'].'）<input type="file" name="file">
				</div>';
			}
			$str.= '
          	<div id="dr_my_'.$name.'_list"  class="col-md-6 my_list">'.$my.'</div>
          	</div>
          	<div id="dr_hide_'.$name.'_list" style="display:none"></div>
          	<script type="text/javascript">
      $("#drag-and-drop-zone-'.$name.'").dmUploader({
        url: "'.$url.'",
        dataType: "json",
        allowedTypes: "*",
        onInit: function(){
          //alert("Plugin initialized correctly");
        },
        onBeforeUpload: function(id){
          $("#dr_hide_'.$name.'_list").html($("#dr_my_'.$name.'_list").html());
          var vhtml = "";
          vhtml+= "<div class=\"progress progress-striped\">";
          vhtml+= "<div class=\"progress-bar progress-bar-warning\" id=\"loading_'.$name.'\" role=\"progressbar\" style=\"width: 10%\">";
          vhtml+= "<span class=\"sr-only\"></span>";
		  vhtml+= "</div>";
		  vhtml+= "</div>";
          $("#dr_my_'.$name.'_list").append(vhtml);
        },
        onNewFile: function(id, file){
          //alert(file);
        },
        onComplete: function(){
          //alert("All pending tranfers completed");
        },
        onUploadProgress: function(id, percent){
		  $("#loading_'.$name.'").attr("style", "width:"+percent + "%");
        },
        onUploadSuccess: function(id, data){
		  if (data.code == 1) {
		  	$("#fileid_'.$name.'").val(data.id);
		  	$("#dr_my_'.$name.'_list").html("<a href=\"javascript:;\" onclick=\"dr_show_file_info(\'"+data.id+"\')\"><img align=\"absmiddle\" src="+data.icon+"><span class=\"badge badge-info badge-roundless\">"+data.size+"</span></a>&nbsp;&nbsp;<a href=\"javascript:;\" title=\"'.fc_lang('删除').'\" onclick=\"dr_delete_file2(\''.$name.'\')\"><img align=\"absmiddle\" src=\"'.THEME_PATH.'admin/images/b_drop.png\"></a>");
		  } else {
		  	dr_tips(data.msg,5);
		  	$("#loading_'.$name.'_html").remove();
            $("#dr_my_'.$name.'_list").html($("#dr_hide_'.$name.'_list").html());
		  }

        },
         onUploadError: function(id, message){
          alert("Failed to Upload file #" + id + ": " + message);
          $("#dr_my_'.$name.'_list").html("");
		  $("#loading_'.$name.'_html").remove();
        },
        onFileTypeError: function(file){
          alert("File \"" + file.name + "\" cannot be added: must be an image");
          $("#dr_my_'.$name.'_list").html("");
		  $("#loading_'.$name.'_html").remove();
        },
        onFileSizeError: function(file){
          alert( "File \"" + file.name + "\" cannot be added: size excess limit");
          $("#dr_my_'.$name.'_list").html("");
		  $("#loading_'.$name.'_html").remove();
        },
        onFallbackMode: function(message){
          alert( "Browser not supported(do something else here!): " + message);
          $("#dr_my_'.$name.'_list").html("");
		  $("#loading_'.$name.'_html").remove();
        }
      });
    </script>
          	';

		}

		return $this->input_format($name, $text, $str);
	}
	
}