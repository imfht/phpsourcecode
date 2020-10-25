<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-25
 * Time: 下午3:53
 */
class ProjectDiscussionComment extends Eloquent
{
    public static function getComments($projectId, $discussionId)
    {
        $builder = ProjectDiscussionComment::with('creater')

            ->where('projectDiscussion_id', $discussionId);

        return Paginate::paginateBuilder( $builder );
    }

    public function creater()
    {
        return $this->belongsTo('User', 'creater_id', 'id');
    }

    public function discussion()
    {
        return $this->belongsTo('ProjectDiscussion', 'projectDiscussion_id', 'id');
    }

    protected $touches = ['discussion'];

    protected $table = 'projectDiscussionComments';

    protected $guarded = ['id'];
}