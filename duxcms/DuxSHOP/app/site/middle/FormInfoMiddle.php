<?php

/**
 * 商品咨询
 */

namespace app\site\middle;

class FormInfoMiddle extends \app\base\middle\BaseMiddle {

    private $formInfo;
    private $info;

    private function getInfo() {
        if($this->formInfo) {
            return $this->formInfo;
        }
        $id = intval($this->params['id']);
        $formId = intval($this->params['form_id']);
        if (empty($formId)) {
            return [];
        }
        $formInfo = target('site/SiteForm')->getInfo($formId);
        if(empty($formInfo) || !$formInfo['status_list']) {
            return [];
        }
        if (empty($formInfo) || !$formInfo['status_info']) {
            return [];
        }
        $info = target('site/SiteFormData')->table('form_' . $formInfo['label'])->getInfo($id);
        if (empty($info)) {
            return [];
        }
        $formFields = target('site/SiteFormField')->loadList(['form_id' => $formId]);
        foreach ($formFields as $field) {
            $info[$field['label']] = call_user_func_array([target('site/SiteFormDataShow'), $field['type']], [$info[$field['label']], $field['config']]);
        }
        $this->formInfo = $formInfo;
        $this->info = $info;
        return [
            'info' => $info,
            'formInfo' => $formInfo,
            'tpl' => $formInfo['tpl_info']
        ];
    }

    protected function meta() {
        $this->getInfo();
        $this->setMeta('详情 - ' .$this->formInfo['name']);
        $this->setName($this->formInfo['name']);
        $this->setCrumb([
            [
                'name' => $this->formInfo['name'],
                'url' => url('index', ['id' => $this->formInfo['form_id']])
            ],
            [
                'name' => '详情',
                'url' => url('info', ['form_id' => $this->formInfo['form_id'], 'id' => $this->info['data_id']])
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $info = $this->getInfo();
        if(empty($info)) {
            return $this->stop('内容不存在!', 404);
        }
        return $this->run($info);
    }

}