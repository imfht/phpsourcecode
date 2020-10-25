<?php
/**
 * Class ModuleController
 */
class ModulesController extends Controller
{
    protected function menus()
    {
        return array(
            'modules',
        );
    }

    protected $message = array(
        'install_error:module_is_installed' => '安装模块失败，模块已经安装',
        'install_error:fwversion_not_support' => '安装模块失败，该模块依赖飞舞小说系统版本为：%s，当前飞舞系统版本为：%s',
        'install_error' => '安装模块失败',
        'install_success' => '安装模块成功',
        'uninstall_error' => '卸载模块失败',
        'uninstall_success' => '卸载模块成功',
        'stop_error' => '停止模块失败',
        'stop_success' => '停止模块成功',
        'start_error' => '启用模块失败',
        'start_success' => '启用模块成功',
        'scan_result' => '共找到 %d 个未安装模块',
    );

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria=new CDbCriteria();

        if(!empty($_GET['Modules']['title']))
            $criteria->addSearchCondition('title',$_GET['Modules']['title']);


//        $criteria->addNotInCondition('status', array(Yii::app()->params['status']['isdelete']));

		$dataProvider=new CActiveDataProvider('Modules',array(
			'criteria'=>$criteria,
			'pagination'=>array(
        		'pageSize'=>Yii::app()->params['girdpagesize'],
    		),
            'sort'=>array(
                'defaultOrder'=>array(
                    'id' => CSort::SORT_DESC,
                ),
                'attributes'=>array(
                    'id',
                    'createtime',
                    'sort',
                ),
            ),
		));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
//			'categorys'=> Category::model()->showAllSelectCategory(Category::SHOW_ALLCATGORY),
//            'model' => Modules::model(),
		));
	}

    /**
     * 扫描本地目录
     */
    public function actionScan()
    {
        $localModules = $this->scanModules();

        $modules = Modules::model()->findAll();

        foreach ($modules as $k => $v) {
            if (isset($localModules[$v->name])) {
                unset($localModules[$v->name]);
            }
        }
        $count = 0;
        if (is_array($localModules)) {
            reset($localModules);

            foreach ($localModules as $k => $v) {
                $m = new Modules();
                $m->title = $v['config']['name'];
                $m->name = $k;
                $m->author = $v['config']['author'];
                $m->version = $v['config']['version'];
                $m->fwversion = $v['config']['fwversion'];
                $m->description = $v['config']['description'];
                $m->createtime = time();
                $m->updatetime = time();
                $m->status = 0;

                $m->save();
                $count++;
            }
        }

        $r = sprintf($this->message['scan_result'], $count);

        echo $r;

        Yii::app()->end();
    }

    /**
     * 安装模块
     * @param $id
     */
    public function actionSetup($id)
    {
        if(!Yii::app()->request->isPostRequest) return;

        $id = intval($id);

        $m = $this->loadModel($id);
        if (!$m) {
            echo $this->message['install_error'];
            Yii::app()->end();
        }

        if ($m->status == 1) {
            echo $this->message['install_error:module_is_installed'];
            Yii::app()->end();
        }

        if (version_compare(FWXSVersion, $m->fwversion) < 0) {
            $s = sprintf($this->message['install_error:fwversion_not_support'], $m->fwversion, FWXSVersion);
            echo $s;
            Yii::app()->end();
        }

        $moduleFile = FW_MODULE_BASE_PATH . DS . $m->name . DS . $m->name . ".php";

        try {
            include_once $moduleFile;
            $moduleCls = ucfirst($m->name) . "Module";
            if (class_exists($moduleCls, false)) {
                $setup = new $moduleCls();
                if ($setup instanceof IModule) {
                    $r = $setup->install(Yii::app()->db);
                    if ($r) {
                        $m->status = 1;
                        $m->save();
                        echo $this->message['install_success'];
                        Yii::app()->end();
                    }
                }
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            echo $this->message['install_error'];
            Yii::app()->end();
        }
    }

    /**
     * 卸载模块
     * @param int $id
     */
    public function actionDelete($id)
    {
        if(!Yii::app()->request->isPostRequest) return;

        $id = intval($id);

        $m = $this->loadModel($id);
        if (!$m) {
            echo $this->message['uninstall_error'];
            Yii::app()->end();
        }

        // 已经安装，需要先执行卸载
        if ($m->status == 1) {
            $moduleFile = FW_MODULE_BASE_PATH . DS . $m->name . ".php";

            try {
                include_once $moduleFile;
                $moduleCls = ucfirst($m->name) . "Module";
                if (class_exists($moduleCls, false)) {
                    $setup = new $moduleCls();
                    if ($setup instanceof IModule) {
                        $r = $setup->uninstall(Yii::app()->db);
                    }
                }
            } catch (Exception $e) {
            }
        }

        // 强制卸载
        $this->loadModel($id)->delete();

        echo $this->message['uninstall_success'];
        Yii::app()->end();
    }

    /**
     * 停止模块
     * @param $id
     */
    public function actionStop($id)
    {
        if(!Yii::app()->request->isPostRequest) return;

        $id = intval($id);
        $m = $this->loadModel($id);
        if (!$m) {
            echo $this->message['stop_error'];
            Yii::app()->end();
        }

        $m->status = -1;
        $m->save();

        echo $this->message['stop_success'];
        Yii::app()->end();
    }

    /**
     * 启用模块
     * @param $id
     */
    public function actionStart($id)
    {
        if(!Yii::app()->request->isPostRequest) return;

        $id = intval($id);
        $m = $this->loadModel($id);
        if (!$m) {
            echo $this->message['start_error'];
            Yii::app()->end();
        }

        $m->status = 1;
        $m->save();

        echo $this->message['start_success'];
        Yii::app()->end();
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Modules::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'');
		return $model;
	}

    /**
     * 扫描本地目录获取模块
     * @return array
     */
    protected function scanModules()
    {
        $dir = FW_MODULE_BASE_PATH ;

        $iterator = new DirectoryIterator($dir);
        $modules = array();
        foreach ($iterator as $f) {
            $name = $f->getFilename();
//            $ext = $f->getExtension();
//            $ext = strtolower($ext);
            if ($f->isDir() && !$f->isDot()) {
                $moduleFile = $f->getPathname() . DS . $name . ".php";
                $moduleConfigFile = $f->getPathname() . DS . "conf.php";
                if (is_file($moduleConfigFile) && is_file($moduleFile)) {
                    include_once $moduleFile;
                    $moduleCls = ucfirst($name) . "Module";
                    if (class_exists($moduleCls, false)) {
                        $m = new $moduleCls();
                        if ($m instanceof IModule) {
                            $modules[$name] = array();
//                            $modules[$name]['module'] = $m;
                            $modules[$name]['config'] = include_once $moduleConfigFile;
                        }
                    }
                }
            }
        }
        return $modules;
    }
}
