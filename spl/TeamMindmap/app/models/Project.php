<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-21
 * Time: 上午10:04
 */

class Project extends Eloquent
{
    public function members()
    {
        return $this->belongsToMany('User', 'project_member', 'project_id', 'member_id');
    }

    public function creater()
    {
        return $this->hasOne('User', 'id', 'creater_id');
    }

    /**
     * 检查用户是否是项目管理员或创建者.
     *
     * @param $user User|int 待检查的用户
     * @param $project Project|int 待检查的项目
     * @return bool
     */
    public static function checkManagerOrCreater($user, $project)
    {
        if( $user instanceof User ){
            $user = $user['id'];
        }

        if( $project instanceof Project ){
            if( $project['creater_id'] == $user ){
                return true;
            }

            $project = $project['id'];
        }

        if( Project::where('id', $project)->where('creater_id', $user)->first() ){
            return true;
        }else{

            $managerRoleId = ProjectRole::where('name', 'manager')->firstOrFail()['id'];

            $checked = Project_Member::where('role_id', $managerRoleId)
                ->where('project_id', $project)
                ->where('member_id', $user)
                ->first();

            if( $checked ){
                return true;
            }else{
                return false;
            }
        }


    }
    protected $table = 'projects';

    protected $guarded = ['id'];
}
