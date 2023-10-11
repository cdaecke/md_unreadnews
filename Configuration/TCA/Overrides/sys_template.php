<?php
defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'md_unreadnews', 
    'Configuration/TypoScript', 
    'Unread news'
);
