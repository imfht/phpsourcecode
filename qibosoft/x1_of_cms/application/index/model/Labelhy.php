<?php
namespace app\index\model;

/**
 * 用户自定义的标签 模型,非系统标签
 */
class Labelhy extends Label
{
    protected $table = '__LABELHY__';
    
    /**
     * 保存用户设置的标签参数
     * @param unknown $data
     * @return string|boolean
     */
    public static function save_data($data){
        if(empty($data['name'])){
            return '缺少name参数';
        }elseif(empty($data['class_cfg'])){
            return '缺少class_cfg参数';
        }elseif(empty($data['type'])){
            return '缺少type参数';
        }elseif(empty($data['ext_id'])){
//             return '缺少ext_id参数';
        }
        
        $info = self::where([
                'name'=>$data['name'],
                'ext_id'=>intval($data['ext_id']),
                'fid'=>intval($data['fid']),
        ])->find();
        unset($data['id']);
        if($info){
            if(self::update($data,['id'=>$info['id']])){
                return true;
            }
        }else{
            if(self::create($data)){
                return true;
            }
        }
    }
    
    /**
     * 取得某个标签的具体数据及相关配置参数，同时也会把整个页面的标签配置参数缓存起来。
     * @param string $tag_name 标签名
     * @param string $page_name 模板页
     * @param number $page_num 第几页，AJAX显示更多用到
     * @param array $live_parameter 跟随页面变化的动态参数
     * @param number $hy_id  圈子ID
     * @param number $hy_tags  重复的标签编号
     * @param array $cfg 页面参数变量
     * @return void|void|unknown|array|NULL[]
     */
    public static function get_tag_data_cfg($tag_name='' , $page_name='' , $page_num=0 , $live_parameter=[], $hy_id=0, $hy_tags='',$cfg=[]){

        //获取当前页面的所有标签的数据库配置参数，如果一个页面有很多标签的时候，比较有帮助，如果标签只有一两个就帮助不太大。
        $page_tags = cache('hyconfig_page_tags_'.$page_name.'-'.$hy_id.'-'.$hy_tags);
        if(empty($page_tags)||SHOW_SET_LABEL===true||LABEL_SET===true){
            $hy_tags = intval($hy_tags);
            $page_tags = self::where(['pagename'=>$page_name])->where(['ext_id'=>$hy_id,'fid'=>$hy_tags])->column(true,'name');
            cache('hyconfig_page_tags_'.$page_name.'-'.$hy_id.'-'.$hy_tags, $page_tags);
        }
        
        //取得具体某个标签的配置数据
        if(!empty($page_tags)&&!empty($page_tags[$tag_name])){
            $tag_config = $page_tags[$tag_name];
        }else{
            //对于layout.htm布局模板的公共标签，$page_name值是反复变化的
            $tag_config = cache('hyconfig_page_tag_'.$tag_name.'-'.$hy_id.'-'.$hy_tags);
            if (empty($tag_config)||SHOW_SET_LABEL===true||LABEL_SET===true) {
                $tag_config = getArray(self::where(['name'=>$tag_name])->where(['ext_id'=>$hy_id,'fid'=>$hy_tags])->find());
                cache('hyconfig_page_tag_'.$tag_name.'-'.$hy_id.'-'.$hy_tags, $tag_config);
            }
        }
        if(empty($tag_config)){
            return ;    //新标签，不存在配置参数，所以也不用执行下面的数据取值
        }
        
        $array = unserialize($tag_config['cfg']);
        $array = array_merge($cfg,$array);
        if($live_parameter){    //跟随页面变化的动态参数            
            $array = array_merge($array,$live_parameter);
        }
        $array['tag_name'] = $tag_name;
        $array['page_name'] = $page_name;
        $tag_config['cfg'] = serialize($array);
        
        if($tag_config['class_cfg']=='@'){
            return $tag_config;                     //列表页标签不需要在这里处理数据
        }
        //具体某个标签从数据库取数据        
        try {
            $tag_data = self::run_tag_class($tag_config , $page_num);
        } catch(\Exception $e) {
            $string = var_export($e,true) ;
            preg_match("/'Error SQL' => '(.*?)',/", $string,$array);
            $err_sql = $array[1];
            preg_match("/'Error Message' => '(.*?)',/", $string,$array);
            $err_msg = $array[1];
            if (!$err_sql || !$err_msg) {
                echo '<pre>当前标签《'.$tag_name.'》调用的数据库出错,详情如下:'.$string.'</pre>';
            }else{
                if (strstr($err_msg,'Unknown column')) {
                    $msg = '当前标签《'.$tag_name.'》调用的数据库缺少字段<br>'.filtrate($err_msg)."<br>".filtrate($err_sql);
                    echo $msg."<script>layer.alert(`".str_replace('`', '', $msg)."`);</script>";
                }
            }
        }
        
        if(!empty($tag_data)){
            if(is_array($tag_data)&&$tag_data['format_data']){   //同时存在HTML数据，比如图片<img src=$pic>
                $tag_config['format_data'] = $tag_data['format_data'];
                $tag_config['data'] = $tag_data['data'];
            }else{
                $tag_data = getArray($tag_data);
                if(is_array($tag_data['data'])){
                    $tag_config['data'] = $tag_data['data'];
                    unset($tag_data['data']);
                    $tag_config['pages'] = $tag_data;   //分页数据
                }else{
                    $tag_config['data'] = $tag_data;
                }               
            }
        }
        return $tag_config;
    }
    
    /**
     * 碎片专用,避免跟get_tag_data_cfg冲突,因为碎片里边可能有多个标签
     * @param string $tag_name
     * @param string $page_name
     * @param number $page_num
     * @param array $live_parameter
     * @param number $hy_id
     * @param string $hy_tags
     * @return void|\app\index\model\unknown|array|\app\index\model\NULL[]
     */
    public static function get_labelmodel_tag_data_cfg($tag_name='' , $page_name='' , $page_num=0 , $live_parameter=[], $hy_id=0, $hy_tags='',$cfg=[]){
        return self::get_tag_data_cfg($tag_name , $page_name , $page_num , $live_parameter , $hy_id , $hy_tags ,$cfg);
    }

}






