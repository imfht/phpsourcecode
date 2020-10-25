<?php
namespace app\common\field;

/**
 * 列表页的表格自定义字段
 */
class Table extends Base
{
    protected static $pagetype = 'table';
    
    /**
     * 把程序中定义的列表字段,转成有字母数组下标key
     * @param array $field
     * @return unknown|unknown[]
     */
    public static function num2letter($field=[]){
        if (empty($field[0])) {
            return $field;
        }
        $array = [
            'type'=>$field[2],
            'name'=>$field[0],
            'title'=>$field[1],
        ];
        if(is_array($field[3])){
            $array['array'] = $field[3];
        }elseif($field[2]=='link'){
            $array['url'] = $field[3];
            $array['target'] = $field[4];
        }elseif($field[2]=='callback'){
            $array['fun'] = $field[3];
            $array['opt'] = $field[4];
        }
        if($field[2]=='select'){   //频道模型那里的栏目不能选择本模型之外的栏目
            $array['sys'] = $field[4];
        }
        return $array;
    }
    
    /**
     * 取得某个字段的表单HTML代码
     * @param array $field 具体某个字段的配置参数, 只能是数据库中的格式,不能是程序中定义的数字下标的格式
     * @param array $info 信息内容
     * @return string[]|unknown[]|mixed[]
     */
    public static function get_tab_field($field=[],$info=[]){
        
        $field = self::num2letter($field);
        
        $name = $field['name'];
        $field_value = $info[$name];
        
        if(empty($info)){   //表格头部标题使用
            return [
                'type'=>$field['type'],
                'name'=>$name,
                'title'=>$field['title'],
                'value'=>'',
            ];
        }
        
        if ( ($show = self::get_item($field['type'],$field,$info)) !='' ) {    //个性定义的表单模板,优先级最高
            
        }elseif ($field['type'] == 'username') {
            $_ar = get_user($field_value);
            $show = $_ar?"<a href='".get_url('user',$field_value)."' target='_blank'>{$_ar['username']}</a>":'';
        }elseif ($field['type'] == 'image') {
            $field_value = tempdir($field_value);
            $show = $field_value?"<a href='".$field_value."' target='_blank'><img class='listimg' src='{$field_value}' width='50' height='50' /></a>":'';
        }elseif ($field['type'] == 'link') {
            //$field['url'] = str_replace('__id__', $info['id'], $field['url']);
            $field['url'] = preg_replace_callback('/__([\w]+)__/i',function($ar)use($info){return $info[$ar[1]]; }, $field['url']);
            $show = "<a href='{$field['url']}' target='{$field['target']}'>$field_value</a>";
        }elseif($field['type'] == 'select'){
            if (ENTRANCE==='member') {
                $show = $field['array'][$field_value];
            }else{
                $mid = 0;
                if($field['sys'] && sort_config($field['sys'])){    //频道模型那里的栏目不能选择本模型之外的栏目
                    $sort_arrray =sort_config($field['sys']);
                    foreach($sort_arrray AS $rs){
                        if($rs['id']==$field_value){
                            $mid = $rs['mid'];
                        }
                    }
                    if($mid){
                        foreach ($field['array'] AS $key=>$v){
                            if($sort_arrray[$key]['mid']!=$mid){
                                unset($field['array'][$key]);
                            }
                        }
                    }
                }
                $show = "<select class='select_edit' data-name='$name' data-value='{$field_value}' data-id='{$info['id']}'>";
                foreach($field['array'] AS $key=>$v){
                    $select = $field_value==$key ? 'selected' : '' ;
                    $show .="<option value='$key' $select>$v";
                }
                $show .= "</select>";
            }
        }elseif($field['type'] == 'checkbox'){  //多选项
            $detail = explode(',',$field_value);
            foreach($detail AS $key=>$value){
                if ($value=='') {
                    unset($detail[$key]);
                    continue;
                }
                $detail[$key] = $field['array'][$value];
            }
            $show = implode('、', $detail);
        }elseif($field['type'] == 'yesno'){
            $show = $field_value>0 ? "<i class='fa fa-check-circle' style='color:orange;font-size:16px;'></i>": "<i style='color:#888;' class='glyphicon glyphicon-ban-circle'></i>" ;
        }elseif($field['type'] == 'switch'){
            $show = "data-value='{$field_value}' data-name='$name' data-id='{$info['id']}'";
            $show = $field_value ? "<i $show class='fa fa-check-circle _switch' title='更改状态' style='color:green;font-size:20px;cursor:pointer;'></i>": "<i $show title='更改状态' style='font-size:20px;cursor:pointer;' class='fa fa-ban _switch'></i>" ;
        }elseif($field['type'] == 'icon'){
            $show = $field_value?"<i class='{$field_value}'></i>":'';
        }elseif($field['type'] == 'select2'){
            $show = $field['array'][$field_value];
        }elseif($field['type'] == 'datetime'){
            $show = format_time($field_value,'Y-m-d H:i');
        }elseif($field['type'] == 'date'){
            $show = format_time($field_value,'Y-m-d');
        }elseif($field['type'] == 'time'){
            $show = format_time($field_value,'H:i');
        }elseif($field['type'] == 'text.edit'){
            $size = 8;
            $_class = '_num';
            if(!is_numeric($field_value)){
                $size = '15';
                $_class = '_string';
            }
            $show = "<input type='text' class='quick_edit {$_class}' data-value='{$field_value}' data-name='$name' data-id='{$info['id']}' name='{$name}[{$info['id']}]' size='$size' value='{$field_value}'>";
        }elseif($field['type'] == 'callback'){
            //$qs 这个参数将要统一为$info的值,不允许再定义为其它值,也即下面几行将要弃用
            $field['opt'] = str_replace('__','',$field['opt']);
            if($field['opt']=='data'||empty($field['opt'])){
                $qs = $info;
            }else{
                $qs = isset($info[$field['opt']])?$info[$field['opt']]:$info;
            }
            $show = $field['fun']($field_value,$qs,$field['opt']);
        }else{
            $show = $info[$name];
        }
        
        return [
            'type'=>$field['type'],
            'name'=>$name,
            'title'=>$field['title'],
            'value'=>$show,
        ];
    }
    
