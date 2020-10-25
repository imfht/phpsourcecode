<?php

/**
 * 商品咨询
 */

namespace app\site\middle;

class FormMiddle extends \app\base\middle\BaseMiddle {

    private $formInfo;

    private function formInfo() {
        if ($this->formInfo) {
            return $this->formInfo;
        }
        $formId = intval($this->params['id']);
        if (empty($formId)) {
            return [];
        }
        $formInfo = target('site/SiteForm')->getInfo($formId);
        if (empty($formInfo)) {
            return [];
        }
        $this->formInfo = $formInfo;

        return $formInfo;
    }

    protected function meta() {
        $formInfo = $this->formInfo();
        $this->setMeta($formInfo['name']);
        $this->setName($formInfo['name']);
        $this->setCrumb([
            [
                'name' => $formInfo['name'],
                'url' => url('index', ['id' => $formInfo['form_id']])
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $formInfo = $this->formInfo();
        $pageLimit = intval($this->params['limit']);
        if (empty($formInfo) || !$formInfo['status_list']) {
            $this->stop('内容不存在!', 404);
        }

        $pageLimit = $pageLimit ? $pageLimit : 20;

        $where = [];
        $model = target('site/SiteFormData');

        $count = $model->table('form_' . $formInfo['label'])->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->table('form_' . $formInfo['label'])->loadList($where, $pageData['limit'], 'data_id desc');
        if ($list) {
            $formFields = target('site/SiteFormField')->loadList(['form_id' => $formInfo['form_id']]);
            foreach ($formFields as $field) {
                foreach ($list as $key => $vo) {
                    $list[$key][$field['label']] = call_user_func_array([target('site/SiteFormDataShow'), $field['type']], [$vo[$field['label']], $field['config']]);
                }
            }
        }
        $tpl = $formInfo['tpl_list'];

        return $this->run([
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'limit' => $pageLimit,
            'tpl' => $tpl,
            'formInfo' => $formInfo
        ]);
    }

    protected function post() {
        $formInfo = $this->formInfo();
        if (empty($formInfo) || !$formInfo['submit']) {
            $this->stop('表单不存在!');
        }
        $post = $this->params['data'];
        $formFields = target('site/SiteFormField')->loadList(['form_id' => $formInfo['form_id']]);
        foreach ($formFields as $field) {
            if (!$field['submit']) {
                continue;
            }
            if (method_exists(target('site/SiteFormFieldMediate'), $field['type'])) {
                $post[$field['label']] = call_user_func_array([target('site/SiteFormFieldMediate'), $field['type']], [$field['label'], $field['config']]);
            }
            $data[$field['label']] = call_user_func_array([target('site/SiteFormFieldFormat'), $field['type']], [$post[$field['label']], $field['config']]);
            if ($field['must']) {
                $validate = call_user_func_array([target('site/SiteFormFieldValidate'), $field['type']], [$data[$field['label']], $field['config']]);
                if (!$validate) {
                    return $this->stop($field['name'] . '输入不正确!');
                }
            }
        }
        if (empty($data)) {
            return $this->stop('暂无提交数据!');
        }
        $id = target('site/SiteFormData')->table('form_' . $formInfo['label'])->add($data);
        if (empty($id)) {
            return $this->stop($formInfo['name'] . '提交失败,请稍后再试!');
        }
        $this->run([], $formInfo['name'] . '提交成功!');
    }


}