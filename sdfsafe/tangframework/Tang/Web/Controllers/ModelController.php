<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Web\Controllers;
use Tang\Database\Sql\Model;
use Tang\Database\Sql\ORM\Builder;
use Tang\Exception\SystemException;
use Tang\Pagination\NowPageLtMaxPageException;
use Tang\Services\EventService;
use Tang\Token\TokenService;
use Tang\Web\Session\SessionService;

/**
 * 附属模型的控制器。
 * 自带了列表 添加 删除 更新方法
 * 能方便开发者快速开发应用
 * Class ModelController
 * @package Tang\Web\Controllers
 */
class ModelController extends WebController
{
    /**
     * 指向的模型名
     * @var string
     */
    protected $modelName;
    /**
     * 模型
     * @var Model
     */
    protected $model;
    /**
     * 分页每页数量
     * @var int
     */
    protected $listRows = 20;
    /**
     * 事件
     * @var \Tang\Event\IEvent
     */
    protected $event = null;

    public function __construct()
    {
        $this->event = EventService::getService();
    }

    /**
     * 默认的列表方法
     * 如果$query为null.那么会从模型上新建一个ORM查询构建器
     * @param Builder $query
     */
    public function main(Builder $query = null)
    {
        if($query == null)
        {
            $query = $this->model->newQuery();
			$query->orderBy($this->model->getPrimaryKey(),'desc');
        }
        $page = $this->request->get('page',1);
        try
        {
            $result = $query->getPagination($page,$this->listRows);
            $this->view->merginData($result);
        } catch(NowPageLtMaxPageException $e)
        {
            $this->notFound($e->getMessage());
        }
        $this->display();
    }

    /**
     * 插入操作
     * $faildUrl根据返回的错误码进行跳转，如果没有对应的错误码跳转地址，则返回第一个错误地址
     * @param string $sucessUrl 成功返回地址
     * @param array $faildUrl 失败返回地址
     */
    public function insert($sucessUrl='',array $faildUrl=array())
    {
        $marking = $this->request->getMarking();
        $session = SessionService::getService();
        try
        {
            if($this->request->getHttpMethod() == 'post' && $this->request->CSRF()->validate() && ($this->isAjax || TokenService::getService()->validate($marking)))
            {

                $this->event->attach('insertData',$_POST['data'],$this->model,$marking);
                $session->delete($marking);
                $this->message($this->i18n->get('The success of the operation!'),200,$sucessUrl);
            }
        }catch (\Exception $e)
        {
            $session->set($marking,$_POST['data']);
            $this->message($e->getMessage(),$e->getCode(),$this->getCodeUrl($e->getCode(),$faildUrl));
        }
        $this->view->assgin('marking',$marking);
        $this->view->assgin('value',$session->get($marking,array()));
        $this->display();
    }

    public function get()
    {
        try
        {
            $value = $this->checkDataPrimary();
        }
        catch (\Exception $e)
        {
            $this->message($e->getMessage(),$e->getCode(),$this->getCodeUrl($e->getCode(),$faildUrl));
        }
        $this->view->assgin('value',$value);
        $this->display();
    }
    /**
     * 更新操作
     * $faildUrl根据返回的错误码进行跳转，如果没有对应的错误码跳转地址，则返回第一个错误地址
     * @param string $sucessUrl 成功返回地址
     * @param array $faildUrl 失败返回地址
     */
    public function update($sucessUrl='',array $faildUrl=array())
    {
        try
        {
            $value = $this->checkDataPrimary();
            $marking = $this->request->getMarking();
            if($this->request->getHttpMethod() == 'post' && $this->request->CSRF()->validate() && ($this->isAjax || TokenService::getService()->validate($marking)))
            {
                    $this->event->attach('updateData',$_POST['data'],$value,$marking);
                    $this->message($this->i18n->get('The success of the operation!'),200,$sucessUrl);
            }
        }
        catch (\Exception $e)
        {
            $this->message($e->getMessage(),$e->getCode(),$this->getCodeUrl($e->getCode(),$faildUrl));
        }
        $this->view->assgin('marking',$marking);
        $this->view->assgin('value',$value);
        $this->display();
    }

    /**
     * 删除操作
     * $faildUrl根据返回的错误码进行跳转，如果没有对应的错误码跳转地址，则返回第一个错误地址
     * @param string $sucessUrl 成功返回地址
     * @param array $faildUrl 失败返回地址
     */
    public function delete($sucessUrl='',array $faildUrl=array())
    {
        try
        {
            $this->request->CSRF()->validate();
            $model = $this->checkDataPrimary();
            $this->event->attach('deleteData',$model);
        } catch (\Exception $e)
        {
            $this->message($e->getMessage(),$e->getCode(),$this->getCodeUrl($e->getCode(),$faildUrl));
        }
        $this->message($this->i18n->get('The success of the operation!'),200,$sucessUrl);
    }

    /**
     * @see WebController::endInvoke
     */
    protected function endInvoke()
    {
        if(!$this->modelName)
        {
            $this->modelName = $this->parameters->module.'.'.$this->parameters->controller;
        }
        $this->model = Model::loadModel($this->modelName);
        //增加默认删除事件
        $this->event->addListener('deleteData',function($model)
        {
            $model->delete();
        });
        //增加默认编辑事件
        $this->event->addListener('updateData',function($data,$model,$marking)
        {
            $model->update($data,$marking);
        });
        //增加默认添加事件
        $this->event->addListener('insertData',function($data,$model,$marking)
        {
            $model->insert($data,$marking);
        });
    }

    /**
     * 处理查询主键
     */
    protected function checkDataPrimary()
    {
        $primaryValue = $this->request->get($this->model->getPrimaryKey(),0);
        if(!$primaryValue || !($newModel = $this->model->find($primaryValue)))
        {
            throw new SystemException('Parameter error!');
        }
        $this->model = $newModel;
        return $newModel;
    }

    /**
     * 根据$code获取$url里面的url地址
     * @param $code
     * @param array $url
     * @return mixed|string
     */
    protected function getCodeUrl($code,array $url = array())
    {
        if(!$url || !isset($url[$code]))
        {
            return '';
        } else
        {
            return $url[$code];
        }
    }
}