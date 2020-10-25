<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface
{

    use UserTrait, RemindableTrait;

    public function joinProjects()
    {

        return $this->belongsToMany('Project', 'project_member', 'member_id', 'project_id');
    }

    public function createProjects()
    {
        return $this->hasMany('Project', 'creater_id', 'id');
    }

    public function createProjectTasks()
    {
        return $this->hasMany('ProjectTask', 'creater_id', 'id');
    }

    public function joinProjectTasks()
    {
        return $this->belongsToMany('ProjectTask', 'projectTask_member', 'task_id', 'member_id');
    }

    public function notifications()
    {
        return $this->belongsToMany('Notification', 'notifyInbox', 'receiver_id', 'notification_id');
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'users';


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $hidden = array('password', 'remember_token', 'active', 'updated_at');
    protected $fillable = array('username', 'password', 'active', 'email');

}
