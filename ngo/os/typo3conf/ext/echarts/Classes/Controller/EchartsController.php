<?php
namespace Jykj\Echarts\Controller;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Cache\CacheManager;

/***
 *
 * This file is part of the "统计数据图表" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 王宏彬 <wanghongbin816@gmail.com>, 宁夏极益科技邮箱公司
 *
 ***/
/**
 * EchartsController
 */
class EchartsController extends \Jykj\Echarts\Controller\ComController
{

    /**
     * 图表类型
     *
     * @var array
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private $charts = array(
        array('key' => 'bar','value' => '柱状图'),
        array('key' => 'pie','value' => '饼状图'),
        array('key' => 'line','value' => '折线图'),
        array('key' => 'funnel','value' => '漏斗图'),
    );

    /**
     * 显示位置
     *
     * @var array
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private $position = array(
        array('key' => 'left-top','value' => '左上方'),
        array('key' => 'right-top','value' => '右上方'),
        array('key' => 'center-top','value' => '上居中'),
        array('key' => 'left-bottom','value' => '左下方'),
        array('key' => 'right-bottom','value' => '右下方'),
        array('key' => 'center-bottom','value' => '下居中'),
    );

    /**
     * 前台显示位置
     *
     * @var array
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private $align = array(
        array('key' => 'center','value' => '默认(居中)'),
        array('key' => 'left','value' => '左对齐'),
        array('key' => 'center','value' => '居中'),
        array('key' => 'right','value' => '右对齐'),
    );
    
    /**
     * action list
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function listAction()
    {
        if($_GET["tx_echarts_pi1"]["@widget_0"]["currentPage"]){
            $page=$_GET["tx_echarts_pi1"]["@widget_0"]["currentPage"];
        }else{
            $page=1;
        }
        $this->view->assign('page', $page);
        $echart = $this->request->hasArgument('echart')?$this->request->getArgument('echart'):'';
        $keyword = $this->request->hasArgument('keyword')?$this->request->getArgument('keyword'):'';
        $echarts = $this->echartsRepository->findAlls($echart,$keyword);
        $this->view->assign('charts', $this->charts);
        $this->view->assign('echart', $echart);
        $this->view->assign('keyword', $keyword);
        $this->view->assign('echarts', $echarts);
    }

    /**
     * action chart
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function chartAction()
    {
        $echarts = $this->echartsRepository->findByUid($this->request->getArgument('echarts'));
        $paramss = $echarts->getDatas();
        $params = self::decodeUnicode(unserialize($paramss));
        if ($echarts->getEchart()=='bar' || $echarts->getEchart()=='line') {
            $axis = json_decode($params['axis']);
            $series = json_decode($params['series']);
            $name = $params['seriesname'];
            for ($i=0; $i < count($axis); $i++) { 
                $datas[] = ['axis' => $axis[$i], 'series' => $series[$i]];
            }
            $this->view->assign('name', $name);
        }
        if ($echarts->getEchart()=='pie' || $echarts->getEchart()=='funnel') {
            $axis = json_decode($params['axis']);
            $datas = json_decode($params['seriesdata']);
            $name = $params['seriesname'];
            for ($i=0; $i < count($axis); $i++) {
                $datas[] = ['axis' => $axis[$i], 'value' => $seriesdata[$i]];
            }
            $this->view->assign('name', $name);
        }
        $this->view->assign('themes', $this->getDirFile());
        $this->view->assign('charts', $this->charts);
        $this->view->assign('position', $this->position);
        $this->view->assign('align', $this->align);
        $this->view->assign('echarts', $echarts);
        $this->view->assign('datas', $datas);
    }


    /**
     * action show
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function showAction(\Jykj\Echarts\Domain\Model\Echarts $echarts)
    {
        $this->view->assign('echarts', $echarts);
    }
    /**
     * action bar
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function barAction()
    {
        $echarts = $this->echartsRepository->findByUid($this->settings['getChartBar']);
        if ($echarts) {
            $echartContent = $this->getEchartContent($echarts);
            $this->view->assign('echarts', $echarts);
            $this->view->assign('echartContent', $echartContent['echartContent']);
        }
    }
    /**
     * action line
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function lineAction()
    {
        $echarts = $this->echartsRepository->findByUid($this->settings['getChartLine']);
        if ($echarts) {
            $echartContent = $this->getEchartContent($echarts);
            $this->view->assign('echarts', $echarts);
            $this->view->assign('echartContent', $echartContent['echartContent']);
        }
    }
    /**
     * action pie
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function pieAction()
    {
        $echarts = $this->echartsRepository->findByUid($this->settings['getChartPie']);
        if ($echarts) {
            $echartContent = $this->getEchartContent($echarts);
            $this->view->assign('echarts', $echarts);
            $this->view->assign('echartContent', $echartContent['echartContent']);
        }
    }
    /**
     * action funnel
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function funnelAction()
    {
        $echarts = $this->echartsRepository->findByUid($this->settings['getChartFunnel']);
        if ($echarts) {
            $echartContent = $this->getEchartContent($echarts);
            $this->view->assign('echarts', $echarts);
            $this->view->assign('echartContent', $echartContent['echartContent']);
        }
    }

    /**
     * action new
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function newAction()
    {
        $this->view->assign('themes', $this->getDirFile());
        $this->view->assign('charts', $this->charts);
        $this->view->assign('position', $this->position);
        $this->view->assign('align', $this->align);
        $this->view->assign('chart', $this->request->hasArgument('chart')?$this->request->getArgument('chart'):'bar');
    }

    /**
     * action create
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function createAction(\Jykj\Echarts\Domain\Model\Echarts $echarts)
    {
        $datas = $this->request->hasArgument('datas')?$this->request->getArgument('datas'):array();
        $author = ($GLOBALS['TSFE']->fe_user->user['name']!='') ? $GLOBALS['TSFE']->fe_user->user['name'] : '匿名';
        $code = 'Resources/Public/Echarts/JScode/'.$echarts->getEchart().'.code';
        $com = $this->objectManager->get(ComController::class);
        $comData = $com->getEchartContent($echarts,$datas);
        $echartContent = $comData['echartContent'];
        $echartDatas = $comData['echartDatas'];

        $echarts->setAuthor($author);
        $echarts->setCode($code);
        $echarts->setDatas($echartDatas);
        $this->echartsRepository->add($echarts);
        $this->addFlashMessage('图表数据添加成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->redirect('list');
    }

    /**
     * action edit
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @ignorevalidation $echarts
     * @return void
     */
    public function editAction(\Jykj\Echarts\Domain\Model\Echarts $echarts)
    {
        $this->view->assign('themes', $this->getDirFile());
        $this->view->assign('charts', $this->charts);
        $this->view->assign('position', $this->position);
        $this->view->assign('align', $this->align);
        $this->view->assign('echarts', $echarts);
    }

