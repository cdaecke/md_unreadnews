<?php

declare(strict_types=1);

return [
    \Mediadreams\MdUnreadnews\Domain\Model\FrontendUser::class => [
        'tableName' => 'fe_users',
    ],

    \Mediadreams\MdUnreadnews\Domain\Model\FrontendUserGroup::class => [
        'tableName' => 'fe_groups',
    ],
];
