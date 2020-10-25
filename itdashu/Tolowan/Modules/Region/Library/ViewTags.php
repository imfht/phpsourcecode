<?php
namespace Modules\Region\Library;

class ViewTags
{
    public static function regionData($id)
    {
        $region = array(
            '#templates' => array(
                'region-' . $id,
                'region',
            ),
            '#module' => 'region',
            'data' => Options::cacheGet('region_' . $id . 'Data'),
        );
        return $region;
    }

    public static function regionHierarchy($id)
    {
        $region = array(
            '#templates' => array(
                'region-' . $id,
                'region',
            ),
            '#module' => 'region',
            'data' => Options::cacheGet('region_' . $id . 'Hierarchy'),
        );
        return $region;
    }

    public static function regionRender($id)
    {
        $region = array(
            '#templates' => array(
                'region',
                'region-' . $id,
            ),
            '#module' => 'region',
            'data' => Options::cacheGet('region_' . $id . 'Data'),
            'hierarchy' => Options::cacheGet('region_' . $id . 'Hierarchy'),
        );
        return $region;
    }

    public static function blockRender($block, $id = null)
    {
        $block = array(
            '#templates' => array(
                'block',
                'block-' . $block['type'],
            ),
            '#module' => 'region',
            'data' => $block,
        );
        if (!is_null($id)) {
            $block['#templates'][] = 'block-' . $id;
        }
        return $block;
    }
}