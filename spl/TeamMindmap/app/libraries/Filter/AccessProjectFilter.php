<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-24
 * Time: 下午4:36
 */

namespace Libraries\Filter;

use \Illuminate\Http\Request;
use \Illuminate\Routing\Route;
use \Auth;

/**
 *
 * Class AccessProjectFilter
 * @package Libraries\Filter
 *
 * 用于对ProjectController方法的过滤.
 */
class AccessProjectFilter extends AbstractFilter
{
    /**
     * 用于进行数据初始化.
     *
     * 初始化的数据包括：当前的用户，当前的项目id
     *
     * @param Route $route
     * @param Request $request
     * @return mixed|void
     */
    protected function initData(Route $route, Request $request)
    {
        $this->currUser = Auth::user();
        $this->projectId = $route->getParameter('project');
        $this->setCurrentAction(explode('@', $route->getActionName())[1]);
    }

    /**
     * 对ProjectController的show方法访问进行过滤.
     *
     * @return \Illuminate\Http\JsonResponse
     */
	protected function showFilter()
    {
		if( ! $this->currUser->joinProjects->find($this->projectId)
		&& ! $this->currUser->createProjects->find($this->projectId) ){
			return $this->responseFailureInfo('错误访问！', 403);
		}
	}

    /**
     * 对ProjectController的destroy方法访问进行过滤.
     *
     * @return \Illuminate\Http\JsonResponse
     */
	protected function destroyFilter(){
		if( ! $this->currUser->createProjects->find($this->projectId) ){
			return $this->responseFailureInfo('只有创建者拥有删除的权限或该项目并不存在！', 403);
		}
	}

    /**
     * 对ProjectController的update方法访问进行过滤.
     *
     * @return \Illuminate\Http\JsonResponse
     */
	protected function updateFilter(){
		if( ! $this->currUser->createProjects->find($this->projectId) ){
			return $this->responseFailureInfo('只有创建者拥有修改的权限或该项目并不存在!', 403);
        }
	}

	protected $projectId;   //当前引用的项目id

	protected $currUser;    //当前引用的登陆用户Model
}