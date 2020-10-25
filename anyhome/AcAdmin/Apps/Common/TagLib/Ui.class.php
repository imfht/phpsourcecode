<?php    
namespace Common\TagLib;
use Think\Template\TagLib;
defined('THINK_PATH') or exit();
class Ui extends TagLib {
    protected $tags = array(
        'col' => array('attr' => 'col,cls','level'=>5,'alias'=>'iterate'),
        'field' => array('attr' => 'col,cls','level'=>5,'alias'=>'iterate'),
    );

    function toDatetime( $time, $format = 'Y-m-d H:i:s' ) {
        if ( empty ( $time ) ) {
            return "";
        }
        if ( is_numeric( $time ) ) {
            return date( $format, $time );
        }
        $format = str_replace( '#', ':', $format );
        return date( $format, strtotime( $time ) );
    }

    function toDate($time, $format = 'Y-m-d' ) {
        if ( empty ( $time ) ) {
            return $time;
        }
        $format = str_replace( '#', ':', $format );
        return $time;
    }

    function getAllForm($tb='')
    {
        $tb = F($tbName);
        return $tb;
    }


    function getFieldInfo($tb='',$field='',$scene = 'Common')
    {
        $tb = F($tb);
        $rtb = $tb[$scene];
        return $rtb[$field];
    }

    function select($attr,$info){
        extract($attr);
        $opt = '';
        $list_str = str_replace("\n", "-", $info['list']);
        $list_str = str_replace("：", ":", $list_str);
        $list_arr = explode('-',$list_str);

        foreach ($list_arr as $k) {
            $tmp_arr = explode(':',$k);
            $v =  array();
            $v['value'] = $tmp_arr[0];
            $v['label'] = $tmp_arr[1];
            $list_data[] = $v;
        }

        foreach ($list_data as $k) {
            $tmp_arr = explode(':',$k);
            $v =  array();
            $v['value'] = $tmp_arr[0];
            $v['label'] = $tmp_arr[1];
            $opt .= "<option ".$selected." value=".$k['value']." >".$k['label']." </option>";
        }
        $str ='<select data-value="'.$value.'" id="field_'.$name.'" class="form-control" name="'.$name.'">
                '.$opt.'
                </select>';
        return $str;
    }


    public function input($attr,$info){
        extract($attr);

        if ($info['auto'] && $value) {
            $fun = $info['auto'];
            // $value = $this->$fun($value);
        }
        if ($required || $info['valid']) {
            $required = ' required="true" ';
        }
        $format = "text";

        if ($type) {
            $format = $type;
        }

        if ($info['format']) {
            $format = $info['format'];
        }

        $str ='<input '.$v.$required.' value="'.$value.'"  name="'.$name.'" type="'.$format.'" placeholder="'.$tip.'" id="field_'.$name.'" class="form-control ">';
        if ($info['ipttype'] == 'fileinput') {
            $str = '<span class="input-icon icon-right inverted">'.$str.'<i data-name="'.$name.'" class="fileinput fa fa-cloud-upload bg-blue"></i></span>';
        }
        return $str;
    }

    public function textarea($attr,$info){
        extract($attr);

        if ($info['auto'] && $value) {
            $fun = $info['auto'];
            // $value = $this->$fun($value);
        }
        if ($required || $info['valid']) {
            $required = ' required="true" ';
        }

        $str ='<textarea '.$v.$required.'  name="'.$name.'" placeholder="'.$tip.'" id="field_'.$name.'" class="form-control ">'.$value.'</textarea>';
        return $str;
    }


    public function _field($attr,$content) {
        extract($attr);
        if (!$tb) $tb = CONTROLLER_NAME;


        if ($tb)  $tb = C('DB_PREFIX').parse_name($tb);

        if ($tb && $name) {
            $finfo = $this->getFieldInfo($tb,$name);
            if ($finfo['label']) $label = $finfo['label'];
            if ($finfo['iptCols']) $col = $finfo['iptCols'];
            if (!$label && $finfo['comment'])
                $label = $finfo['comment'];
        }


        $_col = "col-md-4 col-sm-4 col-xs-4 col-lg-4";
        if ($cls) $_cls .= ' '.$cls;
        if ($col) $_col = ' col-md-'.$col.' col-sm-'.$col.' col-xs-'.$col.' col-lg-'.$col;
        if ($colcls) $_col .= ' '.$colcls;

        if ($hideipt == true) {
            $ipt = $this->tpl->parse($content);
        }elseif ($finfo['ipttype'] == 'select') {
            $ipt = $this->select($attr,$finfo);
        }elseif ($finfo['ipttype'] == 'textarea') {
            $ipt = $this->textarea($attr,$finfo);
        }else{
            $ipt = $this->input($attr,$finfo);
        }

        $str = '<div class="'.$_col.'">
                    <div class="form-group">
                    <label for="field_'.$name.'">'.$label.'</label>
                '.$ipt.'
                </div>
            </div>';

        return $str;
    }

    public function _col($attr,$content)
    {
        extract($attr);
        $_col = "col-md-12 col-sm-21 col-xs-12 col-lg-12 ";
        if ($col) $_col = 'col-md-'.$col.' col-sm-'.$col.' col-xs-'.$col.' col-lg-'.$col;
        if ($cls) $_col .= ' '.$cls;
        $content = $this->tpl->parse($content);
        $str = '<div class="'.$_col.'">
                    '.$content.'
            </div>';
        return $str;
    }
}