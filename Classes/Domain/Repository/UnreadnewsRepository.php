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
 * The repository for Unreadnews
 */
class UnreadnewsRepository extends AbstractRepository
{
    /**
     * Check if news is unread for given user
     *
     * @param  int $newsUid: Uid of news record
     * @param  int $feuserUid: Uid of feuser record
     * @return int
     */
    public function isUnread(int $newsUid, int $feuserUid)
    {
        $query = $this->createQuery();
        $constraints[] = $query->equals('news', $newsUid);
        $constraints[] = $query->equals('feuser', $feuserUid);

        $query->matching($query->logicalAnd($constraints));
        return $query->execute()->count();
    }

    /**
     * Delete entry for given news and unser
     *
     * @param  int $newsUid: Uid of news record
     * @param  int $feuserUid: Uid of feuser record
     * @return void
     */
    public function deleteEntry(int $newsUid, int $feuserUid)
    {
        $table = 'tx_mdunreadnews_domain_model_unreadnews';

        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($table);

        $arrayWhere = [
            'news' => $newsUid, 
            'feuser' => $feuserUid,
        ];

        $databaseConnection->delete($table, $arrayWhere);
    }

    /**
     * Get number of unread news for a selected category
     *
     * @param  int $categoryUid: CategoryUid of news category
     * @param  int $feuserUid: Uid of feuser record
     * @return int
     */
    public function getCountForCategory(int $categoryUid, int $feuserUid)
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
                tx_mdunreadnews_domain_model_unreadnews.feuser = '.$feuserUid.'
                '.$enableFields.'
                AND sys_category_record_mm.uid_local = '.$categoryUid.'
                AND sys_category_record_mm.tablenames = "tx_news_domain_model_news"
                AND sys_category_record_mm.fieldname = "categories"
        ';

        $query->statement($sql);
        return $query->execute()->count();
    }
}
