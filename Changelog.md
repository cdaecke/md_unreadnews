# Version 5.0.2 (2024-12-15)
- [TASK] Update dependency to ext:news

All changes
https://github.com/cdaecke/md_unreadnews/compare/5.0.1...5.0.2

# Version 5.0.0 (2024-07-15)
- [TASK] TYPO3 v12 compatibility

All changes
https://github.com/cdaecke/md_unreadnews/compare/4.0.1...5.0.0

# Version 4.0.1 (2022-11-11)
- [TASK] Update dependency to new version of ext:news.

All changes
https://github.com/cdaecke/md_unreadnews/compare/4.0.0...4.0.1

# Version 4.0.0 (2021-12-29)
- [FEATURE] TYPO3 11 compatibility
- [TASK] Dependency to ext:numbered_pagination was added.

## BREAKING
- `List.html`-Template was changed.
- New paginator was introduced, so partial needs to be change, if you use your own.

## Important
Clear cache in install tool, in oder to get everything working!

All changes
https://github.com/cdaecke/md_unreadnews/compare/3.0.1...4.0.0

# Version 3.0.1 (2021-10-11)
- [BUGFIX] remove lazy loading for news and fe_user in order to get data in list view straight away

All changes
https://github.com/cdaecke/md_unreadnews/compare/3.0.0...3.0.1

# Version 3.0.0 (2020-10-26)
- [FEATURE] TYPO3 10 compatibility
- [FEATURE]  add scheduler task to automatically remove unread information

All changes
https://github.com/cdaecke/md_unreadnews/compare/2.0.0...3.0.0

# Version 2.0.0 (2019-07-12)
- [FEATURE] add "lib.mdAllUnreadCount" which adds a counter for all unread news per user
- [FEATURE] add plugin to show a list of unread news
- [FEATURE] update unread info, if news records gets updated
- [FEATURE] delete unread information if news record gets deleted

## Update procedure
Please run following SQL queries right after updating the extension:

    ALTER TABLE `tx_mdunreadnews_domain_model_unreadnews` DROP INDEX `ids`;
    ALTER TABLE `tx_mdunreadnews_domain_model_unreadnews` CHANGE `news_uid` `news` INT(11) NOT NULL DEFAULT '0';
    ALTER TABLE `tx_mdunreadnews_domain_model_unreadnews` CHANGE `feuser_uid` `feuser` INT(11) NOT NULL DEFAULT '0';

Go to install tool and run `Database analyzer` to finish the update

All changes
https://github.com/cdaecke/md_unreadnews/compare/1.0.0...2.0.0
