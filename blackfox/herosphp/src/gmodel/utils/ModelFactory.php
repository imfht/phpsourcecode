<?php

namespace herosphp\gmodel\utils;

/**
 * 创建model文件。
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use herosphp\gmodel\GModel;
use herosphp\string\StringBuffer;

class ModelFactory {

    /**
     * 根据xml文件创 Model 文件
     * @param SimpleHtmlDom $xml
     * @param array $configs
     */
    public static function createModelByXml($xml, $configs) {

        $root = $xml->find("root", 1);

        $tables = $root->find("table");
        $tempContent = file_get_contents(dirname(__DIR__)."/template/model.tpl");

        foreach ( $tables as $value ) {

            $modelFile = $configs['module_dir'].ucfirst(GModel::underline2hump($value->name)).".model.php";
            if ( file_exists($modelFile) ) { //若文件已经存在则跳过
                tprintWarning("Warnning : model file '{$modelFile}' has existed，skiped.");
                continue;
            }
            $pk = $value->find("pk", 0);
            $content = str_replace("{table_name}", $value->name, $tempContent);
            if ( $pk ) {
                $content = str_replace("{pk}", $pk->name, $content);
            }
            $content = str_replace("{model_name}", ucfirst(GModel::underline2hump($value->name))."Model", $content);
            $content = str_replace("{desc}", $value->name. "数据表模型", $content);
            $content = str_replace("{author}", $configs["author"], $content);
            $content = str_replace("{email}", $configs["email"], $content);
            $content = str_replace("{module}", $configs["module"], $content);

            //检查模型是否分段
            $flagments = $value->find("flagments", 0);
            if ( !empty($flagments) ) {
                $sb = new StringBuffer();
                $tab = 2;
                $sb->appendLine('//设置数据分段');
                $sb->appendTab('$this->isFlagment = true;', $tab);
                $models = $flagments->find("model");
                $sb->appendTab('$this->flagments = array(', $tab);
                foreach ($models as $model) {
                    $sb->appendTab('array(', $tab+1);
                    $sb->appendTab("'fields' => '{$model->fields}',", $tab+2);
                    $sb->appendTab("'model' => '{$model->name}',", $tab+2);
                    $sb->appendTab('),', 3);
                }
                $sb->appendTab(');', $tab);
                $content = str_replace("{flagments}", $sb->toString(), $content);
            } else {
                $content = str_replace("{flagments}", '', $content);
            }

            //设置数据分片
            $shardingNum = intval($value->getAttribute("sharding-num"));
            if ( $shardingNum > 0 ) {
                $sb = new  StringBuffer();
                $sb->appendLine('//设置数据分片数量');
                $sb->appendTab('$this->shardingNum = '.$shardingNum.';', 2);
                $replacements = array('C_Model' => 'SimpleShardingModel');
                $replacements['{sharding_num}'] = $sb->toString();
                $content = str_replace(array_keys($replacements), $replacements, $content);
            } else {
                $content = str_replace("{sharding_num}", '', $content);
            }

            //设置自动自增主键
            if ( $pk->ai ) {
                $content = str_replace("{autoPrimaryKey}", '$this->autoPrimary = false;', $content);
            } else {
                $content = str_replace("{autoPrimaryKey}", '$this->autoPrimary = true;', $content);
            }

            if ( file_put_contents($modelFile, $content) !== false ) {
                tprintOk("create model file '{$modelFile}' successfully.");
            } else {
                tprintError("Error: create model file '{$modelFile}' faild.");
            }

        }

    }

    /**
     * 创建普通 Model 文件
     * @param $options
     */
    public static function createModel($options) {

        if ( !isset($options['model']) ) return tprintError("Error : --model is needed.");
        if ( !isset($options['table']) ) return tprintError("Error : --table is needed.");
        if ( !isset($options['module']) ) return tprintError("Error : --module is needed.");
        if ( !isset($options['pk']) ) $options['pk'] = 'id';
        if ( !isset($options['author']) ) $options['author'] = 'yangjian';
        if ( !isset($options['email']) ) $options['email'] = 'yangjian102621@gmail.com';
        if ( !isset($options['date']) ) $options['date'] = date('Y-m-d');

        $tempContent = file_get_contents(dirname(__DIR__)."/template/model.tpl");
        $modelFile = $options['module_dir'].ucfirst(GModel::underline2hump($options['model']))."Dao.php";
        if ( file_exists($modelFile) ) { //若文件已经存在则跳过
            return tprintWarning("Warnning : model file '{$modelFile}' has existed，skiped.");
        }

        $replacement = array(
            "{table_name}" => $options['table'],
            "{desc}" => $options['desc'] ? $options['desc'] : "{$options['table']} 数据表模型",
            "{pk}" => $options['pk'],
            "{model_name}" => ucfirst(GModel::underline2hump($options['model']))."Dao",
            "{author}" => $options['author'],
            "{email}" => $options['email'],
            "{autoPrimaryKey}" => '',
            "{module}" => $options['module'],
            "{flagments}" => '',
            "{sharding_num}" => '',
        );
        $content = str_replace(array_keys($replacement), $replacement, $tempContent);
        if ( file_put_contents($modelFile, $content) !== false ) {
            tprintOk("create model file '{$modelFile}' successfully.");
        } else {
            tprintError("Error: create model file '{$modelFile}' faild.");
        }
    }

}
