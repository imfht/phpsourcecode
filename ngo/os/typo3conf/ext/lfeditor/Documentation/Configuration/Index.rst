.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Configuration
-------------

================ ========================================================================== ============================
Name           	 Description                                                                Default value
================ ========================================================================== ============================
viewLanguages    List of languages which will be available in the extension                 -
                 (comma separated short names e.g. da, de, fi).
                 If field is empty all languages will be used (Warning: very long list!).
                 Default language is always included.
defaultLanguage  If 'en' is not default language in TYPO3 BE,                               -
                 default language must be defined here (e.g. de). If this field is empty,
                 English is used as default.
extIgnore        Regular expression which limits the extension key list.                    /^(CVS|.svn|.git|csh_)/
extWhitelist     Regular expression which restricts the extension key list.                 -
changeXlfDate    If set to TRUE, LFEditor will change the date in XLF files on each change. 1
================ ========================================================================== ============================

Administrator can choose which backend users can save localization changes directly to extensions,
by selecting checkbox "The user can save localization changes directly to extensions?" when editing non-admin BE user.
The box is located in "Access Rights" tab, just after "Limit to languages" section of non-admin BE user.
Backend users who don't have this check box selected will be in override mode by default,
and they can't switch to another mode.
