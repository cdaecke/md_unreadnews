<?php
defined('TYPO3') or die();

use Mediadreams\MdUnreadnews\Controller\UnreadnewsController;
use Mediadreams\MdUnreadnews\Hooks\TCEmainHook;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(
    function () {

        ExtensionUtility::configurePlugin(
            'MdUnreadnews',
            'Unread',
            [
                UnreadnewsController::class => 'list'
            ],
            // non-cacheable actions
            [
                UnreadnewsController::class => 'list'
            ]
        );

        ExtensionUtility::configurePlugin(
            'MdUnreadnews',
            'UnreadCount',
            [
                UnreadnewsController::class => 'allUnreadCount'
            ],
            [
                UnreadnewsController::class => 'allUnreadCount'
            ]
        );

        ExtensionUtility::configurePlugin(
            'MdUnreadnews',
            'UnreadCategory',
            [
                UnreadnewsController::class => 'categoryCount'
            ],
            [
                UnreadnewsController::class => 'categoryCount'
            ]
        );

        ExtensionUtility::configurePlugin(
            'MdUnreadnews',
            'UnreadIsUnread',
            [
                UnreadnewsController::class => 'isUnread'
            ],
            [
                UnreadnewsController::class => 'isUnread'
            ]
        );

        ExtensionUtility::configurePlugin(
            'MdUnreadnews',
            'UnreadRemove',
            [
                UnreadnewsController::class => 'removeUnread'
            ],
            [
                UnreadnewsController::class => 'removeUnread'
            ]
        );

        $iconRegistry = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        
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
            ['md_unreadnews'] = TCEmainHook::class;

        $GLOBALS['TYPO3_CONF_VARS']
            ['SC_OPTIONS']
            ['t3lib/class.t3lib_tcemain.php']
            ['processCmdmapClass']
            ['md_unreadnews_delete'] = TCEmainHook::class;
    }
);