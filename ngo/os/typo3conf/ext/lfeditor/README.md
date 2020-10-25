#  Ext: lfeditor 

<img src="https://www.sgalinski.de/typo3conf/ext/project_theme/Resources/Public/Images/logo.svg" />

License: [GNU GPL, Version 2](https://www.gnu.org/licenses/gpl-2.0.html)

Repository: https://gitlab.sgalinski.de/typo3/lfeditor

Please report bugs here: https://gitlab.sgalinski.de/typo3/lfeditor

TYPO3 version: >7.6 

## About

The lfeditor is a GUI for adding, editing and deleting translations in .xlf, .xml and .php files. 

**XML** and **PHP** files can be converted to .xlf files.

The editor can be found in the
**user tool section** <img height="20px" width="20px" src="https://camo.githubusercontent.com/7ce33ea8f66e24219bbdbe607d841285719fda4f/68747470733a2f2f7261776769742e636f6d2f5459504f332f5459504f332e49636f6e732f6d61737465722f646973742f6176617461722f6176617461722d64656661756c742e737667"> of the TYPO3 backend (click on your user in the top panel and select LFEditor).

## Configuration

#### Extension Settings
In the Extension manager you can configure the following settings of the **lfeditor** extension:

###### View languages
List of languages which will be available in the extension (comma separated country codes e.g. en, de, fi).

If this setting is empty, all languages will be used (Warning: very long list!). Default language is always included.

---

###### Default language
If **en** is not the default language in TYPO3 backend, the default language must be defined here. If this field is empty,
English is used as default.

---

###### Ignore extensions
Regular expression which can be used to exclude extensions by their extension key.

---

###### Change XLF date
If set to TRUE, LFEditor will change the date in the files on each change.

#### Extension Settings
There is an additional option in the **Access Rights** tab on the very bottom, when editing a non-admin backend user. 
You can now select the **The user can save localization changes directly to extensions?**.

Backend users who don't have this check box selected, will be in override mode by default
and they can't switch to another mode.


## Usage

### The Menu
Main navigation through LFEditor is done by selecting one of the options of drop down menu
on top-left part of the screen.

###### General
This option displays general information about all the languages.

Displayed languages should be configured in the configuration section of LFEditor,
because default behavior of LFEditor for administrators is to displays all existing translation languages of TYPO3.
While non-admin users see only languages which they have permissions for.

Beside the language shortcut, there is state and origin of the related language file.
Clicking any language shortcut redirects to the **Edit File <edit-file-reference>** page for this language.

The column *state* shows the state of translation and contains the amount of translated, unknown and untranslated language constants.
Unknown constants are the ones which exist in the language and don’t exist in default language.

At the bottom of the page there are options for converting and splitting language files.
Those options are not displayed in override mode and it is not possible to merge your files.
Splitting of language files increases the performance of backend and frontend.
There is also a field set which allows editing of the meta information of language files.

---

###### Edit File
This option allows editing of all constants in a language file and a selected language.
Users can choose between translated, untranslated, unknown or all constants.

This whole dialog is handled by a session. This means that a user can translate a whole page and doesn't need to
save the changes before clicking on 'next' button to get the next page with language constants.

---

###### Edit Constant
This option serves for translating a single constant in several languages.
Available languages depend on the user language privileges and on the configured "view languages".

---

###### Add Constant
This option serves for adding new constants to language files.
The user needs to enter a new name and translations for chosen languages can be entered right away.

---

###### Delete Constant
This option allows the user to delete a selected constant. The constant will be deleted from all languages.

---

###### Rename Constant
This option serves for renaming the key of a existing language constant.
The constant to rename is chosen from a select list and then the new name is entered in text field.

---

###### Search constants
This option allows the user to search for constants in selected language files.

- Finds constants which match the search string within a key or value (translation).
- The matching can be case sensitive or insensitive
- Supports regular expressions
- Looks only among a subset of languages which is defined in the **lfeditor** extension configuration

Clicking on a constant key of any search result, redirects to the **Edit Constant <edit-constant-reference>** page.

---

###### View Tree
This option serves for a better overview and easier access to constants.
It displays all constants of a language file arranged in a tree.

Constants are displayed as leafs of the tree and they are colored in three colors which indicate the translation state
of each constant for a selected language (language from first select box).

Constants are compared to a language from the second select box,
resulting in three states:

- **green** - normal constant (translated in both languages)
- **red** - untranslated constant (translated only in second language)
- **blue** - unknown constant (translated only in first language)

Clicking on tree leaf (last segment of constant key) redirects to the **Edit Constant <edit-constant-reference>** page.

---

###### Manage Backups
The backup option displays all backups of the selected extension.
It is possible to recover every backup, delete them or just have a look at the differences.
User can revert splitting and merging of files too. 

**Conversions of the file format are not revertible**.

If language file was converted to other format, there will be red warning in status of the backup table entry
and that backup can only be deleted.

All changes since the backup are visible trough the **differences** functionality.
Green color means that the constant was added and red that it was deleted since the backup was made.

### Editing modes
There are three editing modes:

- Extension mode
- l10n mode
- Override mode

They can be chosen from rightmost select menu on top of the screen.
The select menu is visible only for administrators.

If the user is not an admin, **Override mode** is chosen by default. For administrators the default is the **Extension mode**.

###### Extension mode
This mode is useful for extension developers, because in this mode the **lfeditor** edits the extension files directly.
Even if copies of extension files exist in l10n folder or extension files are overridden,
**lfeditor** will still edit the extension files only.

---

###### l10n mode
This mode is similar to extension mode with the only difference being that the l10n directory has higher priority than the
extension directory. This means that if there is a copy of the language file in l10n folder (e.g. de.locallang),
that copy will be edited instead of original extension file. If there is no corresponding file in l10n folder the original file will be edited.

Here are some of characteristics of l10n mode:

- Files can't be moved to the l10n folder. They can be edited if they already exist in l10n folder
- Merging/splitting is not allowed in l10n mode
- Renaming of a constant will rename it in the main language file (e.g. ext/.../locallang) 
and in all subfiles in the **l10n** (e.g. l10n/.../de.locallang) and **ext** folder 
- Deleting and adding files work similar to renaming

---

###### Override mode
The purpose of this mode is making translations unaffected by changes in an extension
(e.g. when extension updates the translations will be preserved). This mode is useful for translators and is the only mode available for non-admin users.

- If a user makes any changes in a language file, only the changed constants (or meta data) will be saved in the corresponding language file in  **typo3confLFEditorOverrideFiles**
- When reading language file, LFEditor is first reading constants from files in override folder, 
then it reads rest of constants from l10n folder (if there is corresponding file in l10n folder), and then reads from ext folder (if there was no file in l10n folder).
