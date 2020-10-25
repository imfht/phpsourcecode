<?php


class lesscreator_env
{
    public static function GroupByAppList()
    {
        return array(
            '50' => 'Business',
            '51' => 'Collaboration',
            '52' => 'Productivity',
            '53' => 'Developer Tools'
        );
    }
    
    public static function RuntimesList()
    {
        return array(
            'nginx' => array(
                'title' => 'WebServer (nginx)',
            ),
            'php' => array(
                'title' => 'PHP',
            ),
            'go' => array(
                'title' => 'Go',
            ),
            'nodejs' => array(
                'title' => 'NodeJS',
            ),
            'python' => array(
                'title' => 'Python',
            ),
            'java' => array(
                'title' => 'Java',
            ),
        );
    }

    public static function GroupByDevList()
    {
        return array(
            '60' => 'Web Frontend Library, Framework',
            '61' => 'Web Backend Library, Framework',
            '70' => 'System Library',
            '71' => 'System Server, Service',
            '72' => 'Runtime',
        );
    }
    
    public static function ProjInfoDef($proj)
    {
        return array(
            'projid'    => "$proj",
            'name'      => "$proj",
            'summary'   => '',
            'version'   => '0.0.1',
            'depends'   => '',
            'release'   => '1',
            'arch'      => 'all',
            'runtimes'  => array(),
            'props_app' => array(),
            'props_dev' => array(),
        );
    }
    
    public static function NginxConfTypes()
    {
        return array(
            'std'    => 'Standard configuration',
            'static' => 'Pure static files',
            'phpmix' => 'php-fpm (PHP FastCGI Process Manager) and static files',
            'custom' => 'Custom Configuration',
        );
    }
}
