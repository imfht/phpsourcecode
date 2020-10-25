.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _administration:

Installation/updating
=====================

Target group: **Administrators**


.. _read_before_installing_or_updating:

Read before installing or updating!
-----------------------------------

Before installing this extension or updating to a new major release, you should **always**
read the sections "Upgrade procedure" and "Important changes" in the :ref:`ChangeLog <changelog>`.


.. _installation:

Installation
------------

To install the extension, perform the following steps:

#. Go to the Extension Manager
#. Install the extension
#. Include the static template :ref:`*Media Gallery (fs_media_gallery)* <users_manual>`

To use the latest version from the `code repository <https://bitbucket.org/franssaris/fs_media_gallery>`_ install the extension from command line:

.. code:: bash

    cd /your/path/to/typo3root/
    git clone git@bitbucket.org:franssaris/fs_media_gallery.git --single-branch --branch master --depth 1 typo3conf/ext/fs_media_gallery
    ./typo3/cli_dispatch.phpsh extbase extension:install fs_media_gallery


.. _fs_media_gallery_and_realurl:

EXT:fs_media_gallery and RealURL
--------------------------------

EXT:fs_media_gallery comes with a basic automatic RealURL config (see file ``Classes/Hooks/RealUrlAutoConfiguration.php``).
It will add the following configuration to the `postVarSets['_DEFAULT'] <http://docs.typo3.org/typo3cms/extensions/realurl/Realurl/Configuration/ConfigurationDirectives/Index.html#postvarsets-pageindex-keyword>`_ section:

.. code-block:: php

				'postVarSets' => array(
					'_DEFAULT' => array(
						'album' => array(
							array(
								'GETvar' => 'tx_fsmediagallery_mediagallery[mediaAlbum]',
								'lookUpTable' => array(
									'table' => 'sys_file_collection',
									'id_field' => 'uid',
									'alias_field' => 'title',
									'addWhereClause' => ' AND NOT deleted',
									'useUniqueCache' => 1,
									'useUniqueCache_conf' => array(
										'strtolower' => 1,
										'spaceCharacter' => '_',
									),
									'languageGetVar' => 'L',
									'languageExceptionUids' => '',
									'languageField' => 'sys_language_uid',
									'transOrigPointerField' => 'l10n_parent',
									'autoUpdate' => 1,
									'expireDays' => 700,
								),
							),
							array(
								'GETvar' => 'tx_fsmediagallery_mediagallery[@widget_0][currentPage]',
							),
						)
					)
				)
