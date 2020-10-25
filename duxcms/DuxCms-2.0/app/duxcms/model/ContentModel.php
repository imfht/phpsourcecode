<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 内容操作
 */
class ContentModel extends BaseModel {
    //完成
    protected $_auto = array (
        //全部
        array('class_id','intval',3,'function'), //栏目ID
        array('urltitle','getUrlTitle',3,'callback'), //URL
        array('description','getDescription',3,'callback'), //描述
        array('image','getImage',3,'callback'), //形象图
        array('time','strtotime',3,'function'), //时间
        array('status','intval',3,'function'), //状态
        array('sequence','intval',3,'function'), //顺序
        array('views','intval',3,'function'), //访问量
        array('taglink','intval',3,'function'), //TAG链接
        array('font_bold','intval',3,'function'), //加粗
        array('font_em','intval',3,'function'), //倾斜
        array('position','formatPosition',3,'callback'), //推荐
        //编辑
        array('content_id','intval',2,'function'), //内容ID
     );
    //验证
    protected $_validate = array(
        //全部验证
        array('title','1,255', '标题只能为1~250个字符', 1 , 'length'),
        array('class_id','is_empty', '请选择栏目', 1 ,'function'),
        //编辑验证
        array('content_id','is_empty', '内容ID获取不正确', 1 ,'function',2),
    );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $limit = 50, $order = 'A.time desc,A.content_id desc'){

        $pageList = $this->table("content as A")
                    ->join('{pre}category as B ON A.class_id = B.class_id')
                    ->field('A.*,B.name as class_name,B.app,B.urlname as class_urlname,B.image as class_image,B.parent_id')
                    ->where($where)
                    ->limit($limit)
                    ->order($order)
                    ->select();
        //处理数据类型
        $list=array();
        if(!empty($pageList)){
            $i = 0;
            foreach ($pageList as $key=>$value) {
                $list[$key]=$value;
                $list[$key]['app']=strtolower($value['app']);
                $list[$key]['aurl'] = target('duxcms/Content')->getUrl($value);
                $list[$key]['curl'] = target('duxcms/Category')->getUrl($value);
                $list[$key]['i'] = $i++;
            }
        }
        return $list;
    }

    /**
     * 获取数量
     * @return int 数量
     */
    public function countList($where = array()){
        return $this->table("content as A")
                    ->join('{pre}category as B ON A.class_id = B.class_id')
                    ->where($where)
                    ->count();
    }
    
    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where){
        $info = $this->table("content as A")
                    ->join('{pre}category as B ON A.class_id = B.class_id')
                    ->field('A.*,B.name as class_name,B.app,B.urlname as class_urlname,B.image as class_image,B.parent_id')
                    ->where($where)
                    ->find();
        if(!empty($info)){
            $info['app'] = strtolower($info['app']);
        }
        return $info;
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        $data = $this->create();
        if(!$data){
            return false;
        }
        if($type == 'add'){
            //保存基本信息
            $contentId = $this->add($data);
            if(!$contentId){
                return false;
            }
            $data['content_id'] = $contentId;
            //保存扩展表
            if(!$this->saveExtData($data)){
                return false;
            }
            //保存TAG
            $this->hasTags($data['keywords'], $data['content_id']);
            return $contentId;
        }
        if($type == 'edit'){
            if(empty($data['content_id'])){
                return false;
            }
            $status = $this->save();
            if($status === false){
                return false;
            }
            //保存扩展表
            if(!$this->saveExtData($data)){
                return false;
            }
            //保存TAG
            $this->hasTags($data['keywords'], $data['content_id']);
            return true;
        }
        return false;
    }

    /**
     * 修改信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function editData($data){
        $map = array();
        $map['content_id'] = $data['content_id'];
        return $this->where($map)->data($data)->save();
    }

    /**
     * 删除信息
     * @param int $contentId ID
     * @return bool 删除状态
     */
    public function delData($contentId){
        $map = array();
        $map['content_id'] = $contentId;
        $status = $this->where($map)->delete();
        if(!$status){
            return false;
        }
        //获取字段集信息
        $info = $this->getWhereInfo($map);
        $fieldsetInfo = target('duxcms/Fieldset')->getInfoClassId($info['class_id']);
        //删除扩展字段
        if(!empty($fieldsetInfo)){
            $expandModel = target('duxcms/FieldData');
            $expandModel->setTable('ext_'.$fieldsetInfo['table']);
            if($expandModel->getInfo($contentId)){
                if(!$expandModel->delData($contentId)){
                    $this->error = $expandModel->getError();
                    return false;
                }
            }
        }
        //删除TAG关联
        target('duxcms/TagsHas')->delData($map);
        return $status;
    }

    /**
     * 更新扩展信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveExtData($data){
        //查询栏目信息
        $classId = $data['class_id'];
        //获取字段集信息
        $fieldsetInfo = target('duxcms/Fieldset')->getInfoClassId($classId);
        //保存扩展字段
        if(!empty($fieldsetInfo)){
            $expandModel = target('duxcms/FieldData');
            //设置模型信息
            $expandModel->setTable('ext_'.$fieldsetInfo['table']);
            $_POST['data_id'] = $data['content_id'];
            if($expandModel->getInfo($data['content_id'])){
                $type = 'edit';
            }else{
                $type = 'add';
            }
            
            if(!$expandModel->saveData($type,$fieldsetInfo)){
                $this->error = $expandModel->getError();
                return false;
            }
        }
        return true;
    }

    /**
     * 关联TAG
     * @param string $keywords 关键词
     * @param string $contentId 内容ID
     * @return bool 状态
     */
    public function hasTags($keywords, $contentId){
        if (empty($keywords)) {
            return false;
        }
        $str = $keywords;
        $str = str_replace('，', ',', $str);
        $str = str_replace(' ', ',', $str);
        $strArray = explode(",", $str);
        //设置模型
        $TagsHasModel = target('duxcms/TagsHas');
        $TagsModel = target('duxcms/Tags');
        //删除关联
        $where = array();
        $where['content_id'] = $contentId;
        $TagsHasModel->delData($where);
        //关联TAG
        foreach ($strArray as $name) {
            $where = array();
            $where['name'] = $name;
            $info = $TagsModel->getWhereInfo($where);
            if (empty($info)) {
                //添加TAG
                $data = array();
                $data['name'] = $name;
                $data['quote'] = 1;
                $tagId = $TagsModel->saveData('add',$data);
                //添加关联
                $hasData = array();
                $hasData['content_id'] = $contentId;
                $hasData['tag_id'] = $tagId;
                $TagsHasModel->addData($hasData);
            } else {
                //增加引用次数
                $data = array();
                $data['quote'] = $info['quote'] + 1;
                $data['tag_id'] = $info['tag_id'];
                $TagsModel->saveData('edit',$data);
                //查找关联
                $where = array();
                $where['content_id'] = $contentId;
                $where['tag_id'] = $info['tag_id'];
                $infoHas = $TagsHasModel->countList($where);
                //添加关联
                if (!$infoHas) {
                    $hasData = array();
                    $hasData['content_id'] = $contentId;
                    $hasData['tag_id'] = $info['tag_id'];
                    $TagsHasModel->addData($hasData);
                }
            }
        }
        return true;
    }

    /**
     * 内容拼音转换
     * @return string 内容拼音
     */
    public function getUrlTitle(){
        //获取变量
        $name = request('post.title');
        $urlTitle = request('post.urltitle');
        $contentId = request('post.content_id');
        //生成URL
        if (empty($urlTitle))
        {
            $pinyin = new \framework\ext\Pinyin();
            $name = preg_replace('/\s+/', '-', $name);
            $pattern = '/[^\x{4e00}-\x{9fa5}\d\w\-]+/u';
            $name = preg_replace($pattern, '', $name);
            $urlTitle = substr($pinyin->output($name, true),0,30);
            $urlTitle = trim($urlTitle,'-');
        }
        //返回数据
        $where = array();
        if (!empty($contentId))
        {
            $where[] = 'A.content_id <> '.$contentId;
        }
        $where['urltitle'] = $urlTitle;
        $info = $this->getWhereInfo($where);
        if (empty($info))
        {
            return $urlTitle;
        }
        else
        {
            return $urlTitle.substr(unique_number(),8);
        }
    }

    /**
     * 提取描述
     * @return string 内容描述
     */
    public function getDescription(){
        //获取变量
        $getDescStatus = request('post.get_description',0,'intval');
        $description = request('post.description');
        $content = request('post.content');
        //处理数据
        if(!$getDescStatus||empty($content)){
            return $description;
        }
        $description = html_out($content);
        $description = strip_tags($description);
        $description = str_replace(array("\r\n","\t",'&ldquo;','&rdquo;','&nbsp;'), '', $description);
        $description = substr($description, 0,250);
        return $description;
    }

    /**
     * 提取形象图
     * @return string 内容形象图
     */
    public function getImage(){
        //获取变量
        $getImageStatus = request('post.get_image',0,'intval');
        $getImageNum = request('post.get_image_num',0,'intval');
        $image = request('post.image');
        $content = request('post.content');
        //处理数据
        if(!$getImageStatus||!empty($image)||!$content||!$getImageNum){
            return $image;
        }
        return target('duxcms/ContentTools')->getImage($content, $getImageNum);
    }

    /**
     * 格式化推荐位
     * @return string 推荐ID
     */
    public function formatPosition(){
        $position = request('post.position');
        if(!empty($position)){
            return implode(',', $position);
        }else{
            return ;
        }
    }

    /**
     * 获取内容URL
     * @param int $info 栏目信息
     * @return bool 删除状态
     */
    public function getUrl($info)
    {
        return match_url(strtolower($info['app']).'/Content/index',array('content_id'=>$info['content_id'],'urltitle'=>$info['urltitle'],'class_urlname'=>$info['class_urlname']));
    }

}
