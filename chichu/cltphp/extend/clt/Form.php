<?php
namespace clt;
use clt\Leftnav;
class Form{
    public $data = array();

    public function __construct($data=array()) {
        $this->data = $data;
    }
    public function catid($info,$value){
        $validate = getvalidate($info);

        $list = db('category')->select();;
        foreach ($list as $lk=>$v){
            $category[$v['id']] = $v;
        }
        $id = $field = $info['field'];
        $value = $value ? $value : $this->data[$field];
        $moduleid =$info['moduleid'];
        foreach ($category as $r){
            if($r['type']==1){
                continue;
            }
            $arr= explode(",",$r['arrchildid']);
            $show=0;
            foreach((array)$arr as $rr){
                if(isset($category[$rr]['moduleid'])){
                    if($category[$rr]['moduleid']==$moduleid){
                        $show=1;
                    }
                }
            }
            if(empty($show)){
                continue;
            }
            if($r['child']){
                $r['disabled'] = ' disabled';
            }else{
                $r['disabled'] = ' ';
            }
            $array[] = $r;
        }
        $str  = "<option value='\$id' \$disabled \$selected>\$spacer \$catname</option>";
        $tree = new Tree ($array);
        $parseStr = '<select  id="'.$id.'" lay-verify="required" name="'.$field.'"  '.$validate.'>';
        $parseStr .= '<option value="">请选择'.$info['name'].'</option>';
        $parseStr .= $tree->get_tree(0, $str, $value);
        $parseStr .= '</select>';
        return $parseStr;
    }

    public function title($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $style=$info['setup']['style'];
        $thumb=$info['setup']['thumb'];
        $field = $info['field'];
        $name = $info['name'];
        $value = $value ? $value : (isset($this->data[$field])?$this->data[$field]:'');
        $style_color='';
        $style_bold='';
        if(isset($this->data['thumb'])){
            $title_thumb=$this->data['thumb'];
        }else{
            $title_thumb='';
        }
        if(array_key_exists('title_style',$this->data)){
            if($this->data['title_style']){
                $title_style = explode(';',$this->data['title_style']);
                $style_color = explode(':',$title_style[0]);
                $style_bold = explode(':',$title_style[1]);
                $style_color = $style_color[1];
                $style_bold = $style_bold[1];
            }
        }
        $boldchecked= $style_bold=='bold' ? 'checked' : '';
        $titleThumb =$title_thumb?$title_thumb:"/static/admin/images/tong.png";
        if(empty($info['setup']['upload_maxsize'])){
            $info['setup']['upload_maxsize'] =  intval(byte_format(config('attach_maxsize')));
        }
        if($info['pattern']!='defaul'){
            $pattern='|'.$info['pattern'];
        }else{
            $pattern='';
        }
        $parseStr   = '<input type="text" name="'.$field.'" data-required="'.$info['required'].'" value="'.$value.'" data-min="'.$info['minlength'].'" data-max="'.$info['maxlength'].'" data-errormsg="'.$info['errormsg'].'" title="'.$name.'" placeholder="请输入'.$name.'" lay-verify="defaul'.$pattern.'" class="'.$info['class'].' layui-input"/> ';
        $stylestr ='</div>';
        if($info['required']==1){
            $stylestr .='<div class="layui-form-mid layui-word-aux red">*必填</div>';
        }
        $stylestr .='</div>';

        //标题颜色及是否加粗
        $stylestr .='<div class="layui-form-item"><label class="layui-form-label">标题颜色</label>';
        $stylestr .='<div class="layui-input-4"><input type="text" name="style_color" id="style_color" value="'.$style_color.'"/></div></div>';
        $stylestr .='<div class="layui-form-item"><label class="layui-form-label">加粗</label>';
        $stylestr .='<div class="layui-input-4"><input type="checkbox" name="style_bold" value="bold" '.$boldchecked.' title="加粗">';

        //缩略图
        $thumbstr ='</div></div>';
        $thumbstr .='<div class="layui-form-item"><label class="layui-form-label">缩略图</label>';
        $thumbstr .='<div class="layui-input-4"><input type="hidden" name="thumb" id="thumb" value="'.$title_thumb.'"><div class="layui-upload">';
        $thumbstr .='<button type="button" class="layui-btn layui-btn-primary" id="thumbBtn"><i class="icon icon-upload3"></i>点击上传</button><button type="button" id="clearThumb" class="layui-btn">取消</button>';
        $thumbstr .='<div class="layui-upload-list"><img class="layui-upload-img" id="cltThumb" src="'.$titleThumb.'"><p id="thumbText"></p>';
        $thumbstr .='</div></div>';
        if($style){
            $parseStr = $parseStr.$stylestr;
        }
        if($thumb){
            $parseStr = $parseStr.$thumbstr;
        }
        return $parseStr;
    }

