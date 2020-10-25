<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/2
 * Time: 19:50
 */

namespace application\modules\report\model;


use application\core\model\Model;
use application\core\utils\ArrayUtil;
use application\core\utils\Ibos;
use application\modules\user\model\User;

class ModuleReader extends Model
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{module_reader}}';
    }

    /**
     * 添加已读用户
     * @param integer $relateid 关联ID
     * @param integer $uid 用户id
     */
    public function addReader($relateid, $uid)
    {
        $module = $this->getCurrentModule();
        $serachReader = $this->fetchAll('relateid = :relateid AND uid = :uid AND module = :module', array(
            ':relateid' => $relateid,
            ':uid' => $uid,
            ':module' => $module,
        ));
        if (empty($serachReader)) {
            $user = User::model()->fetchByUid($uid);
            $reader = array(
                'module' => $module,
                'relateid' => $relateid,
                'uid' => $uid,
                'addtime' => TIMESTAMP,
                'readername' => $user['realname'],
            );
            $this->add($reader);
        }
    }

    /**
     * 关联模块对应关联ID的已读用户uid
     * @param integer $relateid 关联ID
     * @param boolean $returnCount 是否返回已读人员总数
     * @return array|integer
     */
    public function getReader($relateid, $returnCount = false)
    {
        $reader = Ibos::app()->db->createCommand()
            ->select('uid')
            ->from($this->tableName())
            ->where('`relateid` = :relateid AND `module` = :module', array( ':module' => Ibos::getCurrentModuleName(), ':relateid' => $relateid))
            ->queryColumn();
        $res['reader'] = $reader;
        if ($returnCount === true) {
            $res['count'] = count($reader);
            return $res;
        }else{
            return $res['reader'];
        }
    }

    /**
     * 设置关联id为全部已读
     * @param array $relteids 关联id
     * @param integer $uid 当前用户uid
     */
    public function setAllRead($relteids, $uid)
    {
        if (empty($relteids)) {
            return true;
        } else {
            $readers = array();
            $user = User::model()->fetchByPk($uid);
            foreach ($relteids as $relteid) {
                $readers[] = array(
                    'module' => Ibos::getCurrentModuleName(),
                    'relateid' => $relteid,
                    'uid' => $uid,
                    'addtime' => TIMESTAMP,
                    'readername' => $user['realname'],
                );
            }
            $affectRow = Ibos::app()->db->schema->commandBuilder
                ->createMultipleInsertCommand($this->tableName(), $readers)
                ->execute();
            if ($affectRow > 0) {
                return true;
            }
            return false;
        }
    }

    /**
     * 通过用户uid获得关联模块中的关联id
     * @param integer $uid 用户uid
     * @return array/null
     */
    public function fetchRelateidsByUid($uid)
    {
        return Ibos::app()->db->createCommand()
            ->select('relateid')
            ->from($this->tableName())
            ->where('`module` = :module AND `uid` = :uid', array(':module' => Ibos::getCurrentModuleName(), ':uid' => $uid))
            ->queryColumn();
    }

    /**
     * 返回当前模块（如，article）
     * @return array
     */
    private function getCurrentModule()
    {
        $correctModuleName = Ibos::app()->setting->get('correctModuleName');
        if (empty($correctModuleName)) {
            $correctModuleName = Ibos::getCurrentModuleName();
        }
        return $correctModuleName;
    }

}