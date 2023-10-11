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
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
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

        if ($table === self::TABLE) {
            if ($action == 'new') {
                // get uid of new record
                $newsUid = $pObj->substNEWwithIDs[$recordUid];

                if (!$newsUid) {
                    $this->enqueueFlashmessage('Unread info for news could not be saved!');
                    return;
                }

                $allowedCategories = [];
                $typoscriptSettings = $this->getTyposcriptSettings();
                if ($typoscriptSettings !== false) {
                    $allowedCategories = GeneralUtility::trimExplode(',', $typoscriptSettings['categories'], true);
                }

                // if there are categories configured in typoscript
                if (count($allowedCategories) > 0) {
                    // get selected categories in news record
                    $categories = GeneralUtility::trimExplode(',', $pObj->checkValue_currentRecord['categories'], true);

                    // check, if category in news record is a category which is configured in typoscript
                    $matchedCategories = array_intersect($allowedCategories, $categories);

                    // if $matchedCategories has at least one matched element, add unread info
                    if (count($matchedCategories) > 0) {
                        // add unread data
                        $this->saveUnreadInfo($newsUid, $fieldArray, $typoscriptSettings);
                    }
                } else { // if no categories configured in typoscript, add always unread info
                    $this->saveUnreadInfo($newsUid, $fieldArray, $typoscriptSettings);
                }
            } else if ($action == 'update') {
                $this->updateUnreadInfo($recordUid, $fieldArray);
            }
        }
    }

    /**
     * Delete unread information if news record gets deleted
     *
     * @param string $action action
     * @param string $table table name
     * @param int $recordUid id of the record
     * @param array $fields fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj parent Object
     */
    public function processCmdmap_postProcess(
        $action, 
        $table, 
        int $recordUid, 
        $value, 
        \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj
    )
    {
        if ($table === self::TABLE && $action == 'delete') {
            $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
                                  ->getConnectionForTable('tx_mdunreadnews_domain_model_unreadnews');

            $databaseConnection->delete(
                'tx_mdunreadnews_domain_model_unreadnews', 
                ['news' => $recordUid]
            );
        }
    }
    

    /**
     * Save unread info for news record
     *
     * @param int $newsUid Uid of news record
     * @param array $fieldArray Data of news entry
     * @param array $typoscriptSettings Typoscript settings
     * @return void
     */
    private function saveUnreadInfo(int $newsUid, $fieldArray, $typoscriptSettings)
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        // find users
        $queryBuilderFeusers = $connectionPool->getQueryBuilderForTable('fe_users');
        $feuserData = $queryBuilderFeusers
                        ->select('fe_users.uid')
                        ->from('fe_users');
        
        // if $allowedGroup is set, just find users with given group
        if (isset($typoscriptSettings['feGroup'])) {
            $allowedGroup = trim($typoscriptSettings['feGroup']);
            if ($allowedGroup) {
                $feuserData = $feuserData->where(
                    $queryBuilderFeusers->expr()->inSet(
                        'usergroup',
                        $queryBuilderFeusers->createNamedParameter($allowedGroup, \PDO::PARAM_INT)
                    )
                );
            }
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
                    'pid'           => !empty($typoscriptSettings['storagePid'])? trim($typoscriptSettings['storagePid']):0,
                    'news'          => $newsUid,
                    'feuser'        => $data['uid'],
                    'news_datetime' => $fieldArray['datetime'],
                    'tstamp'        => $timestamp,
                    'crdate'        => $timestamp,
                    'hidden'        => $fieldArray['hidden'],
                    'starttime'     => $fieldArray['starttime'],
                    'endtime'       => $fieldArray['endtime'],
                ];
            }
            
            $colNamesArray = ['pid', 'news', 'feuser', 'news_datetime', 'tstamp', 'crdate', 'hidden', 'starttime', 'endtime'];

            $dbConnectionUnreadnews = $connectionPool->getConnectionForTable('tx_mdunreadnews_domain_model_unreadnews');
            $dbConnectionUnreadnews->bulkInsert(
                'tx_mdunreadnews_domain_model_unreadnews',
                $dataArray,
                $colNamesArray
            );
        }
    }

    /**
     * Update unread info for news record
     *
     * @param int $newsUid Uid of news record
     * @param array $fieldArray Data of news entry
     * @return void
     */
    private function updateUnreadInfo(int $newsUid, $fieldArray)
    {
        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
                              ->getConnectionForTable('tx_mdunreadnews_domain_model_unreadnews');
        
        // build update information
        $arrayUpdateData = ['tstamp' => time()];
        if (isset($fieldArray['datetime'])) {
            $arrayUpdateData = array_merge($arrayUpdateData, ['news_datetime' => $fieldArray['datetime']]);
        }

        if (isset($fieldArray['hidden'])) {
            $arrayUpdateData = array_merge($arrayUpdateData, ['hidden' => $fieldArray['hidden']]);
        }

        if (isset($fieldArray['starttime'])) {
            $arrayUpdateData = array_merge($arrayUpdateData, ['starttime' => $fieldArray['starttime']]);
        }

        if (isset($fieldArray['endtime'])) {
            $arrayUpdateData = array_merge($arrayUpdateData, ['endtime' => $fieldArray['endtime']]);
        }

        // update all unread records with new information
        $databaseConnection->update(
            'tx_mdunreadnews_domain_model_unreadnews',
            $arrayUpdateData,
            ['news' => $newsUid]
        );
    }

    /**
     * Get typoscript settings
     *
     * @return bool|array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    private function getTyposcriptSettings()
    {
        // get typoscript settings
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        try {
            // typoscript settings for ext:md_unreadnews plugin "unread"
            return $extbaseFrameworkConfiguration['plugin.']['tx_mdunreadnews_unread.']['settings.'];
        } catch(\Exception $e) {
            $this->enqueueFlashmessage('Unread info for news could not be saved! Check, if static TypoScript of `ext:md_unreadnews` is included.');

            return false;
        }
    }

    /**
     * Show flash message
     *
     * @param string $message
     * @throws \TYPO3\CMS\Core\Exception
     */
    private function enqueueFlashmessage(string $message): void
    {
        /** @var FlashMessage $flashMessage */
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $message,
            'EXT:md_unreadnews',
            FlashMessage::WARNING,
            true
        );

        /** @var FlashMessageService $flashMessageService */
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        /** @var \TYPO3\CMS\Core\Messaging\FlashMessageQueue $defaultFlashMessageQueue */
        $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $defaultFlashMessageQueue->enqueue($flashMessage);
    }
}
