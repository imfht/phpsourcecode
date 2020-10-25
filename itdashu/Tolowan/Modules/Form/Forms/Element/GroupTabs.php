<?php
namespace Modules\Form\Forms\Element;

use Core\Fun;
use Phalcon\Forms\Element;

class GroupTabs extends Element
{

    public $groupTabs;
    public function getValue(){
        return null;
    }
    public function setGroupTabs($groupTabs)
    {
        $this->groupTabs = $groupTabs;
    }
    public function getGroupTabs()
    {
        return $this->groupTabs;
    }
    public function render($attributes = null)
    {
        $form = $this->getForm();
        $attributes = $this->getAttributes();
        $userOptions = $this->getUserOptions();
        $output = '<div' . Fun::attributes($userOptions['groupAttributes']) . '>';
        $output .= '<h4' . Fun::attributes($userOptions['labelAttributes']) . '>' . $this->getLabel() . '<small>   &nbsp;  &nbsp;<i class="icon-double-angle-right"></i>   &nbsp;  &nbsp;' . $userOptions['description'] . '</small></h4><div class="clear"></div>';
        foreach ($this->groupTabs as $key => $value) {
            $output .= $form->render($key);
            //$form->remove($key);
        }
        $output .= '</div>';
        return $output;
    }
}
