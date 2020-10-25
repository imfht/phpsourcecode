.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _users_manual:

Users Manual
============

Target group: **Editors**

How to create a new media album
-------------------------------

#. Make sure the static template *Media Gallery (fs_media_gallery)* is included

   .. figure:: ../Images/UserManual/include-static-template.png
      :width: 400px
      :alt: Include static template *Media Gallery (fs_media_gallery)*

      **Image 1:** Include static template *Media Gallery (fs_media_gallery)*

   If you use `EXT:bootstrap_package <https://extensions.typo3.org/extension/bootstrap_package/>`_ include static template *Media Gallery Theme 'Bootstrap3' (fs_media_gallery)*.

#. Create a *"<Storage Folder>"* in your page-tree that's gonna hold your albums

   .. figure:: ../Images/UserManual/create-storage-folder.png
      :width: 200px
      :alt: Create a *"<Storage Folder>"* to hold your albums

      **Image 2:** Create a *"<Storage Folder>"* to hold your albums

#. Tell TYPO3 that the *"<Storage Folder>"* holds media albums by setting "Contains Plugin" to "MediaGalleries"

   .. figure:: ../Images/UserManual/create-media-albums-storage-folder.png
      :width: 300px
      :alt: Set "Contains Plugin" to "MediaGalleries"

      **Image 3:** Set "Contains Plugin" to "MediaGalleries"

#. Go to *Filelist* and open the folder you want to turn into a album (the folder should contain some media assets like e.g. images)
#. In *Filelist* click on the *Create new album in "<Storage Folder>"* icon in top toolbar or use *Create new album in "<Storage Folder>"* from the context menu in file list

   .. figure:: ../Images/UserManual/create-new-media-album-from-folder.png
      :width: 400px
      :alt: Create new album in "<Storage Folder>"

      **Image 4:** Create new album in "<Storage Folder>"

#. Fill out the required fields and save your new album
#. Insert plugin *Media Album* on a page

   .. figure:: ../Images/UserManual/add-media-album-plugin.png
      :width: 300px
      :alt: Add media album plugin

      **Image 5:** Add media album plugin

#. Choose a "Display mode" for instance "Selected albums (nested)" see :ref:`Plugin settings<flexforms.mediagallery.tabs.general>`

#. Set the :ref:`Startingpoint <flexforms.mediagallery.tabs.general.startingpoint>` to the *"<Storage Folder>"*
   you created the album in for more configuration options see :ref:`plugin <configuration-plugin>`

   .. figure:: ../Images/UserManual/configure-media-album-plugin.png
      :width: 300px
      :alt: Set starting point

      **Image 6:** Set starting point

#. **Open FE and admire your album :)**

Clear cache after a folder change
---------------------------------

Clearing the cache of a certain page can be set by adding the 'TCEMAIN.clearCacheCmd' in the pageTs of your MediaGallery storage.
After a change in your folder the TCEMAIN.clearCacheCmd will be invoked and the cache of these pages will be flushed.
