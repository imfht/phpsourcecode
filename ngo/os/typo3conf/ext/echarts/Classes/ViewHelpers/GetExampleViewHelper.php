<?php
namespace Jykj\Echarts\ViewHelpers;

/*  | This extension is made for TYPO3 CMS and is licensed
 *  | under GNU General Public License.
 *  |
 *  | (c) 2012-2019 Armin Vieweg <armin@v.ieweg.de>
 */
use Jykj\Echarts\Domain\Model\Echarts;
use Jykj\Echarts\Domain\Repository\EchartsRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns the chart example view.
 */
class GetExampleViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('echarts', Echarts::class, 'echarts item', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments,\Closure $renderChildrenClosure,RenderingContextInterface $renderingContext) {
        /** @var Echarts $echarts */
        $echarts = $arguments['echarts'];
        $echartContent = static::getEchartContent($echarts);
        $modalHtml = static::getModal(['modalid'=>$echarts->getUid(), 'modalbody'=>$echartContent]);
        return $modalHtml;

        /*
        $query = static::getChartRepository()->createQuery();
        $query->matching($query->in('uid', $uidList));
        return $query->execute()->toArray();
        */
    }

    /**
     * @return EchartsRepository
     */
    protected static function getChartRepository()
    {
        return GeneralUtility::makeInstance(ObjectManager::class)->get(EchartsRepository::class);
    }

    /**
     * 获取Echarts代码
     *
     * @param [type] $echarts
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    protected static function getEchartContent($echarts = null)
    {
        $codedir = ExtensionManagementUtility::extPath('echarts') . $echarts->getCode();
        $echartContent = file_get_contents($codedir);
        if ($echarts->getDatas()=='') {
            $params = static::getParams($echarts);
        } else {
            $params = unserialize($echarts->getDatas());
            $params['width'] = '550px';
            $params['height'] = '400px';
            $params['alignment'] = 'center';
        }
        foreach ($params as $key => $str) {
            $echartContent = str_replace('###' . strtoupper($key) . '###', $str, $echartContent);
        }
        return $echartContent;
    }

    /**
     * 获取Echarts常用参数
     *
     * @param [type] $echarts
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    protected static function getParams($echarts = null)
    {
        $params['title'] = static::getTitle($echarts);
        $params['boxid'] = 'chartBox'.$echarts->getUid();
        $params['themename'] = substr($echarts->getListtheme(), 0, -3);
        $params['themelink'] = $echarts->getTheme();
        $params['tooltip'] = (!$echarts->getTooltip()) ? '' : static::getTooltip($echarts->getEchart());
        $params['toolbox'] = (!$echarts->getToolbox()) ? '' : static::getToolbox();
        $params['width'] = (!$echarts->getWidth()) ? '500px' : $echarts->getWidth();
        $params['height'] = (!$echarts->getHeight()) ? '400px' : $echarts->getHeight();
        $params['alignment'] = (!$echarts->getAlignment()) ? 'center' : $echarts->getAlignment();
        if ($echarts->getDatas()=='') {
            if ($echarts->getEchart()=='bar' || $echarts->getEchart()=='line') {
                $params['axis'] = '["星期一","星期二","星期三","星期四","星期五","星期六","星期日"]';
                $params['series'] = '["30","47","77","83","99","135","40"]';
            }
            if ($echarts->getEchart()=='pie') {
                $params['axis'] = '["星期一","星期二","星期三","星期四","星期五","星期六","星期日"]';
                $params['series'] = '["30","47","77","83","99","135","40"]';
            }
            if ($echarts->getEchart()=='funnel') {
                $params['axis'] = '["星期一","星期二","星期三","星期四","星期五","星期六","星期日"]';
                $params['series'] = '["30","47","77","83","99","135","40"]';
            }
        }
        return $params;
    }

    /**
     * 获取Echarts自定义数据
     *
     * @param [type] $echarts
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    protected static function getDatas($echarts = null)
    {
        $datas = '';
        if ($echarts->getDatas()!='') {
            $datas = $echarts->getDatas();
        }
        return $datas;
    }

    /**
     * 获取Echarts提示框
     *
     * @param [type] $echarts
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    protected static function getTitle($echarts = null)
    {
        list($x, $y) = explode('-',$echarts->getTitlepos());
        $x = ($x=='left') ? '60' : $x ;
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
    protected static function getTooltip($type='bar')
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
                        formatter: \"{a} <br/>{b} : {c}%\"
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
                        formatter: \"数据 <br/>{b} : {c}\"
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
    protected static function getToolbox()
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
     * 生成模态框
     *
     * @param array $data
     * @return void
     * @author wanghongbin <wanghongbin@ngoos.org>
     * @since
     */
    protected static function getModal($data = array())
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
                        <div class="modal-body"><div sty;e="clear:both"></div>'.$data['modalbody'].'</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        </div>
                    </div>
                </div>
            </div>';
    }
}
