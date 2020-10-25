<?php

/**
 * 基础内容
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteContentModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'content_id',
        'validate' => [
            'title' => [
                'required' => ['', '标题不能为空!', 'must', 'all'],
            ],
        ],
        'format' => [
            'title' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'keyword' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'description' => [
                'function' => ['htmlspecialchars', 'all'],
            ],
            'sort' => [
                'function' => ['intval', 'all'],
            ],
            'status' => [
                'function' => ['intval', 'all'],
            ],
            'create_time' => [
                'function' => ['strtotime', 'all'],
            ],
            'pos_id' => [
                'callback' => ['posIds', 'all'],
            ],
        ]
    ];

    public function posIds($field, $data = []) {
        if ($data) {
            return implode(',', $data);
        }else {
	    return "";
	}
    }

    public function _saveBefore($data) {
        $data['keyword'] = trim($data['keyword']);
        $data['keyword'] = \dux\lib\Str::htmlClear($data['keyword']);
        $data['keyword'] = preg_replace ( "/\s(?=\s)/",',', $data['keyword']);
        $data['keyword'] = str_replace('，', ',',$data['keyword']);
        $keyword = explode(',', $data['keyword']);
        $tagsId = [];
        if (!empty($keyword)) {
            foreach ($keyword as $vo) {
                $vo = trim($vo);
                if(empty($vo)) {
                    continue;
                }
                $tagInfo = target('site/SiteTags')->getWhereInfo(['name' => $vo, 'app' => $data['app']]);
                if ($tagInfo) {
                    if (!target('site/SiteTags')->where(['tag_id' => $tagInfo['tag_id']])->setInc('quote', 1)) {
                        return false;
                    }
                    $tagId = $tagInfo['tag_id'];
                } else {
                    $tagId = target('site/SiteTags')->add(['name' => $vo, 'app' => $data['app']]);
                    if (!$tagId) {
                        return false;
                    }
                }
                $tagsId[] = $tagId;
            }
        }
        $data['tags_id'] = implode(',', $tagsId);
        return $data;
    }

    public function _saveAfter($type, $data) {
        if (!empty($_POST['attr_data'])) {
            $attrIds = [];
            foreach ($_POST['attr_data'] as $attrId => $attrList) {
                $attrIds[] = $attrId;
                if(is_array($attrList)) {
                    $attrList = implode(',', $attrList);
                }
                $attrData = [
                    'content_id' => $data['content_id'],
                    'attr_id' => $attrId,
                    'value' => $attrList
                ];
                $info = target('site/SiteContentAttr')->getWhereInfo([
                    'A.content_id' => $data['content_id'],
                    'A.attr_id' => $attrId
                ]);
                if ($info['id']) {
                    $attrData['id'] = $info['id'];
                    if (!target('site/SiteContentAttr')->edit($attrData)) {
                        return false;
                    }
                } else {
                    if (!target('site/SiteContentAttr')->add($attrData)) {
                        return false;
                    }
                }
            }
            if($attrIds) {
                target('site/SiteContentAttr')->where([
                    'content_id' => $data['content_id'],
                    '_sql' => 'attr_id NOT IN('.implode(',', $attrIds).')'
                ])->delete();
            }
        }

        $classInfo = target('site/SiteClass')->getInfo($data['class_id']);
        $modelId = intval($classInfo['model_id']);
        if (empty($modelId)) {
            return true;
        }
        $modelFields = target('site/SiteModelField')->loadList(['model_id' => $modelId]);
        if (empty($modelFields)) {
            return true;
        }
        $modelInfo = target('site/SiteModel')->getInfo($modelId);
        foreach ($modelFields as $field) {
            $data[$field['label']] = call_user_func_array([target('site/SiteFormFieldFormat'), $field['type']], [$data[$field['label']], $field['config']]);
            if ($field['must']) {
                $validate = call_user_func_array([target('site/SiteFormFieldValidate'), $field['type']], [$data[$field['label']], $field['config']]);
                if (!$validate) {
                    $this->error = $field['name'] . '输入不正确!';
                    return false;
                }
            }
        }
        if ($type == 'add') {
            $id = $this->table('model_' . $modelInfo['label'])->add($data);
            $data[$this->primary] = $id;
            if (!$id) {
                return false;
            }
            return $id;
        }
        if ($type == 'edit') {
            if (empty($data[$this->primary])) {
                return false;
            }
            if (!$this->table('model_' . $modelInfo['label'])->edit($data)) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function delData($info) {
        $where = array();
        $where['content_id'] = $info['content_id'];
        if (!$this->where($where)->delete()) {
            return false;
        }
        if (!target('site/SiteContentAttr')->where(['content_id' => $info['content_id']])->delete()) {
            return false;
        }
        if($info['model_id']) {
            $modelInfo = target('site/SiteModel')->getInfo($info['model_id']);
            if (!$this->table('model_' . $modelInfo['label'])->where(['content_id' => $info['content_id']])->delete()) {
                return false;
            }
        }
        return true;
    }

}