<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Mediadreams.MdUnreadnews',
            'Unread',
            [
                'Unreadnews' => 'list, isUnread, allUnreadCount, categoryCount, removeUnread'
            ],
            // non-cacheable actions
            [
                'Unreadnews' => 'isUnread, allUnreadCount, categoryCount, removeUnread'
            ]
        );

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        
        $iconRegistry->registerIcon(
            'md_unreadnews-plugin-unread',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:md_unreadnews/Resources/Public/Icons/user_plugin_unread.svg']
        );

        // hook into saving process of news records
        $GLOBALS['TYPO3_CONF_VARS']
            ['SC_OPTIONS']
            ['t3lib/class.t3lib_tcemain.php']
            ['processDatamapClass']
            ['md_unreadnews'] = 'Mediadreams\\MdUnreadnews\\Hooks\\TCEmainHook';

    }
);