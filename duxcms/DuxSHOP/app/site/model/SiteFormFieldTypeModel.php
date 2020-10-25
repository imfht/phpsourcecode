<?php

/**
 * 表单字段
 */
namespace app\site\model;

class SiteFormFieldTypeModel {

    public function text() {
        return $this->fieldVarchar();
    }

    public function number() {
        return $this->fieldInt();
    }

    public function phone() {
        return $this->fieldVarchar(15);
    }

    public function tel() {
        return $this->fieldVarchar(20);
    }

    public function email() {
        return $this->fieldVarchar(60);
    }

    public function textarea() {
        return $this->fieldText();
    }

    public function editor() {
        return $this->fieldText();
    }

    public function date() {
        return $this->fieldInt();
    }

    public function time() {
        return $this->fieldVarchar(50);
    }

    public function datetime() {
        return $this->fieldInt();
    }

    public function select() {
        return $this->fieldVarchar(50);
    }

    public function radio() {
        return $this->fieldVarchar(50);
    }

    public function checkbox() {
        return $this->fieldVarchar(50);
    }

    public function image() {
        return $this->fieldVarchar();
    }

    public function images() {
        return $this->fieldText();
    }

    public function file() {
        return $this->fieldVarchar();
    }

    public function files() {
        return $this->fieldText();
    }

    public function price() {
        return $this->fieldFloat();
    }

    public function color() {
        return $this->fieldVarchar();
    }

    public function area() {
        return $this->fieldVarchar();
    }

    public function baidumap() {
        return $this->fieldText();
    }

    protected function fieldVarchar($len = 250) {
        return [
            'type' => 'varchar',
            'len' => $len,
            'decimal' => 0,
            'default' => ''
        ];
    }

    protected function fieldInt($len = 11) {
        return [
            'type' => 'int',
            'len' => $len,
            'decimal' => 0,
            'default' => 0
        ];
    }

    protected function fieldText() {
        return [
            'type' => 'text',
            'len' => 0,
            'decimal' => 0,
            'default' => ''
        ];
    }

    protected function fieldFloat($len = 11, $decimal = 2) {
        return [
            'type' => 'float',
            'len' => $len,
            'decimal' => $decimal,
            'default' => 0
        ];
    }


    public function type() {
        $list = [
            'text' => '文本框',
            'number' => '数字',
            'phone' => '手机',
            'tel' => '电话',
            'email' => '邮箱',
            'textarea' => '多行文本框',
            'editor' => '编辑器',
            'date' => '日期',
            'time' => '时间',
            'datetime' => '日期时间',
            'select' => '下拉菜单',
            'radio' => '单选框',
            'checkbox' => '复选框',
            'image' => '图片上传',
            'images' => '多图片上传',
            'file' => '文件上传',
            'files' => '多文件上传',
            'price' => '价格',
            'color' => '颜色选择',
            'area' => '省市区选择',
            'baidumap' => '百度地图',
        ];
        return $list;
    }
}