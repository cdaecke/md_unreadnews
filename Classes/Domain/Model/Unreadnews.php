<?php
namespace Mediadreams\MdUnreadnews\Domain\Model;

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

/**
 * Unreadnews
 */
class Unreadnews extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * The uid of the news entry.
     *
     * @var \GeorgRinger\News\Domain\Model\News
     * @lazy
     * @validate NotEmpty
     */
    protected $newsUid = 0;

    /**
     * The uid of the feuser entry.
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     * @lazy
     * @validate NotEmpty
     */
    protected $feuserUid = 0;

    /**
     * Returns the newsUid
     *
     * @return int $newsUid
     */
    public function getNewsUid()
    {
        return $this->newsUid;
    }

    /**
     * Sets the newsUid
     *
     * @param int $newsUid
     * @return void
     */
    public function setNewsUid($newsUid)
    {
        $this->newsUid = $newsUid;
    }

    /**
     * Returns the feuserUid
     *
     * @return int $feuserUid
     */
    public function getFeuserUid()
    {
        return $this->feuserUid;
    }

    /**
     * Sets the feuserUid
     *
     * @param int $feuserUid
     * @return void
     */
    public function setFeuserUid($feuserUid)
    {
        $this->feuserUid = $feuserUid;
    }
}
