==================
(FS) Media Gallery
==================

A FAL based media gallery for TYPO3. Show your assets from your local or remote storage as a gallery of albums.

Features
========

- Create a new album from a folder in the file module with only ONE button click
- Manage your albums from within the file module
- Teaser plugin (show random asset from selected albums)
- Editor friendly
- Make a album from a static collection of files, a folder or selected by category (core file_collections)


Requirements
============

- TYPO3 >= 8 LTS


Quick install notes
===================

- Install extension through Extension Manager or composer ``composer require minifranske/fs-media-gallery``
- Include Static Template "Media Galley (fs_media_gallery)"
- Create a StoragePage and set "Contains Plugin" to "Media Galleries"
- Go to file module and open the folder you want to turn into a album
   - Click on the "Create new album..." in context menu or top toolbar
   - Save your new album
- Insert plugin on page and select "Media Gallery" as plugin type
- Adjust the "Display mode" to the preferred gallery
- Set the "Record Storage Page" to the "StoragePage" you just created
- Open FE and admire your album :)

Known issues
============

- After installing fs_media_gallery the ordering of my exiting sys_file_collections changed
   - This is because fs_media_gallery enables manual sorting of sys_file_collections this is something we need to make the albums manageable.

- I inserted a "Media Gallery" plugin but I see no images in FE
   - Did you set the "Record Storage Page" of the plugin to the "StoragePage" with your albums?
   - Did you set the "Display mode" in the plugin?

- The images shown in the lightbox don't have the max size but are resized to max 1400px * 1400px
   - These limits are set in the typoscript template of the extension and are there to prevent that to big images are used in FE. These max values can be overruled by setting your own values in your typoscript template.
   plugin.tx_fsmediagallery.settings.image.lightbox.maxWidth
   plugin.tx_fsmediagallery.settings.image.lightbox.maxHeight
