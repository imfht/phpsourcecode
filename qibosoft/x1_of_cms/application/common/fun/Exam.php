<?php
namespace app\common\fun;
use think\Db;

/**
 * 考试系统
 */
class Exam{
    
    private static function get_pre(){
        $dirname = config('system_dirname');
        if ($dirname && $dirname!='exam' && class_exists("app\\".$dirname."\\model\\Answer")) {
            return $dirname. '_'; //ajax调用的时候,值可能为空,如果是复制的频道会不正常
        }else{
            return 'exam_';
        }
    }
    
    /**
     * 取出年级,科目,章节的分类
     * @param string $sys 可以为grade kemu step
     * @param string $type
     * @return array
     */
    public function get_sort($sys='',$type='title'){
        static $array = [];
        if (empty($array[$sys])) {
            $array[$sys] = Db::name(self::get_pre().$sys)->order('list desc , id asc')->column('id,name');
        }
        return $array[$sys];
    }
    
    /**
     * 获取年级,科目,章节的名称
     * @param string $sys 可以为grade kemu step
     * @param string $type
     * @return array
     */
    public function title($sys='',$id=0){
        static $array = [];
        if (empty($array[$id])) {
            $array[$sys] = Db::name(self::get_pre().$sys)->where('id',$id)->value('name');
        }
        return $array[$sys];
    }
    
    /**
     * 统计试卷的试题数量
     * @param number $id
     */
    public function paper_num($fid=0){
        static $array = [];
        if (empty($array[$fid])) {
            $array[$fid] = Db::name(self::get_pre().'info')->where('cid',$fid)->count('id');
        }
        return $array[$fid];
    }
    
    /**
     * 已参加考试人数量
     * @param number $fid
     */
    public function test_num($fid=0){
        static $array = [];
        if (empty($array[$fid])) {
            $array[$fid] = Db::name(self::get_pre().'putin')->where('paperid',$fid)->count('id');
        }
        return $array[$fid];
    }
    
    /**
     * 统计试卷的平均分
     * @param number $fid
     * @return number
     */
    public function average($fid=0){
//         $total_fen = Db::name('exam_putin')->where('paperid',$fid)->sum('fen');
//         return $total_fen/$this->test_num($fid);
        return round(Db::name(self::get_pre().'putin')->where('paperid',$fid)->avg('fen'),1);
    }
    
    /**
     * 试卷的第一道题ID
     * @param number $fid
     * @return mixed|PDOStatement|string|boolean|number
     */
    public function paper_first($fid=0){
        return Db::name(self::get_pre().'info')->where('cid',$fid)->order('list desc,id desc')->limit(1)->value('aid');
    }
    
    /**
     * 核对用户选择的答案是否正确
     * -1全错 1全对 2对部分 0无法核对，因没有标准答案
     * @param array $info 试题数据
     * @param string $ans 用户的答案
     */
    public function check_answer($info=[],$ans=''){
        if($info['answer']=='#'){   //调查表,没有标准答案
            return 1;
        }
        if ($ans!='' && strstr($info['answer'],'|') && in_array($ans, str_array($info['answer'],'|'))) {
            return 1;
        }
        if(trim($info['answer'],', 　')==trim($ans,', 　')){
            return 1;
        }else{
            return -1;
        }
    }
    
    /**
     * 某套试卷最高得分
     */
    public function PaperTop($id=0){
        return getArray(Db::name(self::get_pre().'putin')->where('paperid',$id)->order('fen desc')->limit(1)->find());
    }
    
    /**
     * 考生的答题记录
     * @param string $type
     * @return number|string
     */
    public function log_num($type=''){
        $uid = intval(login_user('uid'));
        if ($type=='all_title') {   //考生回答的所有题目
            return Db::name(self::get_pre().'answer')->where('uid',$uid)->count('id');
        }elseif ($type=='all_paper') {  //考生提交的所有试卷
            return Db::name(self::get_pre().'putin')->where('uid',$uid)->count('id');
        }elseif ($type=='err_title') {  //考生回答的所有错误题目
            return Db::name(self::get_pre().'answer')->where('uid',$uid)->where('is_true','-1')->count('id');
        }
    }
    

    /**
     * 查询试卷信息
     * @param number 试卷ID
     * @return array
     */
	public function get_category_byid($id=0){
	    $this_info = getArray(Db::name(self::get_pre().'category')->where('id',$id)->find());
		return $this_info;
	}
 
    
    
    
}