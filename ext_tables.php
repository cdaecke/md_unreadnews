<?php
defined('TYPO3') or die();

call_user_func(
    function()
    {

        /**
         * TODO: Remove when TYPO3 v11 is not supported anymore. The TCA ctrl option `ignorePageTypeRestriction` is used starting in TYPO3 v12
         */
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mdunreadnews_domain_model_unreadnews');

    }
);
