<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-21
 * Time: 上午10:15
 */

class Project_Member extends Eloquent
{
    public function role()
    {
        return $this->belongsTo('ProjectRole', 'role_id', 'id');
    }

    public function member()
    {
        return $this->belongsTo('User', 'member_id', 'id');
    }

    public static function getMembersFromProject($projectId)
    {
        return DB::table('project_member')
            ->where('project_id', $projectId)
            ->leftJoin('users', 'project_member.member_id', '=', 'users.id')
            ->select('username', 'users.id AS id', 'email', 'description', 'head_image', 'role_id', 'users.created_at AS created_at')
            ->get();
    }

    protected $table = 'project_member';

    protected $guarded = ['id'];
}
