<?php
namespace Core\Util;
use Core\Model\Member;
use Think\Template\TagLib;

class Tags extends TagLib {
    protected $tags = array(
        'daterange'     =>      array('attr' => array('name', 'start', 'end', 'time'), 'close' => '0'),
        'date'          =>      array('attr' => array('name', 'value', 'time'), 'close' => '0'),
        'uploader'      =>      array('attr' => array('name', 'type'), 'close' => '0'),
        'editor'        =>      array('attr' => array('name', 'height', 'width'), 'close' => '1'),
        'district'      =>      array('attr' => array('name', 'with-title', 'value'), 'close' => '0'),
        
        'credits'       =>      array('attr' => array('name', 'scope', 'value'), 'close' => '0')
    );

    public function  _daterange($attr, $content) {
        $s = '';
        if (!defined('TAG_INIT_DATERANGE')) {
            $s = '
            <script type="text/javascript">
                require(["daterangepicker"], function($){
                    $(function(){
                        $(".daterange").each(function(){
                            var elm = this;
                            var withTime = $(elm).attr("data-time").toLowerCase() == "true";
                            var format = "YYYY-MM-DD";
                            if(withTime) {
                                format = "YYYY-MM-DD HH:mm"
                            }
                            $(this).daterangepicker({
                                format: format,
                                startDate: $(elm).prev().prev().val(),
                                endDate: $(elm).prev().val(),
                                timePicker: withTime,
                                timePickerIncrement: 1,
                                timePicker12Hour: false
                            }, function(start, end){
                                $(elm).find(".date-title").html(start.format(format) + " 至 " + end.format(format));
                                $(elm).prev().prev().val(start.format(format));
                                $(elm).prev().val(end.format(format));
                            });
                        });
                    });
                });
            </script>';
            define('TAG_INIT_DATERANGE', true);
        }

        $val = $attr;
        if(empty($val['time'])) {
            $val['time'] = 'false';
        }

        $s .= '
        <input name="'.$val['name'].'[start]" type="hidden" value="'. $val['start'].'" />
        <input name="'.$val['name'].'[end]" type="hidden" value="'. $val['end'].'" />
        <button class="btn btn-default daterange" data-time="'.$val['time'].'" type="button"><span class="date-title">'.$val['start'].' 至 '.$val['end'].'</span> <i class="fa fa-calendar"></i></button>
        ';
        return $s;
    }
    
    public function _date($attr, $content) {
        $s = '';
        if (!defined('TAG_INIT_DATETIME')) {
            $s = '
                <script type="text/javascript">
                    require(["datetimepicker"], function($){
                        $(function(){
                            $(".datetimepicker").each(function(){
                                var withtime = $(this).attr("data-time");
                                var opt = {
                                    language: "zh-CN",
                                    format: "yyyy-mm-dd",
                                    minView: 2,
                                    autoclose: true
                                };
                                if(withtime == "true") {
                                    opt.format = "yyyy-mm-dd hh:ii",
                                    opt.minView = 0;
                                }
                                if($(this).attr("data-birth") == "true") {
                                    opt.startView = 4;
                                }
                                $(this).datetimepicker(opt);
                            });
                            
                        });
                    });
                </script>';
            define('TAG_INIT_DATETIME', true);
        }
        $withtime = empty($attr['time']) ? 'false' : 'true';
        $birth = empty($attr['birth']) ? 'false' : 'true';
        $value = !empty($attr['value']) ? $attr['value'] : ($withtime == 'true' ? date('Y-m-d H:i') : date('Y-m-d'));
        $attrs = '';
        $tags = $this->tags['date']['attr'];
        $tags[] = 'placeholder';
        $tags[] = 'readonly';
        $tags[] = 'class';
        foreach($attr as $k => $v) {
            if(!in_array($k, $tags)) {
                $attrs .= " {$k}=\"{$v}\"";
            }
        }
    
        $s .= '<input type="text" name="' . $attr['name'] . '" value="' . $value . '" data-birth="' . $birth . '" data-time="' . $withtime . '" placeholder="请选择日期时间"  readonly="readonly" class="datetimepicker form-control ' . $attr['class'] . '" ' . $attrs . '/>';
        return $s;
    }
    
    public function _uploader($attr, $content) {
        $type = in_array($attr['type'], array('image')) ? $attr['type'] : 'image';
        if($type == 'image') {
            return $this->imageUploader($attr);
        }
    }
    
