<?php
/**
 * 处理飞舞
 * Class FWFrontController
 */
class StubController extends FWFrontController
{
    private $_id;

    private $_modulePath;

	public function init(){
        parent::init();

        // 取得模块标示
        $this->_id = $_GET['_c'];

        $this->_modulePath = FW_MODULE_BASE_PATH . DS . $this->_id;

	}

    public function actions()
    {
        return $this->getControllerActions();
    }

    /**
     * 获得模块action映射表
     * @return array
     */
    protected function getControllerActions()
    {
        //@todo 以后考虑缓存
        $m = Modules::model()->find("name=:name and status=:status", array(
           ':name' => $this->_id,
            ':status' => Yii::app()->params['status']['ischecked'],
        ));

        if (!$m) return array();

        $dir = $this->_modulePath  . DS  . "actions" ;

        $iterator = new DirectoryIterator($dir);
        $actions = array();
        foreach ($iterator as $f) {
            $name = $f->getFilename();
            $ext = $f->getExtension();
            $ext = strtolower($ext);
            if ($f->isFile() && "php" == $ext) {
                $name = $f->getBasename(".php");
                $clsName = ucfirst($name) . "Action";

                require_once $f->getPathname();

                if (class_exists($clsName, false) && get_parent_class($clsName) == 'FWAction') {
                    $actions[$name] = $clsName;
                }
            }
        }
        return $actions;
    }

    public function getId()
    {
        return $this->_id;
    }

    /**
     * 取得模块模板路径
     * @return string
     */
    public function getViewPath()
    {
        return $this->_modulePath . DS . "templates";
    }
}