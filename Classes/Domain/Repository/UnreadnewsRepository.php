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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Class UnreadnewsRepository
 * @package Mediadreams\MdUnreadnews\Domain\Repository
 */
class UnreadnewsRepository extends AbstractRepository
{
    /**
     * The name of the unread table in the database
     */
    const TABLE_NAME = 'tx_mdunreadnews_domain_model_unreadnews';

    /**
     * Check if news is unread for given user
     *
     * @param int $newsUid : Uid of news record
     * @param int $feuserUid : Uid of feuser record
     * @return int
     */
    public function isUnread(int $newsUid, int $feuserUid): int
    {
        $query = $this->createQuery();
        $constraints[] = $query->equals('news', $newsUid);
        $constraints[] = $query->equals('feuser', $feuserUid);

        $query->matching($query->logicalAnd(...$constraints));
        return $query->execute()->count();
    }

    /**
     * Delete entry for given news and user
     *
     * @param int $newsUid : Uid of news record
     * @param int $feuserUid : Uid of feuser record
     * @return void
     */
    public function deleteEntry(int $newsUid, int $feuserUid): void
    {
        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable(static::TABLE_NAME);

        $arrayWhere = [
            'news' => $newsUid,
            'feuser' => $feuserUid,
        ];

        $databaseConnection->delete(static::TABLE_NAME, $arrayWhere);
    }

    /**
     * Delete entries older than the given days
     *
     * @param int $days : Amount of days in the past till then all unread information shall be deleted.
     * @return mixed
     */
    public function deletePeriod(int $days)
    {
        $date = strtotime(-$days . ' days');

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(static::TABLE_NAME);

        return $queryBuilder
            ->delete(static::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->lte('crdate', $queryBuilder->createNamedParameter($date))
            )
            ->execute();
    }

    /**
     * Get number of unread news for a selected category
     *
     * @param int $categoryUid : CategoryUid of news category
     * @param int $feuserUid : Uid of feuser record
     * @return int
     */
    public function getCountForCategory(int $categoryUid, int $feuserUid): int
    {
        $query = $this->createQuery();
        $enableFields = $GLOBALS['TSFE']->sys_page->enableFields('tx_mdunreadnews_domain_model_unreadnews');

        $sql = '
            SELECT sys_category_record_mm.uid_foreign
            FROM sys_category_record_mm
            JOIN 
                tx_mdunreadnews_domain_model_unreadnews 
                ON tx_mdunreadnews_domain_model_unreadnews.news = sys_category_record_mm.uid_foreign
            WHERE 
                tx_mdunreadnews_domain_model_unreadnews.feuser = ' . $feuserUid . '
                ' . $enableFields . '
                AND sys_category_record_mm.uid_local = ' . $categoryUid . '
                AND sys_category_record_mm.tablenames = "tx_news_domain_model_news"
                AND sys_category_record_mm.fieldname = "categories"
        ';

        $query->statement($sql);
        return $query->execute()->count();
    }
}
