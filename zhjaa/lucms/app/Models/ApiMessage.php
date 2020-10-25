<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ApiMessage extends Model
{

    protected $fillable = [
        'user_id', 'admin_id', 'title', 'content', 'url', 'status', 'is_alert_at_home', 'type'
    ];

    public function adminUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'admin_id')->select('id,name');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->select('id', 'phone', 'name');
    }

    public function oneMessage($user_id, $title, $content, $admin_id = 0, $url = '', $is_alert_at_home = 'F', $type='SY')
    {
        $insert_data = [
            'user_id' => $user_id,
            'admin_id' => $admin_id,
            'title' => $title,
            'type' => $type,
            'content' => $content,
            'url' => $url,
            'is_alert_at_home' => $is_alert_at_home,
        ];
        $this->saveData($insert_data);
        return $this->baseSucceed();
    }

    public function manyMessage($data)
    {
        if (count($data) > 1) {
            $this->insert($data);
        } else {
            $this->saveData($data);
        }
        return $this->baseSucceed();

    }

    public function allMessage($title, $content, $admin_id = 0, $url = '', $is_alert_at_home = 'F', $type='SY')
    {
        $user_ids = User::where('enable', 'T')->pluck('id');
        $now = date('Y-m-d H:i:s');
        $sql = "insert into api_messages (user_id,admin_id,title,content,url,is_alert_at_home,type,created_at)  values ";
        foreach ($user_ids as $v) {
            $sql .= "('" . $v . "','" . $admin_id . "','" . $title . "','" . $content . "','" . $url . "','" . $is_alert_at_home . "','" . $type . "','" . $now . "'),";
        }
        $insert_sql = substr($sql, 0, -1);
        DB::insert($insert_sql);
        return $this->baseSucceed();
    }
}