    public function text($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $field = $info['field'];
        $name = $info['name'];

        $info['setup']['ispassword'] ? $inputtext = 'password' : $inputtext = 'text';
        $action = ACTION_NAME;
        if($action=='add'){
            $value = $value ? $value : $info['setup']['default'];
        }else{
            $value = $value ? $value : $this->data[$field];
        }
        $pattern='';
        if($info['pattern']!='defaul'){
            $pattern='|'.$info['pattern'];
        }
        $parseStr   = '<input type="'.$inputtext.'" data-required="'.$info['required'].'" min="'.$info['minlength'].'" max="'.$info['maxlength'].'" errormsg="'.$info['errormsg'].'" title="'.$name.'" placeholder="请输入'.$name.'" lay-verify="defaul'.$pattern.'" class="'.$info['class'].' layui-input" name="'.$field.'" value="'.$value.'" /> ';
        if($info['required']==1){
            $parseStr .='</div>';
            $parseStr .='<div class="layui-form-mid layui-word-aux red">*必填';
        }
        return $parseStr;
    }

    public function textarea($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $field = $info['field'];
        $name = $info['name'];
        if($info['pattern']!='defaul'){
            $pattern='|'.$info['pattern'];
        }
        $action = ACTION_NAME;
        if($action=='add'){
            $value = $value ? $value : $info['setup']['default'];
        }else{
            $value = $value ? $value : $this->data[$field];
        }

        $parseStr   = '<textarea data-required="'.$info['required'].'" min="'.$info['minlength'].'" max="'.$info['maxlength'].'" errormsg="'.$info['errormsg'].'" title="'.$name.'" placeholder="请输入'.$name.'" lay-verify="defaul'.$pattern.'"  class="'.$info['class'].' layui-textarea" name="'.$field.'" />'.$value.'</textarea>';
        if($info['required']==1){
            $parseStr .='</div>';
            $parseStr .='<div class="layui-form-mid layui-word-aux red">*必填';
        }
        return $parseStr;
    }

