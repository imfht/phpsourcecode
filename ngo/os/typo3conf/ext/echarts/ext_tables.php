<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {
        $flexforms = 'FILE:EXT:echarts/Configuration/FlexForms/flexform_';
        $plugins = array(
            ['plugin' => 'Pi1','title' => 'Echarts-数据图表','flex'=> 'pi1'],
            ['plugin' => 'Bar','title' => 'Echarts-柱状图','flex'=> 'bar'],
            ['plugin' => 'Line','title' => 'Echarts-折线图','flex'=> 'line'],
            ['plugin' => 'Pie','title' => 'Echarts-饼状图','flex'=> 'pie'],
            ['plugin' => 'Funnel','title' => 'Echarts-漏斗图','flex'=> 'funnel'],
        );
        foreach ($plugins as $k => $plugin) {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('Jykj.Echarts',$plugin['plugin'], $plugin['title']);
            $pluginSignature = 'echarts_'.$plugin['flex'];
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, $flexforms . $plugin['flex'] . '.xml');
        }
        
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('echarts', 'Configuration/TypoScript', '统计数据图表');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_echarts_domain_model_echarts', 'EXT:echarts/Resources/Private/Language/locallang_csh_tx_echarts_domain_model_echarts.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_echarts_domain_model_echarts');

    }
);
