<?php
defined('TYPO3') or die();

return [
    'ctrl' => [
        'title' => 'LLL:EXT:md_unreadnews/Resources/Private/Language/locallang_db.xlf:tx_mdunreadnews_domain_model_unreadnews',
        'label' => 'news',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id', // TODO: Remove as soon as TYPO3 v11 is not supported anymore
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'default_sortby' => 'ORDER BY news_datetime DESC',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'searchFields' => 'news,feuser',
        'iconfile' => 'EXT:md_unreadnews/Resources/Public/Icons/user_plugin_unread.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, news, news_datetime, feuser',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, news, news_datetime, feuser, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_mdunreadnews_domain_model_unreadnews',
                'foreign_table_where' => 'AND tx_mdunreadnews_domain_model_unreadnews.pid=###CURRENT_PID### AND tx_mdunreadnews_domain_model_unreadnews.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'behaviour' => [
                'allowLanguageSynchronization' => true
            ],
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
            ],
        ],

        'news' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_unreadnews/Resources/Private/Language/locallang_db.xlf:tx_mdunreadnews_domain_model_unreadnews.news',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_news_domain_model_news',
                'suggestOptions' => [
                    'type' => 'suggest',
                    'default' => [
                        'searchWholePhrase' => true,
                    ]
                ],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'multiple' => 0,
                'default' => 0,
                'eval' => 'trim,int,required'
            ],
        ],

        'feuser' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_unreadnews/Resources/Private/Language/locallang_db.xlf:tx_mdunreadnews_domain_model_unreadnews.feuser',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'fe_users',
                'suggestOptions' => [
                    'type' => 'suggest',
                    'default' => [
                        'searchWholePhrase' => true,
                    ]
                ],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'multiple' => 0,
                'default' => 0,
                'eval' => 'trim,int,required'
            ]
        ],

        'news_datetime' => [
            'exclude' => false,
            'label' => 'LLL:EXT:md_unreadnews/Resources/Private/Language/locallang_db.xlf:tx_mdunreadnews_domain_model_unreadnews.news_datetime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 16,
                'readOnly' => true,
                'eval' => 'datetime,int',
            ]
        ],
    ],
];
