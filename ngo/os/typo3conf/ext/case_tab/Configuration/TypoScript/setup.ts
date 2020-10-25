
#page count
lib.calc = TEXT
lib.calc {
  current = 1
  prioriCalc = 1
}
plugin.tx_casetab_case {
    view {
        templateRootPaths.0 = EXT:case_tab/Resources/Private/Templates/
        templateRootPaths.1 = {$plugin.tx_casetab_case.view.templateRootPath}
        partialRootPaths.0 = EXT:case_tab/Resources/Private/Partials/
        partialRootPaths.1 = {$plugin.tx_casetab_case.view.partialRootPath}
        layoutRootPaths.0 = EXT:case_tab/Resources/Private/Layouts/
        layoutRootPaths.1 = {$plugin.tx_casetab_case.view.layoutRootPath}
        widget.TYPO3\CMS\Fluid\ViewHelpers\Widget\PaginateViewHelper.templateRootPaths {
            10 = EXT:news_front_edit/Resources/Private/Templates/
        }
    }
    persistence {
        storagePid = {$plugin.tx_casetab_case.persistence.storagePid}
        #recursive = 1
    }
    features {
        #skipDefaultArguments = 1
        # if set to 1, the enable fields are ignored in BE context
        ignoreAllEnableFieldsInBe = 0
        # Should be on by default, but can be disabled if all action in the plugin are uncached
        requireCHashArgumentForActionArguments = 1
    }
    mvc {
        #callDefaultActionIfActionCantBeResolved = 1
    }
    settings {
        paginate {
            itemsPerPage = 15
            insertAbove = 0
            insertBelow = 1
            prevNextHeaderTags = 1
            maximumNumberOfLinks = 3
        }
    }
}

# these classes are only used in auto-generated templates
plugin.tx_casetab._CSS_DEFAULT_STYLE (
    textarea.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    input.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    .typo3-messages .message-error {
        color:red;
    }

    .typo3-messages .message-ok {
        color:green;
    }
)
