<?php


namespace App\Command;


use App\Entity\Info;
use App\Entity\Log;
use App\Library\Helper\GeneralHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class BuildApiLogCommand extends Command
{
    protected static $defaultName = 'build:api-log';

    private $container;

    private $em;

    private $open_api_config_path;

    private $info = null;


    public function __construct(ContainerInterface $container, string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->open_api_config_path = $this->container->getParameter('app.swagger.openapi.path');
    }

    protected function configure()
    {
        $this->addArgument('info_id', InputArgument::REQUIRED, '需要被执行的info数据id值')
            ->setDescription('生成接口更新报告')
            ->setHelp('根据指定info生成对应接口更新报告');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $info_id = $input->getArgument('info_id');
        $this->info = $this->em->getRepository(Info::class)->find($info_id);

        if (!is_object($this->info)) {
            return 404;
        }

        $info_file = $this->open_api_config_path . $this->info->getTag() . '_openapi.yaml';
        $info_bak_file = $this->open_api_config_path . $this->info->getTag() . '_openapi_bak.yaml';
        $this->buildAPILog($info_file, $info_bak_file);
    }

    /**
     * User: Gao
     * Date: 2020/2/18
     * Description: 生成API更新日志
     * @param $info_file
     * @param $info_bak_file
     * @throws \Exception
     */
    private function buildAPILog($info_file, $info_bak_file)
    {
        $result = [];

        $new_apis = Yaml::parseFile($info_file);
        $old_apis = Yaml::parseFile($info_bak_file);

        $new_api_paths = $new_apis['paths'] ?? [];
        $old_api_paths = $old_apis['paths'] ?? [];
        foreach ($new_api_paths as $url => $item) {
            $data = [];
            $data['path'] = $url;
            $data['action'] = Log::PATH_UPDATE;
            list($new_method, $new_data) = GeneralHelper::myEach($item);
            list($old_method, $old_data) = GeneralHelper::myEach($old_api_paths[$url]);
            if (isset($old_api_paths[$url])) {
                $data['body']['basic'] = $this->buildBasicData($new_method, $new_data, $old_method, $old_data);
                $data['body']['field'] = $this->buildFieldData($new_data, $old_data);
                // 删除已校验接口
                unset($old_api_paths[$url]);
                // 如果接口无变化则跳过
                if (count($data['body']['basic']) == 0 && count($data['body']['field']) == 0) {
                    continue;
                }
            } else {
                // 新增接口数据整理
                $data['body']['basic'] = $this->buildPathBasicData($item);
                $data['body']['field'] = $this->buildPathFieldData($item, Log::ACTION_CREATE);
                $data['action'] = Log::PATH_CREATE;
            }
            if ($new_data['update_admin_id'] != 0) {
                $data['admin_id'] = $new_data['update_admin_id'];
                $data['date'] = (new \DateTime())->setTimestamp($new_data['updated_at']);
            } else {
                $data['admin_id'] = $new_data['create_admin_id'];
                $data['date'] = (new \DateTime())->setTimestamp($new_data['created_at']);
            }
            array_push($result, $data);
        }
        if (count($old_api_paths) > 0) {
            foreach ($old_api_paths as $url => $item) {
                // 删除接口数据整理
                list($old_method, $old_data) = GeneralHelper::myEach($old_api_paths[$url]);
                $data = $body = [];
                $data['path'] = $url;
                $data['action'] = Log::PATH_REMOVE;
                if ($old_data['update_admin_id'] != 0) {
                    $data['admin_id'] = $old_data['update_admin_id'];
                    $data['date'] = (new \DateTime())->setTimestamp($old_data['updated_at']);
                } else {
                    $data['admin_id'] = $old_data['create_admin_id'];
                    $data['date'] = (new \DateTime())->setTimestamp($old_data['created_at']);
                }
                $data['body']['basic'] = $this->buildPathBasicData($item);
                $data['body']['field'] = $this->buildPathFieldData($item, Log::ACTION_REMOVE);
                array_push($result, $data);
            }
        }

        // 记录日志数据
        $this->batchInsertLog($result);
    }

    /**
     * User: Gao
     * Date: 2020/2/18
     * Description: 批量记录日志数据
     * @param $data
     */
    private function batchInsertLog($data)
    {
        $batchSize = 20;
        $i = 0;
        foreach ($data as $item) {
            $log = new Log();
            $log->setInfoId($this->info->getId());
            $log->setVersion($this->info->getVersion());
            $log->setBody(json_encode($item['body'], JSON_UNESCAPED_UNICODE));
            $log->setPath($item['path']);
            $log->setAction($item['action']);
            $log->setAdminId($item['admin_id']);
            $log->setDate($item['date']);
            $this->em->persist($log);
            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
            $i++;
        }
        $this->em->flush();
        $this->em->clear();
    }

    private function buildPathBasicData($data)
    {
        $basic_data = [];
        list($method, $data) = GeneralHelper::myEach($data);

        $basic_data['method'] = $method;
        $basic_data['tag'] = $data['tags'][0] ?? '';
        $basic_data['summary'] = $data['summary'] ?? '';
        $basic_data['description'] = $data['description'] ?? '';
        $basic_data['security'] = count($data['security']) > 0 ? 'true' : 'false';

        return $basic_data;
    }

    private function buildPathFieldData($data, $action)
    {
        $result = [];
        list($method, $data) = GeneralHelper::myEach($data);
        $_new_parameters = $data['parameters'];
        $new_parameters = [];
        foreach ($_new_parameters as $parameter) {
            $new_parameters[$parameter['in']][$parameter['name']] = $parameter;
        }
        // 整理body类型参数
        $_new_body_parameters = $data['requestBody']['content']['application/json']['schema'] ?? [];
        if (count($_new_body_parameters) > 0) {
            $body_required = $_new_body_parameters['required'];
            foreach ($_new_body_parameters['properties'] as $key => $item) {
                $body_parameters = [
                    'name' => $key,
                    'in' => 'body',
                    'description' => $item['description'],
                    'required' => in_array($key, $body_required),
                    'schema' => [
                        'type' => $item['type']
                    ]
                ];
                $new_parameters['body'][$key] = $body_parameters;
            }
        }
        if (count($_new_parameters)) {
            foreach ($new_parameters as $in => $in_data) {
                foreach ($in_data as $field => $new_item) {
                    // 字段新增
                    $field_data = $this->buildItem($in, $field, $action, $new_item);
                    array_push($result, $field_data);
                }
            }
        }

        return $result;
    }

    /**
     * User: Gao
     * Date: 2020/2/14
     * Description: 整合basic更新数据
     * @param $new_method
     * @param $new_data
     * @param $old_method
     * @param $old_data
     * @return array
     */
    private function buildBasicData($new_method, $new_data, $old_method, $old_data): array
    {
        $basic_data = [];
        $sign = '<code>=></code>';

        if ($new_method != $old_method) {
            $basic_data['method'] = '<s>' . $old_method . '</s>' . $sign . $new_method;
        }
        if ($new_data['tags'][0] != $old_data['tags'][0]) {
            $basic_data['tag'] = '<s>' . $old_data['tags'][0] . '</s>' . $sign . $new_data['tags'][0];
        }
        if ($new_data['summary'] != $old_data['summary']) {
            $basic_data['summary'] = '<s>' . $old_data['summary'] . '</s>' . $sign . $new_data['summary'];
        }
        if ($new_data['description'] != $old_data['description']) {
            $basic_data['description'] = '<s>' . $old_data['description'] . '</s>' . $sign . $new_data['description'];
        }
        if (count($new_data['security']) != count($old_data['security'])) {
            $basic_data['security'] = count($new_data['security']) > 0 ? '<s>false</s>' . $sign . 'true' : '<s>true</s>' . $sign . 'false';
        }

        return $basic_data;
    }

    /**
     * User: Gao
     * Date: 2020/2/14
     * Description: 整合field更新数据
     * @param $new_data
     * @param $old_data
     * @return array
     */
    private function buildFieldData($new_data, $old_data): array
    {
        $result = [];
        $sign = '<code>=></code>';

        $_new_parameters = $new_data['parameters'];
        $_old_parameters = $old_data['parameters'];
        $new_parameters = $old_parameters = [];
        foreach ($_new_parameters as $parameter) {
            $new_parameters[$parameter['in']][$parameter['name']] = $parameter;
        }
        foreach ($_old_parameters as $parameter) {
            $old_parameters[$parameter['in']][$parameter['name']] = $parameter;
        }
        // 整理body类型参数
        $_new_body_parameters = $new_data['requestBody']['content']['application/json']['schema'] ?? [];
        $_old_body_parameters = $old_data['requestBody']['content']['application/json']['schema'] ?? [];
        if (count($_new_body_parameters) > 0) {
            $body_required = $_new_body_parameters['required'];
            foreach ($_new_body_parameters['properties'] as $key => $item) {
                $body_parameters = [
                    'name' => $key,
                    'in' => 'body',
                    'description' => $item['description'],
                    'required' => in_array($key, $body_required),
                    'schema' => [
                        'type' => $item['type']
                    ]
                ];
                $new_parameters['body'][$key] = $body_parameters;
            }
        }
        if (count($_old_body_parameters) > 0) {
            $body_required = $_old_body_parameters['required'];
            foreach ($_old_body_parameters['properties'] as $key => $item) {
                $body_parameters = [
                    'name' => $key,
                    'in' => 'body',
                    'description' => $item['description'],
                    'required' => in_array($key, $body_required),
                    'schema' => [
                        'type' => $item['type']
                    ]
                ];
                $old_parameters['body'][$key] = $body_parameters;
            }
        }

        if (count($_new_parameters)) {
            foreach ($new_parameters as $in => $in_data) {
                foreach ($in_data as $field => $new_item) {
                    $field_data = [];
                    // 字段更新
                    if (isset($old_parameters[$in][$field])) {
                        $old_item = $old_parameters[$in][$field];
                        $field_data['type'] = $in;
                        $field_data['action'] = Log::ACTION_UPDATE;
                        $field_data['key'] = $field;
                        if ($new_item['description'] != $old_item['description']) {
                            $field_data['description'] = '<s>' . $old_item['description'] . '</s>' . $sign . $new_item['description'];
                        }
                        if ($new_item['schema']['type'] != $old_item['schema']['type']) {
                            $field_data['format'] = 'type: <s>' . $old_item['schema']['type'] . '</s>' . $sign . $new_item['schema']['type'];
                        }
                        if (isset($new_item['schema']['format'])) {
                            if (isset($old_item['schema']['format'])) {
                                if ($new_item['schema']['format'] != $old_item['schema']['format']) {
                                    $field_data['format'] = 'format: <s>' . $old_item['schema']['format'] . '</s>' . $sign . $new_item['schema']['format'];
                                }
                            } else {
                                $field_data['format'] = 'format: ' . $sign . $new_item['schema']['format'];
                            }
                        }
                        if ($new_item['required'] != $old_item['required']) {
                            $field_data['required'] = $new_item['required'] ? '<s>false</s>' . $sign . 'true' : '<s>true</s>' . $sign . 'false';
                        }
                        // 移除已经匹配的字段，为删除字段数据做准备
                        unset($old_parameters[$in][$field]);
                    } else {
                        // 字段新增
                        $field_data = $this->buildItem($in, $field, Log::ACTION_CREATE, $new_item);
                    }
                    if (count($field_data) > 3) {
                        array_push($result, $field_data);
                    }
                }
                // 字段删除
                if (isset($old_parameters[$in]) && count($old_parameters[$in])) {
                    foreach ($old_parameters[$in] as $field => $remove_item) {
                        $field_data = $this->buildItem($in, $field, Log::ACTION_REMOVE, $remove_item);
                        array_push($result, $field_data);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * User: Gao
     * Date: 2020/2/18
     * Description: 整理field数据
     * @param $in
     * @param $field
     * @param $action
     * @param $item
     * @return array
     */
    private function buildItem($in, $field, $action, $item)
    {
        $field_data = [];

        $field_data['type'] = $in;
        $field_data['action'] = $action;
        $field_data['key'] = $field;
        $field_data['description'] = $item['description'];
        if (isset($item['schema']['format'])) {
            $field_data['format'] = 'type: ' . $item['schema']['type'] . ', format: ' . $item['schema']['format'];
        } else {
            $field_data['format'] = 'type: ' . $item['schema']['type'];
        }
        $field_data['required'] = $item['required'] ? 'true' : 'false';

        return $field_data;
    }

}