    public function editor($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $field = $info['field'];
        $name = $info['name'];
        $pattern = ($info['pattern']!='defaul')?'|'.$info['pattern']:'';
        $action = ACTION_NAME;
        if($action=='add'){
            $value = $value ? $value : (isset($info['setup']['default'])?$info['setup']['default']:'');
        }else{
            $value = $value ? $value : (isset($this->data[$field])?$this->data[$field]:'') ;
        }
        if($info['setup']['edittype']=='UEditor'){
            //配置文件
            $str ='';
            $str .='<input type="hidden" id="editType" value="1">';
            $str .='<textarea name="'.$field.'" class="'.$info['class'].'" id="'.$info['class'].'">'.$value.'</textarea>';
            $str .='<script>var editor = new UE.ui.Editor();editor.render("'.$info['class'].'");</script>';
        }else if($info['setup']['edittype']=='nkeditor') {
            $str ='<div class="layui-col-md12">';
            $str .='<textarea name="'.$info['class'].'" id="'.$info['class'].'text" style="width:100%;height:200px;visibility:hidden;">'.$value.'</textarea></div>';
            $str .='<script>
                        KindEditor.ready(function(K) {
                            K.create(\'textarea[name="'.$info['class'].'"]\', {
                                uploadJson : "'.url("admin/UpFiles/editimg").'",
                                fileManagerJson : K.basePath+\'php/default/file_manager_json.php\',
                                imageSearchJson : K.basePath+\'php/default/image_search_json.php\', //图片搜索url
                                imageGrapJson : K.basePath+\'php/default/image_grap_json.php\', //抓取选中的搜索图片地址
                                allowFileManager : true,
                                allowImageUpload : true,
                                allowMediaUpload : true,
                                themeType : "grey", //主题
                                //错误处理 handler
                                errorMsgHandler : function(message, type) {
                                    try {
                                        JDialog.msg({type:type, content:message, timer:2000});
                                    } catch (Error) {
                                        alert(message);
                                    }
                                }
                            });
                        });
                        </script>';
        }else{
            $str ='<div class="layui-col-md12">';
            $str .='<textarea name="'.$info['class'].'" id="'.$info['class'].'" style="height:300px;width: 100%;">'.$value.'</textarea></div>';
            $str .='<script>
                        var editor = new wangEditor("'.$info['class'].'");
                        editor.config.uploadImgUrl = "'.url("admin/UpFiles/editUpload").'";
                        editor.create();
                        </script>';
        }
        return $str;
    }

    public function datetime($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $field = $info['field'];
        $name = $info['name'];
        $action = ACTION_NAME;
        if($action=='add'){
            $value = $value ? $value : '';
        }else{
            $value = $value ? $value : $this->data[$field];
        }
        $value = $value ?  toDate($value,"Y-m-d H:i:s") : toDate(time(),"Y-m-d H:i:s");

        $parseStr = '<input type="datetime" title="'.$name.'" name="'.$field.'" data-required="'.$info['required'].'" placeholder="请输入'.$name.'" value="'.$value.'" class="'.$info['class'].' layui-input" id="'.$field.'">';
        if($info['required']==1){
            $parseStr .='</div>';
            $parseStr .='<div class="layui-form-mid layui-word-aux red">*必填';
        }
        $parseStr .='<script>
        layui.use("laydate", function () {
            var laydate = layui.laydate;
            laydate.render({
                elem: "#'.$field.'",
                type:"datetime",
                format:"yyyy-MM-dd HH:mm:ss"
            });
        })
        </script>';
        return $parseStr;
    }

    public function number($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $id = $field = $info['field'];
        $validate = getvalidate($info);
        if(isset($info['setup']['ispassowrd'])){
            $inputtext = 'passowrd';
        }else{
            $inputtext = 'text';
        }
        $action = ACTION_NAME;
        if($action=='add'){
            $value = $value ? $value : $info['setup']['default'];
        }else{
            $value = $value ? $value : $this->data[$field];
        }
        if(isset($info['setup']['size'])){
            $size = $info['setup']['size'];
        }else{
            $size = "";
        }
        $parseStr   = '<input type="'.$inputtext.'"   class="input-text '.$info['class'].' layui-input" name="'.$field.'"  id="'.$id.'" value="'.$value.'" size="'.$size.'"  '.$validate.'/> ';
        return $parseStr;
    }

    public function select($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $id = $field = $info['field'];
        $validate = getvalidate($info);
        $action = ACTION_NAME;
        if($action=='add'){
            $value = $value ? $value :(isset($info['setup']['default'])?$info['setup']['default']:'') ;
        }else{
            if(array_key_exists($field,$this->data)){
                $value = $value ? $value : $this->data[$field];
            }else{
                $value = '';
            }
        }
        if($value != '') $value = strpos($value, ',') ? explode(',', $value) : $value;
        if(isset($info['options'])){
            $optionsarr = $info['options'];
        }else{
            $options    = $info['setup']['options'];
            $options = explode("\n",$info['setup']['options']);
            foreach($options as $r) {
                $v = explode("|",$r);
                $k = trim($v[1]);
                $optionsarr[$k] = $v[0];
            }
        }
        if(!empty($info['setup']['multiple'])) {
            $onchange = '';
            if(isset($info['setup']['onchange'])){
                $onchange = $info['setup']['onchange'];
            }
            $parseStr = '<select id="'.$id.'" name="'.$field.'"  onchange="'.$onchange.'" class="'.$info['class'].'"  '.$validate.' size="'.$info['setup']['size'].'" multiple="multiple" ><option value=""></option>';
        }else {
            $onchange = '';
            if(isset($info['setup']['onchange'])){
                $onchange = $info['setup']['onchange'];
            }
            $parseStr = '<select id="'.$id.'" name="'.$field.'" onchange="'.$onchange .'"  class="'.$info['class'].'" '.$validate.'>';
        }

        if(is_array($optionsarr)) {
            foreach($optionsarr as $key=>$val) {
                if(!empty($value)){
                    $selected='';
                    if(is_array($value)){
                        if(in_array($key,$value)){
                            $selected = ' selected="selected"';
                        }
                    }else{
                        if($value==$key){
                            $selected = ' selected="selected"';
                        }
                    }
                    $parseStr   .= '<option '.$selected.' value="'.$key.'">'.$val.'</option>';
                }else{
                    $parseStr   .= '<option value="'.$key.'">'.$val.'</option>';
                }
            }
        }
        $parseStr   .= '</select>';
        return $parseStr;
    }

    public function checkbox($info,$value){

        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $id = $field = $info['field'];
        $validate = getvalidate($info);
        $action = ACTION_NAME;
        if($action=='add'){
            $value = $value ? $value : $info['setup']['default'];
        }else{
            $value = $value ? $value : $this->data[$field];
        }
        $labelwidth = $info['setup']['labelwidth'];


        if(is_array($info['options'])){
            $optionsarr = $info['options'];
        }else{
            if($info['setup']['options']){   //判断选项列表内的值存在，按照之前的方法走
                $options = $info['setup']['options'];
                $options = explode("\n",$info['setup']['options']);
                foreach($options as $r) {
                    $v = explode("|",$r);
                    $k = trim($v[1]);
                    $optionsarr[$k] = $v[0];
                }
            }else{   //选项列表为空，查找[字段名]的数据库数据，$c['id']为 选项的id，$c['typename']为 选项的名称
                $class = db($info['field'])->order('sort asc')->select();
                foreach ($class as $c) {
                    $optionsarr[$c['id']] = $c['typename'];
                }
            }
        }
        if($value != '') $value = strpos($value, ',') ? explode(',', $value) : array($value);
        $i = 1;
        $parseStr ='';
        foreach($optionsarr as $key=>$r) {
            $key = trim($key);
            if($i>1){
                $validate='';
            }
            $checked = ($value && in_array($key, $value)) ? 'checked' : '';
            $parseStr .= '<input name="'.$field.'['.$i.']" id="'.$id.'_'.$i.'" '.$checked.' value="'.htmlspecialchars($key).'"  '.$validate.' type="checkbox" class="ace" title="'.htmlspecialchars($r).'">';
            $i++;
        }
        return $parseStr;

    }

    public function radio($info,$value){
        $info['setup'] = is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $id = $field = $info['field'];

        $action = ACTION_NAME;
        if ($action == 'add') {
            $value = $value ? $value : $info['setup']['default'];
        } else {
            $value = $value ? $value : $this->data[$field];
        }
        $parseStr='';
        if (isset($info['options'])) {
            if (is_array($info['options'])) {
                $optionsarr = $info['options'];
            }
        } else if (isset($info['setup']['options'])) {
            $options = $info['setup']['options'];
            $options = explode("\n",$info['setup']['options']);
            foreach($options as $r) {
                $v = explode("|",$r);
                $k = trim($v[1]);
                $optionsarr[$k] = $v[0];
            }
        }else{   //选项列表为空，查找[字段名]的数据库数据，$c['id']为 选项的id，$c['typename']为 选项的名称
            $class = db($info['field'])->order('sort asc')->select();
            foreach ($class as $c) {
                $optionsarr[$c['id']] = $c['typename'];
            }
        }
        $i = 1;
        foreach($optionsarr as $key=>$r) {

            $checked = trim($value)==trim($key) ? 'checked' : '';
            if(empty($value) && empty($key) ){
                $checked = 'checked';
            }
            $parseStr .= '<input name="'.$field.'" id="'.$id.'_'.$i.'" '.$checked.' value="'.$key.'" type="radio" class="ace" title="'.$r.'" />';
            $i++;
        }
        return $parseStr;
    }

    public function groupid($info,$value){
        $newinfo = $info;
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $list = db('role')->select();;
        foreach ($list as $lk=>$v){
            $groups[$v['id']] = $v;
        }
        $options=array();
        foreach($groups as $key=>$r) {
            if($r['status']){
                $options[$key]=$r['name'];
            }
        }
        $newinfo['options']=$options;
        $fun=$info['setup']['inputtype'];
        return $this->$fun($newinfo,$value);
    }

    public function posid($info,$value){
        $newinfo = $info;
        $list = db('posid')->select();
        foreach ($list as $lk=>$v){
            $posids[$v['id']] = $v;
        }

        $options=array();
        $options[0]= "请选择";
        foreach($posids as $key=>$r) {
            $options[$key]=$r['name'];
        }
        $newinfo['options']=$options;
        if(isset($info['setup']['inputtype'])){
            $fun=$info['setup']['inputtype'];
        }
        return $this->select($newinfo,$value);
    }

    public function typeid($info,$value){
        $newinfo = $info;
        $list = db('type')->select();
        foreach ($list as $lk=>$v){
            $types[$v['id']] = $v;
        }

        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);

        $pid=$info['setup']['default'];

        $options=array();
        $options[0]= '请选择';
        foreach($types as $key=>$r) {
            if($r['pid']!=$pid || empty($r['status'])) continue;
            $options[$key]=$r['name'];
        }
        $newinfo['options']=$options;
        $fun=$info['setup']['inputtype'];
        return $this->$fun($newinfo,$value);
    }

    public function template($info,$value){
        $templates= template_file(MODULE_NAME);
        $newinfo = $info;
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $options=array();
        $options[0]= "请选择";
        if($templates){
            foreach($templates as $key=>$r) {
                if(strstr($r['value'],'_show')){
                    $options[$r['value']]=$r['filename'];
                }
            }
        }
        $newinfo['options']=$options;
        //$fun=$info['setup']['inputtype'];
        return $this->select($newinfo,$value);
    }

    public function image($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $field = $info['field'];
        $action = ACTION_NAME;
        if($action=='add'){
            $value =$value?__PUBLIC__.$value:"/static/admin/images/tong.png";
        }else{
            if($this->data[$field]){
                $value = $value ?$value : $this->data[$field];
            }else{
                $value = "/static/admin/images/tong.png";
            }
        }
        $upload_allowext = isset($info['setup']['upload_allowext'])?$info['setup']['upload_allowext']:'';
        $thumbstr ='<div class="layui-input-4"><input type="hidden" name="'.$field.'" id="'.$field.'Val" value="'.(isset($this->data[$field])?$this->data[$field]:"").'"><div class="layui-upload">';
        $thumbstr .='<button type="button" class="layui-btn layui-btn-primary" id="'.$info['class'].'"><i class="icon icon-upload3"></i>点击上传</button>';
        $thumbstr .='<div class="layui-upload-list"><img class="layui-upload-img" id="'.$info['class'].'Img" src="'.$value.'"><p id="thumbText"></p>';
        $thumbstr .='</div></div></div>';
        $thumbstr.="<script> 
                        layui.use('upload', function () {
                            var upload = layui.upload;
                            upload.render({
                                elem:'#".$info['class']."', 
                                url: '".url('upFiles/upload')."',
                                title: '上传图片',
                                ext: '".$upload_allowext."', 
                                done: function(res){
                                    $('#".$field."Img').attr('src', res.url);
                                    $('#".$field."Val').val(res.url);
                                }
                            });
                        });
                    </script>";
        return $thumbstr;
    }

    public function images($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $field = $info['field'];
        $action = ACTION_NAME;
        if($action=='add'){
            $value = $value ? $value : $info['setup']['default'];
        }else{
            $value = $value ? $value : $this->data[$field];
        }
        $data='';
        $i=0;
        if($value){
            $options = explode(";",$value);
            if(is_array($options)){
                foreach($options as  $r) {
                    $data .='<div class="layui-col-md3"><div class="dtbox"><img src="'.$r.'" class="layui-upload-img"><input type="hidden" name="'.$info['class'].'[]" class="'.$info['class'].'" value="'.$r.'"><i class="delimg layui-icon">&#x1006;</i></div></div>';
                }
            }
        }
        $parseStr   = '<div id="images" class="images"></div><div id="upImg" class="upImg" data-i="'.$i.'">'.$data.'</div>';
        $parseStr   = '<div class="layui-upload">';
        $parseStr   .= '<button type="button" class="layui-btn" id="'.$info['class'].'">多图片上传</button>';
        $parseStr   .= '<blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 10px;">';
        $parseStr   .= '预览图：<div class="layui-upload-list" id="demo'.$info['class'].'"><div class="layui-row layui-col-space10">'.$data.'</div></div> </blockquote></div>';

        $parseStr.="<script>
                        layui.use('upload', function () {
                            var upload = layui.upload;
                            var imagesSrc;
                            upload.render({
                                elem: '#" . $info['class'] . "',
                                url: '".url('UpFiles/upImages')."',
                                multiple: true,
                                done: function(res){
                                    $('#demo" . $info['class'] . " .layui-row').append('<div class=\"layui-col-md3\"><div class=\"dtbox\"><img src=\"'+ res.src +'\" class=\"layui-upload-img\"><input type=\"hidden\" class=\"imgVal\" name=\"".$info['class']."[]\" value=\"'+ res.src +'\"> <i class=\"delimg layui-icon\">&#x1006;</i></div></div>');
                                    imagesSrc +=res.src+';';
                                }
                            });
                        })
                    </script>";
        return $parseStr;
    }
    public function file($info,$value){
        $info['setup']=is_array($info['setup']) ? $info['setup'] : string2array($info['setup']);
        $field = $info['field'];
        $action = ACTION_NAME;
        $ext='';
        if(isset($this->data[$field])){
            $fileArr=explode('.',$this->data[$field]);
            $ext=$fileArr[1];
            $dataField = $this->data[$field];
        }else{
            $dataField ='';
        }
        if($action=='add' or $ext==''){
            $value ="/static/common/images/file.png";
        }else{
            $value = "/static/common/images/".$ext.".png";
        }
        $thumbstr ='<div class="layui-input-4"><input type="hidden" name="'.$field.'" id="'.$field.'fval" value="'.$dataField.'"><div class="layui-upload">';
        $thumbstr .='<button type="button" class="layui-btn layui-btn-primary" id="'.$info['class'].'"><i class="icon icon-upload3"></i>点击上传</button>';
        $thumbstr .='<div class="layui-upload-list"><img class="layui-upload-img" id="'.$info['class'].'File" src="'.$value.'"><p id="thumbText"></p>';
        $thumbstr .='</div></div></div>';
        $thumbstr.="<script> 
                        layui.use('upload', function () {
                            var upload = layui.upload;
                            upload.render({
                                elem:'#".$info['class']."', 
                                accept:'file',
                                url: '".url('upFiles/file')."',
                                title: '上传文件',
                                ext: '".$info['setup']['upload_allowext']."', 
                                done: function(res){
                                    $('#".$field."File').attr('src', '/static/common/images/'+res.ext+'.png');
                                    $('#".$field."fval').val(res.url);
                                }
                            });
                        });
                    </script>";
        return $thumbstr;
    }
    public function linkage($info){
        $field = $info['field'];
        $value = '';
        if($this->data[$field]){
            $value = explode(',',$this->data[$field]);
        }
        $region = db('region')->where(['pid'=>1])->select();
        $html='<div class="layui-input-inline">';
        $html .='<select name="'.$field.'[]" id="province" lay-filter="province">';
        $html .='<option value="">请选择省</option>';
        foreach ($region as $k=>$v){
            if($value[0] == $v['id']){
                $html .='<option selected value="'.$v['id'].'">'.$v['name'].'</option>';
            }else{
                $html .='<option value="'.$v['id'].'">'.$v['name'].'</option>';
            }
        }
        $html .='</select>';
        $html .='</div>';

        $city ='';
        if($value[0]){
            $city = db('region')->where(['pid'=>$value[0]])->select();
        }

        $html .='<div class="layui-input-inline">';
        $html .='<select name="'.$field.'[]" id="city" lay-filter="city">';
        $html .='<option value="">请选择市</option>';
        if($city){
            foreach ($city as $k=>$v){
                if($value[1] == $v['id']){
                    $html .='<option selected value="'.$v['id'].'">'.$v['name'].'</option>';
                }else{
                    $html .='<option value="'.$v['id'].'">'.$v['name'].'</option>';
                }
            }
        }

        $html .='</select>';
        $html .='</div>';

        $district ='';
        if($value[1]){
            $district = db('region')->where(['pid'=>$value[1]])->select();
        }


        $html .='<div class="layui-input-inline">';
        $html .='<select name="'.$field.'[]" id="district" lay-filter="district">';
        $html .='<option value="">请选择县/区</option>';

        if($district){
            foreach ($district as $k=>$v){
                if($value[2] == $v['id']){
                    $html .='<option selected value="'.$v['id'].'">'.$v['name'].'</option>';
                }else{
                    $html .='<option value="'.$v['id'].'">'.$v['name'].'</option>';
                }
            }
        }

        $html .='</select>';
        $html .='</div>';
        return $html;
    }
}
?>