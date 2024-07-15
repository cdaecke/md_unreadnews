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

use GeorgRinger\NumberedPagination\NumberedPagination;
use Mediadreams\MdUnreadnews\Domain\Repository\UnreadnewsRepository;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;

/**
 * Class BaseController
 * @package Mediadreams\MdUnreadnews\Controller
 */
class BaseController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * User Id of the logged in user
     */
    protected $loggedinUserUid = null;

    /**
     * unreadnewsRepository
     *
     * @var UnreadnewsRepository
     */
    protected $unreadnewsRepository = null;

    /**
     * @param UnreadnewsRepository $unreadnewsRepository
     */
    public function injectUnreadnewsRepository(UnreadnewsRepository $unreadnewsRepository)
    {
        $this->unreadnewsRepository = $unreadnewsRepository;
    }

    /**
     * Initialize actions
     * Add possibility to overwrite settings
     */
    protected function initializeAction()
    {
        // Use stdWrap for given defined settings
        // Thanks to Georg Ringer: https://github.com/georgringer/news/blob/2c8522ad508fa92ad39a5effe4301f7d872238a5/Classes/Controller/NewsController.php#L597
        if (
            isset($this->settings['useStdWrap'])
            && !empty($this->settings['useStdWrap'])
        ) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $typoScriptArray = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
            $stdWrapProperties = GeneralUtility::trimExplode(',', $this->settings['useStdWrap'], true);
            foreach ($stdWrapProperties as $key) {
                if (is_array($typoScriptArray[$key . '.'])) {
                    $this->settings[$key] = $this->configurationManager->getContentObject()->stdWrap(
                        $typoScriptArray[$key],
                        $typoScriptArray[$key . '.']
                    );
                }
            }
        }

        // If user is logged in, set uid
        if (isset($GLOBALS['TSFE']->fe_user->user) && $GLOBALS['TSFE']->fe_user->user['uid']) {
            $this->loggedinUserUid = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
        }
    }

    /**
     * Assign pagination to current view object
     *
     * @param $items
     * @param int $itemsPerPage
     * @param int $maximumNumberOfLinks
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    protected function assignPagination($items, $itemsPerPage = 10, $maximumNumberOfLinks = 5)
    {
        $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;

        $paginator = new QueryResultPaginator(
            $items,
            $currentPage,
            $itemsPerPage
        );

        $pagination = new NumberedPagination(
            $paginator,
            $maximumNumberOfLinks
        );

        $this->view->assign('pagination', [
            'paginator' => $paginator,
            'pagination' => $pagination,
        ]);
    }
}
