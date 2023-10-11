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

use Psr\Http\Message\ResponseInterface;

/**
 * Class UnreadnewsController
 * @package Mediadreams\MdUnreadnews\Controller
 */
class UnreadnewsController extends BaseController
{
    /**
     * List all unread news for user
     *
     * @return ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function listAction(): ResponseInterface
    {
        if (isset($this->loggedinUserUid)) {
            $unreadnews = $this->unreadnewsRepository->findByFeuser($this->loggedinUserUid);

            $this->assignPagination(
                $unreadnews,
                $this->settings['list']['paginate']['itemsPerPage'],
                $this->settings['list']['paginate']['maximumNumberOfLinks']
            );
        }

        return $this->htmlResponse();
    }

    /**
     * Get info, if selected news is unread for user
     *
     * @return ResponseInterface
     */
    public function isUnreadAction(): ResponseInterface
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

        return $this->htmlResponse();
    }

    /**
     * Get number of all unread items
     *
     * @return ResponseInterface
     */
    public function allUnreadCountAction(): ResponseInterface
    {
        $unreadnews = $this
            ->unreadnewsRepository
            ->findByFeuser($this->loggedinUserUid)
            ->count();

        $this->view->assign('unreadnews', $unreadnews);

        return $this->htmlResponse();
    }

    /**
     * Get number of unread items per category
     *
     * @return ResponseInterface
     */
    public function categoryCountAction(): ResponseInterface
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

        return $this->htmlResponse();
    }

    /**
     * Remove unread for selected news and user
     *
     * @return ResponseInterface
     */
    public function removeUnreadAction(): ResponseInterface
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

        return $this->htmlResponse();
    }

}
