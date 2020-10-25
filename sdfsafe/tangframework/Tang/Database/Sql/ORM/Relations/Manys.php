<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Database\Sql\ORM\Relations;
/**
 * 一对多与多对多的父类
 * Class Manys
 * @package Tang\Database\Sql\ORM\Relations
 */
abstract class Manys extends Relation
{
    /**
     * 查询条数
     * @var int
     */
    protected $limit = 0;
	protected function configHandler()
	{
		isset($this->config['limit']) && $this->limit = (int)$this->config['limit'];
	}
	public function match($models,$results,$name)
	{
		return $this->matchOneOrMany($models, $results, $name, 'many');
	}
	protected function getDefaultValue()
	{
		return $this->relatedModel->newCollection();
	}
	protected function limit()
	{
		if($this->limit > 0)
		{
			$this->query->limit($this->limit);
		}
	}
}
