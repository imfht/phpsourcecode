<?php
namespace app\common\model;

use think\Model;

class Arctype extends Model
{
	public $listTemp = [	//列表页模板
		'list_article' 	=> 'list_article (文章模型)',
		'post_list'		=> 'POST (文章模型)',
		'list_page' 	=> 'list_page (单页)',
	];
	public $contentTemp = [	//内容页模板
		'article_article'	=> 'article (文章模型)',
		'post_content'		=> 'POST (文章模型)',
		'post_new_content'		=> 'POST (新)',
	];

    protected $insert  = ['description'];
    protected $update = [];

    public function arctypeMod()
    {
        return $this->hasOne('ArctypeMod', 'id', 'mid');
    }

    protected function setDescriptionAttr($value)
    {
        return auto_description($value, input('param.content'));
    }

    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }

    public function getContentAttr($value, $data)
    {
        return htmlspecialchars_decode($data['content']);
    }

	//获取文章分类的全部数据	后台调用
    public function treeList() {
        $list = cache('DB_TREE_ARETYPE');
        if(!$list){
            $list = Arctype::order('sorts ASC,id ASC')->select();
            foreach ($list as $k => $v){
                $v->arctypeMod;
            }
            $treeClass = new \expand\Tree();
            $list = $treeClass->create($list);
            cache('DB_TREE_ARETYPE', $list);
        }
        return $list;
    }

    /********************************************-系统方法-********************************************/

    /**
     * 查询当前ID下的顶级ID
     * @param int $pid
     */
    public function topArctypeData($pid){
        $data = $this->where(['id' => $pid])->find();
        if($data['pid'] == '0'){
            $data->arctypeMod;
            $result = $data;
        }else{
            return $this->topArctypeData($data['pid']);
        }
        return $result;
    }

    /**
     * 查询当前ID下无限分级栏目的所有ID
     * @param int $id
     */
    public function allChildArctype($id=''){
		if( empty($id) ){
	        $where = ['status' => '1'];
		}else{
			$idarr[0] = $id;
	        $where = ['pid' => $id,'status' => '1' ];
		}
        $data = $this->where($where)->order('sorts ASC,id ASC')->select();
        if(!empty($data)){
            foreach($data as $k=>$v){
				$idarr[] = $v['id'];
            }
        }
        return $idarr;
    }

}