    private function imageUploader($attr) {
        $s = '';
        if(!defined('TAG_INIT_IMAGE')) {
            $s = '
                <script type="text/javascript">
                    function showImageDialog(elm, opts) {
                        require(["util"], function(util){
                            var btn = $(elm);
                            var ipt = btn.parent().prev();
                            var val = ipt.val();
                            var img = ipt.parent().next().children();
                            util.image(val, function(url){
                                img.get(0).src = url.url;
                                ipt.val(url.filename);
                            }, opts);
                        });
                    }
                </script>';
            define('TAG_INIT_IMAGE', true);
        }
        if(empty($attr['default'])) {
            $attr['default'] = __SITE__ . 'w/static/img/no-image.jpg';
        }
        $options = array();
        $options['width'] = $attr['width'];
        if(empty($options['width'])) {
            $options['width'] = 800;
        }
        if(empty($attr['title'])) {
            $attr['title'] = '选择图片';
        }

        $s .= '
            <div class="input-group">
                <input type="text" name="'.$attr['name'].'" value="'.$attr['value'].'" class="form-control" autocomplete="off">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="showImageDialog(this, \'' . base64_encode(serialize($options)) . '\');">' . $attr['title'] . '</button>
                </span>
            </div>
            <div class="input-group" style="margin-top:.5em;">
                <img src="' . attach($attr['value']) . '" onerror="this.src=\''.$attr['default'].'\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" width="150" />
            </div>';
        return $s;
    }
    
    public function _editor($attr, $content) {
        $s = '';
        if (!defined('TAG_INIT_EDITOR')) {
            $s = '
                <script type="text/javascript">
                    require(["jquery", "editor"], function($){
                        $(function(){
                            $(".input-content").each(function(){
                                var elm = this;
                                $(elm).summernote({
                                    height: $(elm).attr("data-height"),
                                    width: $(elm).attr("data-width"),
                                    lang: "zh-CN",
                                    onChange: function(contents, $editable) {
                                        $(elm).prev().val(contents);
                                    }
                                });
                                $(elm).prev().val($(elm).code());
                            });
                        });
                    });
                </script>';
            define('TAG_INIT_EDITOR', true);
        }
        $s .= <<<DOT
<div class="panel panel-default">
    <input type="hidden" name="{$attr['name']}">
    <div class="panel-body input-content" data-name="{$attr['name']}" data-width="{$attr['width']}" data-height="{$attr['height']}">{$content}</div>
</div>
<div class="help-block">
    你可以直接使用 <a href="http://xiumi.us/studio/paper" target="_blank">秀米 <span class="fa fa-external-link"></span></a>, <a href="http://wxpb.lnydhl.com/" target="_blank">易小信微信编辑器 <span class="fa fa-external-link"></span></a> 等编辑工具编辑好以后将内容复制进来
</div>
DOT;

        return $s;
    }

    public function _district($attr, $content) {
        $s = '';
        if (!defined('TAG_INIT_DISTRICT')) {
            $s = '
        <script type="text/javascript">
            require(["jquery", "district"], function($, d){
                $(function(){
                    $("div.row.district-container").each(function(){
                        var elms = {
                            province: $(this).find("select.district-state")[0],
                            city: $(this).find("select.district-city")[0],
                            district: $(this).find("select.district-district")[0],
                        };
                        var vals = {
                            province: $(this).find("select.district-state").attr("data-value"),
                            city: $(this).find("select.district-city").attr("data-value"),
                            district: $(this).find("select.district-district").attr("data-value"),
                        };
                        d.render(elms, vals);
                    });
                });
            });
        </script>';
            define('TAG_INIT_DISTRICT', true);
        }

        $names = explode(',', $attr['name'], 3);
        $name = array();
        $name['state'] = trim($names[0]);
        $name['city'] = trim($names[1]);
        $name['district'] = trim($names[2]);
        $vals = explode(',', $attr['value'], 3);
        $val = array();
        $val['state'] = trim($vals[0]);
        $val['city'] = trim($vals[1]);
        $val['district'] = trim($vals[2]);

        $s .= <<<DOC
<div class="row district-container">
<div class="col-xs-4">
    <select class="form-control district-state" name="{$name['state']}" data-value="{$val['state']}"></select>
</div>
<div class="col-xs-4">
    <select class="form-control district-city" name="{$name['city']}" data-value="{$val['city']}"></select>
</div>
<div class="col-xs-4">
    <select class="form-control district-district" name="{$name['district']}" data-value="{$val['district']}"></select>
</div>
</div>
DOC;
        return $s;
    }
    
    public function _credits($attr, $content) {
        Member::loadSettings();
        $setting = C('MS');
        $credits = $setting[Member::OPT_CREDITS];
        $ds = array();
        if(!in_array($attr['scope'], array('enabled', 'disabled', 'all'))) {
            $attr['scope'] = 'enabled';
        }
        foreach($credits as $row) {
            if(!empty($row['enabled'])) {
                if($attr['scope'] == 'enabled' || $attr['scope'] == 'all') {
                    $ds[] = $row;
                    continue;
                }
            } else {
                if($attr['scope'] == 'disabled' || $attr['scope'] == 'all') {
                    $ds[] = $row;
                    continue;
                }
            }
        }
        $s = '<select name="' . $attr['name'] . '" class="form-control"><option value="">请选择积分类型</option>';
        foreach($ds as $row) {
            $selected = '<?php echo ' . $attr['value'] . ' == "' . $row['name'] . '" ? " selected" : "" ?>';
            $s .= "<option value=\"{$row['name']}\"{$selected}>{$row['title']}</option>";
        }
        $s .= '</select>';
        return $s;
    }
}
