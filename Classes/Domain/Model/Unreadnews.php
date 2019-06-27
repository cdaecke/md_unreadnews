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
     * The news entry.
     *
     * @var \GeorgRinger\News\Domain\Model\News
     * @lazy
     * @validate NotEmpty
     */
    protected $news = 0;

    /**
     * The feuser entry.
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     * @lazy
     * @validate NotEmpty
     */
    protected $feuser = 0;

    /**
     * @var \DateTime
     */
    protected $newsDatetime;

    /**
     * Returns the news
     *
     * @return int $news
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * Sets the news
     *
     * @param int $news
     * @return void
     */
    public function setNews($news)
    {
        $this->news= $news;
    }

    /**
     * Returns the feuser
     *
     * @return int $feuser
     */
    public function getFeuser()
    {
        return $this->feuser;
    }

    /**
     * Sets the feuser
     *
     * @param int $feuser
     * @return void
     */
    public function setFeuser($feuser)
    {
        $this->feuser = $feuser;
    }

    /**
     * Get newsDatetime
     *
     * @return \DateTime
     */
    public function getNewsDatetime()
    {
        return $this->newsDatetime;
    }

    /**
     * Set date time
     *
     * @param \DateTime $newsDatetime newsDatetime
     */
    public function setNewsDatetime($newsDatetime)
    {
        $this->newsDatetime = $newsDatetime;
    }
}
