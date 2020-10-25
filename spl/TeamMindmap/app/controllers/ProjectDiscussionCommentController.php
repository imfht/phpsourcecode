<?php

/**
 * 此控制器作为项目讨论中处理评论的辅助控制器
 * Class ProjectDiscussionCommentController
 */
class ProjectDiscussionCommentController extends \BaseController
{
    public function __construct()
    {
        /**
         * 对讨论中评论post请求进行数据过滤
         */
        \Libraries\MarkDownPurifier::purify(['content']);
    }

    /**
     *
     * 获取某一讨论的所有评论
     *
     * 返回的JSON格式：
     *
     * [
     *    {
     *      "id": "主键id",
     *      "content": "评论的内容",
     *      "creater": {
     *          "id": "评论者的id",
     *          "username": "评论者的用户名",
     *          "head_image": "评论者的头像"
     *      },
     *      "created_at": "评论的创建时间",
     *      "updated_at": "评论的更新时间",
     *   },
     *   //注意是个数组..
     * ],
     *
     * @param $projectId
     * @param $discussionId
     * @return \Illuminate\Pagination\Paginator
     */
    public function getIndex( $projectId, $discussionId )
    {
        $respData = ProjectDiscussionComment::getComments($projectId, $discussionId);

        return Response::json( $respData );
    }

    /**
     *
     * 新建评论
     *
     * @param $projectId
     * @param $discussionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIndex($projectId, $discussionId)
    {
        $targetDiscussion = ProjectDiscussion::where('project_id', $projectId)
            ->where('id', $discussionId)
            ->firstOrFail();

        $content = Input::get('content', null);

        if( $content ){
            $add = ProjectDiscussionComment::create([
                'content'=>$content,
                'creater_id'=>Auth::user()['id'],
                'projectDiscussion_id'=>$targetDiscussion['id']
            ]);

            return Response::json( ['id'=>$add['id'] ]);
        }else{
            return Response::make('invalid access', 403);
        }

    }

}
