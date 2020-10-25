<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-11-12
 * Time: 下午4:52
 */
namespace Libraries\Filter;

use \Illuminate\Http\Request;
use \Illuminate\Routing\Route;
use \ProjectTask_Member;
use \ProjectTask;
use \Auth;

/**
 * Class AccessProjectTaskFilter
 * @package Libraries\Filter
 *
 * 用于对TaskController方法的过滤， 该控制器所对应的资源是某一项目中的任务.
 *
 */
class AccessProjectTaskFilter extends AbstractFilter
{
    /**
     * 初始化数据包括：
     *  当前访问的项目id，当前访问度任务id，如没有则为0.
     *
     * @param Route $route
     * @param Request $request
     * @return mixed|void
     */
    protected function initData(Route $route, Request $request)
    {
        $this->projectId = $route->getParameter('project');
        $this->currUser = Auth::user();

        if( $this->isValidateProjectId() ){
            $this->currentTaskId = $request->get('parent_id', 0);
            $this->setCurrentAction(explode('@', $route->getActionName())[1]);
        } else {

            $this->setCurrentAction('projectIdInvalidated');
        }
    }

    /**
     * 当访问的项目id无效时调用.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function projectIdInvalidatedFilter()
    {
        return $this->responseFailureInfo('无效的项目ID!', 403);
    }

    /**
     * 对TaskController的show方法访问进行过滤.
     */
    protected function storeFilter()
    {
        if ( $this->currentTaskId != 0 )
        {
            $currUserId = $this->currUser->id;
            $createrId = ProjectTask::findOrFail($this->currentTaskId)->creater;

            if ( $currUserId != $createrId ){
                ProjectTask_Member::findOrFail($this->currentTaskId)->where('member_id', $this->currUser->id);
            }
        }

    }

    /**
     * 对TaskController的update方法访问进行过滤.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function updateFilter()
    {
        $currUserId = $this->currUser->id;
        $createrId = ProjectTask::find($this->currentTaskId)->creater;

        if ( $currUserId != $createrId->id){
            return $this->responseFailureInfo('', 403);
        }

    }


    /**
     * 对TaskController的destroy方法访问进行过滤.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    protected function destroyFilter(){
        $currUserId = $this->currUser->id;
        $createrId = ProjectTask::find($this->currentTaskId)->creater;

        if($currUserId != $createrId->id){
            return $this->responseFailureInfo('', 403);
        }
    }

    /**
     * 测试向前的项目id是否有效，如果这个项目不是用户创建或参与的则无效.
     *
     * @return bool
     */
    private function isValidateProjectId()
    {
        if ( ! $this->currUser->joinProjects->find($this->projectId)
            && ! $this->currUser->createProjects->find($this->projectId) )
        {
            return false;
        } else {
            return true;
        }
    }



    protected $currentTaskId;   //引用当前的任务id

    protected $projectId;   //引用当前的项目id

    protected $currUser;    //引用当前的用户

}