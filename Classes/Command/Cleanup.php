<?php

namespace Mediadreams\MdUnreadnews\Command;

/**
 *
 * This file is part of the "Unread news" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Christoph Daecke <typo3@mediadreams.org>
 *
 */


use Mediadreams\MdUnreadnews\Domain\Repository\UnreadnewsRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class Cleanup
 * @package Mediadreams\MdUnreadnews\Command
 */
class Cleanup extends Command
{
    /**
     * @var \Mediadreams\MdUnreadnews\Domain\Repository\UnreadnewsRepository
     */
    protected $unreadnewsRepository;

    /**
     * Cleanup constructor.
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        // get objectManager
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        // get unreadnewsRepository
        $this->unreadnewsRepository = $this->objectManager->get(UnreadnewsRepository::class);
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Remove old unread information')
            ->setHelp('This will clean up the unread information table.' . LF . 'It will remove all records, which are older than the specified days.')
            ->addArgument(
                'days',
                InputArgument::OPTIONAL,
                'Amount of days in the past till then all unread information shall be deleted. (integer)'
            );
    }

    /**
     * Executes the command deleting old unread information
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $days = (int)$input->getArgument('days') ? $input->getArgument('days') : 30;
        $res = $this->unreadnewsRepository->deletePeriod($days);

        if ($res) {
            $io->success('All entries, which are older than ' . $days . ' day are deleted.');
        } else {
            $io->error('An error occurred!');
        }

        return 0;
    }
}
