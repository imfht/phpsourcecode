.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _configuration-integrations:

Integrate in EXT:news => v3.2.0
===============================

Target group: **Developers**

The extension adds a new relation field for news items. When editing a news record you will find new field under the tab "Relations" where
you can select multiple albums.
To make these album visible in the detail view of a news item you need to adjust your fluid template of the news detail view.

.. _configuration-integration-news-fluid-template:

Example fluid template snippet that could be added to ext:news/Resources/Private/Templates/News/Detail.html
-----------------------------------------------------------------------------------------------------------

Render related albums ::

   <f:if condition="{newsItem.relatedFsmediaalbums}">
       <h4>Related albums</h4>
       <div class="fs-media-gallery">
       <f:for each="{newsItem.relatedFsmediaalbums}" as="mediaAlbum">
           <div class="thumb">
               <f:link.action pageUid="{settings.detail.fsmediaalbum.targetPid}" pluginName="Mediagallery" extensionName="FsMediaGallery" controller="MediaAlbum" arguments="{mediaAlbum : mediaAlbum}">
                   <f:image
                       image="{mediaAlbum.randomAsset}"
                       alt="{mediaAlbum.title}"
                       title="{mediaAlbum.title}"
                       height="{settings.detail.fsmediaalbum.thumb.width}{settings.detail.fsmediaalbum.thumb.resizeMode}"
                       width="{settings.detail.fsmediaalbum.thumb.height}{settings.detail.fsmediaalbum.thumb.resizeMode}"
                       class="img-responsive"
                   />
                   <div class="name">{mediaAlbum.title}</div>
                   <div class="description"><f:format.crop maxCharacters="17" append="&nbsp;...">{mediaAlbum.webdescription}</f:format.crop></div>
               </f:link.action>
           </div>
       </f:for>
       </div>
   </f:if>

Make sure to set typoscript constant :typoscript:`plugin.tx_fsmediagallery.settings.targetPid` so the shown thumbnails link to the real album.

Have a look at `Changing & editing templates <http://docs.typo3.org/typo3cms/extensions/news/Templating/Start/Index.html>`_ it you do not know how to adjust ext:news templates.