<?php

namespace Mediadreams\MdUnreadnews\Domain\Repository;

/**
 *
 * This file is part of the "Unread news" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Christoph Daecke <typo3@mediadreams.org>
 *
 */

use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AbstractRepository
 * @package Mediadreams\MdUnreadnews\Domain\Repository
 */
abstract class AbstractRepository extends Repository
{
    /**
     * @var Typo3QuerySettings;
     */
    protected $querySettings = null;

    /**
     * @param Typo3QuerySettings $querySettings
     */
    public function injectTypo3QuerySettings(Typo3QuerySettings $querySettings)
    {
        $this->querySettings = $querySettings;
    }

    /**
     * Default orderings
     *
     */
    protected $defaultOrderings = [
        'news_datetime' => QueryInterface::ORDER_DESCENDING,
        'uid' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * Disable storage page for all repository calls
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($this->querySettings);
    }
}
