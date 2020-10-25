<?php
defined('TYPO3_MODE') || die();

$boot = function ($packageKey) {

    if (class_exists(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)) {
        $conf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
        )->get($packageKey);
    } else {
        // Fallback for 8LTS
        $conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$packageKey]);
    }

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MiniFranske.' . $packageKey,
        'Mediagallery',
        [
            'MediaAlbum' => 'index,nestedList,flatList,showAlbum,showAlbumByConfig,showAsset,random',
        ],
        // non-cacheable actions
        [
            'MediaAlbum' => 'random',
        ]
    );

    // Page TSConfig
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $packageKey . '/Configuration/TSConfig/Page.ts">');

    // Resource Icon hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_iconworks.php']['overrideResourceIcon']['FsMediaGallery'] =
        'MiniFranske\\FsMediaGallery\\Hooks\\IconUtilityHook';

    // Module header bar buttons
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Backend\Template\Components\ButtonBar']['getButtonsHook']['FsMediaGallery'] =
        'MiniFranske\\FsMediaGallery\\Hooks\\DocHeaderButtonsHook->moduleTemplateDocHeaderGetButtons';

    // refresh file tree after changen in media album recored (sys_file_collection)
    $GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        'MiniFranske\\FsMediaGallery\\Hooks\\ProcessDatamapHook';
    $GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
        'MiniFranske\\FsMediaGallery\\Hooks\\ProcessDatamapHook';

    // Real Url AutoConfiguration
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration'][$packageKey] =
        'EXT:' . $packageKey . '/Classes/Hooks/RealUrlAutoConfiguration.php:MiniFranske\FsMediaGallery\Hooks\RealUrlAutoConfiguration->addNewsConfig';

    // EXT:news >= 3.2.0 support
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News'][] = $packageKey;

    // Page module hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['fsmediagallery_mediagallery']['fs_media_gallery'] =
        'MiniFranske\\FsMediaGallery\\Hooks\\PageLayoutView->getExtensionSummary';

    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
    $signalSlotDispatcher->connect(
        'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
        \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFolderMove,
        'MiniFranske\\FsMediaGallery\\Hooks\\FolderChangedSlot',
        'preFolderMove'
    );
    $signalSlotDispatcher->connect(
        'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
        \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderMove,
        'MiniFranske\\FsMediaGallery\\Hooks\\FolderChangedSlot',
        'postFolderMove'
    );
    $signalSlotDispatcher->connect(
        'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
        \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFolderDelete,
        'MiniFranske\\FsMediaGallery\\Hooks\\FolderChangedSlot',
        'preFolderDelete'
    );
    $signalSlotDispatcher->connect(
        'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
        \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderDelete,
        'MiniFranske\\FsMediaGallery\\Hooks\\FolderChangedSlot',
        'postFolderDelete'
    );
    $signalSlotDispatcher->connect(
        'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
        \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFolderRename,
        'MiniFranske\\FsMediaGallery\\Hooks\\FolderChangedSlot',
        'preFolderRename'
    );
    $signalSlotDispatcher->connect(
        'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
        \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderRename,
        'MiniFranske\\FsMediaGallery\\Hooks\\FolderChangedSlot',
        'postFolderRename'
    );

    // *** Register file signals to clear the cache when enabled in extension setteings ***
    if (!empty($conf['clearCacheAfterFileChange']) && $conf['clearCacheAfterFileChange']) {

        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
            \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileAdd,
            'MiniFranske\\FsMediaGallery\\Hooks\\FileChangedSlot',
            'postFileAdd'
        );

        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
            \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileCreate,
            'MiniFranske\\FsMediaGallery\\Hooks\\FileChangedSlot',
            'postFileCreate'
        );

        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
            \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileCopy,
            'MiniFranske\\FsMediaGallery\\Hooks\\FileChangedSlot',
            'postFileCopy'
        );

        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
            \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileMove,
            'MiniFranske\\FsMediaGallery\\Hooks\\FileChangedSlot',
            'postFileMove'
        );

        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
            \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileDelete,
            'MiniFranske\\FsMediaGallery\\Hooks\\FileChangedSlot',
            'postFileDelete'
        );

        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
            \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileRename,
            'MiniFranske\\FsMediaGallery\\Hooks\\FileChangedSlot',
            'postFileRename'
        );

        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
            \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileReplace,
            'MiniFranske\\FsMediaGallery\\Hooks\\FileChangedSlot',
            'postFileReplace'
        );
    }


    if (!empty($conf['enableAutoCreateFileCollection']) && $conf['enableAutoCreateFileCollection']) {
        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
            \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderAdd,
            'MiniFranske\\FsMediaGallery\\Hooks\\FolderChangedSlot',
            'postFolderAdd'
        );
    }

    // File tree icon adjustments
    $signalSlotDispatcher->connect(
        'TYPO3\\CMS\\Core\\Imaging\\IconFactory',
        'buildIconForResourceSignal',
        'MiniFranske\\FsMediaGallery\\Hooks\\IconFactory',
        'buildIconForResource'
    );

    if (TYPO3_MODE === 'BE') {

        $GLOBALS['TYPO3_CONF_VARS']['BE']['ContextMenu']['ItemProviders'][1547740001] = \MiniFranske\FsMediaGallery\ContextMenu\ItemProviders\FsMediaGalleryProvider::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/backend.php']['constructPostProcess'][] = \MiniFranske\FsMediaGallery\Hooks\BackendControllerHook::class . '->addJavaScript';

        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Install\\Service\\SqlExpectedSchemaService',
            'tablesDefinitionIsBeingBuilt',
            'MiniFranske\\FsMediaGallery\\Hooks\\Install',
            'tablesDefinitionIsBeingBuiltSlot'
        );

        $signalSlotDispatcher->connect(
            'TYPO3\CMS\Extensionmanager\Utility\InstallUtility',
            'tablesDefinitionIsBeingBuilt',
            'MiniFranske\\FsMediaGallery\\Hooks\\Install',
            'tablesDefinitionIsBeingBuiltForExtension'
        );
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['realurlAliasMediaAlbumsSlug']
        = \MiniFranske\FsMediaGallery\Updates\RealurlAliasMediaAlbumsSlug::class; // Recommended before 'populateMedialAlbumsSlug'

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['populateMedialAlbumsSlug']
        = \MiniFranske\FsMediaGallery\Updates\PopulateMedialAlbumsSlug::class;
};
$boot($_EXTKEY);
unset($boot);
