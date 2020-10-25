.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Editing modes
-------------

There are three editing modes:

- Extension mode
- l10n mode
- Override mode

They can be chosen from rightmost select menu on top of the screen.
The select menu is visible only for administrators. If user is not admin, than override mode is chosen by default.
For administrators, default is extension mode.

Extension mode
^^^^^^^^^^^^^^

This mode is useful for extension developers because in this mode, LFEditor edits extension files directly.

Even if copies of extension files exist in l10n folder, or extension files are overridden,
user will still edit extension files only.

l10n mode
^^^^^^^^^

This mode is similar to extension mode, but only difference is that l10n directory has higher priority than
extension directory. This means that if there is a copy of the language file in l10n folder (e.g. de.locallang),
that copy will be edited instead of original extension file. If there is no corresponding file in l10n folder,
original file will be edited.

Here are some of characteristics of l10n mode:

- Files can't be moved to l10n folder by LFE. They can be edited if they already exist in l10n folder.
- Merging/splitting is not allowed in l10n mode.
- Renaming of constant will make renaming in main lang file (e.g. ext/.../locallang) and in all sub-files in l10n (e.g. l10n/.../de.locallang) and in ext folder (if those files from ext folder don't have duplicate in l10n folder( e.g. ext/.../fi.locallang)). Consequence of this is file ext/.../de.locallang having old constant name.
- Delete and add file work similar like rename in l10n mode.

Override mode
^^^^^^^^^^^^^

Purpose of this mode is making translations resistant to changes in extension
(e.g. when extension updates, translations will be preserved). Thus, this mode is useful for translators,
and it is set as default and only mode for non-admin users.

- Whenever user makes any change in some language file, only changed constants (or meta data) will be saved in corresponding language file in  'typo3conf\LFEditor\OverrideFiles'.

- When reading language file, LFEditor is first reading constants from files in override folder, then it reads rest of constants from l10n folder (if there is corresponding file in l10n folder), and then reads from ext folder (if there was no file in l10n folder).

.. important::
	All the changes (edit/add/delete/rename constant) to language file will be saved in override files only.
	Original extension files will stay unchanged.