<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-11-9
 * Time: 下午4:00
 */
class ProjectTask_Member extends Eloquent
{
    public static function updateTaskMember($task_id, $appointed_member)
    {
        if ( isset($appointed_member['add']) ) {
            foreach ($appointed_member['add'] as $member_id) {
                ProjectTask_Member::create(['task_id' => $task_id, 'member_id' => $member_id]);
            }
        }

        if ( isset($appointed_member['delete']) ) {
            foreach ($appointed_member['delete'] as $member_id) {
                ProjectTask_Member::where('task_id', $task_id)->where('member_id', $member_id)->delete();
            }
        }
    }

    protected $table = 'projectTask_member';

    protected $guarded = ['id'];
}
