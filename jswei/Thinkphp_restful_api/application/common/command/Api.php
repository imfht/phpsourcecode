<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/9/9
 * Time: 16:00
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Api extends Command
{
    public $api= null;
    protected function configure()
    {
        $this->api = config('api.'); //获取api设置
        $this->setName('api')
            ->addOption('namespace', 's', Option::VALUE_REQUIRED, 'namespace of api')
            ->addOption('controller', 'c', Option::VALUE_REQUIRED, 'controller name of api')
            ->addOption('id', 'i', Option::VALUE_REQUIRED, 'doc sub menu of api')
            ->addOption('parent', 'p', Option::VALUE_REQUIRED, 'doc sub menu id of api')
            ->addOption('name', 'm', Option::VALUE_REQUIRED, 'doc sub menu name of api')
            ->setDescription('build a new api controller with auth');
    }

    protected function execute(Input $input, Output $output)
    {
        $namespace = '';
        if($input->hasOption('namespace')){
            $namespace = strtolower(trim($input->getOption('namespace')));
        }
        if(!$namespace){
            $output->writeln("a error appear");
            return;
        }
        if(!$input->hasOption('controller')){
            $output->writeln("arguments  --controller");
            return;
        }

        $controller =  $input->getOption('controller');
        $_controller = ucwords($controller);

        if($input->hasOption('parent')){
            $pid = $input->getOption('parent');
        }else{
            $pid = '0';
        }
        if($input->hasOption('name')){
            $name = $input->getOption('name');
        }else{
            $name = $_controller;
        }
        $this->api['name'] = $name;
        $doc = config('api_doc.');
        if($input->hasOption('id')){
            $id = $input->getOption('id');
            $_doc[$id] =[
                'name' => $name,
                'id' => "{$id}",
                'parent' => $pid,
                'class' => 'app'.DIRECTORY_SEPARATOR.$namespace.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$_controller
            ];
            $doc = $doc + $_doc;
        }else{
            //重新配置doc文档
            $doc = config('api_doc.');
            $last = end($doc);

            $id = ++$last['id'];
            $doc[$id] = [
                'name' => $name,
                'id' => "{$id}",
                'parent' => $pid,
                'class' => 'app'.DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR . 'controller'.DIRECTORY_SEPARATOR.$_controller
            ];
        }
        $_controller = ucwords($controller);
        $base_url = __DIR__.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR;
        $controller_tpl = file_get_contents($base_url.'controller.tpl');
        $model_tpl = file_get_contents($base_url.'model.tpl');
        $validate_tpl = file_get_contents($base_url.'validate.tpl');
        $preg = [
            '/{\$_controller}/si',
            '/{\$api_version}/si',
            '/{\$namespace}/si',
            '/{\$version}/si',
            '/{\$controller}/si',
            '/{\$_name}/si',
        ];
        $replace = [
            $_controller,
            $this->api['api_version'],
            $namespace,
            $this->api['version'],
            $controller,
            $this->api['name']
        ];
        $result_controller_tpl = preg_replace($preg,$replace,$controller_tpl);
        $result_model_tpl = preg_replace($preg,$replace,$model_tpl);
        $result_validate_tpl = preg_replace($preg,$replace,$validate_tpl);
//        $output->writeln($result_validate_tpl);
//        return;

        //创建控制器
        $base_save_url =  env('app_path') . $namespace . DIRECTORY_SEPARATOR;
        $controllerPath = $base_save_url.'controller'. DIRECTORY_SEPARATOR . ucwords($controller)  . '.php';
        $modelPath = $base_save_url.'model'. DIRECTORY_SEPARATOR . ucwords($controller)  . '.php';
        $validatePath = $base_save_url.'validate'. DIRECTORY_SEPARATOR . ucwords($controller)  . '.php';
        if(file_exists($controllerPath)){
            $output->writeln("Controller $_controller is exists");
            return;
        }
//        $tpl = $this->_template($namespace,$controller);
        if(!file_put_contents($controllerPath,$result_controller_tpl)){
            $output->writeln("a error appear");
            return;
        }
        if(!file_put_contents($modelPath,$result_model_tpl)){
            $output->writeln("a error appear");
            return;
        }
        if(!file_put_contents($validatePath,$result_validate_tpl)){
            $output->writeln("a error appear");
            return;
        }
        //写入配置文件
        $configPath = env('config_path').DIRECTORY_SEPARATOR.'api_doc.php';
        if(false!==fopen($configPath,'w+')){
            $text="<?php \n return ".var_export($doc,true).';';
            file_put_contents($configPath,$text);
        }
        $output->writeln("Success");
    }
}