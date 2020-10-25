<?php

include_once __DIR__ .'/../config/redis.php';

class RedisHelper
{
    private static $pRedis = null;  // 公库redis
    private static $dbs = [];   // 已连接的

    private static function connectRedis($rdb) {
        $dsn = parse_url($rdb);
        $dsn['host'] = isset($dsn['host']) ? ($dsn['host']) : '127.0.0.1';
        $dsn['port'] = isset($dsn['port']) ? ($dsn['port']) : '6379';
        $dsn['user'] = isset($dsn['user']) ? ($dsn['user']) : '';
        $dsn['path'] = isset($dsn['path']) ? (substr($dsn['path'], 1)) : '8';

        $redis = new Redis();
        $redis->connect($dsn['host'], $dsn['port']);
        $redis->auth($dsn['user']);
        $redis->select($dsn['path']);

        return $redis;
    }

    private static function getRedisUrl($sn){
        if ( !self::$pRedis || !self::$pRedis->ping() ) {
            self::$pRedis = self::connectRedis(RDB);
        }

        $data = self::$pRedis->hGet('dsn', $sn);
        $data = json_decode($data, true);

        if ( !$data ) return false;

        return $data['redis'];
    }

    public static function getHelper($sn) {
        if ( isset(self::$dbs[$sn]) && self::$dbs[$sn]->ping() ) return self::$dbs[$sn];

        //$url = self::getRedisUrl($sn);
        if ( !self::$pRedis || !self::$pRedis->ping() ) {
            self::$pRedis = self::connectRedis(RDB);
        }

        return self::$pRedis;

        if ( !$url ) return false;

        self::$dbs[$sn] = self::connectRedis($url);;
        return self::$dbs[$sn];
    }

