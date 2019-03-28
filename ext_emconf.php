<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "md_unreadnews"
 *
 * Auto generated by Extension Builder 2019-03-19
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Unread news',
    'description' => 'This extension adds unread information to the records of ext:news for frontend users.',
    'category' => 'plugin',
    'author' => 'Christoph Daecke',
    'author_email' => 'typo3@mediadreams.org',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'news' => '6.0.0-7.99.99',
        ],
    ],
];
