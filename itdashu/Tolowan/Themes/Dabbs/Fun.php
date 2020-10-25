<?php
namespace Themes\Ace;

use Boot\Sbin\Theme;
use Phalcon\Mvc\User\Plugin;

class Fun extends Plugin
{
    public function l(&$variables)
    {
        $variables['attributes']['class']['ajax-load-link'] = 'ajax-load-link';
        if (!isset($variables['attributes']['hrefbox'])) {
            $variables['attributes']['hrefbox'] = array();
            $variables['attributes']['hrefbox'][] = '.main-content';
        }
    }

    public function formRender(&$element)
    {
        $elementAttributes = $element->getAttributes();
        $elementAttributes['class'] = ' width-100';
        $element->setAttributes($elementAttributes);
        $elementOptions = $element->getUserOptions();
        $elementOptions['groupAttributes']['class']['ace'] = '';
        $elementOptions['labelAttributes']['class']['ace'] = 'col-xs-12 col-sm-12';
        $elementOptions['widgetBoxAttributes']['class']['ace'] = 'col-xs-12 col-sm-12';
        $elementOptions['helpAttributes']['class']['ace'] = 'help-block col-xs-12 col-sm-reset inline';
        $element->setUserOptions($elementOptions);
    }

    public function frCheckbox(&$element)
    {
        $elementAttributes = $element->getAttributes();
        $elementAttributes['class'] = ' ace';
        $element->setAttributes($elementAttributes);
        $userOptions = $element->getUserOptions();
        $userOptions['labelAttributes']['class']['ace'] = 'col-xs-12 col-sm-12';
        $output = '';
        if (isset($element->renderBoolen)) {
            $output .= $element;
        } else {
            $output .= '<div' . Theme::attributes($userOptions['groupAttributes']) . '>';
            $output .= '<h5' . Theme::attributes($userOptions['labelAttributes']) . '>';
            $output .= '<label><input type="checkbox" class="ace ace-switch ace-switch-7" name="' . $element->getName() . '"><span class="lbl"></span></label>&nbsp;  &nbsp;' . $element->getLabel();
            $output .= '<small class="help-block col-xs-12 col-sm-reset inline">&nbsp;  &nbsp;<i class="icon-double-angle-right"></i>   &nbsp;  &nbsp;' . $userOptions['description'] . '</small></h5></div><div class="clear"></div>';
        }
        return $output;
    }

    public function frGroupTab(&$element)
    {
        $elementOptions = $element->getUserOptions();
        $output = '';
        $tabs = '';
        $tabsContent = '';
        $form = $element->getForm();
        if (isset($element->renderBoolen)) {
            $output .= $element;
        } else {
            $output .= '<h5 class="col-xs-12 col-sm-12">' . $element->getLabel() . '<small>   &nbsp;  &nbsp;<i class="icon-double-angle-right"></i>   &nbsp;  &nbsp;启用过滤器，对数据进行安全过滤</small></h5><div class="col-sm-12 tabbable tabs-' . $elementOptions['tabFloat'] . '">';
            $output .= '<ul id="' . $element->getName() . '" class="nav nav-tabs">';
            // 新数据
            $ei = 0;
            foreach ($element->group as $key => $value) {
                if ($ei == 0) {
                    $tabs .= '<li class="active">';
                    $tabs .= '<a href="#' . $key . '_content" data-toggle="tab">';
                    $tabs .= $value['label'] . '</a></li>';
                    $tabsContent .= '<div id="' . $key . '_content" class="tab-pane active">' . $form->render($key) . '</div>';
                    $ei = 1;
                } else {
                    $tabs .= '<li>';
                    $tabs .= '<a href="#' . $key . '_content" data-toggle="tab">';
                    $tabs .= $value['label'] . '</a></li>';
                    $tabsContent .= '<div id="' . $key . '_content" class="tab-pane">' . $form->render($key) . '</div>';
                }
                $form->remove($key);
            }
            $tabsContent = '<div class="tab-content">' . $tabsContent . '</div>';
            $output = $output . $tabs . '</ul>' . $tabsContent . '</div><div class="clear"></div><div class="space-4"></div>';
        }
        return $output;
    }

    public function frGroup(&$element)
    {
        $elementOptions = $element->getUserOptions();
        $output = '<div class="widget-box"><div class="widget-header widget-header-flat">';
        $form = $element->getForm();
        if (isset($element->renderBoolen)) {
            $output .= $element;
        } else {
            $output .= '<h5 class="lighter">' . $element->getLabel() . '</h5><div class="widget-toolbar"><a data-action="collapse" href="#"><i class="icon-chevron-up"></i></a></div></div>';
            $output .= '<div class="widget-body widget-main">';
            // 新数据
            foreach ($element->group as $key => $value) {
                $output .= $form->render($key);
            }
            $output .= '</div></div><div class="clear"></div><div class="space-4"></div>';
        }
        return $output;
    }

    public function frCheckboxs(&$element)
    {
        $elementAttributes = $element->getAttributes();
        $elementAttributes['class'] = ' ace';
        $element->setAttributes($elementAttributes);
        $elementOptions = $element->getUserOptions();
        $elementOptions['labelAttributes']['class']['ace'] = 'col-xs-12 col-sm-12';
        $elementOptions['widgetBoxAttributes']['class']['ace'] = 'col-xs-12 col-sm-8 checkbox';
        $elementOptions['helpAttributes']['class']['ace'] = 'lbl';
        $element->setUserOptions($elementOptions);
        $output = '';
        if (isset($element->renderBoolen)) {
            $output .= $element;
        } else {
            $userOptions = $element->getUserOptions();
            $output .= '<div' . Theme::attributes($userOptions['groupAttributes']) . '>';
            $output .= '<h5' . Theme::attributes($userOptions['labelAttributes']) . '>' . $element->getLabel() . '<small>   &nbsp;  &nbsp;<i class="icon-double-angle-right"></i>   &nbsp;  &nbsp;' . $userOptions['description'] . '</small></h5><div class="clear"></div>';
            $output .= '<div' . Theme::attributes($userOptions['widgetBoxAttributes']) . '>' . $element . '</div>';
            $output .= '</div>';
            $output .= '<div class="clear"></div><div class="space-4"></div>';
        }
        return $output;
    }
    public function rfMenuAddForm(&$form)
    {
        $form->formAttributes['class']['ace'] = 'form-horizontal ajax-submit';
        $form->formAttributes['hrefbox'] = '#menu_handle';
        $output = $form->start();
        foreach ($form->formEntity as $key => $value) {
            if (isset($value['widget'])) {
                $output .= $form->render($key);
            }
        }
        $output .= $form->csrf();
        $output .= '<button type="submit" class="btn btn-success btn-block">保存</button>';
        $output .= $form->end();
        return $output;
    }
    public function rf(&$form)
    {
        $form->formAttributes['class']['ace'] = 'form-horizontal ajax-submit';
        $form->formAttributes['hrefbox'] = '.main-content';
        $output = $form->start();
        foreach ($form->formEntity as $key => $value) {
            if (isset($value['widget'])) {
                $output .= $form->render($key);
            }
        }
        $output .= $form->csrf();
        $output .= '<button type="submit" class="btn btn-success btn-block">保存</button>';
        $output .= $form->end();
        return $output;
    }
}
