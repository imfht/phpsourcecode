
plugin.tx_casetab_case {
    view {
        # cat=plugin.tx_casetab_case/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:case_tab/Resources/Private/Templates/
        # cat=plugin.tx_casetab_case/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:case_tab/Resources/Private/Partials/
        # cat=plugin.tx_casetab_case/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:case_tab/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_casetab_case//a; type=string; label=Default storage PID
        storagePid =
    }
}