    private static function addFriend( $redis, $sn, $from, $to ) {
        $friends = $redis->hGetAll($sn .':friend:'. $from);

        foreach ( $friends as $friend) {
            $friend = json_decode($friend, true);
            if ( $friend['name'] == $to ) return;
        }

        $data = ['name' => $to, 'group' => false, 'unread' => 0];
        $redis->hSet($sn .':friend:'. $from, $to, json_encode($data, JSON_UNESCAPED_UNICODE));

        $data['name'] = $from;
        $redis->hSet($sn .':friend:'. $to, $from, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private static function getFriend($redis, $sn, $name){
        //$friends = $redis->lrange($sn .':friend:'. $name, 0, 999);
        $friends = $redis->hGetAll($sn .':friend:'. $name);
        $group = $redis->hGetAll($sn .':group');
        //$group = $group ? json_decode($group, true) : [];
        //$group = array_column($group,NULL,'name');

        foreach ( $friends as &$friend) {
            $friend = json_decode($friend, true);
            if ( $friend['group'] ) {
                $friend['members'] = json_decode($group[$friend['name']], true);
            }
        }

        return $friends;
    }

    private static function searchUser( $redis, $sn, $name) {
        $data = $redis->hget($sn .':user', $name);
    }

    private static function userList( $redis, $sn ) {
        $data = $redis->hgetAll($sn .':user');
        foreach ( $data as $key => &$val ) {
            $val = json_decode($val, true);
            unset($val['passwd']);

            if ( $val['name'] === 'admin' ) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    private static function getUser($redis, $sn, $data, $add = false) {
        if ( $add ) {
            $data = [
                //'id' => $data['id'],
                'name' => $data['name'],
                'passwd' => $data['passwd'],
                'img' => $data['img'],
            ];

            return $redis->hset($sn .':user', $data['name'], json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        $data = $redis->hget($sn .':user', $data['name']);
        return json_decode($data, true);
    }

    private static function saveMsg( $redis, $sn, $name, $msg ) {
        return $redis->rpush($sn .':record:'. $name, $msg);
    }

    private static function getMsg($redis, $sn, $name) {
        $msg = $redis->lRange($sn .':record:'. $name, 0, 999);
        foreach ( $msg as &$val) {
            $val = json_decode($val, true);
        }

        return $msg;
    }

    private static function getGroupMember( $redis, $sn, $name ) {
        $users = $redis->hGet($sn .':group', $name);
        return json_decode($users, true);
    }

    private static function updateUnread( $redis, $sn, $data, $read = false ) {
        $friend = $redis->hGet($sn .':friend:'. $data['to'], $data['from']);
        $newChat = 0;
        if ( $read ) {
            $friend = json_decode($friend, true);
            $friend['unread'] = 0;
        } elseif ( !empty($friend)) {
            $friend = json_decode($friend, true);
            $friend['unread'] += 1;
            $friend['last_time'] = $data['time'];
        } else {
            // 双向加好友
            $redis->hSet($sn .':friend:'. $data['from'], $data['to'], json_encode([
                'group' => false,
                'name'  => $data['to'],
                'unread' => 0,
                'last_time' => $data['time']
            ], JSON_UNESCAPED_UNICODE));

            $friend = [
                'group' => false,
                'name' => $data['from'],
                'unread' => 1,
                'last_time' => $data['time']
            ];
            $newChat = 1;
        }

        $redis->hSet($sn .':friend:'. $data['to'], $data['from'], json_encode($friend, JSON_UNESCAPED_UNICODE));
        return $newChat;
    }

    private static function updateUser( $redis, $sn, $data ) {
        if ( !empty( $data['del'] ) ) {
            return $redis->hdel($sn .':user', $data['name']);
        }
        $user = $redis->hget($sn .':user', $data['name']);
        $data = [
            'name' => $data['name'],
            'passwd' => $data['passwd'],
        ];
        $redis->hset($sn .':user', $data['name'], json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private static function joinGroup( $redis, $sn, $groupName, $data){
        $res = $redis->hExists($sn .':group', $groupName);
        $_data = [];
        $json = json_encode(['group' => true, 'name' => $groupName, 'unread' => 0, 'last_time' => time()], JSON_UNESCAPED_UNICODE);
        if ( $res ) {
            $_data = json_decode( $redis->hget($sn .':group', $groupName), true);
            $_name = array_column($_data,'name');
            $name = array_diff($data, $_name);
            foreach ( $name as $val ) {
                $_data[] = ['name' => $val];

                // 聊天列表中
                $redis->hset($sn .':friend:'. $val, $groupName, $json);
            }
        } else {
            foreach ( $data as $name ) {
                $_data[] = ['name' => $name];
                $redis->hset($sn .':friend:'. $name, $groupName, $json);
            }
        }
        $redis->hset($sn .':group', $groupName, json_encode($_data, JSON_UNESCAPED_UNICODE));
        return $_data;
    }

    private static function leaveGroup($redis, $sn, $data){
        $group = $redis->hget($sn .':group', $data['group_name']);
        $group = json_decode($group, true);

        foreach( $group as $key => $member ) {
            if ( $member['name'] = $data['from'] ) {
                unset($group[$key]);
                break;
            }
        }
        $group = array_values($group);

        $redis->hdel($sn .':friend:'. $data['from'], $data['group_name']);

        if ( empty($group) ) {
            $redis->hdel($sn .':group', $data['group_name']);
        } else {
            $redis->hset($sn .':group', $data['group_name'], json_encode($group, JSON_UNESCAPED_UNICODE));
        }

        return $group;
    }

    private static function delFriend($redis, $sn, $data) {
        $redis->hdel($sn .':friend:'. $data['from'], $data['name']);
    }

    private static function updateLasttime( $redis, $sn, $data ) {
        $friend = $redis->hget($sn .':friend:'. $data['from'], $data['name']);
        $friend = json_decode($friend, true);
        $friend['last_time'] = $data['time'];

        $redis->hset($sn .':friend:'. $data['from'], $data['name'], json_encode($friend, JSON_UNESCAPED_UNICODE));
    }

    public static function __callStatic($name, $arguments) {
        $obj = get_called_class();
        if( !method_exists($obj, $name) ) return false;

        $redis = self::getHelper($arguments[0]);
        if ( !$redis ) return false;

        $arg = array_merge([$redis], $arguments);
        return call_user_func_array([$obj, $name], $arg);
    }
}