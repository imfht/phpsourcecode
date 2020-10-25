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
 * ComController
 */
class ComController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * echartsRepository
     * 
     * @var \Jykj\Echarts\Domain\Repository\EchartsRepository
     * @inject
     */
    protected $echartsRepository = null;

    /**
     * 获取Echarts代码
     *
     * @param [type] $echarts
     * @param array $datas
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function getEchartContent($echarts = null,$datas=array())
    {
        $codedir = ExtensionManagementUtility::extPath('echarts') . $echarts->getCode();
        $echartContent = file_get_contents($codedir);
        if (!empty($datas)) {
            $params = self::getParams($echarts,$datas);
            $echartDatas = serialize($params);
            foreach ($params as $key => $str) {
                $echartContent = str_replace('###' . strtoupper($key) . '###', $str, $echartContent);
            }
            return ['echartContent' => $echartContent,'echartDatas' => $echartDatas];
        } else {
            $params = unserialize($echarts->getDatas());
            foreach ($params as $key => $str) {
                $echartContent = str_replace('###' . strtoupper($key) . '###', $str, $echartContent);
            }
            return ['echartContent' => $echartContent];
        }
        
    }

    /**
     * 获取Echarts常用参数
     *
     * @param [type] $echarts
     * @param array $datas
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private function getParams($echarts = null,$datas = array())
    {
        $params['title'] = self::getTitle($echarts);
        $params['boxid'] = 'chartBox-'.time();
        $params['themename'] = substr($echarts->getListtheme(), 0, -3);
        $params['themelink'] = $echarts->getTheme();
        $params['tooltip'] = (!$echarts->getTooltip()) ? '' : self::getTooltip($datas['charttype']);
        $params['toolbox'] = (!$echarts->getToolbox()) ? '' : self::getToolbox();
        $params['width'] = (!$echarts->getWidth()) ? '600px' : $echarts->getWidth();
        $params['height'] = (!$echarts->getHeight()) ? '400px' : $echarts->getHeight();
        $params['alignment'] = (!$echarts->getAlignment()) ? 'center' : $echarts->getAlignment();
        //柱状图和折线图
        if ($datas['charttype']=='bar' || $datas['charttype']=='line') {
            $params['axis'] = self::json($datas['axis']);
            $params['series'] = self::json($datas['series']);
            $params['seriesname'] = $datas['name'];
        }
        //饼状图和漏斗图
        if ($datas['charttype']=='pie' || $datas['charttype']=='funnel') {
            $params['legend'] = self::json($datas['axis']);
            $params['seriesname'] = $datas['name'];
            $seriesdata = array();
            for ($i=0; $i < count($datas['value']); $i++) { 
                $seriesdata[] = ['value'=>$datas['value'][$i],'name'=>$datas['axis'][$i]];
            }
            $params['seriesdata'] = self::json($seriesdata);
            if ($datas['charttype']=='funnel') $params['seriesmax'] = max($datas['value']);
        }
        // dump($params);
        // exit;
        return $params;
    }

    /**
     * 获取Echarts提示框
     *
     * @param [type] $echarts
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private function getTitle($echarts = null)
    {
        list($x, $y) = explode('-',$echarts->getTitlepos());
        $x = ($x=='left') ? '80' : $x ;
        return "title:{
            text:'".$echarts->getTitle()."',
            subtext:'".$echarts->getSubtitle()."',
            sublink: '".$echarts->getSublink()."',
            left:'".$x."',
            top:'".$y."',
            textAlign:'center'
        },";
    }

    /**
     * 获取Echarts提示框
     *
     * @param [type] $echarts
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private function getTooltip($type='bar')
    {
        switch ($type) {
            case 'funnel':
                $tooltip = "tooltip: {
                        trigger: 'item',
                        formatter: \"{a} <br/>{b} : {c}%\"
                    },";
                break;
            case 'line':
                $tooltip = "tooltip: {
                        trigger: 'item',
                        formatter: \"{a} <br/>{b} : {c}\"
                    },";
                break;
            case 'pie':
                $tooltip = "tooltip: {
                        trigger: 'item',
                        formatter: \"{a} <br/>{b} : {c}%\"
                    },";
                break;
            default:
                $tooltip = "tooltip: {
                        trigger: 'item',
                        formatter: \"{a} <br/>{b} : {c}\"
                    },";
                break;
        }
        return $tooltip;
    }

    /**
     * 获取Echarts工具栏
     *
     * @param [type] $echarts
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private function getToolbox()
    {
        return "toolbox: {
			feature: {
				dataView: { readOnly: false },
				restore: {},
				saveAsImage: {}
			}
		},";
    }

    /**
     * 以模态框形式展示
     *
     * @param array $data
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function getModal($data = array())
    {
        return '<style>
            .echarts-modal .modal-body div:first-child{
                margin-left: auto;
                margin-right: auto;
            }
        </style>
        <div class="modal echarts-modal fade" id="modalBox'.$data['modalid'].'" tabindex="-1" role="dialog" aria-labelledby="modalLabel'.$data['modalid'].'" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title" id="modalLabel'.$data['modalid'].'">图表预览</h4>
                        </div>
                        <div class="modal-body">'.$data['modalbody'].'</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        </div>
                    </div>
                </div>
            </div>';
    }

    /**
     * 展示图表代码
     *
     * @param array $data
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    public function getChartCode($data = array())
    {
        
    }

    /**
     * 使用特定function对数组中所有元素做处理
     *
     * @param [type] $array 要处理的字符串
     * @param [type] $function 要执行的函数
     * @param boolean $apply_to_keys_also 是否也应用到key上
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
	{
	    static $recursive_counter = 0;
	    if (++$recursive_counter > 1000) {
	        die('possible deep recursion attack');
	    }
	    foreach ($array as $key => $value) {
	        if (is_array($value)) {
	            self::arrayRecursive($array[$key], $function, $apply_to_keys_also);
	        } else {
	            $array[$key] = $function($value);
	        }
	  
	        if ($apply_to_keys_also && is_string($key)) {
	            $new_key = $function($key);
	            if ($new_key != $key) {
	                $array[$new_key] = $array[$key];
	                unset($array[$key]);
	            }
	        }
	    }
	    $recursive_counter--;
    }
    
    /**
     * 将数组转换为JSON字符串（兼容中文）
     *
     * @param [type] $array
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    private function json($array) {
	    self::arrayRecursive($array, 'urlencode', true);
	    $json = json_encode($array);
	    return urldecode($json);
	}
}
