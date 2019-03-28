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

use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Base controllerUnreadnewsController
 */
class BaseController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * unreadnewsRepository
     *
     * @var \Mediadreams\MdUnreadnews\Domain\Repository\UnreadnewsRepository
     * @inject
     */
    protected $unreadnewsRepository = null;

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
        if ($GLOBALS['TSFE']->fe_user->user['uid']) {
            $this->loggedinUserUid = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
        }
    }
}