    /**
     * action update
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function updateAction(\Jykj\Echarts\Domain\Model\Echarts $echarts)
    {
        $datas = $this->request->hasArgument('datas')?$this->request->getArgument('datas'):array();
        $code = 'Resources/Public/Echarts/JScode/'.$echarts->getEchart().'.code';
        $com = $this->objectManager->get(ComController::class);
        $comData = $com->getEchartContent($echarts,$datas);
        $echartContent = $comData['echartContent'];
        $echartDatas = $comData['echartDatas'];

        $echarts->setCode($code);
        $echarts->setDatas($echartDatas);
        $this->echartsRepository->update($echarts);
        $this->addFlashMessage('图表数据保存成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->redirect('list');
    }

    /**
     * action chartUpdate
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function chartUpdateAction()
    {
        // $datas = $this->request->getArguments();
        $datas = $this->request->getArgument('datas');
        // dump($datas);exit;
        $echarts = $this->echartsRepository->findByUid($this->request->getArgument('euid'));
        $com = $this->objectManager->get(ComController::class);
        $comData = $com->getEchartContent($echarts,$datas);
        $echartContent = $comData['echartContent'];
        $echartDatas = $comData['echartDatas'];
        $echarts->setDatas($echartDatas);
        $this->echartsRepository->update($echarts);
        $this->addFlashMessage('图表数据添加成功!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->redirect('list');
    }

    /**
     * action delete
     * 
     * @param Jykj\Echarts\Domain\Model\Echarts
     * @return void
     */
    public function deleteAction(\Jykj\Echarts\Domain\Model\Echarts $echarts)
    {
        $this->addFlashMessage('图表已删除！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->echartsRepository->remove($echarts);
        $this->redirect('list');
    }

    /**
     * 多选删除
     *
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function multideleteAction()
    {
        //多选删除
        $list = $this->request->hasArgument('datas') ? $this->request->getArgument('datas') : [];
        if ($list['items']) {
            //接收到格式 1,2,的形式，需要去掉最后一位
            $item = substr($list['items'], 0, -1);
            
            $iRet=$this->echartsRepository->deleteByUidstring($item);
            if($iRet>0){
                $this->addFlashMessage('删除成功！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
                //刷新前台缓存
                GeneralUtility::makeInstance(CacheManager::class)->flushCachesInGroup('pages');
                $this->redirect('list');
            }else{
                $this->addFlashMessage('删除失败！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
                $this->redirect('list');
            }
        }
        $this->addFlashMessage('没有可删除的对象！', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        $this->redirect('list');
    }

    /**
     * action ajaxdata
     * 
     * @return void
     */
    public function ajaxdataAction()
    {
    }

    /**
     * action export
     * 
     * @return void
     */
    public function exportAction()
    {
    }

    /**
     * 获取目录下文件列表 
     *
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private function getDirFile()
    {
        //取得文件所在目录
        $dir  = ExtensionManagementUtility::extPath('echarts') . 'Resources/Public/Echarts/Themes/';
        $link = GeneralUtility::locationHeaderUrl('typo3conf/ext/echarts/Resources/Public/Echarts/Themes/');
        //判断目标目录是否是文件夹
        $file_arr = array();
        if(is_dir($dir)){
            //打开
            if($files = scandir($dir)){
                //遍历
                for ($i=0; $i < count($files); $i++) { 
                    if ($files[$i] != '.' && $files[$i] != '..') {
                        $file_arr[] = array('file' => $link.$files[$i],'title'=>substr($files[$i], 0, -3));
                    }
                }
            }
        }
        return $file_arr;
    }

    /**
     * Unicode转UTF-8 
     *
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private function decodeUnicode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
            create_function(
                '$matches',
                'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
            ),
            $str);
    }
}
