<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * @since 1.5
 */
class HelperHelpAccessCore extends Helper
{
    public $label;
    public $iso_lang;
    public $country;
    public $ps_version;

    public function __construct($label, $iso_lang, $country, $ps_version)
    {
        parent::__construct();
        $this->base_folder = 'helpers/help_access/';

        $this->tpl = $this->createTemplate('button.tpl');
        $this->label = $label;
        $this->iso_lang = $iso_lang;
        $this->country = $country;
        $this->ps_version = $ps_version;
    }

    /**
     * @return string|void
     */
    public function generate()
    {
        $info = HelpAccess::retrieveInfos($this->label, $this->iso_lang, $this->country, $this->ps_version);
        $content = '';

        if (array_key_exists('version', $info) && $info['version'] != '')
        {
            $last_version = HelpAccess::getVersion($this->label);

            $tpl_vars['button_class'] = 'process-icon-help';
            if ($last_version < $info['version'])
                $tpl_vars['button_class'] = 'process-icon-help-new';

            $tpl_vars['label'] = $this->label;
            $tpl_vars['iso_lang'] = $this->iso_lang;
            $tpl_vars['country'] = $this->country;
            $tpl_vars['version'] = $this->ps_version;
            $tpl_vars['doc_version'] = $info['version'];
            $tpl_vars['help_base_url'] = HelpAccess::URL;
            $tpl_vars['tooltip'] = $info['tooltip'];

            $this->tpl->assign($tpl_vars);

            $content = parent::generate();
        }

        return $content;
    }
}