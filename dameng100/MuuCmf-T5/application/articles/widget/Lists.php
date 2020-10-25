<?php
namespace app\Articles\widget;

use think\Controller;


class Lists extends Controller
{
	/**
	 * 根据栏目ID获取列表
	 *
	 * @param      <type>   $category_id  The category identifier
	 * @param      integer  $limit        The limit
	 *
	 * @return     <type>   ( description_of_the_return_value )
	 */
	public function lists($category_id, $limit = 4)
	{
		$category = model('articles/ArticlesCategory')->info($category_id);
		$this->assign('category',$category);

		if(!empty($category_id) || $category_id != 0){

            $cates_list = model('articles/ArticlesCategory')->getCategoryList(['pid'=>$category_id]);
            if(count($cates_list)){
                $cates_list = array_column($cates_list,'id');
                $cates_list = array_merge(array($category_id),$cates_list);
                $map['category'] = array('in',$cates_list);
            }else{
                $map['category'] = $category_id;
            }
        }
        $map['status'] = 1;

        $list = model('articles/Articles')->getListByMap($map,$limit);
        $this->assign('list',$list);

        return $this->fetch();
	}
}