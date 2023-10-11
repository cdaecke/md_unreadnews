<?php
defined('TYPO3') or die();

call_user_func(
    function()
    {
        /**
         * Register plugin
         *
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'MdUnreadnews',
            'Unread',
            'LLL:EXT:md_unreadnews/Resources/Private/Language/locallang.xlf:unreadPlugin'
        );

        /**
         * Load flexform for unread plugin
         *
         */
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['mdunreadnews_unread'] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            'mdunreadnews_unread', 
            'FILE:EXT:md_unreadnews/Configuration/FlexForms/UnreadPlugin.xml'
        );
    }
);
