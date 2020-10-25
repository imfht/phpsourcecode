<?php

/**
 * 表单配置帮助
 */
namespace app\site\model;

class SiteFormFieldHelpModel {

    public function text() {
        return '可限定字段输入长度,如最小1最大10个字符则输入配置信息"1,10"';
    }

    public function number() {
        return '可限定数字输入范围,如1到99则输入配置信息"1,99"';
    }

    public function phone() {
        return '可限定运营商开头号码,如138,186等,多个使用","分割';
    }

    public function tel() {
        return '可限定电话区号,如028,010等,多个区号使用","分割,内容输入格式为010-1234567';
    }

    public function email() {
        return '可限定邮箱域名地址,如qq.com则输入qq,多个域名使用","分割';
    }

    public function textarea() {
        return '可限定字段输入长度,如最小1最大10个字符则输入配置信息"1,10"';
    }

    public function editor() {
        return '可限定编辑器菜单,请参考wangeditor编辑器菜单配置,请不要换行';
    }

    public function date() {
        return '';
    }

    public function time() {
        return '';
    }

    public function datetime() {
        return '';
    }

    public function select() {
        return '多个选项请换行,每个选项格式为"值,描述"';
    }

    public function radio() {
        return '多个选项请换行,每个选项格式为"值,描述"';
    }

    public function checkbox() {
        return '多个选项请换行,每个选项格式为"值,描述"';
    }

    public function image() {
        return '可设置上传图片最大宽度和高度,如"500,300"';
    }

    public function images() {
        return '可设置上传图片最大宽度和高度,如"500,300"';
    }

    public function file() {
        return '可设置上传文件格式,多个格式请使用","分割';
    }

    public function files() {
        return '可设置上传文件格式,多个格式请使用","分割';
    }

    public function price() {
        return '可设置价格范围,如"1,50"';
    }

    public function color() {
        return '';
    }

    public function area() {
        return '';
    }

    public function baidumap() {
        return '';
    }
}