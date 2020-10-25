<?php
namespace app\Search\controller;

use think\Controller;
use think\Loader;

Loader::import('xunsearch.php.lib.XS', EXTEND_PATH,'.php');
//require_once EXTEND_PATH."xunsearch/php/lib/XS.php";

class Index extends Controller
{
	public function index($keyword,$type,$page)
	{
		if ($keyword)
		{
			$keyword = htmlspecialchars(trim($keyword));
				
			if(strlen($keyword)>30)
			{
				$this->error('搜索内容不允许超过10个汉字（或30个字符），请重新搜索','/');
			}
				
			//只允许中英文数字输入，防止非法操作
			if (!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u', $keyword))
			{
				$this->error('违法操作，请输入中文、英文、数字进行搜索','/');
			}
				
			$xs_all = new \XS('all');
				
			$index_all = $xs_all->index;
				
			$search_all = $xs_all->search;

			//if($user_info['group_id'] == 1 && $keyword =="更新")//管理员可以手动更新全部索引
			if($keyword =="更新")
			{
				$this->update_index($index_all);
			}
				
			if($page)
			{
				$jump = 10*($page-1);
			}
				
			$result = $this->search($type,$search_all,$keyword,$jump);
			$all = $result['0'];
			$count = $result['1'];
				
			$page_count = ceil($count/10);
				
			$this->assign(site_name,'thinkask');
			$this->assign(keyword,$keyword);
			$this->assign(type,$type);
			$this->assign(count,$count);
			$this->assign(page_count,$page_count);
			$this->assign(all,$all);
			
			return $this->fetch('search/index');
		}
		else
		{
			$this->error('请输入您要搜索的内容','/question/index');
		}
	}

	/**
	 * 管理员手动更新索引
	 * @param 获取索引对象$index_all
	 */
	public function update_index($index_all)
	{
		$index_all->clean();
		 
		$question = model('question')->getAllList();
		$article  = model('article') ->getAllList();
		$topic    = model('topic')   ->getAllList();
		$people   = model('users')   ->getAllList();

		$doc = new \XSDocument;
		foreach($question as $val)
		{
			$doc->setFields(array(
					'id'               =>  "question"."-".$val['question_id'],
					'type'             =>  "1",
					'title'            =>  $val['question_content'],
					'message'          =>  strip_tags($val['question_detail']),
					'answercount'     =>  $val['answer_count'],
					'addtime'          =>  $val['add_time']
			));
			$index_all->add($doc);
		}
		unset($doc);

		$doc = new \XSDocument;
		foreach($article as $val)
		{
			$doc->setFields(array(
					'id'               =>  "article"."-".$val['id'],
					'type'             =>  "2",
					'title'            =>  $val['title'],
					'message'          =>  strip_tags($val['message']),
					'addtime'          =>  $val['add_time']
			));
			$index_all->add($doc);
		}
		unset($doc);

		$doc = new \XSDocument;
		foreach($topic as $val)
		{
			$doc->setFields(array(
					'id'               =>  "topic"."-".$val['topic_id'],
					'type'             =>  "3",
					'title'            =>  $val['topic_title'],
					'addtime'          =>  $val['add_time']
			));//赋值操作
			$index_all->add($doc);// 提交到索引中
		}
		unset($doc);

		$doc = new \XSDocument;
		foreach($people as $val)
		{
			$doc->setFields(array(
					'id'               =>  "user"."-".$val['uid'],
					'type'             =>  "4",
					'title'            =>  $val['user_name']
			));
			$index_all->add($doc);
		}
		 
		echo "<em style='color:red;'>索引更新成功</em>";
	}

	/**
	 * 搜索全部结果并排序
	 * @param 搜索类型$search_type
	 * @param 搜索对象$search_all
	 * @param 搜索关键词 $keyword
	 * @param 分页，每页显示10条，跳过$page条数据
	 */
	public function search($search_type,$search_all,$keyword,$jump)
	{
		if($search_type == all)
		{
			$count = $search_all->setQuery($keyword)->count();
			$docs_all = $search_all->setQuery($keyword)->setSort('type',true,true)->setLimit(10,$jump)->search();
		}
		else
		{
			switch($search_type)
			{
				case question:
					$num = 1;
					break;
				case article:
					$num = 2;
					break;
				case topic:
					$num = 3;
					break;
				case user:
					$num = 4;
					break;
			}
			$count = $search_all->setQuery("type:$num $keyword")->count();
			$docs_all = $search_all->setQuery("type:$num $keyword")->setLimit(10,$page)->search();
		}

		$all = array();

		foreach ($docs_all as $doc) {
			$id = explode("-", $doc->id);
			$all[] = array(
					'type'             => $id[0],
					'id'               => $id[1],
					'title'            => $search_all->highlight($doc->title),
					'message'          => $search_all->highlight($doc->message),
					'answer_count'     => $doc->answercount,
					'addtime'          => date("Y-m-d H:i:s",$doc->addtime),
			);
		}
		return array($all,$count);
	}
}