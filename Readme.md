# TYPO3 Extension ``md_unreadnews``

This extension adds unread information to the records of ``ext:news`` for frontend users. In a list of news records, a frontend user can see, whether or not an article was read by himself. Additional it is possible to show how many unread articles are in a category.

The extension is shipped with typoscript libraries, which can be included at desired places.

## Requirements

- TYPO3 >= 8.7
- ext:news >= 6.0

## Installation

- Install the extension by using the extension manager
- Include the static TypoScript of the extension
- Configure the extension by setting your own constants

Available constants:

- ``storagePid``: This is the uid of the page, where you want to store the unread records
- ``categories``: Comma separated string of category uids. All news records which are connected to a configured category will be marked as unread. If you don't provide a category, all news records will be considered.
- ``feGroup``: The uid of a feUserGroup. If provided, the unread info will only be set for users, who belong to this group. If not set, all users are considered.


## Usage

As soon as you have installed and activated the extension, it will hook into the saving process of new news records. Everytime a backend user adds a new record, the unread info for this record and the configured feUsers will be added.

### List plugin

The extension ships a plugin, which shows a list of all unread news for a user. In the ``Plugin Options`` you can set the link to the detail page. If you do not provide the detail page, the news record will be linked to the page of the first attached category.

### Show general unread counter

This counter will show the number of unread items for a user. To show this information use the following code in your fluid template:

    <f:cObject typoscriptObjectPath="lib.mdAllUnreadCount" />

### Show unread info in list view

Use the following code in the fluid template of the news extension to show, whether the current logged in feUser has read a news or not.

    <f:cObject typoscriptObjectPath="lib.mdIsUnread" data="{newsUid:'{newsItem.uid}'}" />

Example:
Copy ``ext:news/Ressources/Private/Partials/List/Item.html`` into your extension and modify the following:

    <!-- header -->
    <div class="header">
        <h3>
            <n:link newsItem="{newsItem}" settings="{settings}" title="{newsItem.title}">
                <span itemprop="headline">{newsItem.title}</span>
                <f:cObject typoscriptObjectPath="lib.mdIsUnread" data="{newsUid:'{newsItem.uid}'}" />
            </n:link>
        </h3>
    </div>

### Show unread info for categories

With the following code snippet you can show the number of unread news according to categories:

    <f:cObject typoscriptObjectPath="lib.mdCategoryCount" data="{categoryUid:'{category.item.uid}'}" />

Example:
Copy ``ext:news/Ressources/Private/Templates/Category/List.html`` into your extension and modify the following:

    <f:link.page title="{category.item.title}" pageUid="{settings.listPid}" additionalParams="{tx_news_pi1:{overwriteDemand:{categories: category.item.uid}}}">
        {category.item.title}
        <f:cObject typoscriptObjectPath="lib.mdCategoryCount" data="{categoryUid:'{category.item.uid}'}" />
    </f:link.page>

### Remove unread info

Remove the unread info as soon, as the user has read the news article. Therefore add the following code on the news detail page:

    <f:cObject typoscriptObjectPath="lib.mdRemoveUnread" data="{newsUid:'{newsItem.uid}'}" />

Example:
Copy ``ext:news/Ressources/Private/Templates/News/Detail.html`` into your extension and add the following code at the end of the template:

    <f:if condition="{newsItem}">
        <f:then>
            <f:cObject typoscriptObjectPath="lib.mdRemoveUnread" data="{newsUid:'{newsItem.uid}'}" />
        </f:then>
    </f:if>


## Bugs and Known Issues
If you find a bug, it would be nice if you add an issue on [Github](https://github.com/cdaecke/md_unreadnews/issues).

# THANKS

Thanks a lot to all who make this outstanding TYPO3 project possible!

## Credits

Extension icon was copied from [ext:news](https://github.com/georgringer/news) and then modified.