    /**
     * 右边菜单
     * @param array $btns
     * @param array $info
     * @return string[][]|unknown[][]
     */
    public static function get_rbtn($btns=[],$info=[],$show_title=false){
        $data = [];
        foreach($btns AS $rs){
            if($rs['type']=='callback'){
                $array = $rs['fun']($info);
                if (is_array($array)) {
                    $data[] = $array;
                }else{
                    $data[] = [
                        'title'=>$rs['title'],
                        'value'=>$array,
                    ];
                }
                
            }else{
                $rs['icon'] || $rs['icon']='glyphicon glyphicon-menu-hamburger';
                $rs['href'] || $rs['href']=$rs['url'];
                //$rs['href'] = str_replace('__id__', $info['id'], $rs['href']);
                $rs['href'] = preg_replace_callback('/__([\w]+)__/i',function($ar)use($info){return $info[$ar[1]]; }, $rs['href']);
                $alert = $rs['type']=='delete' ? ' class="_dels" onclick="if(typeof(delete_one)==\'function\'){return delete_one($(this).attr(\'href\').split(\'/ids/\')[1].split(\'.\')[0]);}else{return confirm(\'你确实要删除吗?不可恢复!\')}"' : ' ';
                $target = $rs['target']?" target='{$rs['target']}' ":'';
                $data[] = [
                    'title'=>$rs['title'],
                    'value'=>"<a href='{$rs['href']}' title='{$rs['title']}' $alert $target><i class='{$rs['icon']}'></i> ".($show_title?$rs['title']:'')."</a>",
                    ];
            }
        }
        return $data;
    }
    
    /**
     * 后台列表数据的  搜索 字段
     * @param number $mid
     * @return \app\common\field\unknown[]
     */
    public static function get_search_field($mid=0){
        $array = [];
        $field_array = get_field($mid);
        foreach ($field_array AS $rs){
            if(!$rs['ifsearch']){
                continue;
            }
            if(in_array($rs['type'], ['radio','select','checkbox'])){
                continue;
            }
            $rs['options'] && $rs['options'] = str_array($rs['options']);
            $array[$rs['name']] = $rs['title'];
            
        }
        return $array;
    }
    
    /**
     * 后台列表数据的  筛选 字段
     * @param number $mid 模型ID
     * @param string $if_filtrate 是否为筛选字段
     * @return unknown[]
     */
    public static function get_filtrate_field($mid=0)
    {
        $array = [];
        $field_array = get_field($mid);
        foreach ($field_array AS $rs){
            if(!$rs['ifsearch']){
                continue;
            }
            if(!in_array($rs['type'], ['radio','select','checkbox'])){
                continue;
            }
            $rs['options'] && $rs['options'] = str_array($rs['options']);
            $array[$rs['name']] = $rs['options'];
            
        }
        return $array;
    }
    
    /**
     * 获取列表页面要显示的自定义字段
     * @return unknown[][]|string[][]|mixed[][]
     */
    public static function get_list_field($mid=0,$field_array=[])
    {
        $array = [];
        $field_array || $field_array = get_field($mid);
        
        foreach ($field_array AS $rs){
            if(!$rs['listshow']){
                continue;
            }
            //$rs['options'] && $rs['options'] = str_array($rs['options']);
            $rs['options'] && $rs['options'] = static::options_2array($rs['options']);
            if(in_array($rs['type'], ['radio','select','checkbox'])){
                $type = 'select';
            }elseif($rs['type']=='image'){
                $type = 'image';
            }elseif($rs['type']=='images'){
                $type = 'image';
            }elseif(in_array($rs['type'], ['textarea','ueditor'])){
                $type = 'textarea';
            }elseif($rs['type']=='datetime'){
                $type = 'datetime';
            }elseif($rs['type']=='date'){
                $type = 'date';
            }elseif($rs['type']=='time'){
                $type = 'time';
            }else{
                $type = 'text';
            }
            if($rs['name']=='title'){
                $array[] = ['title', $rs['title'], 'link',iurl('content/show',['id'=>'__id__']),'_blank'];
            }else{
                $array[] = [
                    $rs['name'],
                    $rs['title'],
                    $type,
                    $rs['options'],
                ];
            }
        }
        return $array;
    }
    
}
