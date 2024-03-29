<?php namespace Phpcmf\Field;

/**
 * http://www.xunruicms.com
 * 本文件是框架系统文件，二次开发时不可以修改本文件，可以通过继承类方法来重写此文件
 **/

class Ueditor extends \Phpcmf\Library\A_Field {

    /**
     * 构造函数
     */
    public function __construct(...$params) {
        parent::__construct(...$params);
        $this->use_xss = 1; // 强制xss验证
        $this->fieldtype = ['MEDIUMTEXT' => ''];
        $this->defaulttype = 'MEDIUMTEXT';
    }

    /**
     * 字段相关属性参数
     *
     * @param   array   $value  值
     * @return  string
     */
    public function option($option) {

        $option['mode'] = isset($option['mode']) ? $option['mode'] : 1;
        $option['page'] = isset($option['page']) ? $option['page'] : 0;
        $option['tool'] = isset($option['tool']) ? $option['tool'] : '\'bold\', \'italic\', \'underline\'';
        $option['mode2'] = isset($option['mode2']) ? $option['mode2'] : $option['mode'];
        $option['tool2'] = isset($option['tool2']) ? $option['tool2'] : $option['tool'];
        $option['mode3'] = isset($option['mode3']) ? $option['mode3'] : $option['mode'];
        $option['tool3'] = isset($option['tool3']) ? $option['tool3'] : $option['tool'];
        $option['value'] = isset($option['value']) ? $option['value'] : '';
        $option['width'] = isset($option['width']) ? $option['width'] : '100%';
        $option['height'] = isset($option['height']) ? $option['height'] : 300;
        $option['fieldtype'] = isset($option['fieldtype']) ? $option['fieldtype'] : '';
        $option['autofloat'] = isset($option['autofloat']) ? $option['autofloat'] : 0;
        $option['autoheight'] = isset($option['autoheight']) ? $option['autoheight'] : 0;
        $option['fieldlength'] = isset($option['fieldlength']) ? $option['fieldlength'] : '';
        $option['watermark'] = isset($option['watermark']) ? $option['watermark'] : '';
        $option['show_bottom_boot'] = isset($option['show_bottom_boot']) ? $option['show_bottom_boot'] : '';

        $wm = \Phpcmf\Service::C()->get_cache('site', SITE_ID, 'watermark', 'ueditor') ? '<div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('图片水印').'</label>
                    <div class="col-md-9">
                        <div class="form-control-static">
                            '.dr_lang('系统强制开启水印').'
                        </div>
                    </div>
                </div>' : '<div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('图片水印').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="data[setting][option][watermark]" '.($option['watermark'] == 1 ? 'checked' : '').' > '.dr_lang('开启').' <span></span></label>
                             &nbsp; &nbsp;
                             <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="data[setting][option][watermark]" '.($option['watermark'] == 0 ? 'checked' : '').' > '.dr_lang('关闭').' <span></span></label>
                        </div>
						<span class="help-block">上传的图片会加上水印图</span>
                    </div>
                </div>';

        return ['<div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('下载远程图片').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="data[setting][option][down_img]" '.($option['down_img'] == 1 ? 'checked' : '').' > '.dr_lang('自动').' <span></span></label>
                            &nbsp; &nbsp;
                            <label class="mt-radio mt-radio-outline"><input  type="radio" value="0" name="data[setting][option][down_img]" '.($option['down_img'] == 0 ? 'checked' : '').' > '.dr_lang('手动').' <span></span></label>
                        </div>
						<span class="help-block">自动模式下每一次编辑内容时都会下载图片；手动模式可以在编辑器下放工具栏中控制“是否下载”</span>
                    </div>
                </div>
				'.$wm.
            '
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('底部工具栏').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$(\'#sdmrx\').show()" value="1" name="data[setting][option][show_bottom_boot]" '.($option['show_bottom_boot'] == 1 ? 'checked' : '').' > '.dr_lang('开启').' <span></span></label>
                             &nbsp; &nbsp;
                            <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$(\'#sdmrx\').hide()" value="0" name="data[setting][option][show_bottom_boot]" '.($option['show_bottom_boot'] == 0 ? 'checked' : '').' > '.dr_lang('关闭').' <span></span></label>
                        </div>
						<span class="help-block">编辑器底部工具栏，有截取字符选择、提取缩略图、下载远程图等控制按钮</span>
                    </div>
                </div>
                <div class="form-group" id="sdmrx" '.(!$option['show_bottom_boot'] ? 'style="display:none"' : '').'>
                    <label class="col-md-1 control-label"> &nbsp; &nbsp;</label>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label class="col-md-2 control-label">'.dr_lang("提取描述").'</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[setting][option][tool_select_2]" value="1" '.($option['tool_select_2'] ? 'checked' : '').' data-on-text="'.dr_lang("默认选中").'" data-off-text="'.dr_lang("默认不选").'" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">'.dr_lang("提取缩略图").'</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[setting][option][tool_select_1]" value="1" '.($option['tool_select_1'] ? 'checked' : '').' data-on-text="'.dr_lang("默认选中").'" data-off-text="'.dr_lang("默认不选").'" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">'.dr_lang("下载远程图").'</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[setting][option][tool_select_3]" value="1" '.($option['tool_select_3'] ? 'checked' : '').' data-on-text="'.dr_lang("默认选中").'" data-off-text="'.dr_lang("默认不选").'" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">                             
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">'.dr_lang("去除站外链接").'</label>
                            <div class="col-md-9">
                                <input type="checkbox" name="data[setting][option][tool_select_4]" value="1" '.($option['tool_select_4'] ? 'checked' : '').' data-on-text="'.dr_lang("默认选中").'" data-off-text="'.dr_lang("默认不选").'" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">                             
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('固定编辑器图标栏').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="data[setting][option][autofloat]" '.($option['autofloat'] == 1 ? 'checked' : '').' > '.dr_lang('开启').' <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type="radio"  value="0" name="data[setting][option][autofloat]" '.($option['autofloat'] == 0 ? 'checked' : '').' > '.dr_lang('关闭').' <span></span></label>
                        </div>
						<span class="help-block">当开启时，在完整模式下，编辑器图标栏会固定在页面，不会随浏览器滚动</span>
                    </div>
                </div>
                <div class="form-group hide">
                    <label class="col-md-2 control-label">'.dr_lang('过滤style属性').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="data[setting][option][remove_style]" '.(!$option['remove_style'] ? 'checked' : '').' > '.dr_lang('全部过滤').' <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type="radio"  value="1" name="data[setting][option][remove_style]" '.($option['remove_style'] ==1 ? 'checked' : '').' > '.dr_lang('后台过滤').' <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type="radio"  value="2" name="data[setting][option][remove_style]" '.($option['remove_style'] ==2 ? 'checked' : '').' > '.dr_lang('前端过滤').' <span></span></label>
                        </div>
						<span class="help-block">过滤编辑器里面的多余的style属性参数</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('将div标签转换为p标签').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="data[setting][option][div2p]" '.(!$option['div2p'] ? 'checked' : '').' > '.dr_lang('开启').' <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="data[setting][option][div2p]" '.($option['div2p'] ? 'checked' : '').' > '.dr_lang('关闭').' <span></span></label>
                        </div>
						<span class="help-block">将编辑器的div标签强制转换为p标签</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('自动伸长高度').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="data[setting][option][autoheight]" '.($option['autoheight'] == 1 ? 'checked' : '').' > '.dr_lang('开启').' <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="data[setting][option][autoheight]" '.($option['autoheight'] == 0 ? 'checked' : '').' > '.dr_lang('关闭').' <span></span></label>
                        </div>
						
						<span class="help-block">编辑器会自动增加高度</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('分页标签').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="data[setting][option][page]" '.($option['page'] ? 'checked' : '').' > '.dr_lang('开启').' <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="data[setting][option][page]" '.(!$option['page'] ? 'checked' : '').' > '.dr_lang('关闭').' <span></span></label>
                        </div>
						<span class="help-block">文章内容的分页功能</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('后台编辑器模式').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="data[setting][option][mode]" '.($option['mode'] == 1 ? 'checked' : '').' onclick="$(\'#bjqms1\').hide()"> '.dr_lang('完整').' <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="2" name="data[setting][option][mode]" '.($option['mode'] == 2 ? 'checked' : '').' onclick="$(\'#bjqms1\').hide()"> '.dr_lang('精简').' <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="3" name="data[setting][option][mode]" '.($option['mode'] == 3 ? 'checked' : '').' onclick="$(\'#bjqms1\').show()"> '.dr_lang('自定义').' <span></span></label>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="bjqms1" '.($option['mode'] < 3 ? 'style="display:none"' : '').'>
                    <label class="col-md-2 control-label">'.dr_lang('工具栏').'</label>
                    <div class="col-md-9">
                    <textarea name="data[setting][option][tool]" style="height:90px;" class="form-control">'.$option['tool'].'</textarea>
                    <span class="help-block">'.dr_lang('必须严格按照Ueditor工具栏格式\'fullscreen\', \'source\', \'|\', \'undo\', \'redo\'').'</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('前台编辑器模式').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="data[setting][option][mode2]" '.($option['mode2'] == 1 ? 'checked' : '').' onclick="$(\'#bjqms2\').hide()"> '.dr_lang('完整').' <span></span></label>
                             &nbsp; &nbsp;
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="2" name="data[setting][option][mode2]" '.($option['mode2'] == 2 ? 'checked' : '').' onclick="$(\'#bjqms2\').hide()"> '.dr_lang('精简').' <span></span></label>
                             &nbsp; &nbsp;
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="3" name="data[setting][option][mode2]" '.($option['mode2'] == 3 ? 'checked' : '').' onclick="$(\'#bjqms2\').show()"> '.dr_lang('自定义').' <span></span></label>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="bjqms2" '.($option['mode2'] < 3 ? 'style="display:none"' : '').'>
                    <label class="col-md-2 control-label">'.dr_lang('工具栏').'</label>
                    <div class="col-md-9">
                    <textarea name="data[setting][option][tool2]" style="height:90px;" class="form-control">'.$option['tool2'].'</textarea>
                    <span class="help-block">'.dr_lang('必须严格按照Ueditor工具栏格式\'fullscreen\', \'source\', \'|\', \'undo\', \'redo\'').'</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('移动端编辑器模式').'</label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="data[setting][option][mode3]" '.($option['mode3'] == 1 ? 'checked' : '').' onclick="$(\'#bjqms3\').hide()"> '.dr_lang('完整').' <span></span></label>
                             &nbsp; &nbsp;
                             <label class="mt-radio mt-radio-outline"><input type="radio" value="2" name="data[setting][option][mode3]" '.($option['mode3'] == 2 ? 'checked' : '').' onclick="$(\'#bjqms3\').hide()"> '.dr_lang('精简').' <span></span></label>
                             &nbsp; &nbsp;
                            <label class="mt-radio mt-radio-outline"><input type="radio" value="3" name="data[setting][option][mode3]" '.($option['mode3'] == 3 ? 'checked' : '').' onclick="$(\'#bjqms3\').show()"> '.dr_lang('自定义').' <span></span></label>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="bjqms3" '.($option['mode3'] < 3 ? 'style="display:none"' : '').'>
                    <label class="col-md-2 control-label">'.dr_lang('工具栏').'</label>
                    <div class="col-md-9">
                    <textarea name="data[setting][option][tool3]" style="height:90px;" class="form-control">'.$option['tool3'].'</textarea>
                    <span class="help-block">'.dr_lang('必须严格按照Ueditor工具栏格式\'fullscreen\', \'source\', \'|\', \'undo\', \'redo\'').'</span>
                    </div>
                </div>'.$this->attachment($option).'
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('默认存储值').'</label>
                    <div class="col-md-9">
					<textarea id="field_default_value" style="width: 90%;height: 100px;" class="form-control" name="data[setting][option][value]">'.$option['value'].'</textarea>
					<p><label>'.$this->member_field_select().'</label>
					<span class="help-block">'.dr_lang('也可以设置会员表字段，表示用当前登录会员信息来填充这个值').'</span></p>
                    </div>
                </div>
                '.$this->field_type($option['fieldtype'], $option['fieldlength']),

            '<div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('控件宽度').'</label>
                    <div class="col-md-9">
                        <label><input type="text" class="form-control" name="data[setting][option][width]" value="'.$option['width'].'"></label>
                        <span class="help-block">'.dr_lang('[整数]表示固定宽度；[整数%]表示百分比').'</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">'.dr_lang('控件高度').'</label>
                    <div class="col-md-9">
                        <label><input type="text" class="form-control" name="data[setting][option][height]" value="'.$option['height'].'"></label>
                        <label>px</label>
                    </div>
                </div>'
        ];
    }

    /**
     * 字段入库值
     */
    public function insert_value($field) {

        //$table = [];
        $value = \Phpcmf\Service::L('Field')->post[$field['fieldname']];

        // 第一张作为缩略图
        $slt = isset($_POST['data']['thumb']) && isset($_POST['is_auto_thumb_'.$field['fieldname']])  && !$_POST['data']['thumb'] && $_POST['is_auto_thumb_'.$field['fieldname']];

        // 是否下载图片
        $yct = $field['setting']['option']['down_img'] || (isset($_POST['is_auto_down_img_'.$field['fieldname']]) && $_POST['is_auto_down_img_'.$field['fieldname']]);

		// 下载远程图片
        if (($yct || $slt) && preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|png|webp))\\2/i", $value, $imgs)) {
            foreach ($imgs[3] as $img) {
                if (strpos($img, '/api/ueditor/') !== false
                    || strpos($img, '/api/umeditor/') !== false) {
                    continue;
                }
                // 下载图片
                if ($yct && strpos($img, 'http') === 0) {
                    if (dr_is_app('mfile') && \Phpcmf\Service::M('mfile', 'mfile')->check_upload(\Phpcmf\Service::C()->uid)) {
                        //用户存储空间已满
                    } else {
                        // 正常下载
                        // 判断域名白名单
                        $arr = parse_url($img);
                        $domain = $arr['host'];
                        if ($domain) {
                            $sites = \Phpcmf\Service::R(WRITEPATH.'config/domain_site.php');
                            if (isset($sites[$domain])) {
                                // 过滤站点域名
                            } elseif (strpos(SYS_UPLOAD_URL, $domain) !== false) {
                                // 过滤附件白名单
                            } else {
                                $zj = 0;
                                $remote = \Phpcmf\Service::C()->get_cache('attachment');
                                if ($remote) {
                                    foreach ($remote as $t) {
                                        if (strpos($t['url'], $domain) !== false) {
                                            $zj = 1;
                                            break;
                                        }
                                    }
                                }
                                if ($zj == 0) {
                                    // 可以下载文件
                                    /*
									if ($field['setting']['option']['down_img_type']) {
										// 异步模式
										 if (!$table) {
											$table = \Phpcmf\Service::M('field')->get_table_name(SITE_ID, $field);
										}
										$rt = \Phpcmf\Service::M('cron')->add_cron(SITE_ID, 'ueditor_down_img', [
											'url' => $img,
											'table' => $table,
											'field' => $field['fieldname'],
											'siteid' => SITE_ID,
											'member' => \Phpcmf\Service::C()->member,
											'attachment' => \Phpcmf\Service::M('Attachment')->get_attach_info(intval($field['setting']['option']['attachment'])),
											'image_reduce' => $field['setting']['option']['image_reduce'],
										]);
										if (!$rt['code']) {
											log_message('error', '远程图片下载-任务注册失败：'.$rt['msg']);
										}
										$value = str_replace($img, ROOT_THEME_PATH.'assets/images/down_img.jpg?id='.$rt['code'], $value);
										$img = '';
									} else {
                                    */
										// 同步模式
										// 下载远程文件
										$rt = \Phpcmf\Service::L('upload')->down_file([
											'url' => $img,
											'timeout' => 5,
											'watermark' => \Phpcmf\Service::C()->get_cache('site', SITE_ID, 'watermark', 'ueditor') || $field['setting']['option']['watermark'] ? 1 : 0,
											'attachment' => \Phpcmf\Service::M('Attachment')->get_attach_info(intval($field['setting']['option']['attachment'])),
										]);
										if ($rt['code']) {
											$att = \Phpcmf\Service::M('Attachment')->save_data($rt['data'], 'ueditor_down_img');
											if ($att['code']) {
												// 归档成功
                                                $value = str_replace($img, $rt['data']['url'], $value);
                                                $img = $att['code'];
											}
										}
									//}
                                }
                            }

                        }

                    }
                }
                // 缩略图
                if ($img && $slt && !\Phpcmf\Service::L('Field')->data[1]['thumb']) {
                    if (!is_numeric($img)) {
                        // 下载缩略图
                        // 判断域名白名单
                        $arr = parse_url($img);
                        $domain = $arr['host'];
                        if ($domain) {
                            $file = dr_catcher_data($img, 8);
                            if (!$file) {
                                log_message('error', '服务器无法下载文件：'.$img);
                            } else {
                                // 尝试找一找附件库
                                $att = \Phpcmf\Service::M()->table('attachment')->like('related', 'ueditor')->where('filemd5', md5($file))->getRow();
                                if ($att) {
                                    $img = $att['id'];
                                } else {
                                    // 下载归档
                                    $rt = \Phpcmf\Service::L('upload')->down_file([
                                        'url' => $img,
                                        'timeout' => 5,
                                        'watermark' => \Phpcmf\Service::C()->get_cache('site', SITE_ID, 'watermark', 'ueditor') || $field['setting']['option']['watermark'] ? 1 : 0,
                                        'attachment' => \Phpcmf\Service::M('Attachment')->get_attach_info(intval($field['setting']['option']['attachment'])),
                                        'file_content' => $file,
                                    ]);
                                    if ($rt['code']) {
                                        $att = \Phpcmf\Service::M('Attachment')->save_data($rt['data'], 'ueditor_down_img');
                                        if ($att['code']) {
                                            // 归档成功
                                            $value = str_replace($img, $rt['data']['url'], $value);
                                            $img = $att['code'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    \Phpcmf\Service::L('Field')->data[1]['thumb'] = $_POST['data']['thumb'] = $img;
                }
            }
        }

        // 去除站外链接
        if (isset($_POST['is_remove_a_'.$field['fieldname']]) && $_POST['is_remove_a_'.$field['fieldname']]
            && preg_match_all("/<a(.*)href=(.+)>(.*)<\/a>/Ui", $value, $arrs)) {
            $sites = \Phpcmf\Service::R(WRITEPATH.'config/domain_site.php');
            foreach ($arrs[2] as $i => $a) {
                if (strpos($a, ' ') !== false) {
                    list($a) = explode(' ', $a);
                }
                $a = trim($a, '"');
                $a = trim($a, '\'');
                $arr = parse_url($a);
                if ($arr && $arr['host'] && !isset($sites[$arr['host']])) {
                    // 去除a标签
                    $value = str_replace($arrs[0][$i], $arrs[3][$i], $value);
                }
            }
        }

        /*
        // 默认过滤style标签
        if (!isset($field['setting']['option']['remove_style']) || !$field['setting']['option']['remove_style']
            || (IS_ADMIN && $field['setting']['option']['remove_style'] == 1)
            || (IS_MEMBER && $field['setting']['option']['remove_style'] == 2)
        ) {
            $value = preg_replace('/<div style=".*?"/iU', '', $value);
        }*/

        // 提取描述信息
        if (isset($_POST['data']['description']) && isset($_POST['is_auto_description_'.$field['fieldname']])
            && !\Phpcmf\Service::L('Field')->data[1]['description'] && $_POST['is_auto_description_'.$field['fieldname']]) {
            \Phpcmf\Service::L('Field')->data[1]['description'] = $_POST['data']['description'] = trim(dr_strcut(dr_clearhtml($value), 200));
        }

        // 替换分页
        $value = str_replace('_ueditor_page_break_tag_', '<hr class="pagebreak">', $value);

        // 入库操作
        if (isset($_GET['is_verify_iframe']) && $_GET['is_verify_iframe']) {
            // 来自批量审核内容
            \Phpcmf\Service::L('Field')->data[$field['ismain']][$field['fieldname']] = ($value);
        } else {
            \Phpcmf\Service::L('Field')->data[$field['ismain']][$field['fieldname']] = htmlspecialchars($value);
        }
    }

    /**
     * 字段输出
     *
     * @param   array   $value  数据库值
     * @return  string
     */
    public function output($value) {
        return dr_ueditor_html($value, isset(\Phpcmf\Service::L('Field')->data['title']) ? \Phpcmf\Service::L('Field')->data['title'] : '');
    }

    /**
     * 字段显示
     *
     * @return  string
     */
    public function show($field, $value = null) {
        $html = '
        <div class="portlet  bordered light">
        <div class="portlet-body">
        <div class="scroller" style="width:'.(\Phpcmf\Service::_is_mobile() ? '100%' : ($field['setting']['option']['width'] ? $field['setting']['option']['width'].(is_numeric($field['setting']['option']['width']) ? 'px' : '') : '100%')).';height:'.($field['setting']['option']['height'] ? $field['setting']['option']['height'] : '300').'px" data-always-visible="1" data-rail-visible="1">
        '.htmlspecialchars_decode($value).'                
        </div>
        </div>
        </div>';
        return $this->input_format($field['fieldname'], $field['name'], $html);
    }

    /**
     * 字段表单输入
     *
     */
    public function input($field, $value = '') {

        // 字段禁止修改时就返回显示字符串
        if ($this->_not_edit($field, $value)) {
            return $this->show($field, $value);
        }

        // 字段存储名称
        $name = $field['fieldname'];

        // 字段显示名称
        $text = ($field['setting']['validate']['required'] ? '<span class="required" aria-required="true"> * </span>' : '').$field['name'];

        if (isset($_GET['is_verify_iframe']) && $_GET['is_verify_iframe']) {
            // 来自批量审核内容
            $str = '<textarea class="form-control"  name="data['.$name.']" id="dr_'.$name.'">'.htmlspecialchars($value).'</textarea>';
            return $this->input_format($field['fieldname'], $text, $str);
        }

        // 表单宽度设置
        $is_mobile = \Phpcmf\Service::C()->_is_mobile();
        $width = $is_mobile ? '100%' : ($field['setting']['option']['width'] ? $field['setting']['option']['width'] : '100%');

        // 表单高度设置
        $height = $field['setting']['option']['height'] ? $field['setting']['option']['height'] : '300';

        // 字段提示信息
        $tips = $field['setting']['validate']['tips'] ? '<span class="help-block" id="dr_'.$name.'_tips">'.$field['setting']['validate']['tips'].'</span>' : '';

        // 字段默认值
        $value = htmlspecialchars_decode(strlen($value) ? $value : $this->get_default_value($field['setting']['option']['value']));

        $uri = \Phpcmf\Service::L('router')->uri();
        APP_DIR != 'member' && $uri = str_replace('member/', '', $uri);

        // 输出
        $str = '';

        // 防止重复加载JS
        if (!$this->is_load_js($field['filetype'])) {
            $str.= '
            <script type="text/javascript" src="'.ROOT_URL.'api/ueditor/ueditor.config.js?v='.CMF_UPDATE_TIME.'"></script>
            <script type="text/javascript" src="'.ROOT_URL.'api/ueditor/ueditor.'.(IS_DEV ? 'all' : 'all.min').'.js?v='.CMF_UPDATE_TIME.'"></script>
            ';
            $this->set_load_js($field['filetype'], 1);
        }

        $tool = IS_ADMIN ? "'fullscreen', 'source', '|', " : ''; // 后台引用时显示html工具栏

        // 编辑器模式
        if ($is_mobile) {
            $mode = $field['setting']['option']['mode3'] ? $field['setting']['option']['mode3'] : $field['setting']['option']['mode'];
            $field['setting']['option']['tool'] = $field['setting']['option']['tool3'] ? $field['setting']['option']['tool3'] : $field['setting']['option']['tool'];
        } elseif (IS_ADMIN) {
            $mode = $field['setting']['option']['mode'];
        } else {
            $mode = $field['setting']['option']['mode2'] ? $field['setting']['option']['mode2'] : $field['setting']['option']['mode'];
            $field['setting']['option']['tool'] = $field['setting']['option']['tool2'] ? $field['setting']['option']['tool2'] : $field['setting']['option']['tool'];
        }

        // 编辑器工具
        $pagebreak = (int)$field['setting']['option']['page'] ? ', \'pagebreak\'' : '';
        switch ($mode) {
            case 3: // 自定义
                $tool.= trim($field['setting']['option']['tool'], ',').$pagebreak;
                break;
            case 2: // 精简
                $tool.= "'undo', 'redo', '|',
                        'bold', 'italic', 'underline', 'strikethrough','|', 'pasteplain', 'forecolor', 'fontfamily', 'fontsize','|', 'link', 'simpleupload'$pagebreak";
                break;
            case 1: // 完整模式

                $tool_code = \Phpcmf\Service::R(ROOTPATH.'api/ueditor/php/tool.php');
                if (!$tool_code) {
                    $tool.= "'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'attachment', 'map', 'insertframe', 'insertcode', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'print', 'preview', 'searchreplace', 'drafts'";
                } else {
                    $tool.= str_replace([PHP_EOL, chr(13), chr(10)], ' ', $tool_code);
                }
                $tool.= "$pagebreak";
                break;
        }

        $str.= "<script class=\"dr_ueditor\" name=\"data[$name]\" type=\"text/plain\" id=\"dr_$name\">$value</script>";
        $js = \Phpcmf\Service::L('js_packer');
        $str.= $js->pack("
        <script type=\"text/javascript\">
            $(function(){
                var editorOption = {
                    ismobile: ".(dr_is_mobile() ? 1 : 0).", 
                    UEDITOR_HOME_URL: \"/api/ueditor/\",
                    UEDITOR_ROOT_URL: \"".ROOT_URL."api/ueditor/\",
                    serverUrl:\"/index.php?s=api&c=file&token=".dr_get_csrf_token()."&m=ueditor&image_reduce=".intval($field['setting']['option']['image_reduce'])."&attachment=".intval($field['setting']['option']['attachment'])."&is_wm=".$field['setting']['option']['watermark']."&rid=".($uri.'/id:'.(int)$_GET['id'])."&\",
                    lang: \"".SITE_LANGUAGE."\",
                    langPath: \"".ROOT_URL."api/language/\",
                    toolbars: [
                        [ $tool ]
                    ],
                    initialContent:\"\",
                    pageBreakTag:\"_ueditor_page_break_tag_\",
                    initialFrameWidth: \"".$width."\",
                    initialFrameHeight: \"{$height}\",
                    initialStyle:\"body{font-size:14px}\",
                    autoFloatEnabled:".($field['setting']['option']['autofloat'] ? 'true' : 'false').",
                    allowDivTransToP:".(!$field['setting']['option']['div2p'] ? 'true' : 'false').",
                    autoHeightEnabled:".($field['setting']['option']['autoheight'] ? 'true' : 'false').",
                    charset:\"utf-8\",
                };
                var editor = new baidu.editor.ui.Editor(editorOption);
                editor.render(\"dr_$name\");
            });
        </script>
        ", 0);


        if ($field['setting']['option']['show_bottom_boot']) {

            $str.= '<div class="mt-checkbox-inline" style="margin-top: 10px;">';
            $str.= '     <label style="margin-bottom: 0;" class="mt-checkbox mt-checkbox-outline">
                  <input name="is_auto_thumb_'.$field['fieldname'].'" type="checkbox" '.($field['setting']['option']['tool_select_1'] ? 'checked' : '').' value="1"> 提取第一个图片为缩略图 <span></span>
                 </label>';
            $str.= '
                 <label style="margin-bottom: 0;" class="mt-checkbox mt-checkbox-outline">
                  <input name="is_auto_description_'.$field['fieldname'].'" type="checkbox" '.($field['setting']['option']['tool_select_2'] ? 'checked' : '').' value="1"> 提取前200字为描述信息 <span></span>
                 </label>';
            if (!$field['setting']['option']['down_img']) {
                $str.= '
                 <label style="margin-bottom: 0;" class="mt-checkbox mt-checkbox-outline">
                  <input name="is_auto_down_img_'.$field['fieldname'].'" type="checkbox" '.($field['setting']['option']['tool_select_3'] ? 'checked' : '').' value="1"> 下载远程图片 <span></span>
                 </label>';
            }
            $str.= '
                 <label style="margin-bottom: 0;" class="mt-checkbox mt-checkbox-outline">
                  <input name="is_remove_a_'.$field['fieldname'].'" type="checkbox" '.($field['setting']['option']['tool_select_4'] ? 'checked' : '').' value="1"> 去除站外链接 <span></span>
                 </label>';
            $str.= '</div>';
        }


        return $this->input_format($name, $text, $str.$tips);
    }
}