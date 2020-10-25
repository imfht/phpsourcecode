.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _support:

FAQ/Support
===========


Frequently Asked Questions
--------------------------

After installing EXT:fs_media_gallery the ordering of my exiting "File collections" changed
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

This is because EXT:fs_media_gallery enables manual sorting of ``sys_file_collection``.
This is something we need to make the albums manageable.


I inserted a "Media Gallery" plugin but I see no images in FE
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

* Did you include the static template :ref:`*Media Gallery (fs_media_gallery)* <users_manual>`?
* Did you set the :ref:`Startingpoint <flexforms.mediagallery.tabs.general.startingpoint>` to the "Storage Folder" which holds the album records?


Multiple "Media Gallery" plugins on one page won't work properly
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

EXT:fs_media_gallery is designed to display one single plugin per page.
Thus it's possible to generate much shorter and cleaner URLs.
If you do put multiple plugins on one page please notice that

* Pagination affects all plugins in in ``nestedList``, ``flatList``, ``showAlbumByParam`` and ``showAlbumByConfig`` :ref:`display modes <flexforms.mediagallery.tabs.general.displayMode>`
* Given ``albumUid`` affects all plugins in ``nestedList`` and ``showAlbumByParam`` :ref:`display modes <flexforms.mediagallery.tabs.general.displayMode>`


Bug reports and feature requests
--------------------------------

You found a bug or miss something?

Please file bug reports and request new features or suggest modifications to existing features
in a constructive manner on `https://bitbucket.org/franssaris/fs_media_gallery <https://bitbucket.org/franssaris/fs_media_gallery>`_.
