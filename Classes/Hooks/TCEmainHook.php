<?php
namespace Mediadreams\MdUnreadnews\Hooks;

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
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * TCE amin hook
 */
class TCEmainHook
{

    /**
     * Name of table
     *
     * @var string
     */
    const TABLE = 'tx_news_domain_model_news';

    /**
     * Add unread information for new news record if news category 
     * matches a configured category in typoscript settings and user belongs
     * to given group.
     *
     * @param string $action action
     * @param string $table table name
     * @param int $recordUid id of the record
     * @param array $fields fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj parent Object
     */
    public function processDatamap_afterDatabaseOperations(
        $action, 
        $table, 
        $recordUid, 
        array $fieldArray, 
        &$pObj
    )
    {

        if ($table === self::TABLE && $action == 'new') {
            // get uid of new record
            $newsUid = $pObj->substNEWwithIDs[$recordUid];

            if (!$newsUid) {
                /** @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
                $flashMessage = GeneralUtility::makeInstance(
                    \TYPO3\CMS\Core\Messaging\FlashMessage::class,
                    'Unread info for news could not be saved!',
                    'EXT:md_unreadnews',
                    \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING,
                    true
                );
                
                /** @var \TYPO3\CMS\Core\Messaging\FlashMessageService $flashMessageService */
                $flashMessageService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
                /** @var \TYPO3\CMS\Core\Messaging\FlashMessageQueue $defaultFlashMessageQueue */
                $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
                $defaultFlashMessageQueue->enqueue($flashMessage);

                return;
            }

            $typoscriptSettings = $this->getTyposcriptSettings();
            $allowedCategories = GeneralUtility::trimExplode(',', $typoscriptSettings['categories'], true);

            // if there are categories configured in typoscript
            if (count($allowedCategories) > 0) {
                // get selected categories in news record
                $categories = GeneralUtility::trimExplode(',', $pObj->checkValue_currentRecord['categories'], true);

                // check, if category in news record is a category which is configured in typoscript
                $matchedCategories = array_intersect($allowedCategories, $categories);

                // if $matchedCategories has at least one matched element, add unread info
                if (count($matchedCategories) > 0) {
                    // add unread data
                    $this->saveUnreadInfo($newsUid, $typoscriptSettings);
                }
            } else { // if no categories configured in typoscript, add always unread info
                $this->saveUnreadInfo($newsUid, $typoscriptSettings);
            }
        }
    }

    /**
     * Save unread info for news record
     *
     * @param int $newsUid Uid of news record
     * @param array $typoscriptSettings Typoscript settings
     * @return void
     */
    private function saveUnreadInfo(int $newsUid, $typoscriptSettings)
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        // find users
        $queryBuilderFeusers = $connectionPool->getQueryBuilderForTable('fe_users');
        $feuserData = $queryBuilderFeusers
                        ->select('fe_users.uid')
                        ->from('fe_users');
        
        // if $allowedGroup is set, just find users with given group
        $allowedGroup = trim($typoscriptSettings['feGroup']);
        if ($allowedGroup) {
            $feuserData = $feuserData->where( 
                                $queryBuilderFeusers->expr()->inSet(
                                    'usergroup', 
                                    $queryBuilderFeusers->createNamedParameter($allowedGroup, \PDO::PARAM_INT)
                                )
                            );
        }

        // finally get data
        $feuserData = $feuserData
                        ->execute()
                        ->fetchAll();

        // if there is some data, prepare and save it
        if (count($feuserData) > 0) {
            // prepare data to save
            $timestamp = time();
            foreach ($feuserData as $data) {
                $dataArray[] = [
                    'pid' => !empty($typoscriptSettings['storagePid'])? trim($typoscriptSettings['storagePid']):0,
                    'news' => $newsUid,
                    'feuser' => $data['uid'],
                    'tstamp' =>  $timestamp,
                    'crdate' => $timestamp,
                ];
            }
            
            $colNamesArray = ['news', 'feuser', 'tstamp', 'crdate'];

            $dbConnectionUnreadnews = $connectionPool->getConnectionForTable('tx_mdunreadnews_domain_model_unreadnews');
            $dbConnectionUnreadnews->bulkInsert(
                'tx_mdunreadnews_domain_model_unreadnews',
                $dataArray,
                $colNamesArray
            );
        }
    }

    /**
     * Get typoscript settings
     *
     * @return array
     */
    private function getTyposcriptSettings()
    {
        // get typoscript settings
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        // typoscript settings for ext:md_unreadnews plugin "unread"
        return $extbaseFrameworkConfiguration['plugin.']['tx_mdunreadnews_unread.']['settings.'];
    }
}
