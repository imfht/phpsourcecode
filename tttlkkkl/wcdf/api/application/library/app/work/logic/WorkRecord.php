<?php
/**
 * 考勤记录逻辑
 *
 * Date: 17-5-6
 * Time: 下午10:29
 * author :李华 yehong0000@163.com
 */

namespace app\work\logic;


use db\Db;
use app\work\model\WorkRecord as Model;
use tool\Tool;

class WorkRecord
{
    protected static $Obj;
    /**
     * @var \app\work\model\WorkRecord
     */
    protected $Model;

    private function __construct()
    {
        $this->Model = Model::getInstance();
    }


    /**
     * @return WorkRecord
     */
    static public function getInstance()
    {
        if (!self::$Obj) {
            self::$Obj = new self;
        }
        return self::$Obj;
    }


    public function sign($data)
    {
        if (!isset($data) || !$data['address']) {
            throw new \Exception('未知的打卡地址', 4020);
        }
        if (!isset($data['lat']) || !$data['lat'] || !is_numeric($data['lat']) || $data['lat'] < 0) {
            throw new \Exception('错误的纬度', 4021);
        }
        if (!isset($data['lng']) || !$data['lng'] || !is_numeric($data['lng']) || $data['lng'] < 0) {
            throw new \Exception('错误的经度', 4022);
        }
        $user = user();
        $insertData = [
            'c_id'           => CID,
            'uid'            => UID,
            'time_index'     => 0,
            'create_time'    => time(),
            'device_number'  => isset($user['deviceId']) ? $user['deviceId'] : '',
            'lng'            => $data['lng'],
            'lat'            => $data['lat'],
            'address'        => $data['address'],
            'type'           => 1,
            'is_late'        => 0,
            'is_leave_early' => 0,
            'is_absenteeism' => 0
        ];
        Db::startTrans();
        $workID = Work::getInstance()->save($insertData);
        $insertData['work_id'] = $workID;
        $ins = $this->Model->save($insertData);
        if ($workID && $ins) {
            Db::commit();
            return [
                'time'    => date('H:i:s', $insertData['create_time']),
                'address' => $insertData['address']
            ];
        } else {
            Db::rollback();
            throw new \Exception('系统错误', -1);
        }
    }

    /**
     * 获取成员当天打卡记录
     *
     * @param $uid
     */
    public function getListByUser($data)
    {
        $uid = isset($data['uid']) ? $data['uid'] : UID;
        $page = isset($data['page']) && $data['page'] > 0 ? $data['page'] : 1;
        $map = [
            'uid'         => $uid,
            'create_time' => ['>', Tool::getDayStartTime()],
            'create_time' => ['<', Tool::getDayEndTime()]
        ];
        $list = $this->Model->getListByWhere($map, $page);
        foreach ($list as $key => $item) {
            $list[$key]['create_date_time'] = isset($item['create_time']) ? date('Y-m-d H:i:s', $item['create_time']) : '--:--';
            $list[$key]['create_time'] = isset($item['create_time']) ? date('H:i:s', $item['create_time']) : '--:--';
        }
        return $list ?: null;
    }

    /**
     * @param $data
     *
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getListByWork($data)
    {
        $workID = isset($data['work_id']) ? $data['work_id'] : 0;
        $page = isset($data['page']) && $data['page'] > 0 ? $data['page'] : 1;
        $map = [
            'work_id' => $workID
        ];
        $list = $this->Model->getListByWhere($map, $page);
        foreach ($list as $key => $item) {
            $list[$key]['create_date_time'] = isset($item['create_time']) ? date('Y-m-d H:i:s', $item['create_time']) : '--:--';
            $list[$key]['create_time'] = isset($item['create_time']) ? date('H:i:s', $item['create_time']) : '--:--';
            $list[$key]['isAbsenteeism'] = $item['is_absenteeism'] ? '是' : '否';
            $list[$key]['isLate'] = $item['is_late'] ? '是' : '否';
            $list[$key]['isLeaveEarly'] = $item['is_leave_early'] ? '是' : '否';
            $list[$key]['typeStr'] = '未知';
        }
        return $list ?: null;
    }
}