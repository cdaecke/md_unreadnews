<?php
namespace Mediadreams\MdUnreadnews\Controller;

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
 * UnreadnewsController
 */
class UnreadnewsController extends BaseController
{
    /**
     * Get info, if selected news is unread for user
     *
     * @return void
     */
    public function isUnreadAction()
    {
        if (
            (int)$this->settings['newsUid'] > 0 
            && isset($this->loggedinUserUid)
        ) {
            $unreadnews = $this->unreadnewsRepository->isUnread(
                $this->settings['newsUid'], 
                $this->loggedinUserUid
            );

            $this->view->assign('unreadnews', $unreadnews);
        }
    }

    /**
     * Get number of unread items per category
     *
     * @return void
     */
    public function categoryCountAction()
    {
        if (
            (int)$this->settings['categoryUid'] > 0 
            && isset($this->loggedinUserUid)
        ) {
            $unreadnews = $this->unreadnewsRepository->getCountForCategory(
                $this->settings['categoryUid'], 
                $this->loggedinUserUid
            );

            $this->view->assign('unreadnews', $unreadnews);
        }
    }

    /**
     * Remove unread for selected news and user
     *
     * @return void
     */
    public function removeUnreadAction()
    {
        if ( 
            (int)$this->settings['newsUid'] > 0 
            && isset($this->loggedinUserUid)
        ) {
            $this->unreadnewsRepository->deleteEntry(
                $this->settings['newsUid'], 
                $this->loggedinUserUid
            );
        }
    }

}
