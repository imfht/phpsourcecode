.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Search Constant
---------------

This option allows a user to search for constants in selected language files.

- Finds constants which match the search string within key or value (translation).
- The matching can be case sensitive or insensitive.
- Supports regular expressions.
- Looks only among subset of languages which is defined in LFEditor configuration.

Clicking on constant key of any search result, redirects to :ref:`Edit Constant <edit-constant-reference>` page.

.. figure:: ../../Images/MainMenuOptions/SearchConstant/SearchConstant.png
	:alt: SearchConstant

Picture illustrates case sensitive search for string 'Cancel'.
The search has found the string in 2 language constants:

- as value of button.cancel constant in default language,
- as part of constant key of function.langfile.confirmCancel constant in default and in 'de' languages,
	since there is no more languages that contain translation for this constant key in this example.