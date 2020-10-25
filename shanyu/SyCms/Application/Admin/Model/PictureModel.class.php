<?php
namespace Admin\Model;
use Common\Model\AttachmentHandleModel;

class PictureModel extends AttachmentHandleModel{
	//自动验证(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
	//验证条件(0:存在字段验证|默认,1:必须验证,2:值不为空验证)
	//验证时间(1:新增验证,2:修改验证,3:全部验证|默认)
    protected $_validate = array(

    );

	//内容过滤/填充(完成字段1,完成规则,[完成条件,附加规则,函数参数])
	//完成条件(1:新增时候处理,2:修改时候处理,3:全部时候处理|默认)
	protected $_auto = array (
        array('edit_time','date',2,'function',array('Y-m-d H:i:s')),
        array('tag','implode',2,'function',array(',')),

        array('description','getDescription',3,'callback',array(160)),
        array('keywords','getKeywords',3,'callback',array(5)),
	);

	//截取文章描述
	public function getDescription($string,$length=160){
		if(!empty($string)) return $string;
		$content=I('content','','strip_tags');
		return msubstr($content,0,$length);
	}

	//获取关键字
	public function getKeywords($string,$length=5){
		if(!empty($string)) return $string;
		$content=I('content','','strip_tags');

		$Cws=new \Common\Event\CwsEvent();
		$result=$Cws->getWords($content);

		if($result) $keywords=implode(',', $result);
		else $keywords='';
		
		return $keywords;
	}

	//截取内容中的缩略图
    protected function getThumb($data) {
        $model = ContentModel::getInstance($this->modelid);
        //取得副表下标
        $getRelationName = $model->getRelationName();
        //自动提取缩略图，从content 中提取
        if (empty($data['thumb'])) {
            $isContent = isset($data['content']) ? 1 : 0;
            $content = $isContent ? $data['content'] : $data[$getRelationName]['content'];
            $auto_thumb_no = I('.auto_thumb_no', 1, 'intval') - 1;
            if (preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
                $data['thumb'] = $matches[3][$auto_thumb_no];
            }
        }
    }


}