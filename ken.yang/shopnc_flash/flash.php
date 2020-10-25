<?php

/*
 * 闪存数据Flash
 * 适用环境：数据保存到下一次使用为止
 * 例如:form表单提交，出现错误，需要返回，但是返回后会出现提交过的数据
 * 
 * 使用方法见最后
 * 
 * @auther      ken<695093513@qq.com>
 * @time        2015-11-13
 * @license     GNU General Public License 3.0
 * @version     V0.2
 */

defined('InShopNC') or exit('Access Invalid!');

class Flash {

    //数据目前支持session(FlashSession),cache(FlashCache)方式,可以根据需求写适合自己的扩展
    const FLASH_TYPE = 'FlashSession';

    /**
     * 检查数据
     * @param string $name
     */
    public static function check($name) {
        $flash = self::_shooseFlashType();
        return $flash->check($name);
    }

    /**
     * 获取数据
     * @param string $name
     */
    public static function get($name) {
        $flash = self::_shooseFlashType();
        return $flash->get($name);
    }

    /**
     * 设置单个数据
     * @param string $name
     * @param string $value
     */
    public static function set($name, $value) {
        $flash = self::_shooseFlashType();
        return $flash->set($name, $value);
    }

    /**
     * 设置所有数据为flash
     * @param array $data
     */
    public static function setAll($data) {
        $flash = self::_shooseFlashType();
        return $flash->setAll($data);
    }

    /**
     * 清除单个flash
     * @param string $name
     */
    public static function clear($name) {
        $flash = self::_shooseFlashType();
        $flash->clear($name);
    }

    /**
     * 清除所有flash
     * @param array $data
     */
    public static function clearAll($data) {
        $flash = self::_shooseFlashType();
        $flash->clearAll($data);
    }

    /**
     * 根据flash_type类型实例化对应的类
     * 
     * @return \FlashCache|\FlashSession
     */
    private static function _shooseFlashType() {
        $falseType = self::FLASH_TYPE;
        return new $falseType();
    }

}

/**
 * 抽象顶层类，规范接口
 */
abstract class FlashInterFace {

    //检查flash是否有值
    abstract public function check($name);

    //获取flash
    abstract public function get($name);

    //设置单个flash
    abstract public function set($name, $value);

    //设置所有flash，为数组
    abstract public function setAll($data);

    //清除单个flash
    abstract public function clear($name);

    //清除所有flash
    abstract public function clearAll($data);
}

/**
 * 扩展session类
 */
class FlashSession extends FlashInterFace {

    /**
     * 检查是否有值
     * @param string $name
     * @return string
     */
    public function check($name) {
        $value = $_SESSION['flash'][$name] ? $_SESSION['flash'][$name] : '';
        return $value;
    }

    /**
     * 获取flash
     * @param string $name
     * @return string
     */
    public function get($name) {
        $value = $_SESSION['flash'][$name] ? $_SESSION['flash'][$name] : '';
        unset($_SESSION['flash'][$name]);
        return $value;
    }

    /**
     * 设置单个flash
     * @param string $name
     * @param string $value
     */
    public function set($name, $value) {
        $_SESSION['flash'][$name] = $value;
    }

    /**
     * 设置所有数据为flash
     * @param array $data
     */
    public function setAll($data) {
        foreach ($data as $key => $value) {
            $_SESSION['flash'][$key] = $value;
        }
    }

    /**
     * 清除一个flash
     * @param string $name
     */
    public function clear($name) {
        unset($_SESSION['flash'][$name]);
    }

    /**
     * 清除所有flash
     * 如果data为空，则直接清除flash
     * @param type $data
     */
    public function clearAll($data) {
        if (empty($data)) {
            unset($_SESSION['flash']);
        } else {
            foreach ($data as $key => $value) {
                unset($_SESSION['flash'][$key]);
            }
        }
    }

}

/**
 * 扩展cache类
 * 仅仅适用于内存中的缓存
 */
class FlashCache extends FlashInterFace {

    const FLASH_PREFIX = 'flash_';
    const FLASH_TIME = 3600;

    public function __construct() {
        $this->_flashToken();
    }

    /**
     * 检查是否有值
     * @param string $name
     * @return string
     */
    public function check($name) {
        $value = $this->_rcache($name) ? $this->_rcache($name) : '';
        return $value;
    }

    /**
     * 获取flash
     * @param string $name
     * @return string
     */
    public function get($name) {
        $value = $this->_rcache($name) ? $this->_rcache($name) : '';
        $this->_dcache($name);
        return $value;
    }

    /**
     * 设置单个flash
     * @param string $name
     * @param string $value
     */
    public function set($name, $value) {
        $this->_wcache($name, $value);
    }

    /**
     * 设置所有数据为flash
     * @param array $data
     */
    public function setAll($data) {
        foreach ($data as $key => $value) {
            $this->_wcache($key, $value);
        }
    }

    /**
     * 清除一个flash
     * @param string $name
     */
    public function clear($name) {
        $this->_dcache($name);
    }

    /**
     * 清除所有flash
     * 如果data为空，则直接清除flash
     * @param type $data
     */
    public function clearAll($data) {
        if (empty($data)) {
            //必须传递参数
        } else {
            foreach ($data as $key => $value) {
                $this->_dcache($key);
            }
        }
    }

    /**
     * 封装读写缓存
     * -------------------------------------------------------------
     * @param type $origNamen
     * @param type $value
     */
    private function _wcache($origNamen, $value) {
        $name = $this->_getTrueName($origNamen);
        wcache($name, $value, self::FLASH_PREFIX, self::FLASH_TIME);
    }

    private function _rcache($origNamen) {
        $name = $this->_getTrueName($origNamen);
        rcache($name);
    }

    private function _dcache($origNamen) {
        $name = $this->_getTrueName($origNamen);
        dcache($name, self::FLASH_PREFIX);
    }

    /**
     * 获取存在于cache的真是名字
     * @return string
     */
    private function _getTrueName($name) {
        return $_SESSION['flash_token'] . '_' . $name;
    }

    /**
     * 借助session生成一个flashToken来标示为当前用户
     */
    private function _flashToken() {
        if (!isset($_SESSION['flash_token'])) {
            $random = microtime() . range('111111', '99999');
            $_SESSION['flash_token'] = hash('md5', $random);
        }
    }

}

/**
 * 扩展数据库类
 */
class FlashDatabase {
    //暂时不支持数据库的闪存方式
    //需要做当前用户唯一性的处理
    //如果有需要可以进行扩展
}

/**
 * -------------------------------------------------------------------------
 * 使用方法
 * -------------------------------------------------------------------------
 * 以form表单提交为例,仅仅作为演示，需求根据自身需求修改
 * 
 * 1、form表单input的value设置Flash::get('name');
 * <input value="<?php echo Flash::get('name')?>">
 * 
 * 2、提交表单后，保存闪存数据
 * Flash::setAll($_POST);
 * 
 * 3、表单提交成功，闪存闪存数据
 * Flash::clearAll($_POST);
 */


/**
 * -------------------------------------------------------------------------
 * 备注
 * -------------------------------------------------------------------------
 * 1、form表单成功提交后应该清除闪存
 * 2、闪存数据使用一次后及清除
 */