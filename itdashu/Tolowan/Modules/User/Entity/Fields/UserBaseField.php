<?php
namespace Modules\User\Entity\Fields;

use Modules\User\Entity\User;

class UserBaseField extends User{
    public static function filterValue($value, $option)
    {
        if (isset($option['maxNum']) && $option['maxNum'] > 1) {
            $output = array();
            if ($value) {
                foreach ($value as $m) {
                    if (isset($option['valueType']) && $option['valueType'] == 'id') {
                        $output[] = $m->value;
                    } else {
                        $term = $m->term;
                        if ($term) {
                            $output[] = $term->name;
                        }

                    }
                }
                return implode(',', $output);
            }
        } else {
            if ($value) {
                if ($option['valueType'] == 'id') {
                    return $value->value;
                } else {
                    $term = $value->term;
                    if ($term) {
                        return $term->name;
                    }

                }
            }
        }
        return false;
    }
}